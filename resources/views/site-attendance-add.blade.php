@inject('hlpConstants', 'App\Helpers\Constants')
@inject('Carbon', 'Carbon\Carbon')
@inject('mAttendance', 'App\Models\Attendance')
@inject('mPeople', 'App\Models\People')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $TYPE: string (hlpConstants::FORM_ACTIONS)
    - $ACTION: string
    - $MEETING_DATE: date (Y-m-d)
*/

if (false === array_search($TYPE ?? '', $hlpConstants::FORM_ACTIONS)) {
    $TYPE = $hlpConstants::FORM_VIEW;
}
$ACTION = $ACTION ?? '';
$MEETING_DATE = $MEETING_DATE ?? null;
$READONLY = ($TYPE === $hlpConstants::FORM_VIEW);
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    @include('layout.partials.alert-return-messages')

    <form method="POST" action="{{ $ACTION }}">
        <input type="hidden" name="f-mt" value="{{ $MEETING_DATE }}" />
        @csrf

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            @php
                            $date = old('f-meeting-date') ?: $MEETING_DATE;
                            @endphp

                            <label>Data Reunião *</label>
                            @if($TYPE !== $hlpConstants::FORM_ADD)
                                <input class="form-control input-default" value="{{ $Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }}" disabled />
                            @else
                                <input
                                    {{ (!$READONLY) ?: 'disabled' }}
                                    class="form-control input-default jq-datepicker"
                                    placeholder="Data Reunião"
                                    name="f-meeting-date"
                                    value="{{ (null === $date) ? '': $Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') }}"
                                />
                            @endif
                        </div>
                    </div>
                </div>

                @foreach ($mAttendance::RESPONSABILITIES as $responsability => $responsabilityName)
                    <div class="row p-2 {{ $loop->index % 2 == 0 ? 'bg-light': '' }}">
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label>Função</label>
                                <input
                                    disabled
                                    class="form-control input-default"
                                    placeholder=""
                                    name="f-responsability"
                                    value="{{ $responsabilityName }}"
                                />
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label>Pessoa</label>
                                <select
                                    class="custom-select form-control input-default"
                                    name="f-people-{{ $responsability }}"
                                >
                                    <option value="">Escolha ...</option>

                                    @foreach (
                                        $mPeople::where('active', 1)
                                            ->orderBy('name', 'ASC')
                                            ->get()
                                        as $people
                                    )
                                        @php
                                        // get the people ID from the old input or from the database
                                        $AttendancePeople = $mAttendance::where('meeting_date', $MEETING_DATE)
                                            ->where('responsability', $responsability)
                                            ->first();
                                        $peopleId = old("f-people-{$responsability}") ?: $AttendancePeople?->people_id;
                                        @endphp

                                        <option
                                            value="{{ $people->id }}"
                                            {{ ($people->id == $peopleId) ? 'selected="selected"' : '' }}
                                        >{{ $people->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select
                                    class="custom-select form-control input-default"
                                    name="f-status-{{ $responsability }}"
                                >
                                    <option value="">Escolha ...</option>

                                    @foreach ($mAttendance::STATUSES as $status => $statusName)
                                        @php
                                        // get the people ID from the old input or from the database
                                        $AttendanceStatus = $mAttendance::where('meeting_date', $MEETING_DATE)
                                            ->where('responsability', $responsability)
                                            ->first();
                                        $peopleStatus = old("f-status-{$responsability}") ?: $AttendanceStatus?->status;
                                        @endphp

                                        <option
                                            value="{{ $status }}"
                                            {{ ($peopleStatus === $status) ? 'selected' : '' }}
                                        >{{ $statusName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-actions pb-4">
            <div class="text-right">
                @if ($hlpConstants::FORM_VIEW !== $TYPE)
                    <button type="submit" class="btn btn-primary">
                        Salvar
                    </button>
                @endif

                <a href="{{ route('attendance.index') }}" class="btn btn-outline-dark">Voltar para lista</a>
            </div>
        </div>
    </form>
@endsection
