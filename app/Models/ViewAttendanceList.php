<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property date $meeting_date
 * @property int $cnt_present
 * @property int $cnt_absent
 * @property int $cnt_late
 */
class ViewAttendanceList extends Model
{
    use \App\Traits\ReadOnlyTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_attendance_list';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
