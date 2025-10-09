@extends('adminlte::page')

@section('title', $report->name())

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-chart-bar"></i>
            {{ $report->name() }}
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">{{ __('admin.reports.title') }}</a></li>
            <li class="breadcrumb-item active">{{ $report->name() }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-filter mr-2"></i>
            {{ __('admin.reports.filters.title') }}
        </h3>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>
            {{ __('admin.reports.actions.back') }}
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.show', ['report' => $reportKey]) }}">
            <div class="form-row">
                @foreach ($filterDefinitions as $field => $definition)
                <div class="form-group col-md-3 col-12">
                    <label for="filter-{{ $field }}">{{ $definition['label'] }}</label>
                    @if ($definition['type'] === 'date')
                        <input type="date" name="{{ $field }}" id="filter-{{ $field }}" class="form-control"
                            value="{{ $filters[$field] ?? '' }}">
                    @elseif ($definition['type'] === 'select')
                        <select name="{{ $field }}" id="filter-{{ $field }}" class="form-control">
                            <option value="">{{ __('admin.reports.filters.any_option') }}</option>
                            @foreach ($definition['options'] as $option)
                                <option value="{{ $option['value'] }}" {{ ($filters[$field] ?? '') == $option['value'] ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error($field)
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.reports.show', ['report' => $reportKey]) }}" class="btn btn-link text-danger">
                    <i class="fas fa-undo mr-1"></i>
                    {{ __('admin.reports.actions.clear_filters') }}
                </a>
                <button type="submit" name="apply" value="1" class="btn btn-success">
                    <i class="fas fa-play mr-1"></i>
                    {{ __('admin.reports.actions.generate') }}
                </button>
            </div>
        </form>
    </div>
</div>

@if ($result === null)
<div class="alert alert-info">
    <i class="fas fa-info-circle mr-2"></i>
    {{ __('admin.reports.messages.apply_filters') }}
</div>
@else
    @if ($result->isEmpty())
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ __('admin.reports.messages.empty_results') }}
    </div>
    @else
    <div class="d-flex justify-content-end mb-3">
        @php($downloadQuery = array_merge(['report' => $reportKey], $downloadParams))
        <a href="{{ route('admin.reports.download', $downloadQuery) }}" class="btn btn-outline-success">
            <i class="fas fa-file-download mr-1"></i>
            {{ __('admin.reports.actions.download_csv') }}
        </a>
    </div>

    @if (!empty($result->summary))
    <div class="row">
        @foreach ($result->summary as $summaryKey => $summaryValue)
        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
            <div class="card border-success h-100">
                <div class="card-body">
                    <p class="text-muted text-uppercase small mb-1">
                        {{ __('admin.reports.summary.' . $summaryKey) }}
                    </p>
                    <h3 class="mb-0">
                        @if (is_numeric($summaryValue))
                            {{ is_float($summaryValue) ? number_format($summaryValue, 2, '.', ',') : number_format((int) $summaryValue) }}
                        @elseif ($summaryValue === null)
                            —
                        @else
                            {{ $summaryValue }}
                        @endif
                    </h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-table mr-2"></i>
                {{ __('admin.reports.results.title') }}
            </h3>
            <span class="badge badge-success badge-pill">{{ count($result->rows) }} {{ __('admin.reports.results.rows') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            @foreach ($result->columns as $column)
                            <th>{{ $column['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result->rows as $row)
                        <tr>
                            @foreach ($result->columns as $column)
                            <td>{{ data_get($row, $column['key']) }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
@endif
@stop
