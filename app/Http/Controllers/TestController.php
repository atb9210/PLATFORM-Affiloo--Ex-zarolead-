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
    private function getOrderByPhone($eOrders, $leadPhone)
    {
        foreach ($eOrders as $order) {
            if (!isset($order['shipping_phone']) && !isset($order['phone']) && !isset($order['customer']['phone'])) {
                continue;
            }
            $phone = '';
            if (isset($order['shipping_phone']) && $order['shipping_phone'] != '') {
                $phone = $order['shipping_phone'];
            } else if (isset($order['phone']) && $order['phone'] != '') {
                $phone = $order['phone'];
            } else if (isset($order['customer']['phone']) && $order['customer']['phone'] != '') {
                $phone = $order['customer']['phone'];
            }
            /*
            if ($phone == '+(39)3278321411') {
                dd($order);
            }
            */
            if ($phone != '' && $phone == $leadPhone) {
                return $order;
            }
        }
        return null;
    }

    private function getOrderByNumber($eOrders, $number)
    {
        foreach ($eOrders as $order) {
            if ($order['order_number'] == $number) {
                return $order;
            }
        }
        return null;
    }

    private function getLeadByPhone($wLeads, $phone)
    {
        if ($phone == '') {
            return null;
        }
        foreach ($wLeads as $wl) {
            if (str_contains($wl->phone, $phone)) {
                return $wl;
            }
        }
        return null;
    }

    private function getCurrencyValue($s)
    {
        $s = str_replace(',', '.', $s);
        return floatval($s);
    }

    private $leadsWithNumber = [
        ['3387707954','EL17112021042748',],
        ['3247789690','EL17112021042737',],
        ['3278321411','EL17112021042638',],
        ['3281249757','EL17112021042501',],
        ['3476643549','EL17112021042415',],
        ['3206258148','EL30112021151239',],
        ['3487079264','EL17112021164941',],
        ['3898995927','EL19112021154457',],
        ['3511477223','EL30112021151943',],
        ['3204103236','EL19112021135739',],
        ['3358274887','EL19112021153029',],
        ['3807986630','EL30112021160415',],
        ['3383991028','EL30112021162403',],
        ['3291392952','EL30112021154429',],
        ['3407828528','EL30112021152126',],
        ['3881134175','EL27112021073826',],
        ['3349135317','EL30112021151910',],
        ['998441873','EL30112021154502',],
        ['3425791655','EL30112021152127',],
        ['3927188969','EL30112021154056',],
        ['3479760280','EL30112021160417',],
        ['3332923773','EL30112021152205',],
        ['3294173286','EL30112021160739',],
        ['3492333591','EL30112021152128',],
        ['3456118915','EL30112021154503',],
        ['3387869287','EL30112021160418',],
        ['3283181976','EL30112021151912',],
        ['3397751849','EL30112021162405',],
        ['3281172595','EL22112021204513',],
        ['3398882341','EL22112021174239',],
        ['3939790894','EL28112021163005',],
        ['3774190541','EL30112021152129',],
        ['3486087980','EL30112021154431',],
        ['3929015727','EL30112021160419',],
        ['3471785448','EL30112021151913',],
        ['3331929500','EL30112021154504',],
        ['3295429921','EL30112021162406',],
        ['3713675784','EL30112021160420',],
        ['3283291952','EL30112021151914',],
        ['3248953040','EL30112021152207',],
        ['3384990107','EL30112021152131',],
        ['3497587800','EL30112021160421',],
        ['3356273424','EL30112021151915',],
        ['3384613573','EL30112021154059',],
        ['3290957859','EL30112021151950',],
        ['3478747850','EL30112021152132',],
        ['3201633563','EL30112021160422',],
        ['3807277633','EL30112021154506',],
        ['3382961120','EL30112021160742',],
        ['3663524241','EL30112021162408',],
        ['3332194695','EL30112021160423',],
        ['3492909115','EL30112021151952',],
        ['3714855221','EL30112021154434',],
        ['3713155955','EL30112021160424',],
        ['3351639293','EL30112021151918',],
        ['438486772','EL30112021151953',],
        ['3476260960','EL30112021154101',],
        ['3318192824','EL30112021160425',],
        ['3492299465','EL30112021154435',],
        ['3883775098','EL30112021154508',],
        ['3888827667','EL23112021074810',],
        ['3476102886','EL22112021195800',],
        ['3479953432','EL30112021151954',],
        ['3713216890','EL30112021152136',],
        ['3668918960','EL30112021154102',],
        ['3921057675','EL30112021151955',],
        ['3455965227','EL30112021154436',],
        ['3456974546','EL30112021152137',],
        ['3383827370','EL30112021152211',],
        ['3342449116','EL30112021154103',],
        ['3476472723','EL30112021152138',],
        ['3336100215','EL30112021154437',],
        ['3454843608','EL30112021152139',],
        ['3384865300','EL30112021154104',],
        ['3332343594','EL30112021152212',],
        ['3385861077','EL30112021151958',],
        ['3333235276','EL30112021152140',],
        ['3773511159','EL30112021154438',],
        ['3389301249','EL30112021154511',],
        ['3355445710','EL30112021151959',],
        ['3356273681','EL30112021152141',],
        ['3206482050','EL30112021152213',],
        ['578274380','EL30112021100621',],
        ['3334853986','EL30112021152000',],
        ['3333294367','EL30112021154439',],
        ['3471508201','EL30112021152142',],
        ['3511947763','EL30112021154512',],
        ['3288576142','EL29112021101457',],
        ['3388106060','EL29112021054334',],
        ['3478517482','EL30112021154106',],
        ['3296585954','EL30112021152214',],
        ['3925113345','EL30112021160433',],
        ['3486540769','EL30112021152143',],
        ['3397009397','EL30112021154440',],
        ['3921090963','EL30112021154513',],
        ['3923794433','EL30112021151928',],
        ['3206768674','EL30112021160434',],
        ['3663238580','EL30112021152215',],
        ['3383561207','EL30112021154441',],
        ['3771546541','EL30112021160435',],
        ['335264468','EL30112021152145',],
        ['3932122380','EL30112021154514',],
        ['3351446211','EL23112021065110',],
        ['3272444769','EL30112021160750',],
        ['3464792837','EL30112021160436',],
        ['335315973','EL30112021152146',],
        ['3807979298','EL30112021152216',],
        ['3791228773','EL30112021154442',],
        ['3886342047','EL30112021160437',],
        ['3398255046','EL30112021152147',],
        ['3881104812','EL30112021160751',],
        ['3208508543','EL30112021152006',],
        ['3774533065','EL30112021160438',],
        ['3459724432','EL30112021152148',],
        ['3339506417','EL23112021085334',],
        ['3299747974','EL30112021152007',],
        ['3208671079','EL30112021154516',],
        ['3533160734','EL30112021160439',],
        ['3356071820','EL26112021143110',],
        ['3483747798','EL30112021152008',],
        ['3274097558','EL30112021154110',],
        ['3395365224','EL30112021154444',],
        ['3804539632','EL30112021162418',],
        ['3383093097','EL30112021160440',],
        ['3299178885','EL23112021071127',],
        ['3510151684','EL30112021152009',],
        ['3428763295','EL30112021154517',],
        ['3208853098','EL30112021160753',],
        ['3389418796','EL30112021160441',],
        ['3396772451','EL30112021154111',],
        ['3804779968','EL30112021152010',],
        ['3496305868','EL30112021152219',],
        ['3382393483','EL30112021160442',],
        ['3348123704','EL30112021154518',],
        ['3291417064','EL28112021215527',],
        ['3398608870','EL30112021160754',],
        ['3883824061','EL30112021160754',],
        ['3393790537','EL23112021083853',],
        ['3501116109','EL30112021154446',],
        ['3890664157','EL30112021160443',],
        ['3807918556','EL30112021162420',],
        ['3396453820','EL30112021152012',],
        ['3474910875','EL30112021152220',],
        ['3383523885','EL30112021160444',],
        ['3476242250','EL30112021152154',],
        ['3498630213','EL30112021152013',],
        ['3495002133','EL30112021154447',],
        ['3938950331','EL30112021160445',],
        ['3881172355','EL30112021151939',],
        ['3335710469','EL30112021152155',],
        ['3475160830','EL30112021152014',],
        ['34824742061','EL30112021160446',],
        ['3391450122','EL30112021154520',],
        ['3336790573','EL30112021152156',],
        ['3403760191','EL30112021152015',],
        ['3493026192','EL30112021162422',],
        ['3392315106','EL30112021151941',],
        ['3382734298','EL30112021160447',],
        ['3391263971','EL30112021152016',],
        ['3295671580','EL30112021151942',],
        ['3384273944','EL30112021154521',],
        ['3294459931','EL30112021160448',],
        ['3711911708','EL30112021154449',],
        ['3454727233','EL30112021152017',],
        ['3404773589','EL30112021152158',],
        ['3289435763','EL30112021151943',],
        ['3389827113','EL30112021160449',],
        ['3478893843','EL28112021163324',],
        ['3478067170','EL30112021152018',],
        ['3342324453','EL30112021152159',],
        ['3287473666','EL30112021154522',],
        ['3384847609','EL30112021151944',],
        ['3477009143','EL30112021160450',],
        ['3472391141','EL28112021191639',],
    ];

    private $leadsNoNumber = [
        '3665837211',
        '3274564270',
        '3382360019',
        '3486942963',
        '3398250210',
        '3392944733',
        '3342934284',
        '3314117875',
        '3461014695',
        '3404206042',
        '3880591378',
        '3476747391',
        '3770223064',
        '336835621',
        '3472718248',
        '3478505748',
        '3316385942',
        '3389658257',
        '3384496588',
        '3409714836',
        '3923981072',
        '3200747061',
        '3355835949',
        '3200839345',
        '3471835959',
        '3473141783',
        '3396097762',
        '3792855614',
        '3381938633',
        '3356930912',
        '3479474071',
        '3479264198',
        '3486964308',
        '3923551617',
        '3339074145',
        '3474683721',
        '3405906741',
        '3409644587',
        '3382588916',
        '3383422885',
        '3333866379',
        '3454096862',
        '3334204294',
        '3484740621',
        '337892816',
        '3397410530',
        '3931779731',
        '3287056154',
        '3479717581',
        '3398368691',
        '3383365678',
        '3487079383',
        '3474238021',
        '3939793671',
        '3478157212',
        '3394475844',
        '3398932558',
        '3468370113',
        '3248822891',
        '3459208750',
    ];

    public function test()
    {
        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD')
        );
        $eOrders = $elogy->getOrders();
        $wLeads = \App\third\WarehouseLead::all();
        $created = [];
        $alreadyExists = [];
        $justAdded = [];
        $dontExistInOurDb = [];
        foreach ($this->leadsWithNumber as $lead) {
            $phone = $lead[0];
            $orderNumber = $lead[1];

            $wl = $this->getLeadByPhone($wLeads, $phone);
            if ($wl === null) {
                $dontExistInOurDb[] = $lead[0];
                continue;
            }

            $info = $wl->info;
            if ($info === null) {
                $info = [];
            }

            if (!isset($info['delivery']['order_number']) || $info['delivery']['order_number'] == '') {
                $info['delivery']['order_number'] = $orderNumber;
                $justAdded[] = $lead[0];
            }

            $wl->info = $info;
            $wl->save();
        }
        foreach ($this->leadsNoNumber as $phone) {
            $wl = $this->getLeadByPhone($wLeads, $phone);
            if ($wl === null) {
                $dontExistInOurDb[] = $phone;
                continue;
            }

            $info = $wl->info;
            if ($info === null) {
                $info = [];
            }

            $total = 0.0;
            if (isset($info['total'])) {
                $total = $this->getCurrencyValue($info['total']);
            }

            $firstName = $wl->first_name;
            $lastName = $wl->last_name;
            $infoPhone = $wl->phone;

            $sku = '';
            if (isset($info['sku'])) {
                $sku = $info['sku'];
            }
            $quantity = 0;
            if (isset($info['quantity'])) {
                $quantity = $info['quantity'];
            }
            $price = $total;
            if (isset($info['price'])) {
                $price = $info['price'];
            }

            $deliveryParams = [
                'country' => 'IT',
                'total_price' => $total,
                'subtotal_price' => $total,
                'shipping_price' => 0.0,
                'total_tax' => 0.0,
                'tax_included' => 1,
                'gateway' => 'cashondelivery',
                'currency' => 'EUR',
                'customer_first_name' => $firstName,
                'customer_last_name' => $lastName,
                'shipping_phone' => $infoPhone,
                'created_at' => $wl->created_at,
                'status' => 'lead',
                'products' => [
                    [
                        'sku' => $sku,
                        'quantity' => $quantity,
                        'subtotal' => $price,
                    ],
                ],
            ];
            try {
                $elogyOrder = $elogy->createOrder($deliveryParams);
                if ($elogyOrder !== null) {
                    $orderNumber = strval($elogyOrder['order_number']);
                    $info['delivery']['order_number'] = $orderNumber;
                    $created[] = $phone;
                } else {
                    $alreadyExists[] = $$phone;
                }
            } catch (Exception $e) {
                // Nothing
            }

            $wl->info = $info;
            $wl->save();
        }
        dd([
            'justAdded' => $justAdded,
            'created' => $created,
            'alreadyExists' => $alreadyExists,
            'dontExistInOurDb' => $dontExistInOurDb,
        ]);
        exit();
        /*
        $leads = [
            ['Giovanna Cerbone        ','3387707954','EL17112021042748'],
            ['Alberto Esposito','3247789690','EL17112021042737'],
            ['Giacomo Bronzino','3278321411','EL17112021042638'],
            ['Luisa Costi','3281249757','EL17112021042501'],
            ['Luigi Meloni','3476643549','EL17112021042415'],
            ['Maria pirrello','3206258148',''],
            ['Romina Moretto','3487079264','EL17112021164941'],
            ['Penninah Darko','3898995927','EL19112021154457'],
            ['Favour kido','3511477223',''],
            ['Sidy niang','3204103236',''],
            ['Vivone Nicola','3358274887','EL19112021153029'],
            ['Marco canedoli','3397410530',''],
            ['Maria Gioia Vitali','3931779731',''],
            ['Girolama Bosco','3807986630',''],
            ['sandra cardillo','3383991028',''],
            ['Ilda De Patto','3291392952',''],
            ['Lucia Ziglioli','3407828528',''],
            ['Michela Caponi','3287056154',''],
            ['Ranieri roberta','3881134175','EL27112021073826'],
            ['Maria maj','3479717581',''],
            ['Pomi rita','3349135317',''],
            ['Tiziana Caruso','3398368691',''],
            ['Fusillo marisa','998441873',''],
            ['elena dima','3425791655',''],
            ['Maria pia villa','3927188969',''],
            ['Annalisa Battistoni','3479760280',''],
            ['Prince Djussuf','3332923773',''],
            ['Aurelia Nastasa','3294173286',''],
            ['Annamaria Fenzi','393486964308',''],
            ['Clausi Francesco','3492333591',''],
            ['Francescon claudio','3456118915',''],
            ['Alberto Baldasso','393387869287',''],
            ['Susanna Crepaldi','3283181976',''],
            ['Livia Melchionda','3397751849',''],
            ['Teresa cadeddu','3923551617',''],
            ['Gloria Papa','3281172595','EL22112021204513'],
            ['Antonio De Simone .','3398882341','EL22112021174239'],
            ['Marco Martelli Calvelli','3939790894','EL28112021163005'],
            ['GIMI MUSTO','393774190541',''],
            ['Laura D’Abundo','3486087980',''],
            ['Antonella Gaudini','3929015727',''],
            ['Concetta Reina','3471785448',''],
            ['Fiorella Lopez','3331929500',''],
            ['Teresina rizzuti','3339074145',''],
            ['Tatiana cristea','3295429921',''],
            ['Massimiliano Purpura','3713675784',''],
            ['Alfredo Prado','3283291952',''],
            ['Anna Meda','3474683721',''],
            ['Dediu Magdalena','3248953040',''],
            ['Violetta trevisan','3405906741',''],
            ['Laura Ravone','3384990107',''],
            ['Susanna Anastasio','3497587800',''],
            ['elena cavaliere','3356273424',''],
            ['Simona Sala','3384613573',''],
            ['Graziella Silla','3409644587',''],
            ['Sara Barbini','3290957859',''],
            ['Anna Angelici','3382588916',''],
            ['CLORINDA ALISON','3478747850',''],
            ['Testa Maria Rita','3201633563',''],
            ['Rosaria D\'Asta','3807277633',''],
            ['Emma melocchi','3382961120',''],
            ['Lucia lapolla','3383422885',''],
            ['Liliana Ferrante','3333866379',''],
            ['Elena bavastro','3663524241',''],
            ['Vilma mare','3332194695',''],
            ['Teresa Troia','3454096862',''],
            ['Alessandra Cora','3492909115',''],
            ['Bertha Maria Aucaquizhpi','3714855221',''],
            ['Levati Ilaria','3334204294',''],
            ['Rosa Badessa','3713155955',''],
            ['Pompea Bellanova','3351639293',''],
            ['Carmen De Maddalena','3484740621',''],
            ['Adalgisa Clara Cascio','337892816',''],
            ['Marconi Flora','438486772',''],
            ['Iula Agosto','3476260960',''],
            ['Elisa Paduano','3318192824',''],
            ['Leone Gianna','3492299465',''],
            ['monica di maria','3883775098',''],
            ['Carmela castrogiovanni','3888827667','EL23112021074810'],
            ['Daniela marino','3476102886','EL22112021195800'],
            ['Rosa Francaviglia','3398250210',''],
            ['Patrizia Bertin','3479953432',''],
            ['Maria Grazia .Di Francesco','3392944733',''],
            ['ANY Cappetta','3713216890',''],
            ['Maday','3342934284',''],
            ['Mena caliendo','3668918960',''],
            ['Ciro Minucci','3314117875',''],
            ['Mirella Murgia','3921057675',''],
            ['Novello Adriano','3455965227',''],
            ['Fiona Murtaj','3456974546',''],
            ['franco mancuso','3461014695',''],
            ['Haris','3404206042',''],
            ['Romeo Maria','3383827370',''],
            ['Carmela Caloroso','3342449116',''],
            ['Marcella Miali','3880591378',''],
            ['Carmela Giorgianni','3476472723',''],
            ['Zanchetta Elisabetta','3476747391',''],
            ['Irene macrini','3770223064',''],
            ['Elena Mighali','336835621',''],
            ['Gabriella Lupo','3336100215',''],
            ['Rosalia adamo','3472718248',''],
            ['Roberta Lupoli','3454843608',''],
            ['Daniella Buzzoni','3478505748',''],
            ['Gigliola Savini','3384865300',''],
            ['Arialdo Alberici','3332343594',''],
            ['Lina Spataro','3385861077',''],
            ['Gianni Avellino','3316385942',''],
            ['Giuliana Cordio','3333235276',''],
            ['Iris chiominto','3389658257',''],
            ['Vita conte','3773511159',''],
            ['Alberto Corvi','3389301249',''],
            ['Daniela Milan','3355445710',''],
            ['rosa feola','3384496588',''],
            ['Tunde Rozalia','3356273681',''],
            ['Pina Pignataro','3206482050',''],
            ['enrica FRANZIL','578274380',''],
            ['Loretta Carniel','3409714836',''],
            ['Trimboli Vincenzo','3334853986',''],
            ['Teresa casulli','3333294367',''],
            ['Molignani anton433 ietta','3471508201',''],
            ['Lea convertini','3511947763',''],
            ['Maria Eleonora Ummarino','3288576142','EL29112021101457'],
            ['Lopopolo Rosa','3923981072',''],
            ['Liliana Mihaila','3388106060','EL29112021054334'],
            ['Lucia Righi','3200747061',''],
            ['Lora D’Ambra','3478517482',''],
            ['Teresa rossi','3296585954',''],
            ['AhlaiA Demetri','3925113345',''],
            ['Giovanna Dirodi','3486540769',''],
            ['Kristina Costantini','3397009397',''],
            ['Maria Rosa Lo Bianco','3355835949',''],
            ['Patrizia Venanzetti','3921090963',''],
            ['Blagica Blagica','3200839345',''],
            ['Moussa Faye','3923794433',''],
            ['Marina cacioni di Nicola','3206768674',''],
            ['Conenna agnese via onofrio','3471835959',''],
            ['Quadrano Giuseppe','3663238580',''],
            ['Rachel ferro','3473141783',''],
            ['Giovanni Luisi','3383561207',''],
            ['Larysa Yefimenko','3771546541',''],
            ['Roberta Gallarello','335264468',''],
            ['Mischeri Mara','3396097762',''],
            ['Elisabetta Pappalepore','3932122380',''],
            ['Emma Ventriglia','3351446211','EL23112021065110'],
            ['Anna liccardo','3792855614',''],
            ['Rosa antonia Lentini','3272444769',''],
            ['Antonella','3464792837',''],
            ['Carla Castelletti','335315973',''],
            ['Eugenia Marasà','3807979298',''],
            ['Lara Pagani','3791228773',''],
            ['Palmina Romagnoli','3381938633',''],
            ['Maria Bruno','3356930912',''],
            ['Sory Ibrahima Diabakate','3886342047',''],
            ['Viorica Blendea','3398255046',''],
            ['Snezana Arsic','3881104812',''],
            ['Mimi','3208508543',''],
            ['Rosa Cicciomessere','3479474071',''],
            ['Annapaola Ortu','3479264198',''],
            ['Maria maravolo','3774533065',''],
            ['Pierangela Brambilla','3459724432',''],
            ['anna della gatta','3339506417','EL23112021085334'],
            ['Giovanna ticca','3299747974',''],
            ['Luca Fortunata','3665837211',''],
            ['Luz Fernández','3274564270',''],
            ['Dascari Jessica','3208671079',''],
            ['Veronica','3533160734',''],
            ['Galli lia','3382360019',''],
            ['Bernarda Ventimiglia','3356071820','EL26112021143110'],
            ['CERCEL LILIANA','3483747798',''],
            ['Francesca Aracri','3274097558',''],
            ['Alessandra niccolai','3395365224',''],
            ['Alessandra Turchi','3804539632',''],
            ['Antonella del guerra','3383093097',''],
            ['Paola Congiu','3486942963',''],
            ['Concetta garoglio','3299178885','EL23112021071127'],
            ['Atekha odiase','3510151684',''],
            ['Francesca Murgese','3428763295',''],
            ['Branchesi marzia','3383365678',''],
            ['Dina Astorino','3208853098',''],
            ['Loredana ulpiani','3389418796',''],
            ['Annamaria Donnarumma','3396772451',''],
            ['Silvana Parello','3804779968',''],
            ['Simone coscia','3487079383',''],
            ['Amela Antonella','3496305868',''],
            ['Diego Perini','3474238021',''],
            ['Bruna cere','3382393483',''],
            ['Calogero Schifano','3348123704',''],
            ['Assunta Palumbo','3291417064',''],
            ['Salvo Pilieri','3398608870',''],
            ['Jessica Gabrielli','3883824061',''],
            ['Tiziana Straccali','3393790537','EL23112021083853'],
            ['Laura botticchio','3501116109',''],
            ['Lidia','3890664157',''],
            ['2G stamp srl','3939793671',''],
            ['Chiara Sinibaldi','3807918556',''],
            ['Lucia fortezza','3396453820',''],
            ['Loris tres','3474910875',''],
            ['Chiti Irene','3478157212',''],
            ['Lapolla lucia','3383523885',''],
            ['Virginia Schiavi','3476242250',''],
            ['Rosanna gilardoni','3498630213',''],
            ['Francesca NOBILI','3394475844',''],
            ['Yissel Abreu','3495002133',''],
            ['Luciana Romano','3938950331',''],
            ['Donatella Tomassini','3881172355',''],
            ['Grazia Vitiello','3335710469',''],
            ['Mariana Verde','3475160830',''],
            ['Maria Rosa Bregoli','3398932558',''],
            ['Orsola lenzo','3468370113',''],
            ['Domenica Gandolfini','34824742061',''],
            ['Tiziana Dalla Valle','3391450122',''],
            ['Leone Coppo via Roma 19Turbigo mi','3336790573',''],
            ['Zanetti Marisa','3403760191',''],
            ['Ranieri libera Lucia','3493026192',''],
            ['Giannuzzi Savelli vincenza','3392315106',''],
            ['Nadia ghisi','3382734298',''],
            ['Salvatore Bonomolo','3248822891',''],
            ['Mariangela Caprini','3391263971',''],
            ['Mac Antonella','3459208750',''],
            ['D alimonte valerio','3295671580',''],
            ['Shade xhaferri','3384273944',''],
            ['Filomena','3294459931',''],
            ['Eleonora FEDERICO','3711911708',''],
            ['Cristiana Sbrocca','3454727233',''],
            ['Mirella iemolo','3404773589',''],
            ['Mariella Veneroso','3289435763',''],
            ['Regina Romualdi Bernardini','3389827113',''],
            ['Giancarla Marconi','3478893843',''],
            ['Silva dantoni','3478067170',''],
            ['Vilma Ornella Mosconi','3342324453',''],
            ['Monica Ramella','3287473666',''],
            ['Francesca castaldo','3384847609',''],
            ['Molinaro Argentina','3477009143',''],
            ['Mara de boni','3472391141','EL28112021191639'],
        ];

        $elogy = new \Driverfury\Elogy\Elogy(
            env('ELOGY_API_TOKEN'),
            env('ELOGY_EMAIL'),
            env('ELOGY_PASSWORD')
        );
        $eOrders = $elogy->getOrders();
        $wLeads = \App\third\WarehouseLead::all();
        $created = [];
        $alreadyExists = [];
        $justAdded = [];
        foreach ($leads as $lead) {
            $phone = $lead[1];
            $orderNumber = $lead[2];

            $wl = $this->getLeadByPhone($wLeads, $phone);
            if ($wl === null) {
                continue;
            }

            $info = $wl->info;
            if ($info === null) {
                $info = [];
            }

            $total = 0.0;
            if (isset($info['total'])) {
                $total = $this->getCurrencyValue($info['total']);
            }

            $firstName = $lead->first_name;
            $lastName = $lead->last_name;
            $infoPhone = $lead->phone;

            $sku = '';
            if (isset($info['sku'])) {
                $sku = $info['sku'];
            }
            $quantity = 0;
            if (isset($info['quantity'])) {
                $quantity = $info['quantity'];
            }
            $price = $total;
            if (isset($info['price'])) {
                $price = $info['price'];
            }

            if ($orderNumber == '') {
                $deliveryParams = [
                    'country' => 'IT',
                    'total_price' => $total,
                    'subtotal_price' => $total,
                    'shipping_price' => 0.0,
                    'total_tax' => 0.0,
                    'tax_included' => 1,
                    'gateway' => 'cashondelivery',
                    'currency' => 'EUR',
                    'customer_first_name' => $firstName,
                    'customer_last_name' => $lastName,
                    'shipping_phone' => $infoPhone,
                    'created_at' => $wl->created_at,
                    'status' => 'lead',
                    'products' => [
                        [
                            'sku' => $sku,
                            'quantity' => $quantity,
                            'subtotal' => $price,
                        ],
                    ],
                ];
                try {
                    $elogyOrder = $elogy->createOrder($deliveryParams);
                    if ($elogyOrder !== null) {
                        $orderNumber = strval($elogyOrder['order_number']);
                        $info['delivery']['order_number'] = $orderNumber;
                        $created[] = $lead[0];
                    } else {
                        $alreadyExists[] = $lead[0];
                    }
                } catch (Exception $e) {
                    // Nothing
                }
            } else {
                $info['delivery']['order_number'] = $orderNumber;
                $justAdded[] = $lead[0];
            }
            $wl->info = $info;
            $wl->save();
        }
        dd([
            'justAdded' => $justAdded,
            'created' => $created,
            'alreadyExists' => $alreadyExists,
        ]);
        exit();

        $csv1 = '';
        $csv2 = '';
        $leadsToDo = [];
        foreach ($wLeads as $lead) {
            $fullName = $lead->first_name;
            if ($lead->last_name != '.') {
                $fullName .= ' ' . $lead->last_name;
            }
            $info = $lead->info;
            $hasOrder = true;
            if ($info === null || !isset($info['delivery']['order_number']) || $info['delivery']['order_number'] == '') {
                $hasOrder = false;
                $ordByPhone = $this->getOrderByPhone($eOrders, $lead->phone);
                if ($ordByPhone === null) {
                    $leadsToDo[] = $lead;
                    $csv1 .= $fullName . ',' . $lead->phone . '<br>';
                }
            } else {
                $ordByNumber = $this->getOrderByNumber($eOrders, $info['delivery']['order_number']);
                if ($ordByNumber === null) {
                    $csv2 .= $fullName . ',' . $lead->phone . '<br>';
                }
            }
        }
        echo '<pre>';
        echo $csv1;
        echo '</pre>';
        exit();
        */
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
