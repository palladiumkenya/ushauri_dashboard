<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Auth;
use DB;

class UpiController extends Controller
{
    public function index()
    {
        if (Auth::user()->access_level == 'Admin') {

           //$verification_gender = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array("%","%", "%", "%"));
           $verification_age = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array("%","%", "%", "%"));
           $verification_list = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("National","%","%", "%", "%"));
           $verification_count = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("National", "%","%", "%", "%"));
        }
        if (Auth::user()->access_level == 'Facility') {
            $facility = Auth::user()->facility_id;

            $verification_gender = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array($facility,"%", "%", "%"));
            $verification_age = DB::select('CALL sp_rpt_upi_age(?,?,?,?)', array($facility,"%", "%", "%"));
            $verification_list = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("Facility",$facility,"%", "%", "%"));
            $verification_count = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("Partner",$facility,"%", "%", "%"));
        }
        if (Auth::user()->access_level == 'Partner') {
            $partner = Auth::user()->partner_id;

            $verification_gender = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array("%", $partner , "%", "%"));
            $verification_age = DB::select('CALL sp_rpt_upi_age(?,?,?,?)', array("%",$partner, "%", "%"));
            $verification_list = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("Partner","%", $partner, "%", "%"));
            $verification_count = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("National","%",$partner, "%", "%"));
        }

        //dd($verification_age);
        return view('dashboard.upi_dashboard', compact('verification_age', 'verification_gender', 'verification_list', 'verification_count'));
    }
}
