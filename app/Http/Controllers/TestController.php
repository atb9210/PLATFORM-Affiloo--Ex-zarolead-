<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use App\Landing;

class TestController extends Controller
{
    public function test()
    {
        $rules = [
            'landings' => [
                [
                    'id' => 1,
                    'weight' => 5,
                ],
                [
                    'id' => 2,
                    'weight' => 11,
                ],
            ],
        ];

        $result = '';

        if (isset($rules['landings'])) {
            $landings = $rules['landings'];
            if(is_array($landings) && count($landings) > 0) {
                $totalWeight = 0;
                $intervals = [];
                $intervalsCount = 0;
                foreach ($landings as $landing) {
                    if (isset($landing['weight']) && is_numeric($landing['weight'])) {
                        $intervals[] = [
                            'from' => $totalWeight + 1,
                            'to' => $totalWeight + $landing['weight'],
                        ];
                        $totalWeight += $landing['weight'];
                    } else {
                        $intervals[] = [
                            'from' => 0,
                            'to' => 0,
                        ];
                    }
                }

                if ($totalWeight > 0) {
                    $randomNumber = mt_rand(1, $totalWeight);
                }

                $winner = -1;
                for ($i = 0; $i < count($intervals); $i += 1) {
                    if ($randomNumber >= $intervals[$i]['from'] && $randomNumber <= $intervals[$i]['to']) {
                        $winner = $i;
                        break;
                    }
                }

                if ($winner >= 0 && isset($landings[$winner]['id'])) {
                    $landingId = $landings[$winner]['id'];

                    // TODO: Get landing url
                    $landingObj = Landing::find($landingId);
                    if ($landingObj != null) {
                        $result = $landingObj->url;
                    }
                }
            }
        }

        echo "Result: " . $result;
    }

    public function post(Request $request)
    {
        echo "<pre>" . var_export($request, true) . "</pre>";
        exit();
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
