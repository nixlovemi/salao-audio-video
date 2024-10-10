@inject('hlpConstants', 'App\Helpers\Constants')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $TYPE: string (hlpConstants::FORM_ACTIONS)
    - $ACTION: string
    - $PEOPLE: App\Models\People
*/

if (false === array_search($TYPE ?? '', $hlpConstants::FORM_ACTIONS)) {
    $TYPE = $hlpConstants::FORM_VIEW;
}
$ACTION = $ACTION ?? '';
$PEOPLE = $PEOPLE ?? null;
$READONLY = ($TYPE === $hlpConstants::FORM_VIEW);
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    @include('layout.partials.alert-return-messages')

    <form method="POST" action="{{ $ACTION }}">
        <input type="hidden" name="f-pid" value="{{ $PEOPLE?->codedId }}" />
        @csrf

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-9">
                        <div class="form-group">
                            <label>Nome *</label>
                            <input maxlength="40" {{ (!$READONLY) ?: 'disabled' }} value="{{ old('f-name') ?: $PEOPLE?->name }}" name="f-name" type="text" class="form-control input-default" placeholder="Nome" />
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label>Ativo *</label>
                            <select
                                class="form-control"
                                name="f-active"
                                {{ (!$READONLY) ?: 'disabled' }}
                            >
                                @foreach (['1' => 'Sim', '0' => 'Não'] as $active => $activeLabel)
                                    <option
                                        value="{{ $active }}"
                                        {{ $active != old('f-active', $PEOPLE?->active ?? 1) ? '': 'selected' }}
                                    >{{ $activeLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions pb-4">
            <div class="text-right">
                @if ($hlpConstants::FORM_VIEW !== $TYPE)
                    <button type="submit" class="btn btn-primary">
                        Salvar
                    </button>
                @endif

                <a href="{{ route('people.index') }}" class="btn btn-outline-dark">Voltar para lista</a>
            </div>
        </div>
    </form>
@endsection
