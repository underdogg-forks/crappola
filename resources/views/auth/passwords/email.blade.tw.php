@extends('login')

@section('form')
<div class="container mx-auto">

{!! Former::open('recover_password')->rules(['email' => 'required|email'])->addClass('form-signin') !!}

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
    {!! Button::success(trans('texts.send_email'))->large()->submit()->withAttributes(['class' => 'green'])->block() !!}

    {!! Former::close() !!}

</div>

<script type="text/javascript">
    $(function() {
        $('#email').focus();

        $('.form-signin').submit(function() {
            $('button.btn-success').prop('disabled', true);
        });

    })
</script>

@stop
