<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateViewAttendanceList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW v_attendance_list AS
                SELECT `attendances`.`meeting_date`
                    , (SELECT COUNT(*) FROM attendances a1 WHERE a1.meeting_date = attendances.meeting_date AND a1.status = 'PRESENT') AS cnt_present
                    , (SELECT COUNT(*) FROM attendances a1 WHERE a1.meeting_date = attendances.meeting_date AND a1.status = 'ABSENT') AS cnt_absent
                    , (SELECT COUNT(*) FROM attendances a1 WHERE a1.meeting_date = attendances.meeting_date AND a1.status = 'LATE') AS cnt_late
                FROM `attendances`
                WHERE TRUE
                GROUP BY `attendances`.`meeting_date`
                ORDER BY `attendances`.`meeting_date` DESC;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            DROP VIEW IF EXISTS v_attendance_list;
        ");
    }
}
