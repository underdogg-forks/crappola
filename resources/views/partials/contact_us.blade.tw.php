{!! Former::vertical_open()
        ->onsubmit('return onContactUsFormSubmit()')
        ->addClass('contact-us-form')
        ->rules([
            'contact_us_from' => 'required',
            'contact_us_message' => 'required',
        ]) !!}

<div class="modal opacity-0" id="contactUsModal" tabindex="-1" role="dialog" aria-labelledby="contactUsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="absolute pin-t pin-b pin-r px-4 py-3" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">{{ trans('texts.contact_us') }}</h4>
      </div>

      <div class="container mx-auto" style="width: 100%; padding-bottom: 0px !important">
      <div class="panel panel-default">
      <div class="panel-body">
          <div class="input-div">
              {!! Former::plaintext('contact_us_from')
                    ->label('from')
                    ->value(Auth::user()->present()->email) !!}

              {!! Former::textarea('contact_us_message')
                    ->label('message')
                    ->rows(10) !!}

                @if (! Utils::isNinjaProd())
                    {!! Former::checkbox('include_errors')->label(false)
                        ->text(trans('texts.include_errors_help', [
                            'link' => link_to('/errors', trans('texts.recent_errors'), ['target' => '_blank'])
                        ])) !!}
                @endif
          </div>
          <div class="response-div" style="display: none; font-size: 16px">
              {{ trans('texts.contact_us_response') }}
          </div>
      </div>
      </div>
      </div>

      <div class="modal-footer">
        <div class="input-div">
            <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default" data-dismiss="modal">{{ trans('texts.cancel') }}</button>
            <button type="submit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-green-lightest bg-green hover:bg-green-light">{{ trans('texts.submit') }}</button>
        </div>
        <div class="response-div" style="display: none;">
            <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default" data-dismiss="modal">{{ trans('texts.close') }}</button>
        </div>
      </div>
    </div>
  </div>
</div>

{!! Former::close() !!}

<script type="text/javascript">

    function showContactUs() {
        $('#contactUsModal').modal('show');
    }

    $(function() {
        $('#contactUsModal').on('shown.bs.modal', function() {
            var message = '';
            @if (! Utils::isNinjaProd())
                message = '\n\n' + "{{ Utils::getDebugInfo() }}";
            @endif
            $('#contactUsModal .input-div').show();
            $('#contactUsModal .response-div').hide();
            $("#contact_us_message").val(message).focus().selectRange(0, 0);
        })
    })

    function onContactUsFormSubmit() {
        $('#contactUsModal .modal-footer button').attr('disabled', true);

        $.post("{{ url('/contact_us') }}", $('.contact-us-form').serialize(), function(data) {
            $('#contactUsModal .input-div').hide();
            $('#contactUsModal .response-div').show();
            $('#contact_us_message').val('');
            $('#contactUsModal .modal-footer button').attr('disabled', false);
        }).fail(function(data) {
            $('#contactUsModal .modal-footer button').attr('disabled', false);
        });

        return false;
    }

</script>
