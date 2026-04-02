<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kynova Pharma </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="icon" type="image/png" href="{{ asset('/public/img/KynovaPharma.png') }}">
    <link rel="preload" href="./public/admin/dist/css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->
    <!--begin::Fonts-->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media='all'" /> --}}
    <link rel="stylesheet" href="{{ asset('public/css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/overlayscrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/jsvectormap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/jsvectormap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/dataTables.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/select2.min.css') }}">

    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" /> --}}
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" /> --}}
    <link rel="stylesheet" href="./public/admin/dist/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
    <!-- apexcharts -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" /> --}}
    <!-- jsvectormap -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous" /> --}}
    {{-- <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css"> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"> --}}
    <script href="{{ asset('public/js/jquery-3.6.0.js') }}" crossorigin="anonymous"></script>
    <script href="{{ asset('public/js/jquery-ui.js') }}"></script>
    <script href="{{ asset('public/js/chart.js') }}"></script>
    <script href="{{ asset('public/js/dataTables.js') }}"></script>
    <script href="{{ asset('public/js/select2.min.js') }}"></script>
    <script src="{{ asset('public/plugins/js_ex/jquery-3.6.0.js') }}"></script>
    <script src="{{ asset('public/plugins/js_ex/jquery-ui.js') }}"></script>
    <script src="{{ asset('public/plugins/js_ex/chart.js') }}"></script>
    <script src="{{ asset('public/plugins/js_ex/dataTables.js') }}"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.js"></script> --}}
    {{-- <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" /> --}}
    {{-- <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script> --}}
    //sampe sini
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
    <link rel="stylesheet"
        href="{{ asset('public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }} ">
    <link rel="stylesheet"
        href="{{ asset('public/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <style>
        #my {
            zoom: 90%;
        }

        .mr-2 {
            margin-right: 5px;
        }

        .preloader2 {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: #fff;
            opacity: 0.9;
        }

        .preloader2 .loading {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font: 14px arial;
        }

        .datepicker {
            z-index: 1600 !important;
            /* has to be larger than 1050 */
        }

        .modal {
            overflow: auto !important;
        }

        /* Untuk DataTables standar */
        .dataTables_paginate {
            margin-top: 20px !important;
            padding-top: 10px;
        }

        /* Jika menggunakan Bootstrap */
        .pagination {
            margin-top: 20px !important;
        }

        .dataTables_filter {
            margin-bottom: 15px;
            /* Sesuaikan jaraknya */
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="preloader2" id="loader2">
        <div class="loading">
            <img src="./public/img/fb.gif" width="80">
            <p>Harap Tunggu</p>
        </div>
    </div>
    <div class="app-wrapper">
        {{-- navbar --}}
        @include('Template.Navbar')
        @include('Template.Sidebar')
        <!--end::Sidebar-->
        <!--begin::App Main-->
        <main class="app-main">
            @yield('container')
        </main>
        @include('Template.Footer')
