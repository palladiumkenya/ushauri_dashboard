<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use DB;

class MigrationController extends Controller
{
    public function pull_todays_appointments()
    {
        $drop_table = Schema::dropIfExists('tbl_todays_appointment');
        $create_table_with_data = DB::statement('CREATE TABLE tbl_todays_appointment AS SELECT * FROM todays_appointments');
    }
    public function pull_past_appointment_new()
    {
        $drop_table = Schema::dropIfExists('tbl_past_appointment_new');
        $create_table_with_data = DB::statement('CREATE TABLE tbl_past_appointment_new AS SELECT * FROM past_appointments_view');
    }
    public function sync_tracing_outcome()
    {
        //$latest_ids  = DB::statement('SELECT MAX(Outcome_ID) AS Outcome_ID FROM tbl_outcome_report_raw');
        // $latest_ids = DB::table('tbl_outcome_report_raw')->select('Outcome_ID AS Outcome_ID')->max('Outcome_ID');
        // dd($latest_ids);

        // foreach($latest_ids  as $latest)
        // {
        //    // $new_clients = DB::statement('SELECT * FROM partner_outcome_report WHERE Outcome_ID > '$latest->Outcome_ID' ');
        //     $new_clients = DB::table('partner_outcome_report')->select('*')->where('Outcome_ID', '>', $latest->Outcome_ID);
        //     foreach($new_clients as $new_client)
        //     {
        //       //  DB::insert('INSERT INTO tbl_outcome_report_raw, $new_client');
        //         DB::table('tbl_outcome_report_raw')->insert($new_client);
        //     }
        // }

        // $latest_ids = DB::table('partner_outcome_report')->select('*')->get();
        // DB::table('tbl_outcome_report_raw')->insert($latest_ids);

        $basket_data = DB::table('partner_outcome_report')->distinct('Outcome_ID')->get();
        foreach ($basket_data as $records) {
            DB::table('tbl_outcome_report_raw')->insert(get_object_vars($records));
        }
        // dd($latest_ids);
    }
}
