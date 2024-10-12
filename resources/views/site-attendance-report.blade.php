@inject('mAttendance', 'App\Models\Attendance')
@inject('DB', 'Illuminate\Support\Facades\DB')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/

$DATE_TIME1 = $DATE_TIME1 ?? strtotime('-1 month');
$DATE_TIME2 = $DATE_TIME2 ?? time();
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    @include('layout.partials.alert-return-messages')

    <form method="POST" action="{{ route('attendance.report') }}">
        @csrf

        <div class="row mt-4 mb-3">
            <div class="col-12">
                <div class="card-header">
                    <h5 class="mb-0">Filtrar por data:</h5>
                </div>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Data Inicial</label>
                                    <input type="text" class="form-control input-default jq-datepicker" name="f-date1" value="{{ date('d/m/Y', $DATE_TIME1) }}" />
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Data Final</label>
                                    <input type="text" class="form-control input-default jq-datepicker" name="f-date2" value="{{ date('d/m/Y', $DATE_TIME2) }}" />
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <div class="mt-0 mt-md-2"></div>
                                    <button type="submit" class="btn btn-primary btn-sm mt-1 mt-md-4">
                                        <i class="fas fa-filter"></i>
                                        Filtrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-print"></i>
                    Resultado
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr class="table-light border-top border-bottom">
                                    <th class="align-middle" scope="col">
                                        Pessoa
                                    </th>
                                    @foreach ($mAttendance::STATUSES as $status)
                                        <th class="align-middle" scope="col">
                                            {{ $status }}
                                        </th>
                                    @endforeach
                                    <th class="align-middle" scope="col">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $sqlDt1 = date('Y-m-d', $DATE_TIME1);
                                $sqlDt2 = date('Y-m-d', $DATE_TIME2);
                                $cntCols = '';
                                foreach ($mAttendance::STATUSES as $status => $statusName) {
                                    $cntCols .= ",(SELECT COUNT(*) FROM attendances a2 WHERE a2.people_id = p.id AND a2.status = '{$status}' AND a2.meeting_date BETWEEN '{$sqlDt1}' AND '$sqlDt2') AS ctn_{$status}";
                                }

                                $sql = '
                                    SELECT p.name
                                        '.$cntCols.'
                                        ,(SELECT COUNT(*) FROM attendances a2 WHERE a2.people_id = p.id AND a2.meeting_date BETWEEN \''.$sqlDt1.'\' AND \''.$sqlDt2.'\') AS ctn_total
                                    FROM attendances a
                                    INNER JOIN people p ON (p.id = a.people_id)
                                    WHERE a.meeting_date BETWEEN \''.$sqlDt1.'\' AND \''.$sqlDt2.'\'
                                    GROUP BY p.id, p.name
                                    ORDER BY p.name
                                ';
                                $results = $DB::select($sql);
                                @endphp

                                @foreach ($results as $result)
                                    <tr class="border-bottom">
                                        <th class="align-middle" scope="row">
                                            {{ $result->name }}
                                        </th>
                                        @foreach ($mAttendance::STATUSES as $status => $statusName)
                                            <td class="align-middle">
                                                {{ $result->{'ctn_'.$status} }}
                                                ({{ number_format(($result->{'ctn_'.$status} / $result->ctn_total) * 100, 2, ',', '.') }}%)
                                            </td>
                                        @endforeach
                                        <td class="align-middle">
                                            {{ $result->ctn_total }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
