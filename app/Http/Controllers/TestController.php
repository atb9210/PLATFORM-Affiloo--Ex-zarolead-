<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Form;
use App\FormStats;

class TestController extends Controller
{
    public function test()
    {
        /*
    	$parser = new \Smalot\PdfParser\Parser();
    	$pdf    = $parser->parseFile('test_eng.pdf');
    	$text = $pdf->getText();

    	$rows = explode("\n", $text);
    	foreach ($rows as $row) {
    		echo $row . "<br><br>";
    	}

    	$CHECK_LANGUAGE_ROW_NUMBER = 0;
    	$DATE_ROW_NUMBER = 3;
    	$STATUS_ROW_NUMBER = 10;
    	$AMOUNT_ROW_NUMBER = 11;

    	// Get language
    	$lang = 'unknown';
		$words = explode(' ', $rows[$CHECK_LANGUAGE_ROW_NUMBER]);
    	if ($words[0] == 'Invoice') {
    		$lang = 'eng';
    	} else if ($words[0] == 'Fattura') {
    		$lang = 'it';
    	}

    	// Get invoice date
    	$invoice_date = $rows[$DATE_ROW_NUMBER];
    	if ($lang == 'eng') {
    	}

    	echo " -- Language: " . $lang . "<br>";
    	echo " -- Invoice date: " . $invoice_date . "<br>";

    	$invoice_date = '';
    	if ($lang == 'eng') {
    		$i = 0;
    		while ($i < count($words)) {
    			if ($words[$i] != 'Date') {
    				$i += 1;
    				continue;
    			}
    			$invoice_date .= $words[$i+1] . ' ';
    			$invoice_date .= $words[$i+2] . ' ';
    			$invoice_date .= $words[$i+3] . ' ';
    			$invoice_date .= $words[$i+4] . ' ';
    			$invoice_date .= $words[$i+5];
    			$i += 1;
    		}
    	}

		echo $text."<br><br><br>";

		echo $lang."<br>";
		echo "--".$invoice_date."--"."<br>";
    	exit();
        */

        $forms = Form::all();

        foreach ($forms as $form) {
            $info = $form->info;
            unset($info['build_data']['product']);
            $info['build_data']['product_id'] = 1;

            $form->info = $info;

            $form->save();
        }
    }

    public function post(Request $request) {
        echo "<pre>" . var_export($request, true) . "</pre>";
        exit();
    }
}
