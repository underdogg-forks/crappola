<div class="relative px-3 py-3 mb-4 border rounded text-teal-darker border-teal-dark bg-teal-lighter" id="discountPromo">
    {{ $account->company->present()->promoMessage }} &nbsp;&nbsp;
    <a href="#" onclick="showUpgradeModal()" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light py-1 px-2 text-sm leading-tight">{{ trans('texts.plan_upgrade') }}</a>
    <a href="#" onclick="hideDiscountMessage()" class="pull-right">{{ trans('texts.hide') }}</a>
</div>

<script type="text/javascript">

    function hideDiscountMessage() {
        jQuery('#discountPromo').fadeOut();
        return false;
    }

</script>
