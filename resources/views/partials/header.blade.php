@extends('master')


@section('head')

  <link href="//fonts.googleapis.com/css?family=Roboto:400,700,900,100|Roboto+Slab:400,300,700&subset=latin,latin-ext" rel="stylesheet" type="text/css">
  <link href="{{ asset('css/built.css') }}?no_cache={{ NINJA_VERSION }}" rel="stylesheet" type="text/css"/>

  <style type="text/css">

    body {
      background-color: #EEEEEE;
      padding-top: 114px;
    }

    /* Fix for header covering stuff when the screen is narrower */
    @media screen and (min-width: 1200px) {
      body {
        padding-top: 56px;
      }
    }

    @media screen and (max-width: 768px) {
      body {
        padding-top: 56px;
      }
    }

    @if (Auth::check() && Auth::user()->dark_mode)
        body {
            background: #000 !important;
            color: white !important;
        }

        .panel-body {
            background: #272822 !important;
            /*background: #e6e6e6 !important;*/
        }

        .panel-default {
            border-color: #444;
        }
    @endif

  </style>

<script type="text/javascript">

  function setTheme(id)
  {
    $('#theme_id').val(id);
    $('form.themeForm').submit();
  }

  function handleSignedUp() {
      localStorage.setItem('guest_key', '');
      fbq('track', 'CompleteRegistration');
      window._fbq.push(['track', '{{ env('FACEBOOK_PIXEL_SIGN_UP') }}', {'value':'0.00','currency':'USD'}]);
      trackEvent('/account', '/signed_up');
  }

  function checkForEnter(event)
  {
    if (event.keyCode === 13){
      event.preventDefault();
      validateServerSignUp();
      return false;
    }
  }

  function logout(force)
  {
    if (force) {
      NINJA.formIsChanged = false;
    }

    if (force || NINJA.isRegistered) {
      window.location = '{{ URL::to('logout') }}';
    } else {
      $('#logoutModal').modal('show');
    }
  }

  function showSignUp() {
    $('#signUpModal').modal('show');
  }

  function hideSignUp() {
    $('#signUpModal').modal('hide');
  }

  function buyProduct(affiliateKey, productId) {
    window.open('{{ Utils::isNinjaDev() ? '' : NINJA_APP_URL }}/license?affiliate_key=' + affiliateKey + '&product_id=' + productId + '&return_url=' + window.location);
  }

  function hideMessage() {
    $('.alert-info').fadeOut();
    $.get('/hide_message', function(response) {
      console.log('Reponse: %s', response);
    });
  }

  function wordWrapText(value, width)
  {
    @if (Auth::user()->company->auto_wrap)
    var doc = new jsPDF('p', 'pt');
    doc.setFont('Helvetica','');
    doc.setFontSize(10);

    var lines = value.split("\n");
    for (var i = 0; i < lines.length; i++) {
      var numLines = doc.splitTextToSize(lines[i], width).length;
      if (numLines <= 1) continue;
      var j = 0; space = lines[i].length;
      while (j++ < lines[i].length) {
        if (lines[i].charAt(j) === ' ') space = j;
      }
      if (space == lines[i].length) space = width/6;
      lines[i + 1] = lines[i].substring(space + 1) + ' ' + (lines[i + 1] || '');
      lines[i] = lines[i].substring(0, space);
    }

    var newValue = (lines.join("\n")).trim();

    if (value == newValue) {
      return newValue;
    } else {
      return wordWrapText(newValue, width);
    }
    @else
    return value;
    @endif
  }

  function setSignupEnabled(enabled) {
    $('.signup-form input[type=text]').prop('disabled', !enabled);
    if (enabled) {
        $('.signup-form a.btn').removeClass('disabled');
    } else {
        $('.signup-form a.btn').addClass('disabled');
    }
  }

  function setSocialLoginProvider(provider) {
    localStorage.setItem('auth_provider', provider);
  }

  window.loadedSearchData = false;
  function showSearch() {
    $('#search').typeahead('val', '');
    $('#navbar-options').hide();
    $('#search-form').show();
    $('#search').focus();

    if (!window.loadedSearchData) {
        trackEvent('/activity', '/search');
        var request = $.get('{{ URL::route('get_search_data') }}', function(data) {
          $('#search').typeahead({
            hint: true,
            highlight: true,
          }
          @if (Auth::check() && Auth::user()->company->custom_client_label1)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ Auth::user()->company->custom_client_label1 }}'], 'tokens'),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ Auth::user()->company->custom_client_label1 }}</span>'
            }
          }
          @endif
          @if (Auth::check() && Auth::user()->company->custom_client_label2)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ Auth::user()->company->custom_client_label2 }}'], 'tokens'),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ Auth::user()->company->custom_client_label2 }}</span>'
            }
          }
          @endif
          @foreach (['clients', 'contacts', 'invoices', 'quotes', 'navigation'] as $type)
          ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['{{ $type }}'], 'tokens', true),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">{{ trans("texts.{$type}") }}</span>'
            }
          }
          @endforeach
          ).on('typeahead:selected', function(element, datum, name) {
            window.location = datum.url;
          }).focus();
          window.loadedSearchData = true;
        });

        request.error(function(httpObj, textStatus) {
            // if the session has expried show login page
            if (httpObj.status == 401) {
                location.reload();
            }
        });
    }
  }

  function hideSearch() {
    $('#search-form').hide();
    $('#navbar-options').show();
  }

  $(function() {
    window.setTimeout(function() {
        $(".alert-hide").fadeOut();
    }, 3000);

    $('#search').blur(function(event){
        if (window.loadedSearchData) {
            hideSearch();
        }
    });

    /* Set the defaults for Bootstrap datepicker */
    $.extend(true, $.fn.datepicker.defaults, {
        //language: '{{ $appLanguage }}', // causes problems with some languages (ie, fr_CA) if the date includes strings (ie, July 31, 2016)
        weekStart: 'monday'
    });

    if (isStorageSupported()) {
      @if (Auth::check() && !Auth::user()->registered)
      localStorage.setItem('guest_key', '{{ Auth::user()->password }}');
      @endif
    }


    /* validateSignUp(); */

    $('#signUpModal').on('shown.bs.modal', function () {
      trackEvent('/account', '/view_sign_up');
      $(['first_name','last_name','email','password']).each(function(i, field) {
        var $input = $('form.signUpForm #new_'+field);
        if (!$input.val()) {
          $input.focus();
          return false;
        }
      });
    })


    @if (Auth::check() && !Utils::isNinja() && !Auth::user()->registered)
      $('#closeSignUpButton').hide();
      showSignUp();
    @elseif(Session::get('sign_up') || \Request::get('sign_up'))
      showSignUp();
    @endif

    $('ul.navbar-settings, ul.navbar-search').hover(function () {
        if ($('.user-accounts').css('display') == 'block') {
            $('.user-accounts').dropdown('toggle');
        }
    });

    @yield('onReady')

    @if (\Request::has('focus'))
        $('#{{ \Request::get('focus') }}').focus();
    @endif

    // Ensure terms is checked for sign up form
    @if (Auth::check() && !Auth::user()->registered)
        setSignupEnabled(false);
        $("#terms_checkbox").change(function() {
            setSignupEnabled(this.checked);
        });
    @endif

    // Focus the search input if the user clicks forward slash
    $('body').keypress(function(event) {
        if (event.which == 47 && !$('*:focus').length) {
            event.preventDefault();
            showSearch();
        }
    });

  });

</script>

@stop



























@section('body')

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">

    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="{{ URL::to(NINJA_WEB_URL) }}" class='navbar-brand' target="_blank">
        {{-- Per our license, please do not remove or modify this link. --}}
        <img src="{{ asset('images/invoiceninja-logo.png') }}" style="height:20px;width:auto;padding-right:10px"/>
      </a>
    </div>

    <div class="collapse navbar-collapse" id="navbar-collapse-1">
      <ul class="nav navbar-nav" style="font-weight: bold">
        {!! Form::nav_link('dashboard', 'dashboard') !!}
        {!! Form::menu_link('client') !!}
        {!! Form::menu_link('task') !!}
        {!! Form::menu_link('expense') !!}
        {!! Form::menu_link('invoice') !!}
        {!! Form::menu_link('payment') !!}
      </ul>

      <div id="navbar-options">
      <div class="navbar-form navbar-right">


        {!! Button::success(trans('texts.sign_up'))->withAttributes(array('id' => 'signUpButton', 'data-toggle'=>'modal', 'data-target'=>'#signUpModal', 'style' => 'max-width:100px;;overflow:hidden'))->small() !!} &nbsp;

        <div class="btn-group user-dropdown">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
            <div id="myAccountButton" class="ellipsis nav-account-name" style="max-width:130px;">
                @if (session(SESSION_USER_ACCOUNTS) && count(session(SESSION_USER_ACCOUNTS)))
                    {{ Auth::user()->company->getDisplayName() }}
                @else
                    {{ Auth::user()->getDisplayName() }}
                @endif
              <span class="caret"></span>
            </div>
            <span class="glyphicon glyphicon-user nav-account-icon" style="padding-left:0px"
                title="{{ Auth::user()->company->getDisplayName() }}"/>
          </button>
          <ul class="dropdown-menu user-accounts">
            @if (session(SESSION_USER_ACCOUNTS))
                @foreach (session(SESSION_USER_ACCOUNTS) as $item)
                    @if ($item->user_id == Auth::user()->id)
                        @include('user_account', [
                            'user_account_id' => $item->id,
                            'user_id' => $item->user_id,
                            'account_name' => $item->account_name,
                            'user_name' => $item->user_name,
                            'logo_url' => isset($item->logo_url) ? $item->logo_url : "",
                            'selected' => true,
                        ])
                    @endif
                @endforeach
                @foreach (session(SESSION_USER_ACCOUNTS) as $item)
                    @if ($item->user_id != Auth::user()->id)
                        @include('user_account', [
                            'user_account_id' => $item->id,
                            'user_id' => $item->user_id,
                            'account_name' => $item->account_name,
                            'user_name' => $item->user_name,
                            'logo_url' => isset($item->logo_url) ? $item->logo_url : "",
                            'selected' => false,
                        ])
                    @endif
                @endforeach
            @else
                @include('user_account', [
                    'account_name' => Auth::user()->company->name ?: trans('texts.untitled'),
                    'user_name' => Auth::user()->getDisplayName(),
                    'logo_url' => Auth::user()->company->getLogoURL(),
                    'selected' => true,
                ])
            @endif
            <li class="divider"></li>


                  <li>{!! link_to('/manage_companies', trans('texts.manage_companies')) !!}</li>

                  <li>{!! link_to('/login?new_company=true', trans('texts.add_company')) !!}</li>


            <li>{!! link_to('#', trans('texts.logout'), array('onclick'=>'logout()')) !!}</li>
          </ul>
        </div>

      </div>

      <ul class="nav navbar-nav navbar-right navbar-settings">
        <li class="dropdown">

            <a href="{{ URL::to('/settings') }}" class="dropdown-toggle">
              <span class="glyphicon glyphicon-cog" title="{{ trans('texts.settings') }}"/>
            </a>
            <ul class="dropdown-menu">
              @foreach (\App\Models\Company::$basicSettings as $setting)
                  <li>{!! link_to('settings/' . $setting, uctrans("texts.{$setting}")) !!}</li>
              @endforeach
              <li><a href="{{ url('settings/' . ACCOUNT_INVOICE_SETTINGS) }}">{!! uctrans('texts.advanced_settings') . Utils::getProLabel(ACCOUNT_ADVANCED_SETTINGS) !!}</a></li>
            </ul>

        </li>
      </ul>


      <ul class="nav navbar-nav navbar-right navbar-search">
        <li class="dropdown">
          <a href="#" onclick="showSearch()">
            <span class="glyphicon glyphicon-search" title="{{ trans('texts.search') }}"/>
          </a>
          <ul class="dropdown-menu">
            {{-- @if (count(Session::get(RECENTLY_VIEWED)) == 0)
                <li><a href="#">{{ trans('texts.no_items') }}</a></li>
            @else
                @foreach (Session::get(RECENTLY_VIEWED) as $link)
                    @if (property_exists($link, 'accountId') && $link->accountId == Auth::user()->account_id)
                        <li><a href="{{ $link->url }}">{{ $link->name }}</a></li>
                    @endif
                @endforeach
            @endif --}}
          </ul>
        </li>
      </ul>
      </div>

      <form id="search-form" class="navbar-form navbar-right" role="search" style="display:none">
        <div class="form-group">
          <input type="text" id="search" style="width: 240px;padding-top:0px;padding-bottom:0px"
            class="form-control" placeholder="{{ trans('texts.search') . ': ' . trans('texts.search_hotkey')}}">
        </div>
      </form>


    </div><!-- /.navbar-collapse -->


  </div>
</nav>

<br/>
<div class="container">

  @include('partials.warn_session', ['redirectTo' => '/dashboard'])

  @if (Session::has('warning'))
  <div class="alert alert-warning">{!! Session::get('warning') !!}</div>
  @endif

  @if (Session::has('message'))
    <div class="alert alert-info alert-hide">
      {{ Session::get('message') }}
    </div>
  @elseif (Session::has('news_feed_message'))
    <div class="alert alert-info">
      {!! Session::get('news_feed_message') !!}
      <a href="#" onclick="hideMessage()" class="pull-right">{{ trans('texts.hide') }}</a>
    </div>
  @endif

  @if (Session::has('error'))
      <div class="alert alert-danger">{!! Session::get('error') !!}</div>
  @endif

  @if (!isset($showBreadcrumbs) || $showBreadcrumbs)
    {!! Form::breadcrumbs(isset($entityStatus) ? $entityStatus : '') !!}
  @endif

  @yield('content')

</div>










<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">{{ trans('texts.logout') }}</h4>
      </div>

      <div class="container">
        <h3>{{ trans('texts.are_you_sure') }}</h3>
        <p>{{ trans('texts.erase_data') }}</p>
      </div>

      <div class="modal-footer" id="signUpFooter">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('texts.cancel') }}</button>
        <button type="button" class="btn btn-primary" onclick="logout(true)">{{ trans('texts.logout') }}</button>
      </div>
    </div>
  </div>
</div>


</div>
<br/>
<div class="container">


temp
</div>


<p>&nbsp;</p>


@stop
