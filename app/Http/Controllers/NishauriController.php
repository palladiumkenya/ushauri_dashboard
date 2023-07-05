<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Models\Reschedule;
use App\Models\Txcurr;
use App\Models\NishauriUser;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;

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

        $otp_search = NishauriUser::select('msisdn', 'otp_number')
            ->where('is_active', '=', '0')
            ->where('msisdn', 'like', '%' . $upn_search . '%')
            ->first();

        return response()->json(['otp_number' => $otp_search ? $otp_search->otp_number : null]);
    }
}
