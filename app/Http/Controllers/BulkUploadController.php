<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Excel;
use Carbon\Carbon;
use App\Models\Client;
use Auth;

class BulkUploadController extends Controller
{
    public function uploadClientForm()
    {
        return view('clients.upload-clients-form');
    }
    public function importClients(Request $request)
    {

        $file = request()->file('file');

        if (!empty($file)) {
            $receivedArr = $this->csvToArray($file);
            for ($i = 0; $i < count($receivedArr); $i++) {
                $gender_value = trim(strtolower($receivedArr[$i]['Gender']));

                if ($gender_value == 'm') {
                    $gender = 2;
                } elseif ($gender_value == 'f') {
                    $gender = 1;
                } else {
                    $gender = 5;
                }

                $marital_value = trim(strtolower($receivedArr[$i]['Marital_Status']));

                if ($marital_value == 'Divorced') {
                    $marital = 3;
                } elseif ($marital_value == 'Living with partner') {
                    $marital = 5;
                } elseif ($marital_value == 'Married') {
                    $marital = 2;
                } elseif ($marital_value == 'Never married') {
                    $marital = 1;
                } elseif ($marital_value == 'Polygamous') {
                    $marital = 8;
                } elseif ($marital_value == 'Widowed') {
                    $marital = 4;
                } else {
                    $marital = 6;
                }

                $dob_value = trim(strtolower($receivedArr[$i]['DOB']));

                $dob = Carbon::Parse($dob_value)->format('Y-m-d');

                $art_value = trim(strtolower($receivedArr[$i]['ARTStartDate']));

                $art_start_date = Carbon::Parse($art_value)->format('Y-m-d');

                $enrollment_value = trim(strtolower($receivedArr[$i]['enroll_date']));

                $enrollment_date = Carbon::Parse($enrollment_value)->format('Y-m-d');


                $age_value  = (float)$receivedArr[$i]['ageInYears'];
                if ($age_value >= 20) {
                    $group_id = 1;
                } elseif ($age_value >= 13) {
                    $group_id = 2;
                } else {
                    $group_id = 4;
                }

                $first_name = trim($receivedArr[$i]['FirstName']);
                $middle_name = trim($receivedArr[$i]['MiddleName']);
                $last_name = trim($receivedArr[$i]['LastName']);
                $clinic_number = trim($receivedArr[$i]['CCC_Number']);
                $phone_number = trim($receivedArr[$i]['Phone_Number']);
                $facility_id = trim($receivedArr[$i]['MFL']);
                $mfl_code = trim($receivedArr[$i]['MFL']);
                if (Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Facility') {
                    $partner_id = Auth::user()->partner_id;
                }else{
                    $partner_id = trim($receivedArr[$i]['PartnerID']);
                }
                $status = "Active";
                $client_status = "ART";
                $clinic_id = 1;
                $text_frequency = 168;
                $text_time = 7;
                $wellness = "No";
                $motivational = "No";
                $smsenable = "No";
                $language = 2;


                $client = new Client;

                $client->f_name = $first_name;
                $client->m_name = $middle_name;
                $client->l_name = $last_name;
                $client->clinic_number = $clinic_number;
                $client->phone_no = $phone_number;
                $client->group_id = $group_id;
                $client->language_id = $language;
                $client->facility_id = $facility_id;
                $client->mfl_code = $mfl_code;
                $client->gender = $gender;
                $client->marital = $marital;
                $client->dob = $dob;
                $client->art_date = $art_start_date;
                $client->enrollment_date = $enrollment_date;
                $client->partner_id = $partner_id;
                $client->status = $status;
                $client->client_status = $client_status;
                $client->clinic_id = $clinic_id;
                $client->txt_frequency = $text_frequency;
                $client->txt_time = $text_time;
                $client->wellness_enable = $wellness;
                $client->motivational_enable = $motivational;
                $client->smsenable = $smsenable;
                $client->created_by =  Auth::user()->id;
                $client->updated_by = Auth::user()->id;

                $existing  = Client::where('clinic_number', $clinic_number)->first();

                // function RemoveSpecialChar($clinic_number)
                // {
                //    $res = preg_replace('/[@\-\;\" "]+/', '', $clinic_number);
                //    return $res;
                // }

                if ($existing) {
                    echo ('Client' . $clinic_number  . ' already exists in the system <br>');
                } elseif (strlen($clinic_number) < 10 || strlen($clinic_number) > 10) {
                    echo ('Client' . $clinic_number  . ' has less or more than 10 digit ccc number <br>');
                } else {

                    if ($client->save()) {
                        echo ('Insert Client Record successfully for client.' . $clinic_number . '<br>');
                    } else {
                        echo ('Could not insert record for client.' . $clinic_number . '<br>');
                    }
                }
            }
        }
        echo  "Done";
    }

    public function importSecondClients(Request $request)
    {

        $file = request()->file('file');

        if (!empty($file)) {
            $receivedArr = $this->csvToArray($file);
            for ($i = 0; $i < count($receivedArr); $i++) {

                $dob_value = trim(strtolower($receivedArr[$i]['dob']));

                $dob = Carbon::Parse($dob_value)->format('Y-m-d');

                $art_value = trim(strtolower($receivedArr[$i]['art_date']));

                $art_start_date = Carbon::Parse($art_value)->format('Y-m-d');

                $enrollment_value = trim(strtolower($receivedArr[$i]['enrollment_date']));

                $enrollment_date = Carbon::Parse($enrollment_value)->format('Y-m-d');

                $first_name = trim($receivedArr[$i]['f_name']);
                $middle_name = trim($receivedArr[$i]['m_name']);
                $last_name = trim($receivedArr[$i]['l_name']);
                $clinic_number = trim($receivedArr[$i]['clinic_number']);
                $phone_number = trim($receivedArr[$i]['phone_no']);
                $facility_id = trim($receivedArr[$i]['mfl_code']);
                $mfl_code = trim($receivedArr[$i]['mfl_code']);
                $partner_id = trim($receivedArr[$i]['partner_id']);
                $group_id = trim($receivedArr[$i]['group_id']);
                $gender = trim($receivedArr[$i]['gender']);
                $marital = trim($receivedArr[$i]['marital']);
                $status = "Active";
                $client_status = trim($receivedArr[$i]['client_status']);
                $clinic_id = trim($receivedArr[$i]['clinic_id']);
                $text_frequency = 168;
                $text_time = 19;
                $wellness = "No";
                $motivational = "No";
                $smsenable = trim($receivedArr[$i]['smsenable']);
                $language = trim($receivedArr[$i]['language_id']);


                $client = new Client;

                $client->f_name = $first_name;
                $client->m_name = $middle_name;
                $client->l_name = $last_name;
                $client->clinic_number = $clinic_number;
                $client->phone_no = $phone_number;
                $client->group_id = $group_id;
                $client->language_id = $language;
                $client->facility_id = $facility_id;
                $client->mfl_code = $mfl_code;
                $client->gender = $gender;
                $client->marital = $marital;
                $client->dob = $dob;
                $client->art_date = $art_start_date;
                $client->enrollment_date = $enrollment_date;
                $client->partner_id = $partner_id;
                $client->status = $status;
                $client->client_status = $client_status;
                $client->clinic_id = $clinic_id;
                $client->txt_frequency = $text_frequency;
                $client->txt_time = $text_time;
                $client->wellness_enable = $wellness;
                $client->motivational_enable = $motivational;
                $client->smsenable = $smsenable;
                $client->created_by =  Auth::user()->id;
                $client->updated_by = Auth::user()->id;

                $existing  = Client::where('clinic_number', $clinic_number)->first();

                // function RemoveSpecialChar($clinic_number)
                // {
                //    $res = preg_replace('/[@\-\;\" "]+/', '', $clinic_number);
                //    return $res;
                // }

                if ($existing) {
                    echo ('Client' . $clinic_number  . ' already exists in the system <br>');
                } elseif (strlen($clinic_number) < 10 || strlen($clinic_number) > 10) {
                    echo ('Client' . $clinic_number  . ' has less or more than 10 digit ccc number <br>');
                } else {

                    if ($client->save()) {
                        echo ('Insert Client Record successfully for client.' . $clinic_number . '<br>');
                    } else {
                        echo ('Could not insert record for client.' . $clinic_number . '<br>');
                    }
                }
            }
        }
        echo  "Done";
    }

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = [];
        $data = array();

        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 13000, $delimiter)) !== false) {
                if (!$header) {
                    // print_r("header not empty");
                    $header = $row;
                } else {
                    // print_r("header empty");
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    public function downloadClientTemplate()
    {
        $path = public_path('template/UshauriTemplate.xlsx');
        return response()->download($path);
    }
    public function downloadClientScript()
    {
        $path = public_path('template/UshauriExtract.sql');
        return response()->download($path);
    }
}
