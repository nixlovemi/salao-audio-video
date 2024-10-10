@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    @include('layout.partials.alert-return-messages')

    <a href="{{ route('people.add') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i>
        Adicionar
    </a>

    <div class="row">
        <div class="col-12">
            <div class="card mt-2">
                <div class="card-body px-2 py-0">
                    <livewire:table
                        :config="App\Tables\PeoplesTable::class"
                    />
                </div>
            </div>
        </div>
    </div>
@endsection
