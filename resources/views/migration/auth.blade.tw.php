@extends('header')

@section('content')
    @parent
    @include('accounts.nav', ['selected' => ACCOUNT_MANAGEMENT])

    @include('migration.includes.errors')

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{!! trans('texts.welcome_to_the_new_version') !!}</h3>
        </div>
        <div class="panel-body">
            <h4>Let's continue with authentication.</h4><br/>
            <form action="/migration/auth" method="post" id="auth-form">
                {{ csrf_field() }}
                <div class="mb-4">
                    <label for="email">E-mail address</label>
                    <input type="email" name="email" class="form block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded">
                </div>

                <div class="mb-4">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded">
                </div>
            </form>
        </div>
        <div class="panel-footer text-right">
            <button onclick="document.getElementById('auth-form').submit();" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light">{!! trans('texts.continue') !!}</button>
        </div>
    </div>
@stop
