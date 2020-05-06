<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Form;
use App\FormStats;

class TestController extends Controller {
    public function test() {
        exit();
    }

    public function post(Request $request) {
        echo "<pre>" . var_export($request, true) . "</pre>";
        exit();
    }
}
