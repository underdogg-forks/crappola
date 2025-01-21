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
            <h4>Awesome! Please select the company you would like to apply migration.</h4>
            <form action="/migration/companies" method="post" id="auth-form">
                {{ csrf_field() }}
                <input type="hidden" name="account_key" value="{{ auth()->user()->account->account_key }}">
                    
                @foreach($companies as $company)
                <div class="relative block mb-2">
                    <input class="absolute mt-1 -ml-6" id="company_{{ $company->id }}" type="checkbox" name="companies[{{ $company->id }}][id]" id="company1" value="{{ $company->id }}" checked>
                    <label class="text-grey-dark pl-6 mb-0" for="company_{{ $company->id }}">
                        Name: {{ $company->settings->name }} ID: {{ $company->id }}
                    </label>
                </div>
                <div class="mb-4">
                    <input type="checkbox" name="companies[{{ $company->id }}][force]">
                    <label for="force">Force migration</label>
                    <small>* All current company data will be wiped.</small>
                </div>
                @endforeach
            </form>
        </div>
        <div class="panel-footer text-right">
            <button onclick="document.getElementById('auth-form').submit();" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light">{!! trans('texts.continue') !!}</button>
        </div>
    </div>
@stop