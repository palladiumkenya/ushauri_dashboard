<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Models\User;
use App\Models\Facility;

class UptakeController extends Controller
{
    public function index()
    {
        return view('uptake.index');
    }

    public function registered()
    {
         $data = [];


        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", "%", "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
        }
        if (Auth::user()->access_level == 'Facility') {
            $facility = Auth::user()->facility_id;

            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", $facility, "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));

            $data["consentedTest"]        = json_encode($consentedagesex);
        }
        if (Auth::user()->access_level == 'Partner') {
            $partner = Auth::user()->partner_id;

            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", "%", "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));
        }


        $facilities = Facility::join('tbl_partner_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->select('tbl_master_facility.name as facility', 'tbl_partner_facility.mfl_code')->get();

        return view('uptake.index', compact(
            'registered',
            'consented',
            'txcur',
            'data',
            'consentedagesex',
            'facilities',
            'scheduledappointment',
            'honoredappointment',
            'honoredappointmentagesex',
            'honoredappointmentfacilities',
            'consentedfacilities'
        ));

        dd($honoredappointmentfacilities);
    }
}
