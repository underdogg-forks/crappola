@extends('payments.credit_card')

@section('head')
    @parent

    @if ($companyGateway->getPublishableKey())
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script type="text/javascript" src="https://js.stripe.com/v3/"></script>

    @endif
@stop
