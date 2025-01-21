@extends('header')

@section('content')
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="flex flex-wrap">
                <div class="lg:w-full pr-4 pl-4">
                    <div class="relative px-3 py-3 mb-4 border rounded text-yellow-darker border-yellow-dark bg-yellow-lighter" role="alert">
                        <strong>
                            {{ trans('texts.warning') }}:
                        </strong>
                        {{ trans('texts.update_invoiceninja_warning') }}
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap">
                <div class="lg:w-full pr-4 pl-4">
                    @if(!$updateAvailable)
                        {{ trans('texts.update_invoiceninja_unavailable') }}
                    @else
                        <strong>
                            {{ trans('texts.update_invoiceninja_available') }}
                        </strong>
                        <br/>
                        {!! trans('texts.update_invoiceninja_instructions', ['version' => $versionAvailable]) !!}
                    @endif
                </div>
            </div>
            @if($updateAvailable)
            <div class="flex flex-wrap">
                <div class="lg:w-full pr-4 pl-4">
                    <br/>
                    <form name="download-update-form" action="{{ url('self-update') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="action" id="update-action" value="update"/>
                        <div class="mb-4">
                            <button type="submit" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light" id="do-updade">
                                {{ trans('texts.update_invoiceninja_update_start') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
    <script type="text/javascript">
        $('#download-update').click(function (){
            $('#update-action').val('download');
        });
        $('#do-update').click(function (){
            $('#update-action').val('update');
        });
    </script>
@endsection