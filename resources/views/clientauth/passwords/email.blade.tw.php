@extends('client_login')

@section('form')
    @include('partials.warn_session', ['redirectTo' => '/client/session_expired'])
    <div class="container mx-auto">
        {!! Former::open()
                ->rules(['email' => 'required|email'])
                ->addClass('form-signin') !!}

        <h2 class="form-signin-heading">{{ trans('texts.password_recovery') }}</h2>
        <hr class="green">

        @if (count($errors->all()))
            <div class="relative px-3 py-3 mb-4 border rounded text-red-darker border-red-dark bg-red-lighter">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="relative px-3 py-3 mb-4 border rounded text-teal-darker border-teal-dark bg-teal-lighter">
                {{ session('status') }}
            </div>
        @endif

        <!-- if there are login errors, show them here -->
        @if (Session::has('warning'))
            <div class="relative px-3 py-3 mb-4 border rounded text-yellow-darker border-yellow-dark bg-yellow-lighter">{{ Session::get('warning') }}</div>
        @endif

        @if (Session::has('message'))
            <div class="relative px-3 py-3 mb-4 border rounded text-teal-darker border-teal-dark bg-teal-lighter">{{ Session::get('message') }}</div>
        @endif

        @if (Session::has('error'))
            <div class="relative px-3 py-3 mb-4 border rounded text-red-darker border-red-dark bg-red-lighter">{{ Session::get('error') }}</div>
        @endif

        <div>
            {!! Former::text('email')->placeholder(trans('texts.email_address'))->raw() !!}
        </div>
        {!! Button::success(trans('texts.send_email'))
                    ->withAttributes(['class' => 'green'])
                    ->large()->submit()->block() !!}

        <div class="flex flex-wrap meta">
            <div class="md:w-full pr-4 pl-4 sm:w-full pr-4 pl-4" style="text-align:center;padding-top:8px;">
                {!! link_to('/client/login' . (request()->account_key ? '?account_key=' . request()->account_key : ''), trans('texts.return_to_login')) !!}
            </div>
        </div>

        {!! Former::close() !!}
    </div>

    <script type="text/javascript">
        $(function() {
            $('.form-signin').submit(function() {
                $('button.btn-success').prop('disabled', true);
            });
        })
    </script>

@endsection
