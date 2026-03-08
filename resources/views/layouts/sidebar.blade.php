@section('sidebar')
    <div id="page-container"
        class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
        <!-- Side Overlay-->
        <aside id="side-overlay">
            <!-- Side Header -->
            <div class="bg-image"
                style="background-image: url('{{ asset('public/dashboard_assets/media/various/bg_side_overlay_header.jpg') }}');">
                <div class="  " style="background: #0c0e10!important;">
                    <div class="content-header">
                        <!-- User Avatar -->
                        <a class="img-link mr-1" href="be_pages_generic_profile.html">
                            <img class="img-avatar img-avatar48" src="assets/media/avatars/avatar10.jpg" alt="">
                        </a>
                        <!-- END User Avatar -->

                        <!-- User Info -->
                        <div class="ml-2">
                            <a class="text-grey font-w600" href="be_pages_generic_profile.html">George Taylor</a>
                            <div class="text-grey-75 font-size-sm">Full Stack Developer</div>
                        </div>
                        <!-- END User Info -->

                        <!-- Close Side Overlay -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <a class="ml-auto text-grey" href="javascript:void(0)" data-toggle="layout"
                            data-action="side_overlay_close">
                            <i class="fa fa-times-circle"></i>
                        </a>
                        <!-- END Close Side Overlay -->
                    </div>
                </div>
            </div>
            <!-- END Side Header -->

            <!-- Side Content -->
            <div class="content-side" style="background: #0c0e10">
                <!-- Side Overlay Tabs -->
                <div class="block block-transparent pull-x pull-t">
                    <ul class="nav nav-tabs nav-tabs-block nav-justified" data-toggle="tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#so-settings">
                                <i class="fa fa-fw fa-cog"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#so-people">
                                <i class="far fa-fw fa-user-circle"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#so-profile">
                                <i class="far fa-fw fa-edit"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="block-content tab-content overflow-hidden">
                        <!-- Settings Tab -->
                        <div class="tab-pane pull-x fade fade-up show active" id="so-settings" role="tabpanel">
                            <div class="block mb-0">
                                <!-- Color Themes -->
                                <!-- Toggle Themes functionality initialized in Template._uiHandleTheme() -->


                                <!-- Sidebar -->
                                <div class="block-content block-content-sm block-content-full bg-body">
                                    <span class="text-uppercase font-size-sm font-w700">Sidebar</span>
                                </div>
                                <div class="block-content block-content-full">
                                    <div class="row gutters-tiny text-center">
                                        <div class="col-6 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="sidebar_style_dark" href="javascript:void(0)">Dark</a>
                                        </div>
                                        <div class="col-6 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="sidebar_style_light" href="javascript:void(0)">Light</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- END Sidebar -->

                                <!-- Header -->
                                <div class="block-content block-content-sm block-content-full bg-body">
                                    <span class="text-uppercase font-size-sm font-w700">Header</span>
                                </div>
                                <div class="block-content block-content-full">
                                    <div class="row gutters-tiny text-center mb-2">
                                        <div class="col-6 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="header_style_dark" href="javascript:void(0)">Dark</a>
                                        </div>
                                        <div class="col-6 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="header_style_light" href="javascript:void(0)">Light</a>
                                        </div>
                                        <div class="col-6 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="header_mode_fixed" href="javascript:void(0)">Fixed</a>
                                        </div>
                                        <div class="col-6 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="header_mode_static" href="javascript:void(0)">Static</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- END Header -->

                                <div class="block-content block-content-sm block-content-full bg-body">
                                    <span class="text-uppercase font-size-sm font-w700">Content</span>
                                </div>
                                <div class="block-content block-content-full">
                                    <div class="row gutters-tiny text-center">
                                        <div class="col-6 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="content_layout_boxed" href="javascript:void(0)">Boxed</a>
                                        </div>
                                        <div class="col-6 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="content_layout_narrow" href="javascript:void(0)">Narrow</a>
                                        </div>
                                        <div class="col-12 mb-1">
                                            <a class="d-block py-3 bg-body-dark font-w600 text-dark" data-toggle="layout"
                                                data-action="content_layout_full_width" href="javascript:void(0)">Full
                                                Width</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- END Content -->

                                <!-- Layout API -->
                                <div class="block-content row justify-content-center border-top">
                                    <div class="col-9">
                                        <a class="btn btn-block btn-hero-primary" href="be_layout_api.html">
                                            <i class="fa fa-fw fa-flask mr-1"></i> Layout API
                                        </a>
                                    </div>
                                </div>
                                <!-- END Layout API -->
                            </div>
                        </div>
                        <!-- END Settings Tab -->

                        <!-- People -->
                        <div class="tab-pane pull-x fade fade-up" id="so-people" role="tabpanel">
                            <div class="block mb-0">


                            </div>
                        </div>
                        <!-- END People -->

                        <!-- Profile -->
                        <div class="tab-pane pull-x fade fade-up" id="so-profile" role="tabpanel">

                        </div>
                        <!-- END Profile -->
                    </div>
                </div>
                <!-- END Side Overlay Tabs -->
            </div>
            <!-- END Side Content -->
        </aside>
        <!-- END Side Overlay -->

        <!-- Sidebar -->
        <nav id="sidebar" aria-label="Main Navigation" style="background: #21263C;box-shadow:0px 0px 10px black">
            <!-- Side Header -->
            <div class=" " style="background:#588CB7!important ;border: 1px solid #588CB7; ">
                <div class="content-header  ">
                    <!-- Logo -->
                    <a class="font-w600 text-grey tracking-wide" href="{{ url('/') }}">
                        <span class="smini-visible">
                            S<span class="opacity-75">x</span>
                        </span>
                        <span class="smini-hidden">
                            <img src="{{ asset('public/logo/white-text-logo.png') }}" alt="Kumon"
                                style="height: auto; width: 190px;">
                        </span>
                    </a>
                    <!-- END Logo -->

                    <!-- Options -->
                    <div>
                        <a class="d-lg-none text-grey ml-2" data-toggle="layout" data-action="sidebar_close"
                            href="javascript:void(0)">
                            <i class="fa fa-times-circle"></i>
                        </a>
                        <!-- END Close Sidebar -->
                    </div>
                    <!-- END Options -->
                </div>
            </div>
            <!-- END Side Header -->

            <!-- Sidebar Scrolling -->
            <div class="js-sidebar-scroll">
                <!-- Side Navigation -->
                <div class="content-side">
                    <ul class="nav-main">

                        <li class="nav-main-item">
                            <a class="nav-main-link   {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                <img class="nav-main-link-icon " src="{{ asset('public/img/icon-dashboard-silver.png') }}"
                                    width="20px" data-src="{{ asset('public/img/icon-dashboard-white.png') }}">
                                <span class="nav-main-link-name">Dashboard</span>
                            </a>
                        </li>


                        @auth
                            @if (Auth::user()->role == 'admin')
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('users') ? 'active' : '' }}"
                                        href="{{ url('/users') }}">
                                        <img class="nav-main-link-icon " src="{{ asset('public/img/icon-user-silver.png') }}"
                                            width="20px" data-src="{{ asset('public/img/icon-user-white.png') }}">
                                        <span class="nav-main-link-name">Users</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('clients') ? 'active' : '' }}"
                                        href="{{ url('/clients') }}">
                                        <img class="nav-main-link-icon " src="{{ asset('public/icons/menu-client-grey.png?C') }}"
                                            width="20px" data-src="{{ asset('public/icons/menu-client-white.png?C') }}">
                                        <span class="nav-main-link-name">Clients</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('vacations') ? 'active' : '' }}"
                                        href="{{ url('/vacations') }}">
                                        <img class="nav-main-link-icon " src="{{ asset('public/icons/menu-client-grey.png?C') }}"
                                            width="20px" data-src="{{ asset('public/icons/menu-client-white.png?C') }}">
                                        <span class="nav-main-link-name">Vacations</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('payments') ? 'active' : '' }}"
                                        href="{{ url('/payments') }}">
                                        <img class="nav-main-link-icon " src="{{ asset('public/icons/menu-client-grey.png?C') }}"
                                            width="20px" data-src="{{ asset('public/icons/menu-client-white.png?C') }}">
                                        <span class="nav-main-link-name">Payments</span>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->role == 'staff')
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('clients') ? 'active' : '' }}"
                                        href="{{ url('/clients') }}">
                                        <img class="nav-main-link-icon " src="{{ asset('public/icons/menu-client-grey.png?C') }}"
                                            width="20px" data-src="{{ asset('public/icons/menu-client-white.png?C') }}">
                                        <span class="nav-main-link-name">Clients</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('vacations') ? 'active' : '' }}"
                                        href="{{ url('/vacations') }}">
                                        <img class="nav-main-link-icon " src="{{ asset('public/icons/menu-client-grey.png?C') }}"
                                            width="20px" data-src="{{ asset('public/icons/menu-client-white.png?C') }}">
                                        <span class="nav-main-link-name">Vacations</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('payments') ? 'active' : '' }}"
                                        href="{{ url('/payments') }}">
                                        <img class="nav-main-link-icon " src="{{ asset('public/icons/menu-client-grey.png?C') }}"
                                            width="20px" data-src="{{ asset('public/icons/menu-client-white.png?C') }}">
                                        <span class="nav-main-link-name">Payments</span>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::user()->role == 'parent')
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('vacations') ? 'active' : '' }}"
                                        href="{{ url('/vacations') }}">
                                        <img class="nav-main-link-icon " src="{{ asset('public/icons/menu-client-grey.png?C') }}"
                                            width="20px" data-src="{{ asset('public/icons/menu-client-white.png?C') }}">
                                        <span class="nav-main-link-name">Vacations</span>
                                    </a>
                                </li>
                            @endif
                        @endif
                    </div>
                </div>
                <!-- END Sidebar Scrolling -->
            </nav>
            <!-- END Sidebar -->
        @endsection('sidebar')
