<?php

namespace App\Http\Controllers;

use App\Models\AppointmentNotifications;
use App\Models\MissedAppointmentNotifications;
use App\Models\DefaulterNotifications;
use App\Models\LTFUNotifications;
use App\Models\Appointments;
use App\Models\ClientOutgoing;
use App\Models\OutgoingSms;
use App\Models\Wellness;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ScheduleSMSController extends Controller
{
    protected  $current_date;
    protected $httpresponse;

    public function __construct()
    {
        set_time_limit(0);
        $this->current_date = date("Y-m-d");
    }

    public function wellnessScheduler()
    {
        try{
            //get clients enrolled for the wellness sms as per filter
            $clients = Wellness::all();

            foreach ($clients as $client) {
                $cleaned_msg ="";
                $client_id = $client->client_id;
                $language = $client->language_name;
                $group_id = $client->group_id;
                $language_id = $client->language_id;
                $registration_date = $client->registration_date;
                $time = $client->txt_time;
                $daytime = $client->daytime;
                $frequency = $client->txt_frequency;
                $phone_no = $client->phone_no;
                $wellness_enable = $client->wellness_enable;
                $motivational_enable = $client->motivational_enable;

                 //Check if the  phone number has country code appended
                $mobile = substr($phone_no, -9);
                $len = strlen($mobile);
                if ($len < 10) {
                    $phone_no = "254" . $mobile;
                }

                //check if a wellness message is already sent. If not send it.
                if (DB::table('tbl_clnt_outgoing')
                        ->where('message_type_id', 4)
                        ->where('clnt_usr_id', $client_id)
                        ->where(function ($query) {
                            $query->whereDate('created_at',  $this->current_date)
                                  ->orwhereDate('updated_at',  $this->current_date);
                        })
                        ->doesntExist())
                        {
                             //get message content
                                $message_type_id = 4;
                                $msg_content = DB::table('tbl_content')
                                ->where('message_type_id', $message_type_id)
                                ->where('language_id', $language_id)
                                ->where('group_id', $group_id)
                                ->get()
                                ->take(1);

                                foreach($msg_content  as $msg)
                                {
                                    $content_id = $msg->id;
                                    $cleaned_msg = $msg->message;
                                    $today = date("Y-m-d H:i:s");
                                    $status = "Not Sent";
                                    $responded = "No";

                                    if ($wellness_enable == 'Yes' && trim($cleaned_msg) != '') {
                                            $source = 40149;
                                            $clnt_outgoing = array(
                                                'destination' => $phone_no,
                                                'msg' => $cleaned_msg,
                                                'responded' => $responded,
                                                'status' => $status,
                                                'message_type_id' => $message_type_id,
                                                'source' => $source,
                                                'clnt_usr_id' => $client_id,
                                                'appointment_id' => null,
                                                'no_of_days' => null,
                                                'recepient_type' => 'Client',
                                                'content_id' => $content_id,
                                                'created_at' => $today,
                                                'created_by' => '1'
                                            );

                                            $this->sms_outgoing_insert($clnt_outgoing);
                                    }

                                }


                        }
            }
        }catch (Exception $e) {
            throw $e;
        }
    }

    public function notifiedScheduler()
    {
        try{
            //get one day and seven day appointments for sms notification
            $notifications = AppointmentNotifications::all();

            foreach($notifications as $notification)
            {
                $f_name = $notification->f_name;
                $m_name = $notification->m_name;
                $l_name = $notification->l_name;
                $phone_no = $notification->phone_no;
                $txt_time = $notification->txt_time;
                $alt_phone_no = $notification->alt_phone_no;
                $smsenable = $notification->smsenable;

                $appointment_date = $notification->appntmnt_date;
                $appointment_msg = $notification->app_msg;
                $no_of_days = $notification->no_of_days;

                $app_status = $notification->app_status;
                $appointment_id = $notification->appointment_id;
                $notified = $notification->notified;
                $sent_status = $notification->sent_status;
                $language_id = $notification->language_id;
                $client_id = $notification->client_id;
                $client_name = ucwords(strtolower($f_name)) . " ";
                $client_name = str_replace("'", '', $client_name);

                //check if a notification is already sent. If not send it.
                if (DB::table('tbl_clnt_outgoing')
                        ->where('message_type_id', 1)
                        ->where('clnt_usr_id', $client_id)
                        ->where(function ($query) {
                            $query->whereDate('created_at',  $this->current_date)
                                  ->orwhereDate('updated_at',  $this->current_date);
                        })
                        ->doesntExist())
                {
                    //set the logic flow based on the number of days to appointment
                    $logic_flow_id = $no_of_days == 7 ? 2 : 3;

                    //get message content
                    $msg_content = DB::table('tbl_messages')
                                    ->where('target_group', 'All')
                                    ->where('message_type_id', 1)
                                    ->where('logic_flow', $logic_flow_id)
                                    ->where('language_id', $language_id)
                                    ->get()
                                    ->take(1);

                    foreach($msg_content  as $msg)
                    {
                        $content_id = $msg->id;
                        $content = $msg->message;
                        $today = date("Y-m-d H:i:s");
                        $new_msg = str_replace("XXX", $client_name, $content);
                        $appointment_date = date("d-m-Y", strtotime($appointment_date));
                        $cleaned_msg = str_replace("YYY", $appointment_date, $new_msg);

                        $status = "Not Sent";
                        $responded = "No";
                        $yes_notified = 'Yes';
                        $app_status = "Notified";
                        $Notified_status = "Notified Sent";

                        $update_appointment_array = array(
                            'appointment_id' => $appointment_id,
                            'app_status' => $app_status,
                            'app_msg' => $cleaned_msg,
                            'notified' => $yes_notified,
                            'sent_status' => $Notified_status,
                            'updated_by' => '1'
                        );


                            // update the appointment details with the sms send status and sms message
                            $this->appointment_update($update_appointment_array);


                        if ($smsenable == 'Yes') //send the sms
                        {
                            $source = 40149;
                            $message_type_id = 1;
                            $clnt_outgoing = array(
                                'destination' => $phone_no,
                                'msg' => $cleaned_msg,
                                'responded' => $responded,
                                'status' => $status,
                                'message_type_id' => $message_type_id,
                                'source' => $source,
                                'clnt_usr_id' => $client_id,
                                'appointment_id' => $appointment_id,
                                'no_of_days' => $no_of_days,
                                'recepient_type' => 'Client',
                                'content_id' => $content_id,
                                'created_at' => $today,
                                'created_by' => '1'
                            );

                            //  if($appointment_id == 2783041){
                                    // send the sms
                                    $this->sms_outgoing_insert($clnt_outgoing);
                                // }
                        }
                       // dd($clnt_outgoing);

                    }

                }
            }
        }catch (Exception $e) {
            throw $e;
        }
    }

    public function missedScheduler()
    {
        try{
            //get notification flow info...
            $update_appointment_array = array();
            $notification_flow = DB::table('tbl_notification_flow')
            ->where('status', 'Active')
            ->where('notification_type', 'Missed')
            ->get()
            ->take(1);

            foreach($notification_flow as $row)
            {
                $notification_type = $row->notification_type;
                $notification_value = $row->value;
                $notification_flow_id = $row->id;
                $notification_flow_days = $row->days;
            }

            //get missed appointments
            $notifications = MissedAppointmentNotifications::all();

            foreach($notifications as $notification)
            {
                $cleaned_msg = '';
                $group_name = $notification->group_name;
                $group_id = $notification->group_id;
                $f_name = $notification->f_name;
                $m_name = $notification->m_name;
                $l_name = $notification->l_name;
                $phone_no = $notification->phone_no;
                $txt_time = $notification->txt_time;
                $alt_phone_no = $notification->alt_phone_no;
                $smsenable = $notification->smsenable;
                $no_of_days = $notification->no_of_days;

                $appointment_date = $notification->appntmnt_date;
                $appointment_msg = $notification->app_msg;
                $app_status = $notification->app_status;
                $appointment_id = $notification->appointment_id;
                $notified = $notification->notified;
                $sent_status = $notification->sent_status;
                $language_id = $notification->language_id;
                $client_id = $notification->client_id;
                $client_name = ucwords(strtolower($f_name)) . " ";
                $client_name = str_replace("'", '', $client_name);

                $target_group = 'All';
                $message_type_id = 1;
                $logic_flow_id = 4;

                //get message content
                $get_content = DB::table('tbl_content')
                ->where('identifier', $notification_flow_id)
                ->where('message_type_id', 1)
                ->where('group_id', $group_id)
                ->where('language_id', $language_id)
                ->get()
                ->take(1);

                if(empty($get_content)){

                    $status = "Not Sent";
                    $responded = "No";

                    $yes_notified = 'No';
                    $app_status = "Missed";
                    $missed_status = "Missed Updated";

                    $update_appointment_array= array(
                        'appointment_id' => $appointment_id,
                        'app_status' => $app_status,
                        'app_msg' => "Message content not Found",
                        'notified' => $yes_notified,
                        'sent_status' => $missed_status,
                        'updated_by' => '1'
                    );
                }
                else{
                    foreach ($get_content as $sms)
                    {
                        $missed = "Missed";
                        $content = $sms->content;

                        $content_id = $sms->id;
                        $message_type_id = $sms->message_type_id;
                        //Convert encoded character in the  message to clients real name and appointment day XXX => Client Name  YYY=> Appointment Date
                        $today = date("Y-m-d H:i:s");
                        $new_msg = str_replace("XXX", $client_name, $content);
                        $appointment_date = date("d-m-Y", strtotime($appointment_date));
                        $cleaned_msg = str_replace("YYY", $appointment_date, $new_msg);

                        $status = "Not Sent";
                        $responded = "No";
                        $yes_notified = 'Yes';
                        $app_status = "Missed";
                        $Missed_status = "Missed Sent";

                        $update_appointment_array= array(
                            'appointment_id' => $appointment_id,
                            'app_status' => $app_status,
                            'app_msg' => $cleaned_msg,
                            'notified' => $yes_notified,
                            'sent_status' => $Missed_status,
                            'updated_by' => '1'
                        );
                    }
                }

                // update the appointment details with the sms send status and sms message
                if(!empty($update_appointment_array))
                {
                    $this->appointment_update($update_appointment_array);

                    //check if a notification is already sent. If not send it.
                    if (DB::table('tbl_clnt_outgoing')
                            ->where('message_type_id', 1)
                            ->where('clnt_usr_id', $client_id)
                            ->where('destination', $phone_no)
                            ->whereDate('created_at',  $this->current_date)
                            ->doesntExist())
                    {
                        if ($smsenable == 'Yes' && trim($cleaned_msg) != '') {
                            $source = 40149;
                            $outgoing = array(
                                'destination' => $phone_no,
                                'msg' => $cleaned_msg,
                                'responded' => $responded,
                                'status' => $status,
                                'message_type_id' => $message_type_id,
                                'source' => $source,
                                'clnt_usr_id' => $client_id,
                                'appointment_id' => $appointment_id,
                                'no_of_days' => 0,
                                'recepient_type' => 'Client',
                                'content_id' => $content_id,
                                'created_at' => $today,
                                'created_by' => '1'
                            );

                            //  if($appointment_id == 3009818){
                                // send the sms
                                $this->sms_outgoing_insert($outgoing);
                            // }

                        }
                    }
                }

            }
        }catch (Exception $e) {
            throw $e;
        }

    }

    public function defaultedScheduler()
    {
        try{
            //get notification flow info...
            $notification_flow = DB::table('tbl_notification_flow')
            ->where('status', 'Active')
            ->where('notification_type', 'Defaulted')
            ->get()
            ->take(1);

            foreach($notification_flow as $row)
            {
                $notification_type = $row->notification_type;
                $notification_value = $row->value;
                $notification_flow_id = $row->id;
                $notification_flow_days = $row->days;
            }

            //defaulters for sms notification
            $notifications = DefaulterNotifications::all();

            foreach($notifications as $notification)
            {
                $f_name = $notification->f_name;
                $m_name = $notification->m_name;
                $l_name = $notification->l_name;
                $phone_no = $notification->phone_no;
                $txt_time = $notification->txt_time;
                $alt_phone_no = $notification->alt_phone_no;
                $smsenable = $notification->smsenable;

                $appointment_date = $notification->appntmnt_date;
                $appointment_msg = $notification->app_msg;
                $app_status = $notification->app_status;
                $appointment_id = $notification->appointment_id;
                $notified = $notification->notified;
                $sent_status = $notification->sent_status;
                $language_id = $notification->language_id;
                $client_id = $notification->client_id;
                $group_id = $notification->group_id;
                $client_name = ucwords(strtolower($f_name)) . " ";
                $client_name = str_replace("'", '', $client_name);

                $target_group = 'All';
                $message_type_id = 1;
                $today = date("Y-m-d H:i:s");

                $status = "Not Sent";
                $responded = "No";
                $yes_notified = 'Yes';
                $app_status = "Defaulted";
                $Default_status = "Default Sent";

                $update_appointment_array = array(
                    'appointment_id' => $appointment_id,
                    'app_status' => $app_status,
                    'app_msg' => '',
                    'notified' => $yes_notified,
                    'sent_status' => $Default_status,
                    'updated_by' => '1'
                );

                // update the appointment details with the sms send status and sms message
                $this->appointment_update($update_appointment_array);


            }
        }catch (Exception $e) {
            throw $e;
        }

    }

    public function ltfuScheduler()
    {
        try{
         //ltfu records for sms notification
         $notifications = LTFUNotifications::all();

         foreach($notifications as $notification)
         {
            $f_name = $notification->f_name;
            $m_name = $notification->m_name;
            $l_name = $notification->l_name;
            $phone_no = $notification->phone_no;
            $txt_time = $notification->txt_time;
            $alt_phone_no = $notification->alt_phone_no;
            $smsenable = $notification->smsenable;

            $appointment_date = $notification->appntmnt_date;
            $appointment_msg = $notification->app_msg;
            $app_status = $notification->app_status;
            $appointment_id = $notification->appointment_id;
            $notified = $notification->notified;
            $sent_status = $notification->sent_status;
            $language_id = $notification->language_id;
            $client_id = $notification->client_id;
            $group_id = $notification->group_id;
            $client_name = ucwords(strtolower($f_name)) . " ";
            $client_name = str_replace("'", '', $client_name);

            $update_appointment_array = array(
                'appointment_id' => $appointment_id,
                'app_status' => "LTFU",
                'app_msg' => '',
                'notified' => "No",
                'sent_status' => '',
                'updated_by' => '1'
            );

            // update the appointment details with the sms send status and sms message
            $this->appointment_update($update_appointment_array);
         }

        }catch (Exception $e) {
            throw $e;
        }

    }

    public function sender()
    {
        try{
            //get all outgoing smses
            $messages = OutgoingSms::all();

            foreach($messages as $message)
            {
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

                if ($status == "Not Sent")
                {
                   // DB::connection()->enableQueryLog();
                    //check if a similar message already sent.
                    if (DB::table('tbl_clnt_outgoing')
                    ->where('msg','like', '%'.$msg.'%')
                    ->where('destination', $destination)
                    ->where('status', 'Sent')
                    ->whereRaw('created_at between (CURDATE() - INTERVAL 1 DAY) AND (CURDATE() + INTERVAL 1 DAY) ')
                    ->doesntExist()) //Message has not been sent, send the  current message
                    {
                       // $queries = DB::getQueryLog();
                       // dd( $queries);

                        //Number process , Append conutry code prefix on the  phone no if its not appended e.g 0712345678 => 254712345678
                        $mobile = substr($destination, -9);
                        $len = strlen($mobile);
                        if ($len < 10) {
                            $destination = "254" . $mobile;
                        }

                        //call sms service
                        $result = $this->send_message($source,$destination , $msg);

                        $status = $result['status'];
                        $messageid = '';
                        $cost = 0;

                        foreach($result as $row)
                        {
                            if($status == 'success')
                            {
                                foreach($result['data'] as $data)
                                {
                                    foreach($data['Recipients'] as $Recipient)
                                    {
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
                    }
                    else //delete the current duplicate message
                    {
                        ClientOutgoing::destroy($clnt_outgoing_id);
                    }
                }
            }
        }catch (Exception $e) {
            throw $e;
        }
    }

    private function send_message($source, $destination, $msg)
    {
        $key = env('SMS_SERVICE_KEY', '');
        $host = env('SMS_SERVICE_HOST', '');

        $this->httpresponse = Http::
                                withoutVerifying()
                                ->withHeaders(['api-token'=> "$key"])
                                ->post("$host", [
                                        'destination' => $destination,
                                        'msg' => $msg,
                                        'sender_id' => $destination,
                                        'gateway' => $source,
                                    ]);

       return json_decode( $this->httpresponse->getBody(), true);
    }

    private function sms_outgoing_insert($record)
    {
        try{
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

    private function appointment_update($record)
    {
        try{
            $appointment = Appointments::find($record['appointment_id']);
            $appointment->app_status = $record['app_status'];
            $appointment->app_msg = trim($record['app_msg']) == '' ? $appointment->app_msg : $record['app_msg'];
            $appointment->notified = $record['notified'];
            $appointment->sent_status = trim($record['sent_status']) == '' ? $appointment->sent_status : $record['sent_status'];
            $appointment->updated_by = $record['updated_by'];
            $appointment->save();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
