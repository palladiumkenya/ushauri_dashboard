<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Client;
use App\Models\Appointments;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\Partner;
use App\Models\County;
use App\Models\SubCounty;
use App\Models\PartnerFacility;
use App\Models\AgeDashboard;
use App\Models\Indicator;
use App\Models\ETLAppointment;
use App\Models\ETLClient;
use App\Models\Txcurr;
use Auth;
use DB;
use Carbon\Carbon;

class FinalDashboardController extends Controller
{
    public function appointment()
    {
        if (Auth::user()->access_level == 'Facility') {
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(CASE WHEN app_kept = "Kept" THEN 1 ELSE 0 END) AS kept_app '),
                DB::raw('SUM(CASE WHEN app_kept = "Not" AND future = "No" THEN 1 ELSE 0 END) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN future = "Yes" THEN 1 ELSE 0 END) AS future '),
                DB::raw('SUM(CASE WHEN received_sms = "Yes" THEN 1 ELSE 0 END) AS messages ')
            )
                ->get();
            $consented_clients = ETLClient::select(DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '))->get();
            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
            ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->get();
        }
        return view('dashboard.appointment', compact('all_appoinments', 'consented_clients', 'all_tx_curr'));
    }
}
