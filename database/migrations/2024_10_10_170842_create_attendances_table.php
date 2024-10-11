<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Attendance;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->date('meeting_date');
            $table->enum('responsability', array_keys(Attendance::RESPONSABILITIES));
            $table->foreignId('people_id')
                ->nullable()
                ->constrained('people')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->enum('status', array_keys(Attendance::STATUSES))->nullable();
            $table->timestamps();

            $table->unique(
                ['people_id', 'meeting_date'],
                'uk_attendances_people_meeting_date'
            );
            $table->unique(
                ['responsability', 'meeting_date'],
                'uk_attendances_responsability_meeting_date'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropUnique('uk_attendances_people_meeting_date');
            $table->dropUnique('uk_attendances_responsability_meeting_date');
        });
        Schema::dropIfExists('attendances');
    }
}
