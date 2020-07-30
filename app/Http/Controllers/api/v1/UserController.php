<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User as User;
use App\UserReport as UserReport;
use App\Http\Resources\api\v1\UserReportResource;
use App\Http\Resources\api\v1\UserResource;
use App\Role as Role;

class UserController extends Controller
{
    /**
     * Get all users list.
     *
     * @response {
     *  "data": [users],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\User');

        return UserResource::collection(User::all());
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
     * Update the user's fields.
     *
     * @urlParam id int required User's id
     *
     * @bodyParam name string User's new name
     * @bodyParam email string User's new email
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
        $data = $request->validate([
            'name' => 'string|min:3|max:255',
            'email' => 'email|min:4|max:255',
        ]);

        $user->update($data);

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
     * Change user's roles.
     *
     * @urlParam id int required User's id
     *
     * @bodyParam admin boolean Admin role
     * @bodyParam mediabuyer boolean Admin role
     * @bodyParam affiliate boolean Admin role
     * @bodyParam advertiser boolean Admin role
     * @bodyParam accountant boolean Admin role
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

        return new UserResource(Auth::user());
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
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
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
            'extra_cost' => 'numeric|numeric|min:0',
            'notes' => 'nullable|string|min:0',
        ]);

        $report = UserReport::getObject(Auth::user()->id, $data['day']);
        $report->update($data);

        return new UserReportResource($report);
    }
}
