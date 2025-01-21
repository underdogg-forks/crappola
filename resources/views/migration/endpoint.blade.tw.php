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
            <h4>We need to know the link of your application.</h4><br/>
            <form action="/migration/endpoint" method="post" id="input-endpoint-form">
                {{ csrf_field() }}
                <div class="relative block mb-2">
                    <div class="mb-4">
                        <label for="endpoint">Link</label>
                        <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" name="endpoint" required placeholder="Example: https://myinvoiceninja.com">
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer text-right">
            <button onclick="document.getElementById('input-endpoint-form').submit();" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light">{!! trans('texts.continue') !!}</button>
        </div>
    </div>

@stop
