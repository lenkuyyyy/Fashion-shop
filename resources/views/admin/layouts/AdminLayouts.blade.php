<!doctype html>
<html lang="en">
<!--begin::Head-->
@include('admin.layouts.head')
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        @include('admin.layouts.header')
        <!--end::Header-->
        <!--begin::Sidebar-->
        {{-- PHẦN NÀY LÀ MENNU BÊN TRÁI --}}
        @include('admin.layouts.aside')
        <!--end::Sidebar-->
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            @include('admin.layouts.page_title')
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Content-->
                @yield('content')
                <!--end::Content-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        @include('admin.layouts.footer')
        <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    @include('admin.layouts.scripts')
    <!--end::Script-->
    @yield('scripts')
</body>
<!--end::Body-->

</html>
