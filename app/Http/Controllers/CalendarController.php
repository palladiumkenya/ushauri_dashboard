<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Models\AppointmentType;
use Calendar;
use Auth;
use DB;

class CalendarController extends Controller
{
    private $url_path = "current-appointments";

    public function index()
    {
        return view('appointments.appointment_calender');
    }
    public function app_calendar()
    {

        if (Auth::user()->access_level == 'Facility') {
            $name = 'Total:';
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id_all/', tbl_appointment.id) as url"), DB::raw("CONCAT('Total: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            return response()->json($app_calendar_data);
        }
    }
    public function refill_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {

            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_clinic', 'tbl_clinic.id', '=', 'tbl_client.clinic_id')
                ->select('tbl_client.clinic_number', 'tbl_clinic.name as clinic', 'tbl_client.file_no', 'tbl_appointment.app_status', 'tbl_client.phone_no', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Refill: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'Re-fill')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function refill_apps()
    {
        if (Auth::user()->access_level == 'Facility') {

            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_clinic', 'tbl_clinic.id', '=', 'tbl_client.clinic_id')
                ->select('tbl_client.clinic_number', 'tbl_clinic.name as clinic', 'tbl_client.file_no', 'tbl_appointment.app_status', 'tbl_client.phone_no', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Refill: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'Re-fill')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return view('calendar.refill', compact('app_calendar_data'));
        }
    }

    public function clinical_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Clinical Review: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'Clinical Review')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function adherence_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('Adherence: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'Enhanced Adherence')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function lab_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Lab Investigation: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'Lab Investigation')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function viral_load()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Viral Load: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'Viral Load')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function other_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Other: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'Other')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function pcr_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('PCR: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'PCR')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function normal_calender()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Normal: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'Normal')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function vl_cd_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('VL/CD4: ', ' ', COUNT(tbl_appointment_types.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment_types.name', '=', 'VL/CD4')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function honored_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.phone_no', 'tbl_appointment.app_status', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Honored Apps: ', ' ', COUNT(tbl_appointment.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function not_honored_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_appointment.app_status', 'tbl_client.phone_no', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Not Honored: ', ' ', COUNT(tbl_appointment.id)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->groupBy('tbl_appointment.appntmnt_date')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                // ->where('tbl_appointment.date_attended', '!=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function unscheduled_calendar()
    {
        if (Auth::user()->access_level == 'Facility') {
            $app_calendar_data = Appointments::join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_appointment.app_status', 'tbl_client.phone_no', 'tbl_appointment_types.name as app_type', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_appointment.appntmnt_date as start', DB::raw("CONCAT('$this->url_path','/id/', tbl_appointment.id) as url"), DB::raw("CONCAT('Un-Scheduled: ', ' ', COUNT(tbl_appointment.visit_type)) as title"), 'tbl_appointment.appntmnt_date as end')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->groupBy('tbl_appointment.appntmnt_date')
                ->get();
            return response()->json($app_calendar_data);
        }
    }

    public function current_appointments($slug, $id)
    {
        //get the appointment
        $appointment = DB::table('tbl_appointment')
            ->where('id', $id)
            ->get()
            ->take(1);

        foreach ($appointment as $row) {
            $app_date = $row->appntmnt_date;
            $app_type = $row->app_type_1;
            $unscheduled_app = $row->visit_type;
            $honored_app = $row->date_attended;
            $not_honored_app = $row->app_status;
        }

        $query = DB::table('tbl_client')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
            ->join('tbl_clinic', 'tbl_client.clinic_id', '=', 'tbl_clinic.id')
            ->leftJoin('tbl_clnt_outgoing', 'tbl_appointment.id', '=', 'tbl_clnt_outgoing.appointment_id')
            ->select('tbl_client.file_no', 'tbl_client.smsenable', 'tbl_appointment.id as appointment_id', 'tbl_appointment.date_attended', 'tbl_appointment.visit_type', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.phone_no', 'tbl_client.status', 'tbl_client.clinic_number', 'tbl_client.id as client_id', 'tbl_appointment.app_status', 'tbl_appointment.appntmnt_date', 'tbl_appointment.app_type_1', 'tbl_appointment_types.id as appointment_types_id', 'tbl_appointment_types.name as appointment_types', 'tbl_clinic.name as clinic',
            'tbl_clnt_outgoing.status', 'tbl_clnt_outgoing.callback_status', 'tbl_clnt_outgoing.failure_reason')
            ->where('tbl_client.status', 'Active')
            ->where('tbl_client.mfl_code', Auth::user()->facility_id)
            ->where('tbl_appointment.appntmnt_date', $app_date);

        if ($slug == 'id') {
            $query->where('tbl_appointment.app_type_1', $app_type);
            $query->where('tbl_appointment.visit_type', $unscheduled_app);
            $query->where('tbl_appointment.date_attended', $honored_app);
            $query->where('tbl_appointment.app_status', $not_honored_app);
         }


        $result = $query->get();

        return view('reports.cal_appointments', ['result' => $result]);
    }
}
