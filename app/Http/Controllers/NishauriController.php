<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Models\Reschedule;
use App\Models\Txcurr;
use App\Models\NishauriUser;
use App\Models\Facility;
use App\Models\Partner;
use App\Models\County;
use App\Models\SubCounty;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;


class NishauriController extends Controller
{
    public function reschedule()
    {
        $data = [];
        if (Auth::user()->access_level == 'Facility') {
            $reschedule = Reschedule::join('tbl_appointment', 'tbl_nishauri_appoinment_reschedule.appointment_id', '=', 'tbl_appointment.id')
                ->join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_appointment.appntmnt_date', 'tbl_nishauri_appoinment_reschedule.reason', 'tbl_nishauri_appoinment_reschedule.proposed_date')
                ->where('tbl_nishauri_appoinment_reschedule.status', '=', '0')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id);

            $data["reschedule"]        = $reschedule->count();
            $data["reschedule_list"]        = $reschedule->get();

            return $data;
        }
    }

    public function reschedule_list()
    {
        if (Auth::user()->access_level == 'Facility') {
            $reschedule = Reschedule::join('tbl_appointment', 'tbl_nishauri_appoinment_reschedule.appointment_id', '=', 'tbl_appointment.id')
                ->join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.clinic_number', 'tbl_appointment.id as appointment_id', 'tbl_appointment.appntmnt_date', 'tbl_nishauri_appoinment_reschedule.reason', 'tbl_nishauri_appoinment_reschedule.proposed_date')
                ->where('tbl_nishauri_appoinment_reschedule.status', '=', '0')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            return view('nishauri.reschedule', compact('reschedule'));
        }
    }

    public function approve(Request $request)
    {
        try {
            $client = Reschedule::where('appointment_id', $request->appointment_id)
                ->update([
                    'status' => '1',
                    'process_date' => date('Y-m-d H:i:s'),
                    'process_by' => Auth::user()->id,
                ]);
            if ($client) {
                Alert::success('Success', 'Date Successfully Approved');
                return redirect('reschedule/list');
            } else {
                Alert::error('Failed', 'Could not approve please try again later.');
                return back();
            }
        } catch (Exception $e) {
        }
    }

    public function reject(Request $request)
    {
        try {
            $client = Reschedule::where('appointment_id', $request->appointment_id)
                ->update([
                    'status' => '2',
                    'process_date' => date('Y-m-d H:i:s'),
                    'process_by' => Auth::user()->id,
                ]);
            if ($client) {
                Alert::success('Success', 'Date Successfully Rejected');
                return redirect('reschedule/list');
            } else {
                Alert::error('Failed', 'Could not reject please try again later.');
                return back();
            }
        } catch (Exception $e) {
        }
    }
    public function tet()
    {
        $client = Txcurr::where('mfl_code', '12345')->where('period', '202302')
            ->update([
                'tx_cur' => '202305',
            ]);
    }

    public function otp_search(Request $request)
    {
        $upn_search = $request->input('upn_search');

        $otp_search = NishauriUser::select('msisdn', 'profile_otp_number')
            ->where('is_active', '=', '0')
            ->where('msisdn', 'like', '%' . $upn_search . '%')
            ->first();

        return response()->json(['otp_number' => $otp_search ? $otp_search->otp_number : null]);
    }

    public function dashboard()
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $partners = Partner::where("status", "=", "Active")
                ->get();

            $counties = County::where("status", "=", "Active")
                ->get(['id', 'name']);

            $sub_counties = SubCounty::where("status", "=", "Active")
                ->get(['id', 'name']);

            $facilities = Facility::join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
                ->get(['tbl_master_facility.code', 'tbl_master_facility.name']);

            return view('nishauri.dashboard', compact('partners', 'counties', 'sub_counties', 'facilities'));
        }
        if (Auth::user()->access_level == 'Facility') {

            return view('nishauri.dashboard');
        }
        if (Auth::user()->access_level == 'County') {

            $partners = Partner::select('tbl_partner.id', 'tbl_partner.name')
                ->join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')
                ->where('tbl_partner.status', '=', 'Active')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->groupBy('tbl_partner.name')
                ->get();

            $counties = County::where("status", "=", "Active")
                ->remember($this->remember_period)
                ->get(['id', 'name']);

            $sub_counties = SubCounty::select('tbl_sub_county.id', 'tbl_sub_county.name')
                ->join('tbl_partner_facility', 'tbl_sub_county.id', '=', 'tbl_partner_facility.sub_county_id')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->groupBy('tbl_sub_county.name')
                ->remember($this->remember_period)
                ->get();


            $facilities = Facility::select('tbl_master_facility.code', 'tbl_master_facility.name')
                ->join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->get();

            return view('nishauri.dashboard', compact('partners', 'counties', 'sub_counties', 'facilities'));
        }
        if (Auth::user()->access_level == 'Sub County') {

            $facilities = Facility::select('tbl_master_facility.code', 'tbl_master_facility.name')
                ->join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->get();

            return view('nishauri.dashboard', compact('facilities'));
        }
        if (Auth::user()->access_level == 'Partner') {

            $facilities = Facility::select('tbl_master_facility.code', 'tbl_master_facility.name')
                ->join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->get();

            $counties = County::select('tbl_county.id', 'tbl_county.name')
                ->join('tbl_partner_facility', 'tbl_county.id', '=', 'tbl_partner_facility.county_id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->groupBy('tbl_county.name')
                ->get();

            $sub_counties = SubCounty::select('tbl_sub_county.id', 'tbl_sub_county.name')
                ->join('tbl_partner_facility', 'tbl_sub_county.id', '=', 'tbl_partner_facility.sub_county_id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->groupBy('tbl_sub_county.name')
                ->get();

            return view('nishauri.dashboard', compact('facilities', 'counties', 'sub_counties'));
        }
    }

    public function nishauri_uptake()
    {
        $data = [];

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $currentDate = Carbon::now();
            $txcurr = Txcurr::selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                        FROM tbl_tx_cur t1
                        GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })
                ->groupBy('tbl_tx_cur.mfl_code')
                ->get();
            $txcurr = $txcurr->sum('tx_cur');
            $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
            $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", "%", "%", "1900-01-01", $currentDate]);

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;
        }
        if (Auth::user()->access_level == 'Facility') {
            $facility = Auth::user()->facility_id;
            $currentDate = Carbon::now();
            $txcurr = Txcurr::selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                            FROM tbl_tx_cur t1
                            GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })
                ->where('tbl_partner_facility.mfl_code', Auth::user()->facility_id)
                ->groupBy('tbl_tx_cur.mfl_code')
                ->get();
            $txcurr = $txcurr->sum('tx_cur');
            $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", "%", $facility, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
            $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", "%", $facility, "1900-01-01", $currentDate]);

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;
        }
        if (Auth::user()->access_level == 'Partner') {
            $partner = Auth::user()->partner_id;
            $currentDate = Carbon::now();
            $txcurr = Txcurr::selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                        FROM tbl_tx_cur t1
                        GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->groupBy('tbl_tx_cur.mfl_code')
                ->get();
            $txcurr = $txcurr->sum('tx_cur');
            $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$partner, "%", "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
            $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$partner, "%", "%", "%", "1900-01-01", $currentDate]);

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;
        }
        if (Auth::user()->access_level == 'County') {
            $county = Auth::user()->county_id;
            $currentDate = Carbon::now();
            $txcurr = Txcurr::selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                        FROM tbl_tx_cur t1
                        GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->groupBy('tbl_tx_cur.mfl_code')
                ->get();
            $txcurr = $txcurr->sum('tx_cur');
            $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", $county, "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
            $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", $county, "%", "%", "1900-01-01", $currentDate]);

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;
        }
        if (Auth::user()->access_level == 'Sub County') {
            $subcounty = Auth::user()->sub_county_id;
            $txcurr = Txcurr::selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                        FROM tbl_tx_cur t1
                        GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->groupBy('tbl_tx_cur.mfl_code')
                ->get();
            $txcurr = $txcurr->sum('tx_cur');
            $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", $subcounty, "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
            $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", $subcounty, "%", "1900-01-01", $currentDate]);

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;
        }

        return $data;
    }

    public function filter_nishauri_uptake(Request $request)
    {
        if (Auth::user()->access_level == 'Partner') {

            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_from = $request->from;
            $selected_to = $request->to;

            $partner = Auth::user()->partner_id;
            $currentDate = Carbon::now();

            $query = Txcurr::query()->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id);

            if (!empty($selected_partners)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })->where('tbl_partner_facility.partner_id', $selected_partners)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$selected_partners, "%", "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$selected_partners, "%", "%", "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_counties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_con'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_con.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_con.max_period');
                })->where('tbl_partner_facility.county_id', $selected_counties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$partner, $selected_counties, "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$partner, $selected_counties, "%", "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_subcounties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_sub'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_sub.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_sub.max_period');
                })
                    ->where('sub_county_id', $selected_subcounties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$partner , "%", $selected_subcounties, "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$partner, "%", $selected_subcounties, "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_facilites)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_fac'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_fac.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_fac.max_period');
                })
                    ->where('tbl_partner_facility.mfl_code', $selected_facilites)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$partner, "%", "%", $selected_facilites, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$partner, "%", "%", $selected_facilites, "1900-01-01", $currentDate]);
            }
            if (!empty($selected_from || $selected_to)) {

                $selectedFrom = date('Ym', strtotime($request->from));
                $selectedTo = date('Ym', strtotime($request->to));
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_date'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_date.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_date.max_period');
                })
                    ->where(function ($query) use ($selectedFrom, $selectedTo) {
                        $query->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) >= ?", $selectedFrom)
                            ->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) <= ?", $selectedTo);
                    })
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$partner, "%", "%", "%", date($request->from), date($request->to), date($request->from), date($request->to)]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$partner, "%", "%", "%", date($request->from), date($request->to)]);
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;

            return $data;
        }
        if (Auth::user()->access_level == 'County') {

            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_from = $request->from;
            $selected_to = $request->to;

            $county = Auth::user()->county_id;
            $currentDate = Carbon::now();

            $query = Txcurr::query()->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id);

            if (!empty($selected_partners)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })->where('tbl_partner_facility.partner_id', $selected_partners)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$selected_partners, $county, "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$selected_partners, $county, "%", "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_counties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_con'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_con.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_con.max_period');
                })->where('tbl_partner_facility.county_id', $selected_counties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", $selected_counties, "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", $selected_counties, "%", "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_subcounties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_sub'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_sub.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_sub.max_period');
                })
                    ->where('sub_county_id', $selected_subcounties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", $county, $selected_subcounties, "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", $county, $selected_subcounties, "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_facilites)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_fac'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_fac.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_fac.max_period');
                })
                    ->where('tbl_partner_facility.mfl_code', $selected_facilites)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", $county, "%", $selected_facilites, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", $county, "%", $selected_facilites, "1900-01-01", $currentDate]);
            }
            if (!empty($selected_from || $selected_to)) {

                $selectedFrom = date('Ym', strtotime($request->from));
                $selectedTo = date('Ym', strtotime($request->to));
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_date'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_date.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_date.max_period');
                })
                    ->where(function ($query) use ($selectedFrom, $selectedTo) {
                        $query->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) >= ?", $selectedFrom)
                            ->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) <= ?", $selectedTo);
                    })
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", $county, "%", "%", date($request->from), date($request->to), date($request->from), date($request->to)]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", $county, "%", "%", date($request->from), date($request->to)]);
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;

            return $data;
        }
        if (Auth::user()->access_level == 'Sub County') {

            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_from = $request->from;
            $selected_to = $request->to;

            $subcounty = Auth::user()->subcounty_id;
            $currentDate = Carbon::now();

            $query = Txcurr::query()->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id);

            if (!empty($selected_partners)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })->where('tbl_partner_facility.partner_id', $selected_partners)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$selected_partners, "%", $subcounty, "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$selected_partners, "%", $subcounty, "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_counties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_con'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_con.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_con.max_period');
                })->where('tbl_partner_facility.county_id', $selected_counties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", $selected_counties, $subcounty, "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", $selected_counties, $subcounty, "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_subcounties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_sub'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_sub.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_sub.max_period');
                })
                    ->where('sub_county_id', $selected_subcounties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", $selected_subcounties, "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", $selected_subcounties, "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_facilites)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_fac'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_fac.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_fac.max_period');
                })
                    ->where('tbl_partner_facility.mfl_code', $selected_facilites)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", $subcounty, $selected_facilites, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", $subcounty, $selected_facilites, "1900-01-01", $currentDate]);
            }
            if (!empty($selected_from || $selected_to)) {

                $selectedFrom = date('Ym', strtotime($request->from));
                $selectedTo = date('Ym', strtotime($request->to));
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_date'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_date.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_date.max_period');
                })
                    ->where(function ($query) use ($selectedFrom, $selectedTo) {
                        $query->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) >= ?", $selectedFrom)
                            ->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) <= ?", $selectedTo);
                    })
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", $subcounty, "%", date($request->from), date($request->to), date($request->from), date($request->to)]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", $subcounty, "%", date($request->from), date($request->to)]);
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;

            return $data;
        }
        if (Auth::user()->access_level == 'Facility') {

            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_from = $request->from;
            $selected_to = $request->to;

            $facility = Auth::user()->facility_id;
            $currentDate = Carbon::now();

            $query = Txcurr::query()->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id);

            if (!empty($selected_partners)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })->where('tbl_partner_facility.partner_id', $selected_partners)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$selected_partners, "%", "%", $facility, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$selected_partners, "%", "%", $facility, "1900-01-01", $currentDate]);
            }
            if (!empty($selected_counties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_con'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_con.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_con.max_period');
                })->where('tbl_partner_facility.county_id', $selected_counties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", $selected_counties, "%", $facility, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", $selected_counties, "%", $facility, "1900-01-01", $currentDate]);
            }
            if (!empty($selected_subcounties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_sub'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_sub.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_sub.max_period');
                })
                    ->where('sub_county_id', $selected_subcounties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", $selected_subcounties, $facility, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", $selected_subcounties, $facility, "1900-01-01", $currentDate]);
            }
            if (!empty($selected_facilites)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_fac'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_fac.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_fac.max_period');
                })
                    ->where('tbl_partner_facility.mfl_code', $selected_facilites)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", "%", $selected_facilites, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", "%", $selected_facilites, "1900-01-01", $currentDate]);
            }
            if (!empty($selected_from || $selected_to)) {

                $selectedFrom = date('Ym', strtotime($request->from));
                $selectedTo = date('Ym', strtotime($request->to));
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_date'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_date.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_date.max_period');
                })
                    ->where(function ($query) use ($selectedFrom, $selectedTo) {
                        $query->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) >= ?", $selectedFrom)
                            ->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) <= ?", $selectedTo);
                    })
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", "%", $facility, date($request->from), date($request->to), date($request->from), date($request->to)]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", "%", $facility, date($request->from), date($request->to)]);
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;

            return $data;
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_from = $request->from;
            $selected_to = $request->to;

            $currentDate = Carbon::now();

            $query = Txcurr::query()->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code');

            if (!empty($selected_partners)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest.max_period');
                })->where('tbl_partner_facility.partner_id', $selected_partners)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", [$selected_partners, "%", "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", [$selected_partners, "%", "%", "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_counties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_con'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_con.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_con.max_period');
                })->where('tbl_partner_facility.county_id', $selected_counties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", $selected_counties, "%", "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", $selected_counties, "%", "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_subcounties)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_sub'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_sub.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_sub.max_period');
                })
                    ->where('sub_county_id', $selected_subcounties)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", $selected_subcounties, "%", "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", $selected_subcounties, "%", "1900-01-01", $currentDate]);
            }
            if (!empty($selected_facilites)) {
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_fac'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_fac.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_fac.max_period');
                })
                    ->where('tbl_partner_facility.mfl_code', $selected_facilites)
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", "%", $selected_facilites, "1900-01-01", $currentDate, "1900-01-01", $currentDate]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", "%", $selected_facilites, "1900-01-01", $currentDate]);
            }
            if (!empty($selected_from || $selected_to)) {

                $selectedFrom = date('Ym', strtotime($request->from));
                $selectedTo = date('Ym', strtotime($request->to));
                $query->join(DB::raw('(SELECT t1.mfl_code, MAX(t1.period) AS max_period
                FROM tbl_tx_cur t1
                GROUP BY t1.mfl_code) latest_date'), function ($join) {
                    $join->on('tbl_tx_cur.mfl_code', '=', 'latest_date.mfl_code')
                        ->on('tbl_tx_cur.period', '=', 'latest_date.max_period');
                })
                    ->where(function ($query) use ($selectedFrom, $selectedTo) {
                        $query->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) >= ?", $selectedFrom)
                            ->whereRaw("SUBSTRING(tbl_tx_cur.period, 1, 6) <= ?", $selectedTo);
                    })
                    ->groupBy('tbl_tx_cur.mfl_code');
                $txcurr = $query->selectRaw('SUM(tbl_tx_cur.tx_cur) as tx_cur')
                    ->get()
                    ->sum('tx_cur');
                $all_enrollment  = DB::select("CALL sp_nishauri_uptake(?,?,?,?,?,?,?,?)", ["%", "%", "%", "%", date($request->from), date($request->to), date($request->from), date($request->to)]);
                $all_module = DB::select("CALL sp_nishauri_access_uptake(?,?,?,?,?,?)", ["%", "%", "%", "%", date($request->from), date($request->to)]);
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment;
            $data['all_module'] =  $all_module;

            return $data;
        }
    }
}
