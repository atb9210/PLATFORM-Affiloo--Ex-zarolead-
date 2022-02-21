<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\User as User;
use App\UserReport as UserReport;
use App\Http\Resources\api\v1\UserReportResource;
use App\Http\Resources\api\v1\UserResource;
use App\Role as Role;

class UserController extends Controller
{
    private $roles = [
        'admin',
        'mediabuyer',
        'affiliate',
        'advertiser',
        'accountant',
        'buyer',
        'account_manager',
    ];

    /**
     * Get all users list.
     *
     * @urlParam filter_roles array User's roles
     *
     * @response {
     *  "data": [users],
     * }
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', 'App\User');

        // Param validation
        $params = $request->validate([
            'filter_roles' => 'array',
            'filter_roles.*' => 'integer|exists:roles,id',
        ]);

        $users = User::query();
        if (isset($params['filter_roles'])) {
            $users = $users->join('role_user', 'role_user.user_id', '=', 'id')
                ->whereIn('role_user.role_id', $params['filter_roles']);
        }

        return UserResource::collection($users->get());
    }

    /**
     * Get user's informations.
     *
     * @urlParam id int required User's id
     *
     * @response {
     *  "data": user,
     * }
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Create user.
     *
     * @bodyParam name string required User's name
     * @bodyParam email string required User's email
     * @bodyParam password string required User's password
     * @bodyParam role string required User's role
     *
     * @response {
     *  "data": user,
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\User');

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:3|max:255',
            'email' => 'email|min:4|max:255|unique:users,email',
            'password' => 'string|min:7|max:255',
            'role' => 'required|in:' . join(',', $this->roles),
        ]);

        // Create user
        $user = User::create([
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => Hash::make($params['password']),
        ]);

        // Set role
        $role = Role::where('name', $params['role'])->first();
        $user->roles()->attach($role->id);

        return response([
            'data' => new UserResource($user)
        ], 201);
    }

    /**
     * Update the user's fields.
     *
     * @urlParam id int required User's id
     *
     * @bodyParam name string User's new name
     * @bodyParam email string User's new email
     * @bodyParam password string User's new password
     * @bodyParam status string User's new status
     * @bodyParam role string User's new role
     * @bodyParam info json User's new info
     *
     * @response {
     *  "data": user,
     * }
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        // Param validation
        $params = $request->validate([
            'name' => 'string|min:3|max:255',
            'email' => 'email|min:4|max:255',
            'password' => 'string|min:7|max:255',
            'status' => 'string|in:active,blocked',
            'role' => 'in:' . join(',', $this->roles),
            'info' => 'json',
        ]);
        if (isset($params['password'])) {
            $params['password'] = Hash::make($params['password']);
        }
        if (isset($params['info'])) {
            $params['info'] = json_decode($params['info'], TRUE);
        }

        $user->update($params);

        if (isset($params['role'])) {
            $user->setOnlyRole($params['role']);
        }

        return new UserResource($user);
    }

    /**
     * Delete the specified user.
     *
     * @urlParam id int required User's id
     *
     * @response {
     *  "message": "User archived",
     * }
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        // Archive the user
        $user->archived = 1;
        $user->save();

        return response([
            'message' => 'User archived'
        ]);
    }

    /**
     * Change user's password.
     *
     * @urlParam id int required User's id
     *
     * @bodyParam old_password string required User's old password
     * @bodyParam password string required User's new password
     * @bodyParam password_confirmation string required Repeat user's new password
     *
     * @response {
     *  "data": user,
     * }
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        // Param validation
        $params = $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($params['old_password'], $user->password)) {
            return response([
                "errors" => [
                    "old_password" => ["Current password is wrong"],
                ],
            ], 403);
        }

        $user->password = Hash::make($params['password']);
        $user->save();

        if ($user->id === Auth::user()->id) {
            Auth::user()->refresh();
        }

        return new UserResource($user);
    } 

    /**
     * Change user's roles.
     *
     * @urlParam id int required User's id
     *
     * @bodyParam admin boolean Admin role
     * @bodyParam mediabuyer boolean Admin role
     * @bodyParam affiliate boolean Admin role
     * @bodyParam advertiser boolean Admin role
     * @bodyParam accountant boolean Admin role
     * @bodyParam buyer boolean Admin role
     * @bodyParam account_manager boolean Admin role
     *
     * @response {
     *  "data": user,
     * }
     */
    public function changeRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('changeRoles', $user);

        // Param validation
        $data = $request->validate([
            'admin' => 'boolean',
            'mediabuyer' => 'boolean',
            'affiliate' => 'boolean',
            'advertiser' => 'boolean',
            'accountant' => 'boolean',
            'buyer' => 'boolean',
            'account_manager' => 'boolean',
            'callcenter' => 'boolean',
        ]);

        // User cannot remove himself admin role
        if (Auth::user()->id == $user->id && isset($data['admin']) && !$data['admin']) {
            return response([
                "message" => "You cannot remove admin role to yourself."
            ], 403);
        }

        // Change roles
        foreach ($data as $name => $value) {
            $role = Role::where('name', $name)->first();
            if ($value) {
                if (!$user->roles()->where('id', $role->id)->exists()) {
                    $user->roles()->attach($role->id);
                }
            } else {
                if ($user->roles()->where('id', $role->id)->exists()) {
                    $user->roles()->detach($role->id);
                }
            }
        }
        Auth::user()->refresh();

        return new UserResource($user);
    }

    /**
     * Get user's report.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     *
     * @response {
     *  "data": [user_report],
     * }
     */
    public function getReports(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to' => 'date|date_format:Y-m-d',
        ]);

        $reports = UserReport::where('user_id', Auth::user()->id);
        if (isset($params['filter_date_from'])) {
            $reports = $reports->where('day', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $reports = $reports->where('day', '<=', $params['filter_date_to']);
        }
        $reports = $reports->orderBy('day', 'desc');

        return UserReportResource::collection($reports->get());
    }

    /**
     * Update the user's report fields.
     *
     * @bodyParam day required string User's report day
     * @bodyParam commission float User's report commission
     * @bodyParam cost float User's report cost
     * @bodyParam extra_cost float User's report extra cost
     * @bodyParam notes string User's report notes
     *
     * @response {
     *  "data": user_report,
     * }
     */
    public function updateReport(Request $request)
    {
        // Param validation
        $data = $request->validate([
            'day' => 'required|date|date_format:Y-m-d',
            'commission' => 'numeric|numeric|min:0',
            'cost' => 'numeric|numeric|min:0',
            'extra_cost' => 'numeric|numeric|min:0',
            'notes' => 'nullable|string|min:0',
        ]);

        $report = UserReport::getObject(Auth::user()->id, $data['day']);
        $report->update($data);

        // Update report stats
        $report->profit = $report->commission - ($report->cost + $report->extra_cost);
        $report->save();

        return new UserReportResource($report);
    }
}
