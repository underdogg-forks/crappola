{!! Button::normal(trans('texts.help'))
    ->appendIcon(Icon::create('question-sign'))
    ->withAttributes(['onclick' => 'showProposalHelp()']) !!}

<script>

function showProposalHelp() {
    $('#proposalHelpModal').modal('show');
}

</script>

<div class="modal opacity-0" id="proposalHelpModal" tabindex="-1" role="dialog" aria-labelledby="proposalHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="text-align:left">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="absolute pin-t pin-b pin-r px-4 py-3" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="proposalHelpModalLabel">{{ trans('texts.help') }}</h4>
            </div>

            <div class="container mx-auto" style="width: 100%; padding-bottom: 0px !important">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @include('partials/variables_help', ['entityType' => ENTITY_QUOTE, 'account' => auth()->user()->account])
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default" data-dismiss="modal">{{ trans('texts.close') }}</button>
                <!-- <a class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light" href="{{ config('ninja.video_urls.custom_design') }}" target="_blank">{{ trans('texts.video') }}</a> -->
            </div>

        </div>
    </div>
</div>
