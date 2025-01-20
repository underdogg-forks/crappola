@if ($company->hasLogo())
    @if ($company->website)
        <a href="{{ $company->website }}" style="color: #19BB40; text-decoration: underline;">
            @endif

            <img src="{{ isset($message) ? $message->embed($company->getLogoPath()) : 'cid:' . $company->getLogoName() }}"
                 height="50" style="height:50px; max-width:140px; margin-left: 33px; padding-top: 2px" alt=""/>

            @if ($company->website)
        </a>
    @endif
@endif
