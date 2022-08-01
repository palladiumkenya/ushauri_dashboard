<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class MigrationController extends Controller
{
    public function pull_todays_appointments()
    {
        $drop_table = DB::statement('DROP TABLE tbl_todays_appointment');
        $create_table_with_data = DB::statement('CREATE TABLE tbl_todays_appointment AS SELECT * FROM todays_appointments');
    }
    public function pull_past_appointment_new()
    {
        $drop_table = DB::statement('DROP TABLE tbl_past_appointment_new');
        $create_table_with_data = DB::statement('CREATE TABLE tbl_past_appointment_new AS SELECT * FROM past_appointments_view');
    }
}
