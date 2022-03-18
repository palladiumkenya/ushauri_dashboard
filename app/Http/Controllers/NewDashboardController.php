<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\Partner;
use App\Models\County;
use App\Models\SubCounty;
use Auth;

class NewDashboardController extends Controller
{
    public function dashboard(){

       // showing all the active clients
       if (Auth::user()->access_level == 'Facility'){
        $client = Client::where('status', '=', 'Active')
        ->where('mfl_code', Auth::user()->facility_id)
        ->count();
       }
       if (Auth::user()->access_level == 'Partner'){
        $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.status', '=', 'Active')
        ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
        ->count();
       }
       if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor'){
        $client = Client::where('status', '=', 'Active')
        ->count();
       }

    }

    public function client_charts(){

        // active clients by gender
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor'){
            $clients_male = Client::where('gender', '=', '2')
            ->count()

            $clients_female = Client::where('gender', '=', '1')
            ->count()

        }
        if (Auth::user()->access_level == 'Facility'){
            $clients_male = Client::where('gender', '=', '2')
            ->where('mfl_code', Auth::user()->facility_id)
            ->count()

            $clients_female = Client::where('gender', '=', '1')
            ->where('mfl_code', Auth::user()->facility_id)
            ->count()
        }
        if (Auth::user()->access_level == 'Partner'){
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.gender', '=', '2')
            ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
            ->count()

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.gender', '=', '1')
            ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
            ->count()
        }

    }
}
