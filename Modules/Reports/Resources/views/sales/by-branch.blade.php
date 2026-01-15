@extends('layouts.app')

@section('title', 'Reporte de Ventas por Sede')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Reporte por Sede</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.sales-report-by-branch :customers="\Modules\People\Entities\Customer::all()" />
    </div>
@endsection