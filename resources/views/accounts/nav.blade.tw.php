@if (!Utils::isPro() && isset($advanced) && $advanced)
    <div class="relative px-3 py-3 mb-4 border rounded text-yellow-darker border-yellow-dark bg-yellow-lighter" style="font-size:larger;">
    <center>
        {!! trans('texts.pro_plan_advanced_settings', ['link'=>'<a href="javascript:showUpgradeModal()">' . trans('texts.pro_plan_remove_logo_link') . '</a>']) !!}
    </center>
    </div>
@endif

<script type="text/javascript">
    $(function() {
        if (isStorageSupported() && /\/settings\//.test(location.href)) {
            localStorage.setItem('last:settings_page', location.href);
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            if (history.pushState) {
                history.pushState(null, null, target);
            }
        });

    })
</script>

<div class="flex flex-wrap">

    <div class="md:w-1/4 pr-4 pl-4">
        @foreach([
            BASIC_SETTINGS => \App\Models\Account::$basicSettings,
            ADVANCED_SETTINGS => \App\Models\Account::$advancedSettings,
        ] as $type => $settings)
            <div class="panel panel-default">
                <div class="panel-heading" style="color:white">
                    {{ trans("texts.{$type}") }}
                    @if ($type === ADVANCED_SETTINGS && ! Utils::isPaidPro())
                        <sup>{{ strtoupper(trans('texts.pro')) }}</sup>
                    @endif
                </div>
                <div class="flex flex-col pl-0 mb-0 border rounded border-grey-light">
                    @foreach ($settings as $section)
                        @if ($section != ACCOUNT_USER_DETAILS || auth()->user()->registered)
                            <a href="{{ URL::to("settings/{$section}") }}" class="relative block py-3 px-6 -mb-px border border-r-0 border-l-0 border-grey-light no-underline {{ $selected === $section ? 'selected' : '' }}"
                                style="width:100%;text-align:left">{{ trans("texts.{$section}") }}</a>
                        @endif
                    @endforeach
                    @if ($type === ADVANCED_SETTINGS && !Utils::isNinjaProd())
                        <a href="{{ URL::to("settings/system_settings") }}" class="relative block py-3 px-6 -mb-px border border-r-0 border-l-0 border-grey-light no-underline {{ $selected === 'system_settings' ? 'selected' : '' }}"
                            style="width:100%;text-align:left">{{ trans("texts.system_settings") }}</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="md:w-3/4 pr-4 pl-4">
