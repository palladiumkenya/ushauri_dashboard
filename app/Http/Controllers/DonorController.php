<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donor;
use Auth;
use PhpParser\Node\Stmt\TryCatch;
use Session;
use RealRashid\SweetAlert\Facades\Alert;

class DonorController extends Controller
{
    public function index()
    {
        $all_donor = Donor::all()->where('status', '=', 'Active');

        return view('donor.donors')->with('all_donor', $all_donor);
    }

    public function adddonorform()
    {
        return view('donor.adddonor');
    }

    public function adddonor(Request $request)
    {
        try {
            $donor = new Donor;
            $validate = Donor::where('phone_no', $request->phone)
                ->first();

            if ($validate) {
                Alert::error('Failed', 'Phone Number is already used in the system!');

                return redirect('admin/donors/form');
            }

            $donor->name = $request->name;
            $donor->description = $request->description;
            $donor->phone_no = $request->phone;
            $donor->e_mail = $request->email;
            $donor->status = $request->status;


            // $donor->created_by = Auth::;

            if ($donor->save()) {
                Alert::success('Success', 'Donor has been saved successfully!');
                return redirect('admin/donors');
            } else {

                Alert::error('Failed', 'An error has occurred please try again later.');
                return back();
            }
        } catch (Exception $e) {
            Alert::error('Failed', 'An error has occurred please try again later.');
            return back();
        }
    }
    public function editdonor(Request $request)
    {
        try {
            $donor = Donor::where('id', $request->id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'phone_no' => $request->phone,
                    'e_mail' => $request->email,
                    'status' => $request->status,
                ]);
            if ($donor) {
                Alert::success('Success', 'Donor was successfully Updated in the system!');
                return redirect('admin/donors');
            } else {
                Alert::error('Failed', 'An error has occurred please try again later.');
                return back();
            }
        } catch (Exception $e) {
            return back();
        }
    }

    public function deletedonor(Request $request)
    {
        try {
            $donor = Donor::find($request->id);
            // $donor->update_at = date('Y-m-d H:i:s');
            if ($donor->save()) {
                Alert::success('Success', 'Donor has been deleted successfully');
                return redirect('admin/donors');
            } else {
                Alert::error('Failed', 'An error has occurred please try again later.');
                return back();
            }
        } catch (Exception $e) {
        }
    }
}
