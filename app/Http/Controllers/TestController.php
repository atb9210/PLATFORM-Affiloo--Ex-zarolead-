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
    private function getDirectoryContent($directory) {
        $content = [];

        $directoryPathname = $directory;
        if ($directoryPathname[strlen($directoryPathname) - 1] != '/') {
            $directoryPathname .= '/';
        }

        $directories = Storage::disk('landings')->directories($directory);
        foreach ($directories as $dir) {
            $dirContent = $this->getDirectoryContent($dir);
            $content[] = [
                'pathname' => str_replace($directoryPathname, '', $dir),
                'type' => 'directory',
                'data' => $dirContent,
            ];
        }

        $files = Storage::disk('landings')->files($directory);
        foreach ($files as $file) {
            $content[] = [
                'pathname' => str_replace($directoryPathname, '', $file),
                'type' => 'file',
                'data' => '',
            ];
        }

        return $content;
    }

    public function test()
    {
        $content = $this->getDirectoryContent('test-landing-barba');
        echo "<pre>".var_export($content, true)."</pre>";
        exit();

        $params = [
            'test1' => 'ciaone',
            'test2' => '_ciaone',
            'test3' => '3ciaone',
            'test4' => 'Ci2_aon.,a.?e',
            'test5' => 'Ci2_P?e',
            'test6' => 's123123_123123123',
            'test7' => 'ciao a tutti',
            'test8' => '34 ciao',
        ];
        $validator = Validator::make($params, [
            'test1' => 'required|string|landing_path',
            'test2' => 'required|string|landing_path',
            'test3' => 'required|string|landing_path',
            'test4' => 'required|string|landing_path',
            'test5' => 'required|string|landing_path',
            'test6' => 'required|string|landing_path',
            'test7' => 'required|string|landing_path',
            'test8' => 'required|string|landing_path',
        ]);

        //echo "<pre>".var_export($validator->messages(), true)."</pre>";
        //if ($validator->fails()) {
        //    echo "<pre>".var_export($validator->errors(), true)."</pre>";
        //} else {
        //    echo "OK";
        //}
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
