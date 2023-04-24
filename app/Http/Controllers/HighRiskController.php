<?php

namespace App\Http\Controllers;

date_default_timezone_set('Africa/Nairobi');

use Illuminate\Http\Request;
use App\Models\HighRisk;
use GuzzleHttp\Client;
use App\Models\PartnerFacility;
use App\Models\Content;
use App\Models\ClientOutgoing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HighRiskController extends Controller
{
    protected  $current_date;
    protected $httpresponse;

    public function __construct()
    {
        set_time_limit(0);
        $this->current_date = date("Y-m-d");
    }
    public function get_high_risk_clients()
    {
        // Set the endpoint URL
        $url = 'https://data.kenyahmis.org:9783/api/Dataset';

        // Set the query parameters
        $params = [
            'code' => 'FND',
            'name' => 'predictions',
            'pageNumber' => 1,
            'pageSize' => 50,
            'siteCode' => '13738',
        ];

        // Set the authorization header
        $token = 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjZDMjBBQTk4MEMyRUNEQjNCQkVCMjUzNzZCNjVCRURDRDkxNDMwODgiLCJ0eXAiOiJhdCtqd3QiLCJ4NXQiOiJiQ0NxbUF3dXpiTzc2eVUzYTJXLTNOa1VNSWcifQ.eyJuYmYiOjE2ODAyMDA0MTgsImV4cCI6MTY4MDIwNDAxOCwiaXNzIjoiaHR0cHM6Ly9hdXRoMi5rZW55YWhtaXMub3JnOjg0NDMiLCJhdWQiOiJwZGFwaXYxIiwiY2xpZW50X2lkIjoiY2RjIiwic2NvcGUiOlsicGRhcGl2MSJdfQ.HMUtm4eXVuHhfHVMcjokIId-nmzhEkcLtsUCFgmw9fUoNqXlEixvt7Wx-n52s11OyB3tEOJKmXcKByHnWGE4if0UW_C4ouHKV5NVw4rDl5OstYT27BkWYsPIUULxt0NhDEQjzwHi-KFNVjXf3PUf5pOn-qIYWTt5bEoKc-IVE_gcSpnLHavT0u-FbBMQwxCtZD9i5ZfQMMaVz3o2oa4qfGSgH3BzXb9mZjifvFmSl23IU7pHCEoNbozJCc9kDU9SFuy9QqputrRBwdTOACytawYQ83qd4nvZZUM-O4SYwOVwKBXLZYbM3xG9GT6A3erUvM8HjLn71_WD2es47eaiBg';
        $headers = [
            'Authorization: Bearer ' . $token,
        ];

        // Initialize the cURL session
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Send the request and get the response
        $response = curl_exec($ch);
        //   dd($ch);
        // Close the cURL session
        curl_close($ch);


        // Decode the response JSON
        $data = json_decode($response, true);
        // Loop over the records and insert them into the database
        foreach ($data['extract'] as $record) {
            $res = new HighRisk;
            $res->ccc_number = $record['PatientCccNumber'];
            $res->mfl_code = $record['code'];
            $res->risk_score = $record['risk_score'];
            $res->evaluation_date = $record['EvaluationDate'];
            $res->risk_description = $record['Description'];
            $res->save();
        }
    }

    public function getAllData()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://auth2.kenyahmis.org:8443/connect/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => 'cdc',
                'client_secret' => '7f11e3b4-5741-11ec-bf63-0242ac130002',
                'scope' => 'pdapiv1'
            ]
        ]);

        if ($response->getStatusCode() == 401) {
            $response = $client->post('https://auth2.kenyahmis.org:8443/connect/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'cdc',
                    'client_secret' => '7f11e3b4-5741-11ec-bf63-0242ac130002',
                    'scope' => 'pdapiv1'
                ]
            ]);
        }
        // Get the new access token from the response
        $access_token = json_decode($response->getBody())->access_token;

        $facilities = PartnerFacility::select('mfl_code')->get();

        $get_facilities = array();

        foreach ($facilities as $facility) {
            $code = $facility->mfl_code;

            array_push($get_facilities, $code);
        }
        $results = array_unique($get_facilities);
        $final_facilities =  implode(',', $get_facilities);

        // Define the API endpoint URL
        $url = 'https://data.kenyahmis.org:9783/api/Dataset';

        foreach ($get_facilities as $final_facility) {
            // Define the parameters to send to the API
            $params = [
                'code' => 'FND',
                'name' => 'predictions',
                'siteCode' => $final_facility,

            ];


            // Define the page number and page size
            $pageNumber = 1;
            $pageSize = 50;

            do {
                // Add the page number and page size to the parameters
                $params['pageNumber'] = $pageNumber;
                $params['pageSize'] = $pageSize;

                // Build the query string
                $queryString = http_build_query($params);

                $fullUrl = $url . '?' . $queryString;

                // Make the curl request
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $fullUrl);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $access_token,
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                // Decode the JSON response
                $data = json_decode($response, true);

                // Loop through the extract and insert
                foreach ($data['extract'] as $record) {
                    $respon = HighRisk::where('ccc_number', $record['PatientCccNumber'])->first();

                    if ($respon) {
                        $respon->update([
                            'mfl_code' => $record['code'],
                            'risk_score' => $record['risk_score'],
                            'evaluation_date' => $record['EvaluationDate'],
                            'risk_description' => $record['Description'],

                          ]);

                    } elseif ($record['Description'] == 'High Risk') {
                        $res = new HighRisk;
                        $res->ccc_number = $record['PatientCccNumber'];
                        $res->mfl_code = $record['code'];
                        $res->risk_score = $record['risk_score'];
                        $res->evaluation_date = $record['EvaluationDate'];
                        $res->risk_description = $record['Description'];
                        $res->save();
                    }
                }

                // Increment the page number
                $pageNumber++;

                // Continue looping while there are more pages of data
            } while ($data['pageNumber'] < $data['pageCount']);
        }
    }


    private function send_message($source, $destination, $msg)
    {
        $key = env('SMS_SERVICE_KEY', '');
        $host = env('SMS_SERVICE_HOST', '');

        $this->httpresponse = Http::withoutVerifying()
            ->withHeaders(['api-token' => "$key"])
            ->post("$host", [
                'destination' => $destination,
                'msg' => $msg,
                'sender_id' => $destination,
                'gateway' => $source,

            ]);

        return json_decode($this->httpresponse->getBody(), true);
    }

    public function task()
    {
        $client = HighRisk::join('tbl_client', 'tbl_high_risk.ccc_number', '=', 'tbl_client.clinic_number')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->select('tbl_client.f_name as client_name', 'tbl_client.id as client_id',  'tbl_appointment.id as appointment_id', 'tbl_high_risk.risk_description', 'tbl_client.language_id', 'tbl_client.phone_no', 'tbl_client.txt_time', 'tbl_client.smsenable', 'tbl_appointment.appntmnt_date', 'tbl_appointment.consented')
            ->where('tbl_high_risk.risk_description', '=', 'High Risk')
            ->groupBy('tbl_appointment.client_id')
            ->get();


        foreach ($client as $value) {
            $client_name = $value->client_name;
            $risk_description = $value->risk_description;
            $language_client = $value->language_id;
            $phone_no = $value->phone_no;
            $txt_time = $value->txt_time;
            $smsenable = $value->smsenable;
            $appntmnt_date = $value->appntmnt_date;
            $consented = $value->consented;
            $appointment_id = $value->appointment_id;
            $client_id = $value->client_id;

            $TwoWeeksBefore = Carbon::parse($appntmnt_date)->subWeeks(2)->format('Y-m-d');
            $OneMonthBefore = Carbon::parse($appntmnt_date)->subMonth()->format('Y-m-d');
            $ThreeWeeksBefore = Carbon::parse($appntmnt_date)->subWeeks(3)->format('Y-m-d');
            $message = Content::join('tbl_notification_flow', 'tbl_notification_flow.id', '=', 'tbl_content.identifier')->select('tbl_content.content', 'tbl_content.message_type_id', 'tbl_content.language_id', 'tbl_content.identifier', 'tbl_notification_flow.days')->where('tbl_content.message_type_id', 9)->where('tbl_content.language_id', $language_client)->get();

            //check if a notification is already sent. If not send it.
            $client_exist = DB::table('tbl_clnt_outgoing')
                ->where('message_type_id', 9)
                ->where('clnt_usr_id', $client_id)
                ->whereDate('created_at',  $this->current_date)
                ->orwhereDate('updated_at',  $this->current_date)
                ->get();

            if ($client_exist) {
                echo 'Message already sent to client';
            } else {


                if ($TwoWeeksBefore) {
                    foreach ($message as $sms) {

                        if ($sms->days == 14) {

                            $content = $sms->content;
                            $content_id = $sms->id;

                            $today = date("Y-m-d H:i:s");
                            $new_msg = str_replace("XXX", $client_name, $content);
                            //$appointment_date = date("d-m-Y", strtotime($appntmnt_date));
                            // $cleaned_msg = str_replace("YYY", $appointment_date, $new_msg);

                            $status = "Not Sent";
                            $responded = "No";

                            if ($smsenable == 'Yes' || $consented == 'YES' && trim($new_msg) != '') {
                                $source = 40149;
                                $outgoing = array(
                                    'destination' => $phone_no,
                                    'msg' => $new_msg,
                                    'responded' => $responded,
                                    'status' => $status,
                                    'message_type_id' => 9,
                                    'source' => $source,
                                    'clnt_usr_id' => $client_id,
                                    'appointment_id' => $appointment_id,
                                    'no_of_days' => 14,
                                    'recepient_type' => 'Client',
                                    'content_id' => $content_id,
                                    'created_at' => $today,
                                    'created_by' => '1'
                                );
                                $this->sms_outgoing_insert($outgoing);
                            }
                        }
                    }
                } elseif ($OneMonthBefore) {
                    foreach ($message as $sms) {
                        if ($sms->days == 30) {
                            $content = $sms->content;
                            $content_id = $sms->id;

                            $today = date("Y-m-d H:i:s");
                            $new_msg = str_replace("XXX", $client_name, $content);

                            $status = "Not Sent";
                            $responded = "No";

                            if ($smsenable == 'Yes' || $consented == 'YES' && trim($new_msg) != '') {
                                $source = 40149;
                                $outgoing = array(
                                    'destination' => $phone_no,
                                    'msg' => $new_msg,
                                    'responded' => $responded,
                                    'status' => $status,
                                    'message_type_id' => 9,
                                    'source' => $source,
                                    'clnt_usr_id' => $client_id,
                                    'appointment_id' => $appointment_id,
                                    'no_of_days' => 14,
                                    'recepient_type' => 'Client',
                                    'content_id' => $content_id,
                                    'created_at' => $today,
                                    'created_by' => '1'
                                );
                                $this->sms_outgoing_insert($outgoing);
                            }
                        }
                    }
                } elseif ($ThreeWeeksBefore) {

                    foreach ($message as $sms) {

                        if ($sms->days == 30) {
                            $content = $sms->content;
                            $content_id = $sms->id;


                            $today = date("Y-m-d H:i:s");
                            $new_msg = str_replace("XXX", $client_name, $content);

                            $status = "Not Sent";
                            $responded = "No";

                            if ($smsenable == 'Yes' || $consented == 'YES' && trim($new_msg) != '') {
                                $source = 40149;
                                $outgoing = array(
                                    'destination' => $phone_no,
                                    'msg' => $new_msg,
                                    'responded' => $responded,
                                    'status' => $status,
                                    'message_type_id' => 9,
                                    'source' => $source,
                                    'clnt_usr_id' => $client_id,
                                    'appointment_id' => $appointment_id,
                                    'no_of_days' => 14,
                                    'recepient_type' => 'Client',
                                    'content_id' => $content_id,
                                    'created_at' => $today,
                                    'created_by' => '1'
                                );
                                $this->sms_outgoing_insert($outgoing);
                            }
                        }
                    }
                } else {
                    echo 'No client found';
                }
            }
        }
    }
    private function sms_outgoing_insert($record)
    {
        try {
            $sms = new ClientOutgoing;
            $sms->destination = $record['destination'];
            $sms->msg = $record['msg'];
            $sms->responded = $record['responded'];
            $sms->status = $record['status'];
            $sms->message_type_id = $record['message_type_id'];
            $sms->source = $record['source'];
            $sms->clnt_usr_id = $record['clnt_usr_id'];
            $sms->appointment_id = $record['appointment_id'];
            $sms->no_of_days = $record['no_of_days'];
            $sms->recepient_type = $record['recepient_type'];
            $sms->content_id = $record['content_id'];
            $sms->created_by = $record['created_by'];
            $sms->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function sender()
    {
        try {
            //get all outgoing smses
            $messages = ClientOutgoing::where('message_type_id', 9)->get();

            foreach ($messages as $message) {
                $clnt_outgoing_id = $message->id;
                $source = $message->source;
                $destination = $message->destination;
                $msg = $message->msg;
                $status = $message->status;
                $responded = $message->responded;
                $content_id = $message->content_id;
                $message_type_id = $message->message_type_id;
                $clnt_usr_id = $message->clnt_usr_id;
                $created_at = $message->created_at;
                $recepient_type = $message->recepient_type;

                if ($status == "Not Sent") {

                    //check if a similar message already sent.
                    if (DB::table('tbl_clnt_outgoing')
                        ->where('msg', 'like', '%' . $msg . '%')
                        ->where('destination', $destination)
                        ->where('status', 'Sent')
                        ->whereRaw('created_at between (CURDATE() - INTERVAL 1 DAY) AND (CURDATE() + INTERVAL 1 DAY) ')
                        ->doesntExist()
                    ) //Message has not been sent, send the  current message
                    {

                        //Number process , Append conutry code prefix on the  phone no if its not appended e.g 0712345678 => 254712345678
                        $mobile = substr($destination, -9);
                        $len = strlen($mobile);
                        if ($len < 10) {
                            $destination = "254" . $mobile;
                        }

                        //call sms service
                        $result = $this->send_message($source, $destination, $msg);

                        $status = $result['status'];
                        $messageid = '';
                        $cost = 0;

                        foreach ($result as $row) {
                            if ($status == 'success') {
                                foreach ($result['data'] as $data) {
                                    foreach ($data['Recipients'] as $Recipient) {
                                        $messageid = $Recipient['messageId'];
                                        $cost = $Recipient['cost'];
                                    }
                                }
                            }

                            //update the sent message with the sms cost and send status
                            $sms = ClientOutgoing::find($clnt_outgoing_id);
                            $sms->status = $status;
                            $sms->cost = $cost;
                            $sms->message_id = $messageid;
                            $sms->save();
                            //dd($result);

                        }
                    } else //delete the current duplicate message
                    {
                        ClientOutgoing::destroy($clnt_outgoing_id);
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
