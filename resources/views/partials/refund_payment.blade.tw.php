<div class="modal opacity-0" id="paymentRefundModal" tabindex="-1" role="dialog" aria-labelledby="paymentRefundModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="min-width:150px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="absolute pin-t pin-b pin-r px-4 py-3" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="paymentRefundModalLabel">{{ trans('texts.refund_payment') }}</h4>
            </div>

            <div class="container mx-auto" style="width: 100%; padding-bottom: 0px !important">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-horizontal">
                            <div class="mb-4">
                                <label for="refundAmount" class="col-sm-offset-2 sm:w-1/5 pr-4 pl-4 control-label">{{ trans('texts.amount') }}</label>
                                <div class="sm:w-1/3 pr-4 pl-4">
                                    <div class="relative flex items-stretch w-full">
                                        <span class="py-1 px-2 mb-1 text-base font-normal leading-normal text-grey-darkest text-center bg-grey-light border border-4 border-grey-lighter rounded" id="refundCurrencySymbol"></span>
                                        <input type="number" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-grey-darker border border-grey rounded" id="refundAmount" name="refund_amount" step="0.01" min="0.01" placeholder="{{ trans('texts.amount') }}">
                                    </div>
                                    <div class="help-block">{{ trans('texts.refund_max') }} <span id="refundMax"></span></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="col-sm-offset-2 sm:w-1/5 pr-4 pl-4 control-label"></label>
                                <div class="sm:w-1/2 pr-4 pl-4">
                                    <div class="relative flex items-stretch w-full">
                                        {!! Former::checkbox('refund_email')->text('send_email_to_client')->raw() !!}
                                    </div>
                                </div>
                            </div><br/>

                            <div id="refundLocalWarning" class="text-grey">
                                {{ trans('texts.warning_local_refund') }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 2px">
                <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default" data-dismiss="modal">{{ trans('texts.cancel') }}</button>
                <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light" id="completeRefundButton">{{ trans('texts.refund') }}</button>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">
var paymentId = null;
function showRefundModal(id, amount, formatted, symbol, local) {
    paymentId = id;
    $('#refundCurrencySymbol').text(symbol);
    $('#refundMax').text(formatted);
    $('#refundAmount').val(amount).attr('max', amount);
    $('#refundLocalWarning').toggle(!!local);
    $('#paymentRefundModal').modal('show');
}

function onRefundClicked(){
    $('#completeRefundButton').prop('disabled', true);
    submitForm_payment('refund', paymentId);
}

function onRefundEmailChange() {
    if (! isStorageSupported()) {
        return;
    }
    var checked = $('#refund_email').is(':checked');
    localStorage.setItem('last:send_refund_email', checked ? true : '');
}

$(function() {
    $('#completeRefundButton').click(onRefundClicked);
    $('#refund_email').click(onRefundEmailChange);

    if (isStorageSupported()) {
        if (localStorage.getItem('last:send_refund_email')) {
            $('#refund_email').prop('checked', true);
        }
    }
})

</script>
