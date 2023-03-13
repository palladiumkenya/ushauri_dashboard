<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Appointments;
use App\Models\Marital;
use App\Models\Partner;
use App\Models\Lab;
use App\Models\AppointmentType;
use App\Models\FutureApp;
use Session;
use Redirect, Response;
use Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        if (Auth::user()->access_level == 'Facility') {
            $all_future_apps = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->select('tbl_appointment.id', 'tbl_appointment.client_id', 'tbl_client.clinic_number', 'tbl_client.upi_no', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name as app_type')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            $all_app_types = AppointmentType::all();
        }
        return view('appointments.future_apps_edit', compact('all_future_apps', 'all_app_types'));
    }
    public function editappointment(Request $request)
    {
        try {
            $appointment = Appointments::where('id', $request->id)
                ->update([
                    'app_type_1' => $request->app_type,
                    'reason' => $request->reason,
                    'appntmnt_date' => date("Y-m-d", strtotime($request->appntmnt_date)),
                    'reason' => $request->reason,
                    'expln_app' => "EDITED",

                ]);

            if ($appointment) {
                Session::flash('statuscode', 'success');
                return redirect('report/future/appointments')->with('status', 'Appointment was updated successfully!');
            } else {
                Session::flash('statuscode', 'error');
                return back()->with('error', 'Could not consent client please try again later.');
            }
        } catch (Exception $e) {
            return back();
        }
    }
    public function get_future_appointments()
    {
        if (Auth::user()->access_level == 'Admin') {
            $all_future_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.upi_no, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type')
                ->where('tbl_appointment.appntmnt_date', '>', Now());
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_future_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.upi_no, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->where('tbl_appointment.appntmnt_date', '>', Now());
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_future_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.upi_no, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->where('tbl_appointment.appntmnt_date', '>', Now());
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_future_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.upi_no, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type')
                ->where('donor_id', Auth::user()->donor_id)
                ->where('tbl_appointment.appntmnt_date', '>', Now());
        }

        return view('appointments.future_appointments')->with('all_future_appointments', $all_future_appointments->get());
    }

    public function get_missed_appointments()
    {
        $all_missed_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
            ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
            ->where('tbl_appointment.app_status', '=', 'Missed');

        if (Auth::user()->access_level == 'Facility') {
            $all_missed_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_missed_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_missed_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'Missed');
            // $all_missed_appointments->where('donor_id', Auth::user()->donor_id);
        }

        return view('appointments.missed_appointments')->with('all_missed_appointments', $all_missed_appointments->get());
    }

    public function get_defaulted_appointments()
    {
        if (Auth::user()->access_level == 'Admin') {
            $all_defaulted_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'Defaulted');
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_defaulted_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_defaulted_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_defaulted_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'Defaulted');
            //->where('donor_id', Auth::user()->donor_id);
        }

        return view('appointments.defaulted_appointments')->with('all_defaulted_appointments', $all_defaulted_appointments->get());
    }

    public function get_ltfu_appointments()
    {
        if (Auth::user()->access_level == 'Admin') {
            $all_ltfu_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'LTFU');
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_ltfu_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_appointment.app_msg, tbl_appointment.no_calls, tbl_appointment.no_msgs, tbl_appointment.home_visits')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_ltfu_appointments->where('tbl_client.partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            // $all_ltfu_appointments->where('donor_id', Auth::user()->donor_id);
        }

        return view('appointments.ltfu_appointments')->with('all_ltfu_appointments', $all_ltfu_appointments->get());
    }

    public function get_appointment_list()
    {
        $all_appointments = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
            ->selectRaw('tbl_client.clinic_number, tbl_client.file_no, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment_types.name as app_type_1, tbl_client.status, tbl_client.enrollment_date, tbl_client.art_date, tbl_client.created_at')
            ->whereNotNull('tbl_appointment.appntmnt_date')->orderBy('tbl_appointment.appntmnt_date');

        if (Auth::user()->access_level == 'Facility') {
            $all_appointments->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_appointments->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_appointments->where('donor_id', Auth::user()->donor_id);
        }

        return view('appointments.appointments_list')->with('all_appointments', $all_appointments->get());
    }

    public function get_count_appointments()
    {
        $data = [];
        $all_count_appointment = Appointments::selectRaw('tbl_appointment_types.name, tbl_client.clinic_number, tbl_appointment.appntmnt_date AS start, COUNT(tbl_appointment.id) AS title')
            ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
            ->whereNotNull('tbl_appointment.id');

        // ->innerJoin('tbl_appointment_types') ON 'tbl_appointment_types.id' = 'tbl_appointment.app_type_1'

        $data['all_count_appointment'] = $all_count_appointment->get();
        return $data;
    }

    public function get_appointment_count()
    {

        $all_count_appointment = Appointments::selectRaw('tbl_appointment_types.name, tbl_appointment.appntmnt_date AS start, COUNT(tbl_appointment.id) AS title')
            ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
            ->whereNotNull('tbl_appointment.id');

        if (Auth::user()->access_level == 'Facility') {
            $all_count_appointment->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        return view('appointments.appointment_calender')->with('all_count_appointment', $all_count_appointment->count());
    }
    public function get_created_appointments()
    {
        $data = [];

        $all_created_appointment = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->where('tbl_client.mfl_code', '=', 12345);

        if (Auth::user()->access_level == 'Facility') {
            $all_created_appointment->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_created_appointment->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_created_appointment->where('donor_id', Auth::user()->donor_id);
        }

        $data['all_created_appointment'] = $all_created_appointment->count();

        return $data;
    }

    public function get_active_missed()
    {
        $data = [];

        $all_active_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->where('active_app', '=', 1)
            ->where('app_status', '=', 'Missed');

        if (Auth::user()->access_level == 'Facility') {
            $data->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $data->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $data->where('donor_id', Auth::user()->donor_id);
        }

        $data['all_active_missed'] = $all_active_missed->count();

        return $data;
    }
    public function get_active_defaulted()
    {
        $data = [];

        $all_active_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->where('active_app', '=', 1)
            ->where('app_status', '=', 'Defaulted');

        if (Auth::user()->access_level == 'Facility') {
            $all_active_defaulted->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_active_defaulted->where('tbl_client.partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_active_defaulted->where('donor_id', Auth::user()->donor_id);
        }

        $data['all_active_defaulted'] = $all_active_defaulted->count();

        return $data;
    }
    public function get_active_ltfu()
    {
        $data = [];

        $all_active_ltfu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->where('active_app', '=', 1)
            ->where('app_status', '=', 'LTFU');

        if (Auth::user()->access_level == 'Facility') {
            $all_active_ltfu->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_active_ltfu->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_active_ltfu->where('donor_id', Auth::user()->donor_id);
        }

        $data['all_active_ltfu'] = $all_active_ltfu->count();

        return $data;
    }
    public function get_app_honourned()
    {
        $data = [];

        $all_app_honourned = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->where('tbl_appointment.active_app', '=', 0)
            ->where('tbl_appointment.appointment_kept', '=', 'Yes')
            ->where('tbl_appointment.appntmnt_date', '<', Now());
        if (Auth::user()->access_level == 'Facility') {
            $all_app_honourned->where('tbl_client.mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_app_honourned->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_app_honourned->where('donor_id', Auth::user()->donor_id);
        }

        $data['all_app_honourned'] = $all_app_honourned->count();

        return $data;
    }
    public function appointment_dashboard()
    {
        $all_partners = Partner::where('status', '=', 'Active')
        ->pluck('name', 'id');
        if (Auth::user()->access_level == 'Facility') {

            $all_ltfu_app_10_14 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereBetween('tbl_client.dob', [now()->subYears(10), now()->subYears(14)])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->pluck('count')->first();

            $all_missed_app_0_9 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->where('tbl_client.dob', '<=',  now()->subYears(9))
                ->pluck('count')->first();

            $all_ltfu_app_15_19 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereBetween('tbl_client.dob', [now()->subYears(15), now()->subYears(19)])
                ->where('tbl_client.mfl_code',  Auth::user()->facility_id)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->pluck('count')->first();

            $all_ltfu_app_20_24 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where(function ($query) {
                    $query->where('tbl_client.dob', '>=', now()->subYears(20))
                        ->where('tbl_client.dob', '<=', now()->subYears(24));
                })
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->toSql();


            $all_ltfu_app_25 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.dob', '>=',   Carbon::now()->subYears(25))
                ->pluck('count')->first();


            // appointments count
            $created_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $kept_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $defaulted_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $missed_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $ltfu_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();


            // single active appointment

            $all_appointment_by_marital_single_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_single_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_single_ltfu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            // Married Monogomous Active Appointment

            $all_appointment_by_marital_monogomous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_monogomous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_monogomous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            // Divorced Active Appointment

            $all_appointment_by_marital_divorced_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_divorced_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_divorced_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            // Widowed Active Appointment

            $all_appointment_by_marital_widowed_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_widowed_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_widowed_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            // Cohabiting Active Appointment

            $all_appointment_by_marital_cohabiting_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_cohabiting_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_cohabiting_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            // Unavailable Active Appointment

            $all_appointment_by_marital_unavailable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_unavailable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_unavailable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            // Polygamous Active Appointment

            $all_appointment_by_marital_polygamous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_polygamous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_polygamous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            // Not applicable Active Appointment

            $all_appointment_by_marital_notapplicable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();


            $all_appointment_by_marital_notapplicable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_notapplicable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();
        }

        if (Auth::user()->access_level == 'Admin') {

            $all_ltfu_app_10_14 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereBetween('tbl_client.dob', [now()->subYears(10), now()->subYears(14)])
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->pluck('count')->first();

            $all_missed_app_0_9 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('tbl_client.dob', '<=',  now()->subYears(9))
                ->pluck('count')->first();

            $all_ltfu_app_15_19 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereBetween('tbl_client.dob', [now()->subYears(15), now()->subYears(19)])
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->pluck('count')->first();

            $all_ltfu_app_20_24 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where(function ($query) {
                    $query->where('tbl_client.dob', '>=', now()->subYears(20))
                        ->where('tbl_client.dob', '<=', now()->subYears(24));
                })
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->toSql();


            $all_ltfu_app_25 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.dob', '>=',   Carbon::now()->subYears(25))
                ->pluck('count')->first();


            // appointments count
            $created_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("tbl_appointment.id as count"))
                ->whereNotNull('tbl_appointment.id')
                ->count();

            $kept_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("tbl_appointment.id as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->count();

            $defaulted_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("tbl_appointment.id as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->count();

            $missed_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("tbl_appointment.id as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->count();

            $ltfu_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("tbl_appointment.id as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->count();


            // single active appointment

            $all_appointment_by_marital_single_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->pluck('count')->first();

            $all_appointment_by_marital_single_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->pluck('count')->first();

            $all_appointment_by_marital_single_ltfu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->pluck('count')->first();

            // Married Monogomous Active Appointment

            $all_appointment_by_marital_monogomous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->pluck('count')->first();

            $all_appointment_by_marital_monogomous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->pluck('count')->first();

            $all_appointment_by_marital_monogomous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->pluck('count')->first();

            // Divorced Active Appointment

            $all_appointment_by_marital_divorced_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->pluck('count')->first();

            $all_appointment_by_marital_divorced_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->pluck('count')->first();

            $all_appointment_by_marital_divorced_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->pluck('count')->first();

            // Widowed Active Appointment

            $all_appointment_by_marital_widowed_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->pluck('count')->first();

            $all_appointment_by_marital_widowed_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->pluck('count')->first();

            $all_appointment_by_marital_widowed_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->pluck('count')->first();

            // Cohabiting Active Appointment

            $all_appointment_by_marital_cohabiting_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->pluck('count')->first();

            $all_appointment_by_marital_cohabiting_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->pluck('count')->first();

            $all_appointment_by_marital_cohabiting_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->pluck('count')->first();

            // Unavailable Active Appointment

            $all_appointment_by_marital_unavailable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->pluck('count')->first();

            $all_appointment_by_marital_unavailable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->pluck('count')->first();

            $all_appointment_by_marital_unavailable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->pluck('count')->first();

            // Polygamous Active Appointment

            $all_appointment_by_marital_polygamous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->pluck('count')->first();

            $all_appointment_by_marital_polygamous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->pluck('count')->first();

            $all_appointment_by_marital_polygamous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->pluck('count')->first();

            // Not applicable Active Appointment

            $all_appointment_by_marital_notapplicable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->pluck('count')->first();


            $all_appointment_by_marital_notapplicable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->pluck('count')->first();

            $all_appointment_by_marital_notapplicable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->pluck('count')->first();
        }
        if (Auth::user()->access_level == 'Donor') {

            $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');

            $all_ltfu_app_10_14 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereBetween('tbl_client.dob', [now()->subYears(10), now()->subYears(14)])
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->pluck('count')->first();

            $all_missed_app_0_9 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('tbl_client.dob', '<=',  now()->subYears(9))
                ->pluck('count')->first();

            $all_ltfu_app_15_19 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereBetween('tbl_client.dob', [now()->subYears(15), now()->subYears(19)])
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->pluck('count')->first();

            $all_ltfu_app_20_24 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where(function ($query) {
                    $query->where('tbl_client.dob', '>=', now()->subYears(20))
                        ->where('tbl_client.dob', '<=', now()->subYears(24));
                })
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->toSql();


            $all_ltfu_app_25 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.dob', '>=',   Carbon::now()->subYears(25))
                ->pluck('count')->first();


            // appointments count
            $created_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->pluck('count')->first();

            $kept_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count')->first();

            $defaulted_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count')->first();

            $missed_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count')->first();

            $ltfu_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count')->first();


            // single active appointment

            $all_appointment_by_marital_single_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->pluck('count')->first();

            $all_appointment_by_marital_single_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->pluck('count')->first();

            $all_appointment_by_marital_single_ltfu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->pluck('count')->first();

            // Married Monogomous Active Appointment

            $all_appointment_by_marital_monogomous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->pluck('count')->first();

            $all_appointment_by_marital_monogomous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->pluck('count')->first();

            $all_appointment_by_marital_monogomous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->pluck('count')->first();

            // Divorced Active Appointment

            $all_appointment_by_marital_divorced_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->pluck('count')->first();

            $all_appointment_by_marital_divorced_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->pluck('count')->first();

            $all_appointment_by_marital_divorced_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->pluck('count')->first();

            // Widowed Active Appointment

            $all_appointment_by_marital_widowed_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->pluck('count')->first();

            $all_appointment_by_marital_widowed_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->pluck('count')->first();

            $all_appointment_by_marital_widowed_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->pluck('count')->first();

            // Cohabiting Active Appointment

            $all_appointment_by_marital_cohabiting_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->pluck('count')->first();

            $all_appointment_by_marital_cohabiting_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->pluck('count')->first();

            $all_appointment_by_marital_cohabiting_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->pluck('count')->first();

            // Unavailable Active Appointment

            $all_appointment_by_marital_unavailable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->pluck('count')->first();

            $all_appointment_by_marital_unavailable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->pluck('count')->first();

            $all_appointment_by_marital_unavailable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->pluck('count')->first();

            // Polygamous Active Appointment

            $all_appointment_by_marital_polygamous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->pluck('count')->first();

            $all_appointment_by_marital_polygamous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->pluck('count')->first();

            $all_appointment_by_marital_polygamous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->pluck('count')->first();

            // Not applicable Active Appointment

            $all_appointment_by_marital_notapplicable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->pluck('count')->first();


            $all_appointment_by_marital_notapplicable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->pluck('count')->first();

            $all_appointment_by_marital_notapplicable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->pluck('count')->first();
        }

        if (Auth::user()->access_level == 'Partner') {

            $all_partners = Partner::where('status', '=', 'Active')
            ->where('id', Auth::user()->partner_id)
            ->pluck('name', 'id');

            $all_ltfu_app_10_14 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereBetween('tbl_client.dob', [now()->subYears(10), now()->subYears(14)])
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->pluck('count')->first();

            $all_missed_app_0_9 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('tbl_client.dob', '<=',  now()->subYears(9))
                ->pluck('count')->first();

            $all_ltfu_app_15_19 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereBetween('tbl_client.dob', [now()->subYears(15), now()->subYears(19)])
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->pluck('count')->first();

            $all_ltfu_app_20_24 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where(function ($query) {
                    $query->where('tbl_client.dob', '>=', now()->subYears(20))
                        ->where('tbl_client.dob', '<=', now()->subYears(24));
                })
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->toSql();


            $all_ltfu_app_25 = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', '=', 12345)
                ->where('active_app', '=', 1)
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.dob', '>=',   Carbon::now()->subYears(25))
                ->pluck('count')->first();


            // appointments count
            $created_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $kept_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $defaulted_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $missed_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $ltfu_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();


            // single active appointment

            $all_appointment_by_marital_single_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_single_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_single_ltfu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Single')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            // Married Monogomous Active Appointment

            $all_appointment_by_marital_monogomous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_monogomous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_monogomous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Married Monogamous')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            // Divorced Active Appointment

            $all_appointment_by_marital_divorced_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_divorced_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_divorced_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Divorced')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            // Widowed Active Appointment

            $all_appointment_by_marital_widowed_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_widowed_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_widowed_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Widowed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            // Cohabiting Active Appointment

            $all_appointment_by_marital_cohabiting_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_cohabiting_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_cohabiting_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Cohabiting')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            // Unavailable Active Appointment

            $all_appointment_by_marital_unavailable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_unavailable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_unavailable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Unavailable')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            // Polygamous Active Appointment

            $all_appointment_by_marital_polygamous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_polygamous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_polygamous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Maried polygamous')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            // Not applicable Active Appointment

            $all_appointment_by_marital_notapplicable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();


            $all_appointment_by_marital_notapplicable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();

            $all_appointment_by_marital_notapplicable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_marital_status.marital', '=', 'Not Applicable')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count')->first();
        }

        return view('appointments.appointment_dashboard', compact(
            'all_partners',
            'created_appointmnent_count',
            'kept_appointmnent_count',
            'missed_appointmnent_count',
            'defaulted_appointmnent_count',
            'ltfu_appointmnent_count',
            'all_appointment_by_marital_single_missed',
            'all_appointment_by_marital_single_defaulted',
            'all_appointment_by_marital_single_ltfu',
            'all_appointment_by_marital_monogomous_missed',
            'all_appointment_by_marital_monogomous_defaulted',
            'all_appointment_by_marital_monogomous_lftu',
            'all_appointment_by_marital_divorced_missed',
            'all_appointment_by_marital_divorced_defaulted',
            'all_appointment_by_marital_divorced_lftu',
            'all_appointment_by_marital_widowed_missed',
            'all_appointment_by_marital_widowed_defaulted',
            'all_appointment_by_marital_widowed_lftu',
            'all_appointment_by_marital_cohabiting_missed',
            'all_appointment_by_marital_cohabiting_defaulted',
            'all_appointment_by_marital_cohabiting_lftu',
            'all_appointment_by_marital_unavailable_missed',
            'all_appointment_by_marital_unavailable_defaulted',
            'all_appointment_by_marital_unavailable_lftu',
            'all_appointment_by_marital_polygamous_missed',
            'all_appointment_by_marital_polygamous_defaulted',
            'all_appointment_by_marital_polygamous_lftu',
            'all_appointment_by_marital_notapplicable_missed',
            'all_appointment_by_marital_notapplicable_defaulted',
            'all_appointment_by_marital_notapplicable_lftu'
        ));
    }

    public function filter_appointment_dashboard(Request $request)
    {
        $data                = [];

        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;

         // appointments count
         $created_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->whereNotNull('tbl_appointment.id');

     $kept_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->whereNotNull('tbl_appointment.id')
         ->where('tbl_appointment.appointment_kept', '=', 'Yes');

     $defaulted_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->whereNotNull('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted');

     $missed_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
     ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
     ->select('tbl_appointment.id')
         ->whereNotNull('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed');

     $ltfu_appointmnent_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
     ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
     ->select('tbl_appointment.id')
         ->whereNotNull('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU');


     // single active appointment

     $all_appointment_by_marital_single_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed')
         ->where('tbl_marital_status.marital', '=', 'Single');

     $all_appointment_by_marital_single_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted')
         ->where('tbl_marital_status.marital', '=', 'Single');

     $all_appointment_by_marital_single_ltfu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU')
         ->where('tbl_marital_status.marital', '=', 'Single');

     // Married Monogomous Active Appointment

     $all_appointment_by_marital_monogomous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed')
         ->where('tbl_marital_status.marital', '=', 'Married Monogamous');

     $all_appointment_by_marital_monogomous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted')
         ->where('tbl_marital_status.marital', '=', 'Married Monogamous');

     $all_appointment_by_marital_monogomous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU')
         ->where('tbl_marital_status.marital', '=', 'Married Monogamous');

     // Divorced Active Appointment

     $all_appointment_by_marital_divorced_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed')
         ->where('tbl_marital_status.marital', '=', 'Divorced');

     $all_appointment_by_marital_divorced_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted')
         ->where('tbl_marital_status.marital', '=', 'Divorced');

     $all_appointment_by_marital_divorced_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU')
         ->where('tbl_marital_status.marital', '=', 'Divorced');

     // Widowed Active Appointment

     $all_appointment_by_marital_widowed_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed')
         ->where('tbl_marital_status.marital', '=', 'Widowed');

     $all_appointment_by_marital_widowed_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted')
         ->where('tbl_marital_status.marital', '=', 'Widowed');

     $all_appointment_by_marital_widowed_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU')
         ->where('tbl_marital_status.marital', '=', 'Widowed');

     // Cohabiting Active Appointment

     $all_appointment_by_marital_cohabiting_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed')
         ->where('tbl_marital_status.marital', '=', 'Cohabiting');

     $all_appointment_by_marital_cohabiting_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted')
         ->where('tbl_marital_status.marital', '=', 'Cohabiting');

     $all_appointment_by_marital_cohabiting_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU')
         ->where('tbl_marital_status.marital', '=', 'Cohabiting');

     // Unavailable Active Appointment

     $all_appointment_by_marital_unavailable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed')
         ->where('tbl_marital_status.marital', '=', 'Unavailable');

     $all_appointment_by_marital_unavailable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted')
         ->where('tbl_marital_status.marital', '=', 'Unavailable');

     $all_appointment_by_marital_unavailable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU')
         ->where('tbl_marital_status.marital', '=', 'Unavailable');

     // Polygamous Active Appointment

     $all_appointment_by_marital_polygamous_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed')
         ->where('tbl_marital_status.marital', '=', 'Maried polygamous');

     $all_appointment_by_marital_polygamous_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted')
         ->where('tbl_marital_status.marital', '=', 'Maried polygamous');

     $all_appointment_by_marital_polygamous_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU')
         ->where('tbl_marital_status.marital', '=', 'Maried polygamous');

     // Not applicable Active Appointment

     $all_appointment_by_marital_notapplicable_missed = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Missed')
         ->where('tbl_marital_status.marital', '=', 'Not Applicable');


     $all_appointment_by_marital_notapplicable_defaulted = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'Defaulted')
         ->where('tbl_marital_status.marital', '=', 'Not Applicable');

     $all_appointment_by_marital_notapplicable_lftu = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
         ->join('tbl_marital_status', 'tbl_marital_status.id', '=', 'tbl_client.marital')
         ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->select('tbl_appointment.id')
         ->where('tbl_appointment.active_app', '=', 1)
         ->where('tbl_appointment.app_status', '=', 'LTFU')
         ->where('tbl_marital_status.marital', '=', 'Not Applicable');

         if (!empty($selected_partners)) {
            $created_appointmnent_count = $created_appointmnent_count->where('tbl_partner_facility.partner_id', $selected_partners);
            $kept_appointmnent_count = $kept_appointmnent_count->where('tbl_partner_facility.partner_id', $selected_partners);
            $defaulted_appointmnent_count = $defaulted_appointmnent_count->where('tbl_partner_facility.partner_id', $selected_partners);
            $missed_appointmnent_count = $missed_appointmnent_count->where('tbl_partner_facility.partner_id', $selected_partners);
            $ltfu_appointmnent_count = $ltfu_appointmnent_count->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_single_missed = $all_appointment_by_marital_single_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_single_defaulted = $all_appointment_by_marital_single_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_single_ltfu = $all_appointment_by_marital_single_ltfu->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_monogomous_missed = $all_appointment_by_marital_monogomous_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_monogomous_defaulted = $all_appointment_by_marital_monogomous_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_monogomous_lftu = $all_appointment_by_marital_monogomous_lftu->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_divorced_missed = $all_appointment_by_marital_divorced_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_divorced_defaulted = $all_appointment_by_marital_divorced_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_divorced_lftu = $all_appointment_by_marital_divorced_lftu->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_widowed_missed = $all_appointment_by_marital_widowed_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_widowed_defaulted = $all_appointment_by_marital_widowed_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_widowed_lftu = $all_appointment_by_marital_widowed_lftu->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_cohabiting_missed = $all_appointment_by_marital_cohabiting_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_cohabiting_defaulted = $all_appointment_by_marital_cohabiting_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_cohabiting_lftu = $all_appointment_by_marital_cohabiting_lftu->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_unavailable_missed = $all_appointment_by_marital_unavailable_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_unavailable_defaulted = $all_appointment_by_marital_unavailable_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_unavailable_lftu = $all_appointment_by_marital_unavailable_lftu->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_polygamous_missed = $all_appointment_by_marital_polygamous_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_polygamous_defaulted = $all_appointment_by_marital_polygamous_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_polygamous_lftu = $all_appointment_by_marital_polygamous_lftu->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_notapplicable_missed = $all_appointment_by_marital_notapplicable_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_notapplicable_defaulted = $all_appointment_by_marital_notapplicable_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $all_appointment_by_marital_notapplicable_lftu = $all_appointment_by_marital_notapplicable_lftu->where('tbl_partner_facility.partner_id', $selected_partners);

        }

        if (!empty($selected_counties)) {
            $created_appointmnent_count = $created_appointmnent_count->where('tbl_partner_facility.county_id', $selected_counties);
            $kept_appointmnent_count = $kept_appointmnent_count->where('tbl_partner_facility.county_id', $selected_counties);
            $defaulted_appointmnent_count = $defaulted_appointmnent_count->where('tbl_partner_facility.county_id', $selected_counties);
            $missed_appointmnent_count = $missed_appointmnent_count->where('tbl_partner_facility.county_id', $selected_counties);
            $ltfu_appointmnent_count = $ltfu_appointmnent_count->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_single_missed = $all_appointment_by_marital_single_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_single_defaulted = $all_appointment_by_marital_single_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_single_ltfu = $all_appointment_by_marital_single_ltfu->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_monogomous_missed = $all_appointment_by_marital_monogomous_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_monogomous_defaulted = $all_appointment_by_marital_monogomous_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_monogomous_lftu = $all_appointment_by_marital_monogomous_lftu->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_divorced_missed = $all_appointment_by_marital_divorced_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_divorced_defaulted = $all_appointment_by_marital_divorced_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_divorced_lftu = $all_appointment_by_marital_divorced_lftu->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_widowed_missed = $all_appointment_by_marital_widowed_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_widowed_defaulted = $all_appointment_by_marital_widowed_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_widowed_lftu = $all_appointment_by_marital_widowed_lftu->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_cohabiting_missed = $all_appointment_by_marital_cohabiting_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_cohabiting_defaulted = $all_appointment_by_marital_cohabiting_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_cohabiting_lftu = $all_appointment_by_marital_cohabiting_lftu->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_unavailable_missed = $all_appointment_by_marital_unavailable_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_unavailable_defaulted = $all_appointment_by_marital_unavailable_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_unavailable_lftu = $all_appointment_by_marital_unavailable_lftu->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_polygamous_missed = $all_appointment_by_marital_polygamous_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_polygamous_defaulted = $all_appointment_by_marital_polygamous_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_polygamous_lftu = $all_appointment_by_marital_polygamous_lftu->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_notapplicable_missed = $all_appointment_by_marital_notapplicable_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_notapplicable_defaulted = $all_appointment_by_marital_notapplicable_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $all_appointment_by_marital_notapplicable_lftu = $all_appointment_by_marital_notapplicable_lftu->where('tbl_partner_facility.county_id', $selected_counties);

        }

        if (!empty($selected_subcounties)) {
            $created_appointmnent_count = $created_appointmnent_count->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $kept_appointmnent_count = $kept_appointmnent_count->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $defaulted_appointmnent_count = $defaulted_appointmnent_count->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $missed_appointmnent_count = $missed_appointmnent_count->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $ltfu_appointmnent_count = $ltfu_appointmnent_count->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_single_missed = $all_appointment_by_marital_single_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_single_defaulted = $all_appointment_by_marital_single_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_single_ltfu = $all_appointment_by_marital_single_ltfu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_monogomous_missed = $all_appointment_by_marital_monogomous_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_monogomous_defaulted = $all_appointment_by_marital_monogomous_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_monogomous_lftu = $all_appointment_by_marital_monogomous_lftu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_divorced_missed = $all_appointment_by_marital_divorced_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_divorced_defaulted = $all_appointment_by_marital_divorced_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_divorced_lftu = $all_appointment_by_marital_divorced_lftu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_widowed_missed = $all_appointment_by_marital_widowed_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_widowed_defaulted = $all_appointment_by_marital_widowed_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_widowed_lftu = $all_appointment_by_marital_widowed_lftu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_cohabiting_missed = $all_appointment_by_marital_cohabiting_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_cohabiting_defaulted = $all_appointment_by_marital_cohabiting_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_cohabiting_lftu = $all_appointment_by_marital_cohabiting_lftu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_unavailable_missed = $all_appointment_by_marital_unavailable_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_unavailable_defaulted = $all_appointment_by_marital_unavailable_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_unavailable_lftu = $all_appointment_by_marital_unavailable_lftu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_polygamous_missed = $all_appointment_by_marital_polygamous_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_polygamous_defaulted = $all_appointment_by_marital_polygamous_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_polygamous_lftu = $all_appointment_by_marital_polygamous_lftu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_notapplicable_missed = $all_appointment_by_marital_notapplicable_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_notapplicable_defaulted = $all_appointment_by_marital_notapplicable_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $all_appointment_by_marital_notapplicable_lftu = $all_appointment_by_marital_notapplicable_lftu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);

        }

        if (!empty($selected_facilites)) {
            $created_appointmnent_count = $created_appointmnent_count->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $kept_appointmnent_count = $kept_appointmnent_count->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $defaulted_appointmnent_count = $defaulted_appointmnent_count->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $missed_appointmnent_count = $missed_appointmnent_count->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $ltfu_appointmnent_count = $ltfu_appointmnent_count->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_single_missed = $all_appointment_by_marital_single_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_single_defaulted = $all_appointment_by_marital_single_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_single_ltfu = $all_appointment_by_marital_single_ltfu->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_monogomous_missed = $all_appointment_by_marital_monogomous_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_monogomous_defaulted = $all_appointment_by_marital_monogomous_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_monogomous_lftu = $all_appointment_by_marital_monogomous_lftu->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_divorced_missed = $all_appointment_by_marital_divorced_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_divorced_defaulted = $all_appointment_by_marital_divorced_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_divorced_lftu = $all_appointment_by_marital_divorced_lftu->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_widowed_missed = $all_appointment_by_marital_widowed_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_widowed_defaulted = $all_appointment_by_marital_widowed_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_widowed_lftu = $all_appointment_by_marital_widowed_lftu->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_cohabiting_missed = $all_appointment_by_marital_cohabiting_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_cohabiting_defaulted = $all_appointment_by_marital_cohabiting_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_cohabiting_lftu = $all_appointment_by_marital_cohabiting_lftu->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_unavailable_missed = $all_appointment_by_marital_unavailable_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_unavailable_defaulted = $all_appointment_by_marital_unavailable_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_unavailable_lftu = $all_appointment_by_marital_unavailable_lftu->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_polygamous_missed = $all_appointment_by_marital_polygamous_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_polygamous_defaulted = $all_appointment_by_marital_polygamous_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_polygamous_lftu = $all_appointment_by_marital_polygamous_lftu->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_notapplicable_missed = $all_appointment_by_marital_notapplicable_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_notapplicable_defaulted = $all_appointment_by_marital_notapplicable_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $all_appointment_by_marital_notapplicable_lftu = $all_appointment_by_marital_notapplicable_lftu->where('tbl_partner_facility.mfl_code', $selected_facilites);

        }

        $data["created_appointmnent_count"]        = $created_appointmnent_count->count();
        $data["kept_appointmnent_count"]        = $kept_appointmnent_count->count();
        $data["defaulted_appointmnent_count"]        = $defaulted_appointmnent_count->count();
        $data["missed_appointmnent_count"]        = $missed_appointmnent_count->count();
        $data["ltfu_appointmnent_count"]        = $ltfu_appointmnent_count->count();
        $data["all_appointment_by_marital_single_missed"]        = $all_appointment_by_marital_single_missed->count();
        $data["all_appointment_by_marital_single_defaulted"]        = $all_appointment_by_marital_single_defaulted->count();
        $data["all_appointment_by_marital_single_ltfu"]        = $all_appointment_by_marital_single_ltfu->count();
        $data["all_appointment_by_marital_monogomous_missed"]        = $all_appointment_by_marital_monogomous_missed->count();
        $data["all_appointment_by_marital_monogomous_defaulted"]        = $all_appointment_by_marital_monogomous_defaulted->count();
        $data["all_appointment_by_marital_monogomous_lftu"]        = $all_appointment_by_marital_monogomous_lftu->count();
        $data["all_appointment_by_marital_divorced_missed"]        = $all_appointment_by_marital_divorced_missed->count();
        $data["all_appointment_by_marital_divorced_defaulted"]        = $all_appointment_by_marital_divorced_defaulted->count();
        $data["all_appointment_by_marital_divorced_lftu"]        = $all_appointment_by_marital_divorced_lftu->count();
        $data["all_appointment_by_marital_widowed_missed"]        = $all_appointment_by_marital_widowed_missed->count();
        $data["all_appointment_by_marital_widowed_defaulted"]        = $all_appointment_by_marital_widowed_defaulted->count();
        $data["all_appointment_by_marital_widowed_lftu"]        = $all_appointment_by_marital_widowed_lftu->count();
        $data["all_appointment_by_marital_cohabiting_missed"]        = $all_appointment_by_marital_cohabiting_missed->count();
        $data["all_appointment_by_marital_cohabiting_defaulted"]        = $all_appointment_by_marital_cohabiting_defaulted->count();
        $data["all_appointment_by_marital_cohabiting_lftu"]        = $all_appointment_by_marital_cohabiting_lftu->count();
        $data["all_appointment_by_marital_unavailable_missed"]        = $all_appointment_by_marital_unavailable_missed->count();
        $data["all_appointment_by_marital_unavailable_defaulted"]        = $all_appointment_by_marital_unavailable_defaulted->count();
        $data["all_appointment_by_marital_unavailable_lftu"]        = $all_appointment_by_marital_unavailable_lftu->count();
        $data["all_appointment_by_marital_polygamous_missed"]        = $all_appointment_by_marital_polygamous_missed->count();
        $data["all_appointment_by_marital_polygamous_defaulted"]        = $all_appointment_by_marital_polygamous_defaulted->count();
        $data["all_appointment_by_marital_polygamous_lftu"]        = $all_appointment_by_marital_polygamous_lftu->count();
        $data["all_appointment_by_marital_notapplicable_missed"]        = $all_appointment_by_marital_notapplicable_missed->count();
        $data["all_appointment_by_marital_notapplicable_defaulted"]        = $all_appointment_by_marital_notapplicable_defaulted->count();
        $data["all_appointment_by_marital_notapplicable_lftu"]        = $all_appointment_by_marital_notapplicable_lftu->count();

        return $data;

    }

    public function lab_investigation()
    {
        if (Auth::user()->access_level == 'Facility') {
            $all_lab_app = Lab::select('partner_name', 'county_name', 'sub_county_name', 'facility_name', 'mfl_code', 'gender', 'age_group', \DB::raw('count(age_group) as total'))
                ->groupBy('clinic_number')
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();
        }

        if (Auth::user()->access_level == 'Admin') {
            $all_lab_app = Lab::select('partner_name', 'county_name', 'sub_county_name', 'facility_name', 'mfl_code', 'gender', 'age_group', \DB::raw('count(age_group) as total'))
                ->groupBy('clinic_number')
                ->get();
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_lab_app = Lab::select('partner_name', 'county_name', 'sub_county_name', 'facility_name', 'mfl_code', 'gender', 'age_group', \DB::raw('count(age_group) as total'))
                ->groupBy('clinic_number')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
        }
        return view('appointments.lab_investigation', compact('all_lab_app'));
    }
}
