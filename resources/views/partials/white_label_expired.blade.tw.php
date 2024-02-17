<div class="relative px-3 py-3 mb-4 border rounded text-teal-darker border-teal-dark bg-teal-lighter" id="whiteLabelExpired">
    {{ trans('texts.white_label_expired') }} &nbsp;&nbsp;
    <a href="#" onclick="buyWhiteLabel()" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light py-1 px-2 text-sm leading-tight">{{ trans('texts.renew_license') }}</a>
    <a href="#" onclick="hideWhiteLabelExpiredMessage()" class="pull-right">{{ trans('texts.hide') }}</a>
</div>

<script type="text/javascript">
    function hideWhiteLabelExpiredMessage() {
        jQuery('#whiteLabelExpired').fadeOut();
        $.get('/white_label/hide_message', function(response) {
            console.log('Reponse: %s', response);
        });
        return false;
    }
</script>
