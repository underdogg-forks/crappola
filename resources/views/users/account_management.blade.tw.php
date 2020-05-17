@extends('header')

@section('content')

<center>
    @if (!session(SESSION_USER_ACCOUNTS) || count(session(SESSION_USER_ACCOUNTS)) < 5)
        {!! Button::success(trans('texts.add_company'))->withAttributes(['onclick' => 'showSignUp()']) !!}
    @endif
</center>

<p>&nbsp;</p>

<div class="flex flex-wrap">
    <div class="md:w-1/2 pr-4 pl-4 col-md-offset-3">
    </div>
</div>

<div class="flex flex-wrap">
    <div class="md:w-1/2 pr-4 pl-4 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-body">
            <table class="w-full max-w-full mb-4 bg-transparent table-striped">
            @foreach (Session::get(SESSION_USER_ACCOUNTS) as $account)
                <tr>
                    <td>
                    @if (isset($account->logo_url))
                        {!! HTML::image($account->logo_url.'?no_cache='.time(), 'Logo', ['width' => 100]) !!}
                    @endif
                    </td>
                    <td>
                        <h3>{{ $account->account_name }}<br/>
                        <small>{{ $account->user_name }}
                        </small></h3>
                    </td>
                    <td>
                        @if ($account->user_id == Auth::user()->id)
                            <b>{{ trans('texts.logged_in')}}</b>
                        @else
                            {{-- Button::primary(trans('texts.unlink'))->withAttributes(['onclick'=>"return showUnlink({$account->id}, {$account->user_id})"]) --}}
                        @endif
                    </td>
                </tr>
            @endforeach
            </table>
            </div>
        </div>
    </div>
</div>

<div class="modal opacity-0" id="unlinkModal" tabindex="-1" role="dialog" aria-labelledby="unlinkModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="absolute pin-t pin-b pin-r px-4 py-3" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">{{ trans('texts.unlink_account') }}</h4>
      </div>

      <div class="container mx-auto">
        <h3>{{ trans('texts.are_you_sure') }}</h3>
      </div>

      <div class="modal-footer" id="signUpFooter">
        <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline btn-default" data-dismiss="modal">{{ trans('texts.cancel') }}</button>
        <button type="button" class="inline-block align-middle text-center select-none border font-normal whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-blue-lightest bg-blue hover:bg-blue-light" onclick="unlinkAccount()">{{ trans('texts.unlink') }}</button>
      </div>
    </div>
  </div>
</div>


    <script type="text/javascript">
      function showUnlink(userAccountId, userId) {
        NINJA.unlink = {
            'userAccountId': userAccountId,
            'userId': userId
        };
        $('#unlinkModal').modal('show');
        return false;
      }

      function unlinkAccount() {
        window.location = '{{ URL::to('/unlink_account') }}' + '/' + NINJA.unlink.userAccountId + '/' + NINJA.unlink.userId;
      }

    </script>

@stop
