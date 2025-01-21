@if ($message == strip_tags($message))
    <div class="relative px-3 py-3 mb-4 border rounded text-yellow-darker border-yellow-dark bg-yellow-lighter custom-message">{!! nl2br(Utils::isNinja() ? HTMLUtils::sanitizeHTML($message) : $message) !!}</div>    
@else
    {!! Utils::isNinja() ? HTMLUtils::sanitizeHTML($message) : $message !!}
@endif
