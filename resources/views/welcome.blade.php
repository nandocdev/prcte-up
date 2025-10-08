@extends('adminlte::master')

@php
$bodyClasses = 'landing-page';
@endphp

@section('adminlte_css')
<style>
    body.landing-page {
        font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .hero-section {
        background: linear-gradient(135deg, #1a7327 0%, #339327 50%, #5cb35c 100%);
        color: white;
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .features-section {
        padding: 80px 0;
        background-color: #f8f9fa;
    }

    .feature-card {
        background: white;
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        font-size: 3rem;
        color: #339327;
        /* Cambiado de #1976d2 */
        margin-bottom: 20px;
    }

    .cta-section {
        background: linear-gradient(135deg, #339327 0%, #1a7327 100%);
        /* Cambiado de azul */
        color: white;
        padding: 80px 0;
    }

    .stats-section {
        background-color: white;
        padding: 60px 0;
    }

    .stat-card {
        text-align: center;
        padding: 30px 20px;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: bold;
        color: #339327;
        /* Cambiado de #1976d2 */
    }

    .university-logo {
        max-height: 80px;
        margin-bottom: 20px;
    }

    .btn-hero {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid white;
        padding: 15px 40px;
        font-size: 18px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .btn-hero:hover {
        background: white;
        color: #339327;
        /* Cambiado de #1976d2 */
    }

    .navbar-custom {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .footer-section {
        background-color: #212529;
        color: white;
        padding: 50px 0 30px;
    }
</style>
@stop

@section('classes_body'){{ $bodyClasses }}@stop

@section('body')
{{-- Navigation --}}
<nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('assets/img/icons/base.png') }}" alt="VIEX UP" class="img-fluid me-2"
                style="height: 40px;">
            <strong style="color: #1976d2;">VIEX UP</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#inicio">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#funcionalidades">Funcionalidades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#estadisticas">Estadísticas</a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="nav-link btn btn-success text-white px-3" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i> Ir al Dashboard
                    </a>
                </li>
                @else
                <li class="nav-item me-2">
                    <a class="nav-link btn btn-outline-success px-3" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

{{-- Hero Section --}}
<section id="inicio" class="hero-section">
    <div class="container">
        <div class="row align-items-center hero-content">
            <div class="col-lg-6">
                <img src="{{ asset('assets/img/icons/icon_dark.png') }}" alt="Universidad de Panamá"
                    class="university-logo">
                <h1 class="display-4 fw-bold mb-4">
                    Plataforma de Registro y Certificación de Trabajos de Extensión
                </h1>
                <p class="lead mb-5">
                    Digitaliza y automatiza el proceso completo de gestión de trabajos de extensión universitarios de la
                    Universidad de Panamá, desde el registro inicial hasta la certificación final.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    @guest
                    <a href="{{ route('login') }}" class="btn btn-hero btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                    </a>
                    @else
                    <a href="{{ route('dashboard') }}" class="btn btn-hero btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i> Ir al Dashboard
                    </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-university" style="font-size: 15rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features Section --}}
<section id="funcionalidades" class="features-section">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Funcionalidades Principales</h2>
                <p class="lead text-muted">Todo lo que necesitas para gestionar trabajos de extensión de manera
                    eficiente</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 p-2">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Registro Digital</h4>
                    <p class="text-muted">
                        Formularios dinámicos adaptados a cada tipo de trabajo de extensión: Proyectos, Actividades,
                        Publicaciones y Asistencias Técnicas.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 p-2">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Flujo Automatizado</h4>
                    <p class="text-muted">
                        Proceso automatizado desde el docente hasta la certificación VIEX, pasando por Coordinador y
                        Decano con seguimiento en tiempo real.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 p-2">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Certificación Digital</h4>
                    <p class="text-muted">
                        Certificados electrónicos oficiales con firma digital y código QR para verificación pública de
                        autenticidad.
                    </p>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 p-2">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Gestión de Evidencias</h4>
                    <p class="text-muted">
                        Almacenamiento seguro de documentos, control de versiones y acceso organizado a todas las
                        evidencias del trabajo.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 p-2">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Notificaciones</h4>
                    <p class="text-muted">
                        Sistema de alertas automáticas por correo y en plataforma para cada cambio de estado en el
                        proceso de revisión.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 p-2">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Reportes y Analytics</h4>
                    <p class="text-muted">
                        Dashboards ejecutivos, reportes de gestión con SLA y estadísticas detalladas para la toma de
                        decisiones.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section id="estadisticas" class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Impacto de la Extensión Universitaria</h2>
                <p class="lead text-muted">Números que reflejan nuestro compromiso con la comunidad</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-number">4</div>
                    <h5 class="fw-bold">Tipos de Trabajos</h5>
                    <p class="text-muted">Proyectos, Actividades, Publicaciones y Asistencias Técnicas</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-number">20</div>
                    <h5 class="fw-bold">Días Hábiles</h5>
                    <p class="text-muted">Tiempo máximo para certificación con seguimiento automatizado</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-number">100%</div>
                    <h5 class="fw-bold">Digital</h5>
                    <p class="text-muted">Proceso completamente digitalizado sin papelería física</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-number">24/7</div>
                    <h5 class="fw-bold">Disponible</h5>
                    <p class="text-muted">Acceso continuo para registro y seguimiento de trabajos</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-4">¿Listo para digitalizar tus trabajos de extensión?</h2>
                <p class="lead mb-5">
                    Únete a la transformación digital de la Universidad de Panamá y optimiza el proceso de registro y
                    certificación de tus proyectos de extensión.
                </p>
                @auth
                <a href="{{ route('dashboard') }}" class="btn btn-hero btn-lg">
                    <i class="fas fa-plus me-2"></i> Registrar Nuevo Trabajo
                </a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="footer-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 mx-auto">
                <div class="text-center">
                    <img src="{{ asset('vendor/viex/icons/icono.png') }}" alt="VIEX UP" class="img-fluid mb-3"
                        style="height: 60px;">
                    <h5 class="fw-bold mb-3">VIEX - Universidad de Panamá</h5>
                    <p class="text-muted mb-4">
                        Plataforma oficial para el registro y certificación de trabajos de extensión de la Universidad
                        de Panamá.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="text-light"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fas fa-envelope fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-12 text-center">
                <p class="mb-0 text-muted">
                    &copy; {{ date('Y') }} Universidad de Panamá - Vicerrectoría de Extensión. Todos los derechos
                    reservados.
                </p>
            </div>
        </div>
    </div>
</footer>
@stop

@section('adminlte_js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar background on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar-custom');
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(255,255,255,0.98)';
        } else {
            navbar.style.background = 'rgba(255,255,255,0.95)';
        }
    });
</script>
@stop