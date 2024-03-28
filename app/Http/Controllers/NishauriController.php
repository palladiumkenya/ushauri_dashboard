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
use App\Models\NishauriUptake;
use App\Models\NishauriAccess;
use App\Models\NishauriFacility;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Models\NishauriDrugDelivery;
use App\Models\NishauriDrugOrder;


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

        return response()->json(['profile_otp_number' => $otp_search ? $otp_search->profile_otp_number : null]);
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
                ->get(['id', 'name']);

            $sub_counties = SubCounty::select('tbl_sub_county.id', 'tbl_sub_county.name')
                ->join('tbl_partner_facility', 'tbl_sub_county.id', '=', 'tbl_partner_facility.sub_county_id')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->groupBy('tbl_sub_county.name')
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
            $all_enrollment  = NishauriUptake::select('*');
            $all_module = NishauriAccess::select('*');

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] = $all_enrollment->get()->map(function ($item) {
                $item['no_of_clients'] = (int)$item['no_of_clients'];
                return $item;
            });
            $data['all_module'] =  $all_module->get();
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
            $all_enrollment  = NishauriFacility::select('*')->where('mfl_code', Auth::user()->facility_id);
            $all_module = NishauriAccess::select('*')->where('mfl_code', Auth::user()->facility_id);

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment->get();
            $data['all_module'] =  $all_module->get();
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
            $all_enrollment  = NishauriUptake::select('*')->where('partner_id', Auth::user()->partner_id);
            $all_module = NishauriAccess::select('*')->where('partner_id', Auth::user()->partner_id);

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] = $all_enrollment->get()->map(function ($item) {
                $item['no_of_clients'] = (int)$item['no_of_clients'];
                return $item;
            });
            $data['all_module'] =  $all_module->get();
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
            $all_enrollment  = NishauriUptake::select('*')->where('county_id', Auth::user()->county_id);
            $all_module = NishauriAccess::select('*')->where('county_id', Auth::user()->county_id);

            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] = $all_enrollment->get()->map(function ($item) {
                $item['no_of_clients'] = (int)$item['no_of_clients'];
                return $item;
            });
            $data['all_module'] =  $all_module->get();
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
            $all_enrollment  = NishauriUptake::select('*')->where('sub_county_id', Auth::user()->subcounty_id);
            $all_module = NishauriAccess::select('*')->where('sub_county_id', Auth::user()->subcounty_id);


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] = $all_enrollment->get()->map(function ($item) {
                $item['no_of_clients'] = (int)$item['no_of_clients'];
                return $item;
            });
            $data['all_module'] =  $all_module->get();
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
                $all_enrollment  = NishauriUptake::select('*')->where('partner_id', $selected_partners);
                $all_module = NishauriAccess::select('*')->where('partner_id', $selected_partners);
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
                $all_enrollment  = NishauriUptake::select('*')->where('partner_id', Auth::user()->partner_id)->where('county_id', $selected_counties);
                $all_module = NishauriAccess::select('*')->where('partner_id', Auth::user()->partner_id)->where('county_id', $selected_counties);
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
                $all_enrollment  = NishauriUptake::select('*')->where('partner_id', Auth::user()->partner_id)->where('sub_county_id', $selected_subcounties);
                $all_module = NishauriAccess::select('*')->where('partner_id', Auth::user()->partner_id)->where('sub_county_id', $selected_subcounties);
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
                $all_enrollment  = NishauriUptake::select('*')->where('partner_id', Auth::user()->partner_id)->where('mfl_code', $selected_facilites);
                $all_module = NishauriAccess::select('*')->where('partner_id', Auth::user()->partner_id)->where('mfl_code', $selected_facilites);
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
                $all_enrollment  = NishauriUptake::select('*')->where('partner_id', Auth::user()->partner_id)->where('enrolled_date', '>=', date($request->from))->where('enrolled_date', '<=', date($request->to));
                $all_module = NishauriAccess::select('*')->where('partner_id', Auth::user()->partner_id)->where('date', '>=', date($request->from))->where('date', '<=', date($request->to));
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] = $all_enrollment->get()->map(function ($item) {
                $item['no_of_clients'] = (int)$item['no_of_clients'];
                return $item;
            });
            $data['all_module'] =  $all_module->get();

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
                $all_enrollment  = NishauriUptake::select('*')->where('county_id', Auth::user()->county_id)->where('partner_id', $selected_partners);
                $all_module = NishauriAccess::select('*')->where('county_id', Auth::user()->county_id)->where('partner_id', $selected_partners);
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
                $all_enrollment  = NishauriUptake::select('*')->where('county_id', Auth::user()->county_id)->where('county_id', $selected_counties);
                $all_module = NishauriAccess::select('*')->where('county_id', Auth::user()->county_id)->where('county_id', $selected_counties);
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
                $all_enrollment  = NishauriUptake::select('*')->where('county_id', Auth::user()->county_id)->where('sub_county_id', $selected_subcounties);
                $all_module = NishauriAccess::select('*')->where('county_id', Auth::user()->county_id)->where('sub_county_id', $selected_subcounties);
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
                $all_enrollment  = NishauriUptake::select('*')->where('mfl_code', $selected_facilites);
                $all_module = NishauriAccess::select('*')->where('mfl_code', $selected_facilites);
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
                $all_enrollment  = NishauriUptake::select('*')->where('county_id', Auth::user()->county_id)->where('enrolled_date', '>=', date($request->from))->where('enrolled_date', '<=', date($request->to));
                $all_module = NishauriAccess::select('*')->where('county_id', Auth::user()->county_id)->where('date', '>=', date($request->from))->where('date', '<=', date($request->to));
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] = $all_enrollment->get()->map(function ($item) {
                $item['no_of_clients'] = (int)$item['no_of_clients'];
                return $item;
            });
            $data['all_module'] =  $all_module->get();

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
                $all_enrollment  = NishauriUptake::select('*')->where('partner_id', $selected_partners);
                $all_module = NishauriAccess::select('*')->where('partner_id', $selected_partners);
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
                $all_enrollment  = NishauriUptake::select('*')->where('county_id', $selected_counties);
                $all_module = NishauriAccess::select('*')->where('county_id', $selected_counties);
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
                $all_enrollment  = NishauriUptake::select('*')->where('sub_county_id', $selected_subcounties);
                $all_module = NishauriAccess::select('*')->where('sub_county_id', $selected_subcounties);
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
                $all_enrollment  = NishauriUptake::select('*')->where('mfl_code', $selected_facilites);
                $all_module = NishauriAccess::select('*')->where('mfl_code', $selected_facilites);
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
                $all_enrollment  = NishauriUptake::select('*')->where('sub_county_id', Auth::user()->subcounty_id)->where('enrolled_date', '>=', date($request->from))->where('enrolled_date', '<=', date($request->to));
                $all_module = NishauriAccess::select('*')->where('sub_county_id', Auth::user()->subcounty_id)->where('date', '>=', date($request->from))->where('date', '<=', date($request->to));
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] = $all_enrollment->get()->map(function ($item) {
                $item['no_of_clients'] = (int)$item['no_of_clients'];
                return $item;
            });
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
                ->where('tbl_partner_facility.mfl_code', Auth::user()->facility_id);

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
                $all_enrollment  = NishauriFacility::select('*')->where('partner_id', $selected_partners);
                $all_module = NishauriAccess::select('*')->where('partner_id', $selected_partners);
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
                $all_enrollment  = NishauriFacility::select('*')->where('county_id', $selected_counties);
                $all_module = NishauriAccess::select('*')->where('county_id', $selected_counties);
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
                $all_enrollment  = NishauriFacility::select('*')->where('sub_county_id', $selected_subcounties);
                $all_module = NishauriAccess::select('*')->where('sub_county_id', $selected_subcounties);
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
                $all_enrollment  = NishauriFacility::select('*')->where('mfl_code', $selected_facilites);
                $all_module = NishauriAccess::select('*')->where('mfl_code', $selected_facilites);
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
                $all_enrollment  = NishauriFacility::select('*')->where('mfl_code', Auth::user()->facility_id)->where('enrolled_date', '>=', date($request->from))->where('enrolled_date', '<=', date($request->to));
                $all_module = NishauriAccess::select('*')->where('mfl_code', Auth::user()->facility_id)->where('date', '>=', date($request->from))->where('date', '<=', date($request->to));
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] =  $all_enrollment->get();
            $data['all_module'] =  $all_module->get();

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
                $all_enrollment  = NishauriUptake::select('*')->where('partner_id', $selected_partners);
                $all_module = NishauriAccess::select('*')->where('partner_id', $selected_partners);
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
                $all_enrollment  = NishauriUptake::select('*')->where('county_id', $selected_counties);
                $all_module = NishauriAccess::select('*')->where('county_id', $selected_counties);
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
                $all_enrollment  = NishauriUptake::select('*')->where('sub_county_id', $selected_subcounties);
                $all_module = NishauriAccess::select('*')->where('sub_county_id', $selected_subcounties);
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
                $all_enrollment  = NishauriUptake::select('*')->where('mfl_code', $selected_facilites);
                $all_module = NishauriAccess::select('*')->where('mfl_code', $selected_facilites);
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
                $all_enrollment  = NishauriUptake::select('*')->where('enrolled_date', '>=', date($request->from))->where('enrolled_date', '<=', date($request->to));
                $all_module = NishauriAccess::select('*')->where('date', '>=', date($request->from))->where('date', '<=', date($request->to));
            }


            $data['txcurr'] =  $txcurr;
            $data['all_enrollment'] = $all_enrollment->get()->map(function ($item) {
                $item['no_of_clients'] = (int)$item['no_of_clients'];
                return $item;
            });
            $data['all_module'] =  $all_module->get();

            return $data;
        }
    }

    public function drug_delivery_list()
    {
        if (Auth::user()->access_level == 'Facility') {

            $drug_delivery = NishauriDrugDelivery::select('tbl_nishauri_drug_order.id as order_id', 'tbl_appointment.appntmnt_date', 'tbl_client.clinic_number', 'tbl_nishauri_drug_order.mode', 'tbl_nishauri_drug_order.delivery_method', 'tbl_nishauri_drug_order.delivery_person', 'tbl_nishauri_drug_order.delivery_person_contact', 'tbl_nishauri_drug_order.delivery_pickup_time', 'tbl_nishauri_drug_order.status', 'tbl_nishauri_drug_order.appointment_id')
            ->leftJoin('tbl_appointment', 'tbl_appointment.id', '=', 'tbl_nishauri_drug_order.appointment_id')
            ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_nishauri_drug_order.program_identifier')
            ->where('tbl_client.mfl_code', Auth::user()->facility_id)
            ->where('tbl_nishauri_drug_order.status', '=', 'Pending')
            ->get();
            $drug_dispatch = NishauriDrugDelivery::select('tbl_nishauri_drug_order.id as order_id', 'tbl_appointment.appntmnt_date', 'tbl_client.clinic_number', 'tbl_nishauri_drug_order.mode', 'tbl_nishauri_drug_order.delivery_method', 'tbl_nishauri_drug_order.delivery_person', 'tbl_nishauri_drug_order.delivery_person_contact', 'tbl_nishauri_drug_order.delivery_pickup_time', 'tbl_nishauri_drug_order.status', 'tbl_nishauri_drug_order.appointment_id')
            ->leftJoin('tbl_appointment', 'tbl_appointment.id', '=', 'tbl_nishauri_drug_order.appointment_id')
            ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_nishauri_drug_order.program_identifier')
            ->where('tbl_client.mfl_code', Auth::user()->facility_id)
            ->where('tbl_nishauri_drug_order.status', '=', 'Approved')
            ->get();

            $drug_fullfilled = NishauriDrugDelivery::select('tbl_nishauri_drug_order.id as order_id', 'tbl_appointment.appntmnt_date', 'tbl_client.clinic_number', 'tbl_nishauri_drug_order.mode', 'tbl_nishauri_drug_order.delivery_method', 'tbl_nishauri_drug_order.delivery_person', 'tbl_nishauri_drug_order.delivery_person_contact', 'tbl_nishauri_drug_order.delivery_pickup_time', 'tbl_nishauri_drug_order.status', 'tbl_nishauri_drug_order.appointment_id')
            ->leftJoin('tbl_appointment', 'tbl_appointment.id', '=', 'tbl_nishauri_drug_order.appointment_id')
            ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_nishauri_drug_order.program_identifier')
            ->where('tbl_client.mfl_code', Auth::user()->facility_id)
            ->where('tbl_nishauri_drug_order.status', '=', 'Fullfilled')
            ->get();

            return view('nishauri.drug_delivery', compact('drug_delivery', 'drug_dispatch', 'drug_fullfilled'));
        }
    }

    public function delivery_approval(Request $request)
    {
        try {
            $client = NishauriDrugDelivery::where('appointment_id', $request->appointment_id)
                ->update([
                    'status' => 'Approved',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // dd( $client);
            if ($client) {
                NishauriDrugOrder::create([
                    'order_id' => $request->order_id,
                    'initiated_by' => Auth::user()->id,
                ]);
                Alert::success('Success', 'Delivery Successfully Approved');
                return redirect('delivery/list');
            } else {
                Alert::error('Failed', 'Could not approve please try again later.');
                return back();
            }
        } catch (Exception $e) {
        }
    }

    public function delivery_dispatch(Request $request)
    {
        try {
            $client = NishauriDrugDelivery::where('appointment_id', $request->appointment_id)
                ->update([
                    'status' => 'Dispatched',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // dd( $client);
            if ($client) {
                NishauriDrugOrder::where('order_id', $request->order_id)
                ->update([
                    'initiated_by' => Auth::user()->id,
                    'dispatch_notes' => $request->dispatch_notes,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                Alert::success('Success', 'Delivery Successfully Dispatched');
                return redirect('delivery/list');
            } else {
                Alert::error('Failed', 'Could not dispatch please try again later.');
                return back();
            }
        } catch (Exception $e) {
        }
    }
}
