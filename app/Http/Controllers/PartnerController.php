<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\PartnerType;
use Session;
use Exception;
use Auth;

class PartnerController extends Controller
{
    public function index()
    {

        $all_partners = Partner::join('tbl_partner_type', 'tbl_partner.partner_type_id', '=', 'tbl_partner_type.id')
            ->select('tbl_partner.id', 'tbl_partner.name as partner_name', 'tbl_partner.phone_no', 'tbl_partner.description', 'tbl_partner.e_mail', 'tbl_partner.location', 'tbl_partner.status', 'tbl_partner.created_at', 'tbl_partner.updated_at', 'tbl_partner_type.name as partner_type')
            ->where('tbl_partner.status', '=', 'Active')
            ->get();
        $partner_type = PartnerType::all();

        if (Auth::user()->access_level == 'Sub County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')
            ->join('tbl_partner_type', 'tbl_partner.partner_type_id', '=', 'tbl_partner_type.id')
            ->select('tbl_partner.id', 'tbl_partner.name as partner_name', 'tbl_partner.phone_no', 'tbl_partner.description', 'tbl_partner.e_mail', 'tbl_partner.location', 'tbl_partner.status', 'tbl_partner.created_at', 'tbl_partner.updated_at', 'tbl_partner_type.name as partner_type')
            ->where('tbl_partner.status', '=', 'Active')
            ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
            ->groupBy('tbl_partner.name')
            ->get();
        }
        if (Auth::user()->access_level == 'County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')
            ->join('tbl_partner_type', 'tbl_partner.partner_type_id', '=', 'tbl_partner_type.id')
            ->select('tbl_partner.id', 'tbl_partner.name as partner_name', 'tbl_partner.phone_no', 'tbl_partner.description', 'tbl_partner.e_mail', 'tbl_partner.location', 'tbl_partner.status', 'tbl_partner.created_at', 'tbl_partner.updated_at', 'tbl_partner_type.name as partner_type')
            ->where('tbl_partner.status', '=', 'Active')
            ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
            ->groupBy('tbl_partner.name')
            ->get();
        }

        return view('partners.new_partner', compact('all_partners', 'partner_type'));
    }
    public function addpartnerform()
    {
        $partner_type = PartnerType::all();
        return view('partners.addpartner', compact('partner_type'));

    }
    public function addpartner(Request $request)
    {

        try {
            $partner = new Partner;
            // $validate = Partner::where('phone_no', $request->phone)->first();

            // if ($validate) {
            //     Session::flash('statuscode', 'error');
            //     return redirect('admin/partners/form')->with('status', 'Phone Number is already used in the system!');
            // }

            $partner->name = $request->name;
            $partner->description = $request->description;
            $partner->phone_no = $request->phone;
            $partner->location = $request->location;
            $partner->e_mail = $request->email;
            $partner->partner_type_id = $request->partner_type;
            $partner->status = $request->status;

            if ($partner->save()) {
                Session::flash('statuscode', 'success');

                return redirect('admin/partners')->with('status', 'Partner has been saved successfully!');
            } else {

                Session::flash('statuscode', 'error');
                return back()->with('error', 'An error has occurred please try again later.');
            }
        } catch (Exception $e) {
            return back();
        }
    }
    public function editpartner(Request $request)
    {
        try {
            $partner = Partner::where('id', $request->id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'phone_no' => $request->phone,
                    'e_mail' => $request->email,
                    'status' => $request->status,
                    'partner_type_id' => $request->partner_type,
                    'location' => $request->location,
                ]);
            if ($partner) {
                Session::flash('statuscode', 'success');
                return redirect('admin/partners')->with('status', 'Partner was successfully Updated in the system!');
            } else {
                Session::flash('statuscode', 'error');
                return back()->with('error', 'Could not update partner please try again later.');
            }
        } catch (Exception $e) {
            return back();
        }
    }
    public function deletepartner(Request $request)
    {
        try {
            $partner = Partner::find($request->id);

            if ($partner->save()) {

                return response(['status' => 'success', 'details' => 'Partner has been deleted successfully']);
            } else {
                return response(['status' => 'error', 'details' => 'An error has occurred please try again later.']);
            }
        } catch (Exception $e) {
        }
    }
}
