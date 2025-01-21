<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta
            name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
        />
        <title>@yield('title') | TiQPas</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <!-- Bootstrap 4.1.1 -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" />
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css"
            rel="stylesheet"
        />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css"
        />
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css" />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css"
        />
        <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet" />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css"
        />
        @yield('page_css')
        <link href="{{ mix('/style/css/style.css') }}" rel="stylesheet" type="text/css" />
        @yield('css')
    </head>
    <body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
        <header class="app-header flex flex-wrap list-reset pl-0 mb-0bar">
            <button class="flex flex-wrap list-reset pl-0 mb-0bar-toggler sidebar-toggler lg:hidden mr-auto" type="button" data-toggle="sidebar-show">
                <span class="flex flex-wrap list-reset pl-0 mb-0bar-toggler-icon"></span>
            </button>
            <a class="flex flex-wrap list-reset pl-0 mb-0bar-brand" href="#">
                <img
                    class="flex flex-wrap list-reset pl-0 mb-0bar-brand-full"
                    src="{{ asset('/img/logo-red-black.png') }}"
                    width="50px"
                    alt="Infyom Logo"
                />&nbsp;&nbsp;<span class="flex flex-wrap list-reset pl-0 mb-0bar-brand-full">InfyOm</span>
                <img
                    class="flex flex-wrap list-reset pl-0 mb-0bar-brand-minimized"
                    src="{{ asset('/img/logo-red-black.png') }}"
                    width="50px"
                    alt="InfyOm Logo"
                />
            </a>
            <button class="flex flex-wrap list-reset pl-0 mb-0bar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
                <span class="flex flex-wrap list-reset pl-0 mb-0bar-toggler-icon"></span>
            </button>
            <ul class="flex flex-wrap list-reset pl-0 mb-0 flex flex-wrap list-reset pl-0 mb-0 ml-auto">
                <li class=" relative">
                    <a
                        class="inline-block py-2 px-4 no-underline"
                        style="margin-right: 10px"
                        data-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <img src="{{ Auth::user()->image_path }}" alt="" class="img-avatar" />
                        <span class="pr-3 align-middle">{!! Auth::user()->name !!}</span>
                    </a>
                    <div class=" absolute pin-l z-50 float-left hidden list-reset	 py-2 mt-1 text-base bg-white border border-grey-light rounded dropdown-menu-right">
                        <a
                            href="#"
                            class="block w-full py-1 px-6 font-normal text-grey-darkest whitespace-no-wrap border-0 inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light btn btn-default btn-flat edit-profile"
                            data-toggle="modal"
                            data-id="1"
                        >
                            <i class="fa fa-user"></i>Profile
                        </a>
                        <a
                            href="#"
                            class="block w-full py-1 px-6 font-normal text-grey-darkest whitespace-no-wrap border-0 inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light btn btn-default btn-flat"
                            data-toggle="modal"
                            data-id="1"
                            data-target="#ChangePasswordModal"
                        >
                            <i class="fa fa-key"></i>Change Password
                        </a>
                        <a
                            class="block w-full py-1 px-6 font-normal text-grey-darkest whitespace-no-wrap border-0"
                            href="{!! url('/logout') !!}"
                            class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default btn-flat"
                            onclick="event.preventDefault(); localStorage.clear(); document.getElementById('logout-form').submit();"
                        >
                            <i class="fa fa-lock"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
            </ul>
            <button
                class="flex flex-wrap list-reset pl-0 mb-0bar-toggler aside-menu-toggler d-md-down-none"
                type="button"
                data-toggle="aside-menu-lg-show"
            >
                <span class="flex flex-wrap list-reset pl-0 mb-0bar-toggler-icon"></span>
            </button>
            <button class="flex flex-wrap list-reset pl-0 mb-0bar-toggler aside-menu-toggler lg:hidden" type="button" data-toggle="aside-menu-show">
                <span class="flex flex-wrap list-reset pl-0 mb-0bar-toggler-icon"></span>
            </button>
        </header>
        <div class="app-body">
            @include('partials.sidebar')
            <main class="main">
                @yield('content')
                {{-- @include('profile.edit_profile') @include('profile.change_password') --}}
            </main>
            @include('partials.aside')
        </div>
        {{-- @include('time_tracker.index'); @include('time_tracker.adjust_time_entry'); --}}
        <footer class="app-footer justify-content-between">
            <div>
                <a href="https://infyom.com">InfyOm </a>
                <span>&copy; 2019 - {{ date('Y') }} InfyOmLabs.</span>
            </div>
            <div>
                <span>Powered by</span>
                <a href="https://coreui.io">CoreUI</a>
            </div>
        </footer>
    </body>
    <!-- jQuery 3.1.1 -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/perfect-scrollbar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@2.1.16/dist/js/coreui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    @yield('page_js')
    <script src="{{ asset('/js/main.js') }}" type="text/javascript"></script>
    {{--
    <script>
        let loggedInUserId = '{{ getLoggedInUserId() }}';
        let storeTimeEntriesUrl = "{{route('time-entries.store')}}";
        let myTasksUrl = "{{url('my-tasks')}}";
        let myProjectsUrl = "{{url('my-projects')}}";
        let lastTaskWorkUrl = "{{url('user-last-task-work')}}";
        let closeWatchImg = "{{asset('/img/close.png')}}";
        let stopWatchImg = "{{asset('/img/stopwatch.png')}}";
        let usersUrl = "{{ url('users') }}/";
        let projectsUrl = "{{ url('projects') }}/";
        let startTimerUrl = "{{ url('start-timer') }}";
        let pusherAppKey = "{{config('broadcasting.connections.pusher.key')}}";
        let pusherAppCluster = "{{config('broadcasting.connections.pusher.options.cluster')}}";
        let pusherBroadcaster = "{{config('broadcasting.connections.pusher.driver')}}";
        let baseUrl = "{{url('/')}}/";
    </script>
    --}} {{--
    <script src="{{ mix('/js/time_tracker/time_tracker.js') }}"></script>
    --}} @yield('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    {{--
    <script src="{{ mix('/js/profile/profile.js') }}"></script>
    --}}
    <script>
        var loginUrl = '{{ route('login') }}';
        // Loading button plugin (removed from BS4)
        (function ($) {
            $.fn.button = function (action) {
                if (action === 'loading' && this.data('loading-text')) {
                    this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
                }
                if (action === 'reset' && this.data('original-text')) {
                    this.html(this.data('original-text')).prop('disabled', false);
                }
            };
        }(jQuery));
    </script>
</html>
