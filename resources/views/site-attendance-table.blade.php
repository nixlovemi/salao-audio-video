@php
/*
View variables:
===============
    - $DATE_TIME1: timestamp
    - $DATE_TIME2: timestamp
*/
@endphp

<div class="row">
    <div class="col-12">
        <div class="card mt-2">
            <div class="card-body px-2 py-0">
                <livewire:table
                    :config="App\Tables\AttendancesTable::class"
                    :configParams="[
                        'vDate1' => date('Y-m-d', $DATE_TIME1),
                        'vDate2' => date('Y-m-d', $DATE_TIME2),
                    ]"
                />
            </div>
        </div>
    </div>
</div>
