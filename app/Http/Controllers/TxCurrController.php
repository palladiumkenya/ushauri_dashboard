<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Txcurr;

class TxCurrController extends Controller
{
    public function txcurr(Request $request)
    {
        set_time_limit(0);
        $period = date('Ym') - 1;
        $username = env('DHIS2_USERNAME');
        $password = env('DHIS2_PASSWORD');

        // for($y=2023; $y < 2024; $y++)
        // {
        //     for($x=1; $x < 6; $x++){
        //         $period = $y.str_pad($x, 2, "0", STR_PAD_LEFT);

                $response = Http::withBasicAuth("$username", "$password")->withOptions(['verify' => false])->get("https://hiskenya.org/api/analytics?dimension=ou:LEVEL-5;&dimension=dx:PUrg2dmCjGI;&dimension=pe:$period&displayProperty=NAME&showHierarchy=true&tableLayout=true&columns=dx;pe&rows=ou&hideEmptyRows=true&paging=false");

                    $response_data = $response->json();
                    if (array_key_exists('rows', $response_data)) {
                        foreach($response_data['rows'] as $data)
                        {
                            $tx_curr = Txcurr::updateOrCreate(
                                ['period' => $period,'mfl_code' => $data[7]],
                                ['tx_cur' => $data[9] == '' ? 0 : $data[9]]
                            );
                        }
                        return response()->json(['status' =>"success",'message'=>"Tx Curr for period $period fetched successfully."]);
                    } else{
                        return response()->json(['status' =>"error",'message'=>"No data found for period $period."]);
                    }
        //     }
        // }



    }
}
