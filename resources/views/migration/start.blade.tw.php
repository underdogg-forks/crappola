@extends('header')

@section('content')
    @parent
    @include('accounts.nav', ['selected' => ACCOUNT_MANAGEMENT])

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{!! trans('texts.welcome_to_the_new_version') !!}</h3>
        </div>
        <div class="panel-body">
            <h4>In order to start the migration, we need to know where do you want to migrate.</h4><br/>
            <form action="/migration/type" method="post" id="select-type-form">
                {{ csrf_field() }}
                <!-- <div class="relative block mb-2">
                    <input class="absolute mt-1 -ml-6" type="radio" name="option" id="option1" value="0" checked>
                    <label class="text-grey-dark pl-6 mb-0" for="option1">
                        Hosted
                    </label>
                    <p>If you chose 'hosted', we will migrate your data to official Invoice Ninja servers & take care of server handling.</p>
                </div> -->
                    <div class="relative block mb-2">
                    <input class="absolute mt-1 -ml-6" type="radio" name="option" id="option2" value="1" checked">
                    <label class="text-grey-dark pl-6 mb-0" for="option2">
                        Self-hosted
                    </label>
                    <p>By choosing the 'self-hosted', you are the one in charge of servers.</p>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer text-right">
            <button onclick="document.getElementById('select-type-form').submit();" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light">{!! trans('texts.continue') !!}</button>
        </div>
    </div>

@stop
