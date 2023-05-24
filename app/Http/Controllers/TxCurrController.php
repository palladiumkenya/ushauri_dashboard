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

        $period = $request->route('period');

        $response = Http::withBasicAuth('jaywadi', 'F1$h1ng123')->withOptions(['verify' => false])->get("https://hiskenya.org/api/analytics?dimension=ou:LEVEL-5;&dimension=dx:PUrg2dmCjGI;&dimension=pe:$period&displayProperty=NAME&showHierarchy=true&tableLayout=true&columns=dx;pe&rows=ou&hideEmptyRows=true&paging=false");

        $response_data = $response->json();
        foreach($response_data['rows'] as $data)
        {
            $tx_curr = Txcurr::updateOrCreate(
                ['period' => $period,'mfl_code' => $data[7]],
                ['tx_curr' => $data[9]]
            );
        }
        return response()->json(['status' =>"success",'message'=>"Tx Curr for period $period fetched successfully."]);
    }
}
