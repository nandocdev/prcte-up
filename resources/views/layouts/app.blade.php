@extends('adminlte::page')

@section('title', config('app.name', 'VIEX'))

@section('content_header')
@isset($header)
    {{ $header }}
@endisset
@stop

@section('content')
{{ $slot ?? '' }}
@yield('main-content')
@stop

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="/css/admin_custom.css">
@stack('styles')
@stop

@section('js')
<script>
    // Configuración global para CSRF Token
    window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};

    // Configuración de Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        preventDuplicates: true,
        timeOut: 5000,
        extendedTimeOut: 1000
    };

    // Configuración de mensajes de éxito/error usando Toastr
    @if(session('success'))
        toastr.success({!! json_encode(session('success')) !!});
    @endif

    @if(session('error'))
        toastr.error({!! json_encode(session('error')) !!});
    @endif

    @if(session('warning'))
        toastr.warning({!! json_encode(session('warning')) !!});
    @endif

    @if(session('info'))
        toastr.info({!! json_encode(session('info')) !!});
    @endif

    // Mostrar errores de validación
    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error({!! json_encode($error) !!});
        @endforeach
    @endif

    // Configuración global de AJAX para incluir CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@stack('scripts')
@stop
