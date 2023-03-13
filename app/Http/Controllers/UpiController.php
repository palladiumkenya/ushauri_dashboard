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

           $verification_gender = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array("%","%", "%", "%"));
           $verification_age = DB::select('CALL sp_rpt_upi_age(?,?,?,?)', array("%","%", "%", "%"));
           $verification_list = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("National","%","%", "%", "%"));
           $verification_count = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("National", "%","%", "%", "%"));
           $verification_count_total = DB::select('CALL sp_rpt_upi_verification_summary(?,?,?,?,?)', array("National", "%","%", "%", "%"));
        }
        if (Auth::user()->access_level == 'Facility') {
            $facility = Auth::user()->facility_id;

            $verification_gender = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array($facility,"%", "%", "%"));
            $verification_age = DB::select('CALL sp_rpt_upi_age(?,?,?,?)', array($facility,"%", "%", "%"));
            $verification_list = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("Facility",$facility,"%", "%", "%"));
            $verification_count = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("Partner",$facility,"%", "%", "%"));
            $verification_count_total = DB::select('CALL sp_rpt_upi_verification_summary(?,?,?,?,?)', array("Facility", $facility,"%", "%", "%"));
        }
        if (Auth::user()->access_level == 'Partner') {
            $partner = Auth::user()->partner_id;

            $verification_gender = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array("%", $partner , "%", "%"));
            $verification_age = DB::select('CALL sp_rpt_upi_age(?,?,?,?)', array("%",$partner, "%", "%"));
            $verification_list = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("Partner","%", $partner, "%", "%"));
            $verification_count = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("National","%",$partner, "%", "%"));
            $verification_count_total = DB::select('CALL sp_rpt_upi_verification_summary(?,?,?,?,?)', array("Partner", "%", $partner, "%", "%"));
        }
        if (Auth::user()->access_level == 'County') {
            $county = Auth::user()->county_id;

            $verification_gender = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array("%", "%", $county, "%"));
            $verification_age = DB::select('CALL sp_rpt_upi_age(?,?,?,?)', array("%","%", $county, "%"));
            $verification_list = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("Partner","%", "%", $county, "%"));
            $verification_count = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("National","%","%", $county, "%"));
            $verification_count_total = DB::select('CALL sp_rpt_upi_verification_summary(?,?,?,?,?)', array("National", "%", "%", $county, "%"));
        }
        if (Auth::user()->access_level == 'Sub County') {
            $subcounty = Auth::user()->subcounty_id;

            $verification_gender = DB::select('CALL sp_rpt_upi_gender(?,?,?,?)', array("%", "%", "%", $subcounty));
            $verification_age = DB::select('CALL sp_rpt_upi_age(?,?,?,?)', array("%","%", "%", $subcounty));
            $verification_list = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("Partner","%", "%", "%", $subcounty));
            $verification_count = DB::select('CALL sp_rpt_upi_verification_list(?,?,?,?,?)', array("National","%","%", "%", $subcounty));
            $verification_count_total = DB::select('CALL sp_rpt_upi_verification_summary(?,?,?,?,?)', array("National", "%", "%", "%", $subcounty));
        }

        //dd($verification_age);
        return view('dashboard.upi_dashboard', compact('verification_age', 'verification_count_total', 'verification_gender', 'verification_list', 'verification_count'));
    }
}
