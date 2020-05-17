<div class="modal opacity-0" id="custom{{ $number }}GatewayModal" tabindex="-1" role="dialog" aria-labelledby="custom{{ $number }}GatewayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="absolute pin-t pin-b pin-r px-4 py-3" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ $customGateway->getConfigField('name') }}</h4>
            </div>
            <div class="panel-body">
                {!! $customGateway->getCustomHtml($invitation) !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default" data-dismiss="modal">{{ trans('texts.close') }}</button>
            </div>
        </div>
    </div>
</div>
