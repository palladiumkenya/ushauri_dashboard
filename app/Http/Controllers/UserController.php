<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Client;
use App\Models\AccessLevel;
use App\Models\Facility;
use App\Models\Donor;
use App\Models\Partner;
use App\Models\County;
use App\Models\Role;
use App\Models\SubCounty;
use App\Models\PartnerFacility;
use Auth;
use DB;
use Session;

class UserController extends Controller
{
    //
    public function user_info()
    {
        if (Auth::user()->access_level == 'Sub County') {
            $data  = [];

            $user_info = DB::table('tbl_users')->select(
                'tbl_sub_county.name as sub_county'
            )->join('tbl_partner_facility', 'tbl_users.subcounty_id', '=', 'tbl_partner_facility.sub_county_id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
                ->where('tbl_sub_county.id', '=', Auth::user()->subcounty_id)
                ->groupBy('tbl_sub_county.name');
            $data["user_info"] = $user_info->get();

            return $data;
        }
        if (Auth::user()->access_level == 'County') {
            $data  = [];

            $user_info = DB::table('tbl_users')->select(
                'tbl_county.name as county'
            )->join('tbl_partner_facility', 'tbl_users.county_id', '=', 'tbl_partner_facility.county_id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->where('tbl_county.id', '=', Auth::user()->county_id)
                ->groupBy('tbl_county.name');
            $data["user_info"] = $user_info->get();

            return $data;
        }
        if (Auth::user()->access_level == 'Facility') {
            $data  = [];

            $user_info = DB::table('tbl_users')->select(
                'tbl_master_facility.name as facility', 'tbl_master_facility.code'
            )->join('tbl_partner_facility', 'tbl_users.facility_id', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->where('tbl_master_facility.code', '=', Auth::user()->facility_id)
                ->groupBy('tbl_master_facility.name');
            $data["user_info"] = $user_info->get();

            return $data;
        }
        if (Auth::user()->access_level == 'Partner') {
            $data  = [];

            $user_info = DB::table('tbl_users')->select(
                'tbl_partner.name as partner'
            )->join('tbl_partner_facility', 'tbl_users.facility_id', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->where('tbl_partner.id', '=', Auth::user()->partner_id)
                ->groupBy('tbl_partner.name');
            $data["user_info"] = $user_info->get();

            return $data;
        }
    }
    public function showUsers()
    {
        if (Auth::user()->access_level == 'Partner') {
            $all_users = User::join('tbl_clinic', 'tbl_clinic.id', '=', 'tbl_users.clinic_id')
                ->join('tbl_role', 'tbl_role.id', '=', 'tbl_users.role_id')
                ->select(
                    DB::raw("CONCAT(`tbl_users`.`f_name`, ' ', `tbl_users`.`m_name`, ' ', `tbl_users`.`l_name`) as user_name"),
                    'tbl_users.f_name',
                    'tbl_users.m_name',
                    'tbl_users.l_name',
                    'tbl_users.dob',
                    'tbl_users.phone_no',
                    'tbl_users.e_mail',
                    'tbl_users.access_level',
                    'tbl_users.status',
                    'tbl_users.created_at',
                    'tbl_users.updated_at',
                    'tbl_clinic.name AS clinic_name',
                    'tbl_role.id AS role_id',
                    'tbl_users.view_client',
                    'tbl_users.rcv_app_list',
                    'tbl_users.daily_report',
                    'tbl_users.monthly_report',
                    'tbl_users.month3_report',
                    'tbl_users.month6_report',
                    'tbl_users.yearly_report',
                    'tbl_users.status',
                    'tbl_users.id as id',
                    'tbl_users.facility_id',
                    'tbl_clinic.id as clinic_id'
                )
                ->where('tbl_users.status', '=', 'Active')
                ->where('tbl_users.partner_id', '=', Auth::user()->partner_id)
                ->get();
        }
        if (Auth::user()->access_level == 'Sub County') {
            $all_users = User::join('tbl_clinic', 'tbl_clinic.id', '=', 'tbl_users.clinic_id')
                ->join('tbl_role', 'tbl_role.id', '=', 'tbl_users.role_id')
                ->join('tbl_partner_facility', 'tbl_partner_facility.sub_county_id', '=', 'tbl_users.subcounty_id')
                ->select(
                    DB::raw("CONCAT(`tbl_users`.`f_name`, ' ', `tbl_users`.`m_name`, ' ', `tbl_users`.`l_name`) as user_name"),
                    'tbl_users.f_name',
                    'tbl_users.m_name',
                    'tbl_users.l_name',
                    'tbl_users.dob',
                    'tbl_users.phone_no',
                    'tbl_users.e_mail',
                    'tbl_users.access_level',
                    'tbl_users.status',
                    'tbl_users.created_at',
                    'tbl_users.updated_at',
                    'tbl_clinic.name AS clinic_name',
                    'tbl_role.id AS role_id',
                    'tbl_users.view_client',
                    'tbl_users.rcv_app_list',
                    'tbl_users.daily_report',
                    'tbl_users.monthly_report',
                    'tbl_users.month3_report',
                    'tbl_users.month6_report',
                    'tbl_users.yearly_report',
                    'tbl_users.status',
                    'tbl_users.id as id',
                    'tbl_users.facility_id',
                    'tbl_clinic.id as clinic_id'
                )
                ->where('tbl_users.status', '=', 'Active')
                ->where('tbl_partner_facility.sub_county_id', '=', Auth::user()->subcounty_id)
                ->groupBy('user_name')
                ->get();
        }
        if (Auth::user()->access_level == 'County') {
            $all_users = User::join('tbl_clinic', 'tbl_clinic.id', '=', 'tbl_users.clinic_id')
                ->join('tbl_role', 'tbl_role.id', '=', 'tbl_users.role_id')
                ->join('tbl_partner_facility', 'tbl_partner_facility.county_id', '=', 'tbl_users.county_id')
                ->select(
                    DB::raw("CONCAT(`tbl_users`.`f_name`, ' ', `tbl_users`.`m_name`, ' ', `tbl_users`.`l_name`) as user_name"),
                    'tbl_users.f_name',
                    'tbl_users.m_name',
                    'tbl_users.l_name',
                    'tbl_users.dob',
                    'tbl_users.phone_no',
                    'tbl_users.e_mail',
                    'tbl_users.access_level',
                    'tbl_users.status',
                    'tbl_users.created_at',
                    'tbl_users.updated_at',
                    'tbl_clinic.name AS clinic_name',
                    'tbl_role.id AS role_id',
                    'tbl_users.view_client',
                    'tbl_users.rcv_app_list',
                    'tbl_users.daily_report',
                    'tbl_users.monthly_report',
                    'tbl_users.month3_report',
                    'tbl_users.month6_report',
                    'tbl_users.yearly_report',
                    'tbl_users.status',
                    'tbl_users.id as id',
                    'tbl_users.facility_id',
                    'tbl_clinic.id as clinic_id'
                )
                ->where('tbl_users.status', '=', 'Active')
                ->where('tbl_partner_facility.county_id', '=', Auth::user()->county_id)
                ->groupBy('user_name')
                ->get();
        }

        if (Auth::user()->access_level == 'Admin') {
            $all_users = User::join('tbl_clinic', 'tbl_clinic.id', '=', 'tbl_users.clinic_id')
                ->join('tbl_role', 'tbl_role.id', '=', 'tbl_users.role_id')
                ->select(
                    DB::raw("CONCAT(`tbl_users`.`f_name`, ' ', `tbl_users`.`m_name`, ' ', `tbl_users`.`l_name`) as user_name"),
                    'tbl_users.f_name',
                    'tbl_users.m_name',
                    'tbl_users.l_name',
                    'tbl_users.dob',
                    'tbl_users.phone_no',
                    'tbl_users.e_mail',
                    'tbl_users.access_level',
                    'tbl_users.status',
                    'tbl_users.created_at',
                    'tbl_users.updated_at',
                    'tbl_clinic.name AS clinic_name',
                    'tbl_role.id AS role_id',
                    'tbl_users.view_client',
                    'tbl_users.rcv_app_list',
                    'tbl_users.daily_report',
                    'tbl_users.monthly_report',
                    'tbl_users.month3_report',
                    'tbl_users.month6_report',
                    'tbl_users.yearly_report',
                    'tbl_users.status',
                    'tbl_users.id as id',
                    'tbl_users.facility_id',
                    'tbl_clinic.id as clinic_id',
                    'tbl_users.partner_id',
                    'tbl_users.county_id',
                    'tbl_users.donor_id'
                )
                ->where('tbl_users.status', '=', 'Active')
                ->get();
        }
        $access_level = AccessLevel::all()->where('status', '=', 'Active');
        $donors = Donor::all();
        $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->select('tbl_partner_facility.id', 'tbl_master_facility.name', 'tbl_partner_facility.mfl_code as code')
            ->orderBy('tbl_master_facility.name', 'asc')
            ->get();
        $counties = County::all();
        $clinics = Clinic::all();
        if (Auth::user()->access_level == 'Partner') {
            $roles = Role::all()->where('status', '=', 'Active')
                ->where('access_level', '=', 'Facility');
            $partners = Partner::all()->where('status', '=', 'Active')->where('id',  Auth::user()->partner_id);
        }
        if (Auth::user()->access_level == 'Sub County') {
            $roles = Role::all()->where('status', '=', 'Active')
                ->where('access_level', '=', 'Facility');
            $partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.sub_county_id',  Auth::user()->subcounty_id)->get();
        }
        if (Auth::user()->access_level == 'County') {
            $roles = Role::all()->where('status', '=', 'Active')
                ->where('access_level', '=', 'Facility');
            $partners = Partner::all()->where('status', '=', 'Active')->where('id',  Auth::user()->partner_id);
        }
        if (Auth::user()->access_level == 'Admin') {
            $roles = Role::all()->where('status', '=', 'Active');
            $partners = Partner::all()->where('status', '=', 'Active');
        }
        $sub_counties = SubCounty::all();

        return view('users.users', compact('all_users', 'roles', 'access_level', 'partners', 'donors', 'facilities', 'counties', 'clinics'));
    }

    public function adduserform(Request $request)
    {
        $partners = Partner::all();
        $donors = Donor::all();
        $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->select('tbl_partner_facility.id', 'tbl_master_facility.name', 'tbl_partner_facility.mfl_code as code')
            ->orderBy('tbl_master_facility.name', 'asc')
            // ->where('tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->get();
        if (Auth::user()->access_level == 'Partner') {
            $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->select('tbl_partner_facility.id', 'tbl_master_facility.name', 'tbl_partner_facility.mfl_code as code')
                ->orderBy('tbl_master_facility.name', 'asc')
                ->where('tbl_partner_facility.partner_id', '=', Auth::user()->partner_id)
                ->get();
        }
        $counties = County::all();
        $clinics = Clinic::all();
        $roles = Role::all()->where('status', '=', 'Active');
        $sub_counties = SubCounty::all();
        $access_level = AccessLevel::all()->where('status', '=', 'Active');
        if (Auth::user()->access_level == 'Partner') {
            $partners = Partner::all()->where('status', '=', 'Active')->where('id',  Auth::user()->partner_id);
            $access_level = AccessLevel::all()->where('status', '=', 'Active')
                // ->where('name', '=', 'Partner')
                ->where('name', '=', 'Facility');
        }

        $clients = Client::select('tbl_client.clinic_number', 'tbl_clinic.name')
            ->join('tbl_clinic', 'tbl_client.clinic_id', '=', 'tbl_clinic.id')
            ->get();




        $data = array(
            'facilities' => $facilities,
            'counties' => $counties,
            'donors' => $donors,
            'partners' => $partners,
            'sub_counties' => $sub_counties,
            'access_level' => $access_level,
            'roles' => $roles,
            'clinics' => $clinics,
        );

        // dd($facilities);

        return view('users.adduser')->with($data);
    }

    public function access_level_load(Request $request)
    {
        $select = $request->get('select');
        $value = $request->get('value');
        $dependant = $request->get('value');

        $level = Role::where($select, $value)
            ->groupBy($dependant) . '</option>'
            ->get();

        $ouput = '<option value="">Select ' . ucfirst($dependant) . '</option>';
        foreach ($level as $row) {
            $ouput .= '<option value="' . $row->dependant . '">' . $row->dependant . '</option>';
        }
        echo $ouput;
    }
    public function get_roles($id)
    {
        $roles = Role::join('tbl_access_level', 'tbl_role.access_level', '=', 'tbl_access_level.id')
            ->where("tbl_role.access_level", $id)
            ->where('tbl_role.status', '=', 'Active')
            ->pluck("tbl_role.name", "tbl_role.id");
        return json_encode($roles);
    }
    public function get_counties($id)
    {
        $counties = PartnerFacility::join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
            ->where("tbl_partner_facility.partner_id", $id)
            ->pluck("tbl_county.name", "tbl_county.id");
        return json_encode($counties);
    }

    public function get_sub_counties($id)
    {
        $subcounties = SubCounty::join('tbl_county', 'tbl_sub_county.county_id', '=', 'tbl_county.id')
            ->where("tbl_sub_county.county_id", $id)
            ->pluck("tbl_sub_county.name", "tbl_sub_county.id");
        return json_encode($subcounties);
    }

    public function adduser(Request $request)
    {
        try {
            $user = new User;

            $validate = User::where('phone_no', $request->phone)
                ->first();
            $validate_email = User::where('e_mail', $request->email)
                ->orwhere('email', $request->email)
                ->first();

            if ($validate) {
                Session::flash('statuscode', 'error');

                return redirect('admin/users/form')->with('status', 'Phone Number is already used in the system!');
            }
            if ($validate_email) {
                Session::flash('statuscode', 'error');

                return redirect('admin/users/form')->with('status', 'Email is already used in the system!');
            }

            $user->f_name = $request->fname;
            $user->m_name = $request->mname;
            $user->l_name = $request->lname;
            $user->email = $request->email;
            $user->e_mail = $request->email;
            $user->phone_no = $request->phone;
            $user->access_level = $request->add_access_level;

            $add_partner = PartnerFacility::select('partner_id')->where('mfl_code', $request->facilityname)->get();

            if ($request->add_access_level == 'Admin') {
                $user->role_id = $request->rolename;
            }
            if ($request->add_access_level == 'Donor') {
                $user->role_id = $request->rolename;
                $user->donor_id = $request->donor;
            }
            if ($request->add_access_level == 'County') {
                $user->role_id = $request->rolename;
                $user->county_id = $request->county;
            }
            if ($request->add_access_level == 'Partner') {
                $user->role_id = $request->rolename;
                $user->partner_id = $request->partner;
            }
            if ($request->add_access_level == 'Sub County') {
                $user->role_id = $request->rolename;
                $user->subcounty_id = $request->sub_county;
            }
            if ($request->add_access_level == 'Facility') {
                $user->role_id = $request->rolename;
                $user->facility_id = $request->facilityname;
                $user->clinic_id = $request->clinicname;
                $user->partner_id = $request->partner;
            }
            $user->view_client = $request->bio_data;
            $user->rcv_app_list = $request->app_receive;
            $user->daily_report = $request->daily_report;
            $user->weekly_report = $request->weekly_report;
            $user->monthly_report = $request->monthly_report;
            $user->month3_report = $request->month3_report;
            $user->month6_report = $request->month6_report;
            $user->yearly_report = $request->yearly_report;
            $user->password = bcrypt($request->phone);
            $user->first_access = "Yes";
            $user->status = $request->status;
            $user->created_by = Auth::user()->id;


            if ($user->save()) {
                Session::flash('statuscode', 'success');

                return redirect('admin/users')->with('status', 'User has been saved successfully!');
            } else {
                Session::flash('statuscode', 'error');
                return back()->with('error', 'An error has occurred please try again later.');
            }
        } catch (Exception $e) {
            toastr()->error('An error has occurred please try again later.');

            return back();
        }
    }

    public function edituser(Request $request)
    {
        try {
            $user = User::where('id', $request->id)
                ->update([
                    'f_name' => $request->fname,
                    'm_name' => $request->mname,
                    'l_name' => $request->lname,
                    'e_mail' => $request->email,
                    'email' => $request->email,
                    'phone_no' => $request->phone,
                    'access_level' => $request->add_access_level,
                    'role_id' => $request->rolename,
                    'donor_id' => $request->donor,
                    'county_id' => $request->county,
                    'partner_id' => $request->partner,
                    'subcounty_id' => $request->sub_county,
                    'facility_id' => $request->facilityname,
                    'clinic_id' => $request->clinicname,
                    'view_client' => $request->bio_data,
                    'rcv_app_list' => $request->app_receive,
                    'daily_report' => $request->daily_report,
                    'weekly_report' => $request->weekly_report,
                    'monthly_report' => $request->monthly_report,
                    'month3_report' => $request->month3_report,
                    'month6_report' => $request->month6_report,
                    'yearly_report' => $request->yearly_report,
                    'updated_by' => Auth::user()->id,
                    'status' => $request->status,
                ]);
            if ($user) {
                Session::flash('statuscode', 'success');
                return redirect('admin/users')->with('status', 'User was successfully Updated in the system!');
            } else {
                Session::flash('statuscode', 'error');
                return back()->with('error', 'Could not update user please try again later.');
            }
        } catch (Exception $e) {
            return back();
        }
    }

    public function resetuser(Request $request)
    {
        try {
            $user = User::find($request->id);
            $user->password = bcrypt($user->phone_no);
            $user->first_access = 'Yes';
            $user->updated_at = date('Y-m-d H:i:s');
            $user->updated_by = Auth::user()->id;

            if ($user->save()) {
                Session::flash('statuscode', 'success');
                return redirect('admin/users')->with('status', 'User has been reset successfull');
            } else {
                Session::flash('statuscode', 'error');
                return redirect('admin/users')->with('status', 'An error has occurred please try again later');
            }
        } catch (Exception $e) {
            Session::flash('statuscode', 'error');
            return redirect('admin/users')->with('status', 'An error has occurred please try again later');
        }
    }

    public function resetshow()
    {
        return view('users.passreset');
    }
    public function reset()
    {
        return view('users.reset');
    }

    public function changepass(Request $request)
    {
        try {
            $user = User::find($request->id);

            $user->password = bcrypt($request->new_password);
            $user->first_access = 'No';

            if ($user->save()) {
                Session::flash('statuscode', 'success');
                return redirect('/')->with('status', 'Password has been changed successfully!');
            } else {

                Session::flash('statuscode', 'error');
                return back()->with('error', 'An error has occurred please try again later.');
            }
        } catch (Exception $e) {

            return back();
        }
    }
}
