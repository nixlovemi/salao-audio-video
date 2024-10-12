@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/

$datetime1 = strtotime('-1 month');
$datetime2 = time();
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    @include('layout.partials.alert-return-messages')

    <a href="{{ route('attendance.add') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i>
        Adicionar
    </a>

    <form id="frm-filter-attendance" method="POST" action="{{ route('attendance.ajaxFilterTable') }}">
        @csrf

        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-2">
                    <div class="card-header">
                        <h5 class="mb-0">Filtrar por data:</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Data Inicial</label>
                                    <input type="text" class="form-control input-default jq-datepicker" name="f-date1" value="{{ date('d/m/Y', $datetime1) }}" />
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Data Final</label>
                                    <input type="text" class="form-control input-default jq-datepicker" name="f-date2" value="{{ date('d/m/Y', $datetime2) }}" />
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

    <div id="attendance-list-table">
        @include('site-attendance-table', [
            'DATE_TIME1' => $datetime1,
            'DATE_TIME2' => $datetime2,
        ])
    </div>
@endsection
