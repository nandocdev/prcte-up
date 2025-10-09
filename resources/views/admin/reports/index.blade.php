@extends('adminlte::page')

@section('title', __('admin.reports.title'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-chart-pie"></i>
            {{ __('admin.reports.title') }}
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('admin.reports.title') }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row">
    @foreach ($reports as $key => $report)
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-gradient-success text-white">
                <h3 class="card-title mb-0">
                    <i class="fas fa-chart-area mr-2"></i>
                    {{ $report->name() }}
                </h3>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="text-muted flex-grow-1">{{ $report->description() }}</p>
                <a href="{{ route('admin.reports.show', ['report' => $key]) }}" class="btn btn-success mt-3">
                    <i class="fas fa-file-alt mr-2"></i>
                    {{ __('admin.reports.actions.view_report') }}
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@stop
