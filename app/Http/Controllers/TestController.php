<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Spatie\Ssh\Ssh;

use App\FormStats;
use App\User;

class TestController extends Controller
{
    private function generateRandomString(int $length = 32): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function importToDropinLeadsTable(Request $request)
    {
        $todayLeads = [
            '+(39)3804916544',
            '+(39)3488149818',
            '+(39)3317443111',
            '+(39)3405614088',
            '+(39)3487008053',
            '+(39)3421022680',
            '+(39)3393682737',
        ];
        $leads = \App\third\DropinLead::where('id', '>=', 69)
            ->where('id', '<=', 164)->get();
        $leadsDone = [];
        foreach ($leads as $lead) {
            if (in_array($lead->phone, $todayLeads)) {
                continue;
            }

            $info = $lead->info;
            if ($info === null) {
                $info = [];
            }
            /*
            unset($info['products']);
            $info['sku'] = '2433-911-18031';
            $info['product_id'] = 343;
            $info['product_name'] = 'Tazza Elettrica';
            */

            $wLead = \App\third\WarehouseLead::where('phone', $lead->phone)->where('archived', 0)->get()->last();
            if ($wLead !== null) {
                //$lead->created_at = $wLead->created_at;
                if ($wLead->info !== null && isset($wLead->info['shipping'])) {
                    $info['shipping'] = $wLead->info['shipping'];
                }
            }

            $lead->info = $info;

            /*
            $notes = $lead->notes;
            if ($notes === null) {
                $notes = '';
            }
            $notes = 'IMPORTED ' . $notes;
            $lead->notes = $notes;
            */

            $lead->save();

            $leadsDone[] = [
                'phone' => $lead->phone,
                'info'  => $lead->info,
                'notes' => $lead->notes,
                'date'  => $lead->created_at,
            ];
        }
        dd([
            'count' => count($leadsDone),
            'leads' => $leadsDone,
        ]);
        exit();
        // TODO: HERE

        $tmpLeads = \App\third\WarehouseLead::where('created_at', '>=', '2022-01-01 00:00:00')
            ->whereIn('status', ['to do', 'confirmed', 'abandoned', 'to recall'])
            ->get();
        $warehouseLeads = [];
        foreach ($tmpLeads as $lead) {
            $info = $lead->info;
            if ($info === null) {
                $info = [];
            }

            $productName = null;
            if (isset($info['product_name'])) {
                $productName = $info['product_name'];
            }
            if ($productName === 'Tazza Elettrica') {
                $warehouseLeads[] = $lead;
            }

            if (!isset($info['gateway'])) {
                $info['gateway'] = 'cashondelivery';
                $info['payment_method'] = 'cashondelivery';
            }

            $info['price'] = '39.90';
            $info['total'] = '39.90';
            $info['sku'] = '2433-911-18031';
        }
        $dropinLeads = [];
        foreach ($warehouseLeads as $lead) {
            $dropinLeads[] = [
                'first_name' => $lead->first_name,
                'last_name' => $lead->last_name,
                'phone' => $lead->phone,
                'email' => $lead->email,
                'status' => $lead->status,
                'info' => $info,
                'notes' => $lead->notes,
                'hash' => $this->generateRandomString(64),
                'archived' => $lead->archived,
                'created_at' => $lead->created_at,
                'updated_at' => $lead->updated_at,
            ];
        }
        $finalLeads = [];
        foreach ($dropinLeads as $lead) {
            $finalLeads[] = \App\third\DropinLead::create($lead);
        }
        dd($finalLeads);
        exit();
    }

    public function test(Request $request)
    {
        /*
        $strings = array(
            'general' => array (
                'currency' => '€'
            ),

            'head' => array(
                'title' => "KitchPro - Pianeta Bellezza",
                'short_description' => "Esplora il tuo vero stile.",
                'description' => " Carne, verdura e frutta si tritano facilmente e velocemente Precedente Successivo RISPARMIA TEMPO UN DESIGN FACILE DA USARE 2 modalità di macinazione Facile da tagliare e durerà per anni. Acciaio inossidabile, taglio efficace. Adatto per uso domestico e aziendale SODDISFARE IL BISOGNO DI CASA - GRANDE CAPACITÀ Interruttore conveniente, lavora facilmente. Sicuro da usare,...",
                'url' => "https://pianetabellezza.com/kitchpro/",
                'language' => "it_IT",
            ),

            'section-1' => array(
                'title' => "Carne, verdura e frutta si tritano facilmente e velocemente",
                'advantages' => array(
                    1 => 'RISPARMIA TEMPO',
                    2 => 'UN DESIGN FACILE DA USARE',
                    3 => '2 modalità di macinazione',
                    4 => 'Facile da tagliare e durerà per anni.',
                    5 => 'Acciaio inossidabile, taglio efficace.',
                    6 => 'Adatto per uso domestico e aziendale',
                    7 => 'SODDISFARE IL BISOGNO DI CASA - GRANDE CAPACITÀ',
                    8 => 'Interruttore conveniente, lavora facilmente.',
                    9 => 'Sicuro da usare, il motore termico si fermerà automaticamente',
                ),
                'image' => array(
                    1 => './files/B-2.jpeg',
                    2 => './files/AAGIIF.gif',
                    3 => './files/C.png',
                ),
                'truck-symbol-text' => 'Consegna gratuita entro 48 ore',
                'money-symbol-text' => 'Pagamento sicuro alla consegna',
            ),
            'section-2' => array (
                'title' => 'OFFERTA SPECIALE -40%',
                'old_price' => '99,83',
                'new_price' => '59,90',
                'button_text' => 'ORDINA ORA -40%',
            ),
            'section-3' => array (
                'title' => "L'elettrodomestico da cucina universale macina, taglia, sminuzza e impasta il cibo per preparare piatti deliziosi più velocemente!",
                'sub_title' => 'Prepara pasti completi con un solo utensile da cucina ! Ordinando oggi a SOLI 59€',
                'image' => './files/BAANANNA-1024x576.png',
            ),
            'section-4' => array (
                'description' => "<b><i>KitchPro  </i></b> è una cucina polivalente, che macina, taglia, sminuzza e impasta i cibi per preparare piatti sfiziosi più velocemente! Con una forte lama in acciaio inossidabile taglierà facilmente la carne più dura e le verdure più dure. Scegli come e quanto vuoi tagliare o macinare gli ingredienti nei tuoi piatti.<br>Sfrutta al massimo il tuo tempo! Questo apparecchio è il modo più efficiente di cucinare perché elabora tutti gli ingredienti necessari per il cibo della tua famiglia.",
                'items' => array (
                    1 => array (
                        'image_url' => './files/CARA1.png',
                        'title' => 'TUTTO È COTTO IN MODO UNIFORME',
                        'description' => 'Il robot taglia gli ingredienti in pezzi di uguali dimensioni. Carne e verdure saranno cotte contemporaneamente, senza bruciature e pezzi crudi.',
                    ),
                    2 => array (
                        'image_url' => './files/CARA2-2.jpeg',
                        'title' => 'ELABORAZIONE VELOCE DEGLI INGREDIENTI',
                        'description' => 'Bastano solo 30 secondi per tagliare o macinare gli ingredienti desiderati! Ciò è garantito da un potente motore in rame da 300 W, un albero di alloggiamento in acciaio e 4 robuste lame.',
                    ),
                    3 => array (
                        'image_url' => './files/CARA3-1.jpeg',
                        'title' => 'DISORDINE IN CUCINA',
                        'description' => 'Oltre a fornire più spazio sulla scrivania, la parte inferiore del dispositivo è protetta da un anello antiscivolo. Il dispositivo non ruota e gli ingredienti non si rovesciano o si rovesciano sul piano di lavoro.',
                    ),
                    4 => array (
                        'image_url' => './files/CARA4-1.jpeg',
                        'title' => 'SOSTITUISCE ALTRI ELETTRODOMESTICI DA CUCINA',
                        'description' => 'Dì addio a un bancone della cucina disordinato pieno di elettrodomestici diversi! In esso ottieni tutte le funzioni di un dispositivo per spremere, frantumare, mescolare e impastare il succo.',
                    ),
                ),
            ),
            'section-5' => array (
                'title' => 'CARATTERISTICHE:',
                'caratteristiche' => array(
                    1 => 'Capacità: 2L',
                    2 => 'Peso del prodotto: 2 KG',
                    3 => 'Tensione nominale: 220V.',
                    4 => 'Frequenza nominale: 50 Hz.',
                    5 => 'Materiale: acciaio inossidabile.',
                    6 => 'Potenza nominale: 200 W-300W.',
                    7 => 'Dimensioni del pacchetto: 22,5 * 25 cm.',
                    8 => 'Velocità di rotazione: 18000-22000 giri/min.',
                ),
                'image' => array(
                    1 => './files/2-3.jpeg',
                    2 => './files/3-1.jpeg',
                    3 => './files/1.png',
                ),
            ),
        );
        $template = \App\Template::create([
            'name' => 'KitchPro',
            'info' => [ 'strings' => $strings ],
        ]);
        dd($template);
        */
        $template = \App\Template::findOrFail(2);
        dd($template->installImages([
            "http://zarolead.localhost/storage/tmp//RqfuoTNPCDtEKHco.png",
            "http://zarolead.localhost/storage/tmp//mDBraXSXWgKwrzAh.png",
            "http://zarolead.localhost/storage/tmp//XTAPAaqIsdXHOnjH.png",
            "http://zarolead.localhost/storage/tmp//vWpjiqLuCYHUuGNR.png",
            "http://zarolead.localhost/storage/tmp//SbFNYQRcMctZkSiD.png",
            "http://zarolead.localhost/storage/tmp//cvkuhUWFhPDIMjhK.png",
            "http://zarolead.localhost/storage/tmp//MTNwUviHMVYZYoyG.png",
            "http://zarolead.localhost/storage/tmp//srPtMGSibQStKMIc.png",
            "http://zarolead.localhost/storage/tmp//XRDXwGqrHuXPdfvG.png",
            "http://zarolead.localhost/storage/tmp//bTScwbOkwggGAAyI.png",
            "http://zarolead.localhost/storage/tmp//wPqQUuTqykqusemH.png",
        ]));
        dd($template->cloneRoot());
        dd($template->install(2, 'public/template_installed'));
        dd($template);
        dd('test');
    }

    public function test2(Request $request)
    {
        dd($request->all());
    }
    
    // DEBUG TODO: to delete
    public function testCheckout()
    {
        return view('checkout');
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
