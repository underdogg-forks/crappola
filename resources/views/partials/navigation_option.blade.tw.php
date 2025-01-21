<li class="nav-{{ $option }} {{ Request::is("{$option}*") ? 'active' : '' }}">

    @if ($option == 'settings')
        <a type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default py-1 px-2 text-sm leading-tight pull-right" title="{{ Utils::getReadableUrl(request()->path()) }}"
            href="{{ Utils::getDocsUrl(request()->path()) }}" target="_blank">
            <i class="fa fa-info-circle" style="width:20px"></i>
        </a>
    @elseif ($option == 'reports')
        <a type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default py-1 px-2 text-sm leading-tight pull-right" title="{{ trans('texts.calendar') }}"
            href="{{ url('/reports/calendar') }}">
            <i class="fa fa-calendar" style="width:20px"></i>
        </a>
    @elseif ($option == 'dashboard')

    @elseif (Auth::user()->can('create', $option) || Auth::user()->can('create', substr($option, 0, -1)))
        <a type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light py-1 px-2 text-sm leading-tight pull-right"
            href="{{ url("/{$option}/create") }}">
            <i class="fa fa-plus-circle" style="width:20px" title="{{ trans('texts.create_new') }}"></i>
        </a>
    @endif

    <a href="{{ url($option == 'recurring' ? 'recurring_invoice' : $option) }}"
        style="padding-top:6px; padding-bottom:6px"
        class="inline-block py-2 px-4 no-underline {{ Request::is("{$option}*") ? 'active' : '' }}">
        <i class="fa fa-{{ empty($icon) ? \App\Models\EntityModel::getIcon($option) : $icon }}" style="width:46px; padding-right:10px"></i>
        {{ ($option == 'recurring_invoices') ? trans('texts.recurring') : mtrans($option) }}
        {!! Utils::isTrial() && in_array($option, ['reports']) ? '&nbsp;<sup>' . trans('texts.pro') . '</sup>' : '' !!}
    </a>

</li>
