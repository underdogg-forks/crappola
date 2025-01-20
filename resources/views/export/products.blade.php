<tr>
    @if ($multiUser)
        <td>{{ trans('texts.user') }}</td>
    @endif
    <td>{{ trans('texts.product') }}</td>
    <td>{{ trans('texts.notes') }}</td>
    <td>{{ trans('texts.cost') }}</td>
    @if ($company->customLabel('product1'))
        <td>{{ $company->present()->customLabel('product1') }}</td>
    @endif
    @if ($company->customLabel('product2'))
        <td>{{ $company->present()->customLabel('product2') }}</td>
    @endif
</tr>

@foreach ($products as $product)
    <tr>
        @if ($multiUser)
            <td>{{ $product->present()->user }}</td>
        @endif
        <td>{{ $product->product_key }}</td>
        <td>{{ $product->notes }}</td>
        <td>{{ $product->cost }}</td>
        @if ($company->customLabel('product1'))

        @endif
        @if ($company->customLabel('product1'))
            <td>{{ $product->custom_value1 }}</td>
        @endif
        @if ($company->customLabel('product2'))
            <td>{{ $product->custom_value2 }}</td>
        @endif
    </tr>
@endforeach
