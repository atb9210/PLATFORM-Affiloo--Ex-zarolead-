<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\FormStats;
use App\User;

class TestController extends Controller
{
    public function test()
    {
        $users = User::query();
        $users = $users->join('role_user', 'role_user.user_id', '=', 'id')
            ->whereIn('role_user.role_id', [2]);
        $users = $users->get();
        foreach ($users as $user) {
            echo $user->name . "<br>";
        }
    }

    public function post(Request $request)
    {
        return response([
            'YO' => 'man'
        ]);
    }

    public function hash(Request $request)
    {
        $result = '';
        $psw = '';
        if($request->input('psw') != '') {
            $psw = $request->input('psw');
            $result = Hash::make($psw);
        }
        return view('psw-maker')->with([
            'psw' => $psw,
            'result' => $result,
        ]);
    }
}
