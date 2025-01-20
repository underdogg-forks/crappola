@extends('payments.payment_method')

@section('head')
    @parent

    @if (isset($companyGateway) && $companyGateway->getPlaidEnabled())
        <a href="https://plaid.com/products/auth/" target="_blank" style="display:none" id="secured_by_plaid">
        <img src="{{ URL::to('images/plaid-logowhite.svg') }}">{{ trans('texts.secured_by_plaid') }}</a>
        <script src="https://cdn.plaid.com/link/stable/link-initialize.js"></script>
    @endif

@stop
