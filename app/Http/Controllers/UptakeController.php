<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Models\User;
use App\Models\Facility;
use App\Models\Partner;

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
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->pluck('name', 'id');
            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", "%", "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));

            $ushauristatistics = DB::select('CALL sp_rpt_quick_stats(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
        }
        if (Auth::user()->access_level == 'Facility') {
            $facility = Auth::user()->facility_id;
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->pluck('name', 'id');

            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", $facility, "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));

            $ushauristatistics = DB::select('CALL sp_rpt_quick_stats(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));

            $data["consentedTest"]        = json_encode($consentedagesex);
        }
        if (Auth::user()->access_level == 'Partner') {
            $partner = Auth::user()->partner_id;
            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->pluck('name', 'id');

            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", "%", $partner));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));

            $ushauristatistics = DB::select('CALL sp_rpt_quick_stats(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));
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
            'consentedfacilities',
            'all_partners',
            'ushauristatistics',
            'messages_sent'
        ));

        dd($ushauristatistics);
    }

    public function filter_uptake(Request $request)
    {

        $data = [];
        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;
        $selected_from = $request->from;
        $selected_to = $request->to;

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->pluck('name', 'id');
            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", "%", "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));

            $ushauristatistics = DB::select('CALL sp_rpt_quick_stats(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', "%"));
        }
        if (Auth::user()->access_level == 'Facility') {
            $facility = Auth::user()->facility_id;
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->pluck('name', 'id');

            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", $facility, "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));

            $ushauristatistics = DB::select('CALL sp_rpt_quick_stats(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", $facility, '1900-01-01', '2900-01-01', "%"));

            $data["consentedTest"]        = json_encode($consentedagesex);
        }
        if (Auth::user()->access_level == 'Partner') {
            $partner = Auth::user()->partner_id;
            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->pluck('name', 'id');

            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", "%", $partner));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01',  $partner));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));

            $ushauristatistics = DB::select('CALL sp_rpt_quick_stats(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $partner));
        }

        if (!empty($selected_partners)) {
            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", "%", $selected_partners));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", "%", '1900-01-01', '2900-01-01', $selected_partners));
        }
        if (!empty($selected_counties)) {
            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array($selected_counties, "%", "%", "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array($selected_counties, "%", "%", '1900-01-01', '2900-01-01', "%"));
        }
        if (!empty($selected_subcounties)) {
            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", $selected_subcounties, "%", "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%",  $selected_subcounties, "%", '1900-01-01', '2900-01-01', "%"));

        }
        if (!empty($selected_facilites)) {
            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
            $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", $selected_facilites, "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", $selected_facilites, '1900-01-01', '2900-01-01', "%"));
        }
        if (!empty($selected_from || $selected_to)) {
            $consented = DB::select('CALL sp_rpt_consentedclients(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
            $registered = DB::select('CALL sp_rpt_registeredclients(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
             $txcur = DB::select('CALL sp_rpt_txcur(?,?,?,?)', array("%", "%", "%", "%"));

            $consentedagesex = DB::select('CALL sp_rpt_consentedbyagesex(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
            $scheduledappointment = DB::select('CALL sp_rpt_scheduled_appointments(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
            $honoredappointment = DB::select('CALL sp_rpt_honored_appointments(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
            $honoredappointmentagesex = DB::select('CALL sp_rpt_honored_appointmentsbyagesex(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
            $honoredappointmentfacilities = DB::select('CALL sp_rpt_honoredclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
            $consentedfacilities = DB::select('CALL sp_rpt_consentedclients_facilities(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
            $ushauristatistics = DB::select('CALL sp_rpt_quick_stats(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
            $messages_sent = DB::select('CALL sp_rpt_messeges(?,?,?,?,?,?)', array("%", "%", "%", $selected_from, $selected_to, "%"));
        }

        $data["consented"]    = json_decode($consented[0]->consented);
        $data["registered"]    = json_decode($registered[0]->registeredClients);
        $data["txcur"]    = json_decode($txcur[0]->tx_cur);
        $data["consentedagesex"]    = $consentedagesex;
        $data["scheduledappointment"]    = json_decode($scheduledappointment[0]->appointments);
        $data["honoredappointment"]    = json_decode($honoredappointment[0]->honored);
        $data["honoredappointmentagesex"]    = $honoredappointmentagesex;
        $data["honoredappointmentfacilities"]    = $honoredappointmentfacilities;
        $data["consentedfacilities"]    = $consentedfacilities;
        $data["ushauristatistics"]    = $ushauristatistics;
        $data["messages_sent"]    = json_decode($messages_sent[0]->sentmesseges);

        return $data;
    }
}
