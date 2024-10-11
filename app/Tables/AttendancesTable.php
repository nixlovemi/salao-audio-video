<?php

namespace App\Tables;

use App\Helpers\SysUtils;
use App\Models\Attendance;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Table;
use Illuminate\Support\Facades\DB;
use App\Tables\RowActions\DeleteAttendanceRowAction;
use Okipa\LaravelTable\RowActions\ShowRowAction;
use Okipa\LaravelTable\RowActions\EditRowAction;

class AttendancesTable extends AbstractTableConfiguration
{
    public string $vDate1;
    public string $vDate2;

    protected function table(): Table
    {
        $this->init();

        return Table::make()
            ->model(Attendance::class)
            ->query(function(Builder $query) {
                $arrSelect = [];

                // add meeting_date to select
                $arrSelect[] = DB::raw('MAX(id) AS id');
                $arrSelect[] = 'attendances.meeting_date';

                // add one count columns for each status
                foreach (array_keys(Attendance::STATUSES) as $status) {
                    $arrSelect[] = DB::raw("(SELECT COUNT(*) FROM attendances a1 WHERE a1.meeting_date = attendances.meeting_date AND a1.status = '$status') AS cnt_{$status}");
                }

                // select meeting_date
                $query->select($arrSelect);

                // group by meeting_date
                $query->groupBy('attendances.meeting_date');
                $query->havingRaw("attendances.meeting_date BETWEEN '{$this->vDate1}' AND '{$this->vDate2}'");

                // order by meeting_date DESC
                $query->orderBy('attendances.meeting_date', 'DESC');
                $query->limit(15);
            })
            ->rowActions(fn(Attendance $attendance) => [
                (new ShowRowAction(route('attendance.view', ['timestamp' => strtotime($attendance->meeting_date)]))),
                (new EditRowAction(route('attendance.edit', ['timestamp' => strtotime($attendance->meeting_date)]))),
                (new DeleteAttendanceRowAction()),
            ]);
    }

    protected function columns(): array
    {
        $arrColumns = [];

        // add meeting_date column
        $arrColumns[] = Column::make('meeting_date')
            ->title('Dt Reunião')
            ->format(function(Attendance $Attendance) {
                // format date d/m/Y
                return SysUtils::timezoneDate($Attendance->meeting_date, 'd/m/Y');
            });

        // add one count columns for each status
        foreach (array_keys(Attendance::STATUSES) as $status) {
            $arrColumns[] = Column::make("cnt_{$status}")
                ->title(Attendance::STATUSES[$status]);
        }

        return $arrColumns;
    }

    private function init(): void
    {
        // if vDate1 is empty, set to today - 1 month
        if (empty($this->vDate1)) {
            $this->vDate1 = date('Y-m-d', strtotime('-1 month'));
        }

        // if vDate2 is empty, set to today
        if (empty($this->vDate2)) {
            $this->vDate2 = date('Y-m-d');
        }
    }
}
