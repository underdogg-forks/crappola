<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Source: https://github.com/invoiceninja/invoiceninja -->
    <!-- Version: 4.5.19 -->
            <meta charset="utf-8">

            <title>Localization | Invoice Ninja</title>
        <meta name="description" content="Invoice Ninja is a free, open-source solution for invoicing and billing customers. With Invoice Ninja, you can easily build and send beautiful invoices from any device that has access to the web. Your clients can print your invoices, download them as pdf files, and even pay you online from within the system."/>
        <link href="/favicon-v2.png" rel="shortcut icon" type="image/png">

        <meta property="og:site_name" content="Invoice Ninja"/>
        <meta property="og:url" content="http://invninjv1.local"/>
        <meta property="og:title" content="Invoice Ninja"/>
        <meta property="og:image" content="/images/round_logo.png"/>
        <meta property="og:description" content="Simple, Intuitive Invoicing."/>

        <!-- http://realfavicongenerator.net -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/manifest.json">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#3bc65c">
        <link rel="shortcut icon" href="/favicon.ico">
        <meta name="apple-mobile-web-app-title" content="Invoice Ninja">
        <meta name="application-name" content="Invoice Ninja">
        <meta name="theme-color" content="#ffffff">
    
    <!-- http://stackoverflow.com/questions/19012698/browser-cache-issues-in-laravel-4-application -->
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="cache-control" content="no-store"/>
    <meta http-equiv="cache-control" content="must-revalidate"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT"/>
    <meta http-equiv="pragma" content="no-cache"/>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="frPRPZtD0NIePrpcFHDC2ELJ89NvFzlG0dzIkrWt">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="canonical" href="https://app.invoiceninja.com/settings/localization"/>

        <link href="/css/built.css?no_cache=4.5.19" rel="stylesheet" type="text/css"/>

    
    <script src="/built.js?no_cache=4.5.19" type="text/javascript"></script>

    <script type="text/javascript">
        function trackEvent(category, action) {
        }

        var NINJA = NINJA || {};
        NINJA.fontSize = 9;
        NINJA.isRegistered = true;
        NINJA.loggedErrorCount = 0;

        NINJA.parseFloat = function(str) {
            if (! str) {
                return '';
            } else {
                str = str + '';
            }

            // check for comma as decimal separator
            if (str.match(/,[\d]{1,2}$/)) {
                str = str.replace(',', '.');
                str = str.replace('.', ',');
            }

            str = str.replace(/[^0-9\.\-]/g, '');

            return window.parseFloat(str);
        }

        window.onerror = function (errorMsg, url, lineNumber, column, error) {
            if (NINJA.loggedErrorCount > 5) {
                return;
            }
            NINJA.loggedErrorCount++;

            // Error in hosted third party library
            if (errorMsg.indexOf('Script error.') > -1) {
                return;
            }
            // Error due to incognito mode
            if (errorMsg.indexOf('DOM Exception 22') > -1) {
                return;
            }
                        // Less than IE9 https://stackoverflow.com/a/14835682/497368
            if (! document.addEventListener) {
                return;
            }
            try {
                $.ajax({
                    type: 'GET',
                    url: 'http://invninjv1.local/log_error',
                    data: 'error=' + encodeURIComponent(errorMsg + ' | Line: ' + lineNumber + ', Column: '+ column)
                    + '&url=' + encodeURIComponent(window.location)
                });

                trackEvent('/error', errorMsg);
            } catch (exception) {
                console.log('Failed to log error');
                console.log(exception);
            }

            return false;
        }

        // http://t4t5.github.io/sweetalert/
        function sweetConfirm(successCallback, text, title, cancelCallback) {
            title = title || "Are you sure?";
            swal({
                //type: "warning",
                //confirmButtonColor: "#DD6B55",
                title: title,
                text: text,
                cancelButtonText: "No",
                confirmButtonText: "Yes",
                showCancelButton: true,
                closeOnConfirm: false,
                allowOutsideClick: true,
            }).then(function() {
                successCallback();
                swal.close();
            }).catch(function() {
                if (cancelCallback) {
                    cancelCallback();
                }
            });
        }

        function showPasswordStrength(password, score) {
            if (password) {
                var str = "Password Strength" + ': ';
                if (password.length < 8 || score < 50) {
                    str += "Weak";
                } else if (score < 75) {
                    str += "Good";
                } else {
                    str += "Strong";
                }
                $('#passwordStrength').html(str);
            } else {
                $('#passwordStrength').html('&nbsp;');
            }
        }

        /* Set the defaults for DataTables initialisation */
        $.extend(true, $.fn.dataTable.defaults, {
            "bSortClasses": false,
            "sDom": "t<'row-fluid'<'span6 dt-left'i><'span6 dt-right'p>>l",
            "sPaginationType": "bootstrap",
            "bInfo": true,
            "oLanguage": {
                'sEmptyTable': "No data available in table",
                'sInfoEmpty': "Showing 0 to 0 of 0 entries",
                'sLengthMenu': '_MENU_ rows',
                'sInfo': "Showing _START_ to _END_ of _TOTAL_ entries",
                'sSearch': ''
            }
        });

    </script>

<script type="text/javascript">

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
      window.location = 'http://invninjv1.local/logout' + (force ? '?force_logout=true' : '');
    } else {
      $('#logoutModal').modal('show');
    }
  }

  function hideMessage() {
    $('.alert-info').fadeOut();
    $.get('/hide_message', function(response) {
      console.log('Reponse: %s', response);
    });
  }

  function openTimeTracker() {
      var width = 1060;
      var height = 700;
      var left = (screen.width/2)-(width/4);
      var top = (screen.height/2)-(height/1.5);
      window.open("/time_tracker", "time-tracker", "width="+width+",height="+height+",scrollbars=no,toolbar=no,screenx="+left+",screeny="+top+",location=no,titlebar=no,directories=no,status=no,menubar=no");
  }

  window.loadedSearchData = false;
  function onSearchBlur() {
      $('#search').typeahead('val', '');
  }

  function onSearchFocus() {
    $('#search-form').show();

    if (!window.loadedSearchData) {
        window.loadedSearchData = true;
        trackEvent('/activity', '/search');
        var request = $.get('http://invninjv1.local/company/get_search_data', function(data) {
          $('#search').typeahead({
            hint: true,
            highlight: true,
          }
                                                            ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['clients'], 'tokens', true),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">Clients</span>'
            }
          }
                    ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['contacts'], 'tokens', true),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">Contacts</span>'
            }
          }
                    ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['invoices'], 'tokens', true),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">Invoices</span>'
            }
          }
                    ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['quotes'], 'tokens', true),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">Quotes</span>'
            }
          }
                    ,{
            name: 'data',
            limit: 3,
            display: 'value',
            source: searchData(data['navigation'], 'tokens', true),
            templates: {
              header: '&nbsp;<span style="font-weight:600;font-size:16px">Navigation</span>'
            }
          }
                    ).on('typeahead:selected', function(element, datum, name) {
            window.location = datum.url;
          }).focus();
        });

        request.error(function(httpObj, textStatus) {
            // if the session has expried show login page
            if (httpObj.status == 401) {
                location.reload();
            }
        });
    }
  }

  $(function() {
    // auto-logout after 8 hours
    window.setTimeout(function() {
        window.location = 'http://invninjv1.local/logout?reason=inactive';
    }, 28800000);

    // auto-hide status alerts
    window.setTimeout(function() {
        $(".alert-hide").fadeOut();
    }, 3000);

    /* Set the defaults for Bootstrap datepicker */
    $.extend(true, $.fn.datepicker.defaults, {
        //language: 'en', // causes problems with some languages (ie, fr_CA) if the date includes strings (ie, July 31, 2016)
        weekStart: 1
    });

    if (isStorageSupported()) {
          }

    $('ul.navbar-settings, ul.navbar-search').hover(function () {
        if ($('.user-accounts').css('display') == 'block') {
            $('.user-accounts').dropdown('toggle');
        }
    });

        $('#currency_id').focus();
    updateCurrencyCodeRadio();

    
    // Focus the search input if the user clicks forward slash
    $('#search').focusin(onSearchFocus);
    $('#search').blur(onSearchBlur);

    // manage sidebar state
    function setupSidebar(side) {
        $("#" + side + "-menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled-" + side);

            var toggled = $("#wrapper").hasClass("toggled-" + side) ? '1' : '0';
            $.post('http://invninjv1.local/save_sidebar_state?show_' + side + '=' + toggled);

            if (isStorageSupported()) {
                localStorage.setItem('show_' + side + '_sidebar', toggled);
            }
        });

        if (isStorageSupported()) {
            var storage = localStorage.getItem('show_' + side + '_sidebar') || '0';
            var toggled = $("#wrapper").hasClass("toggled-" + side) ? '1' : '0';

            if (storage != toggled) {
                setTimeout(function() {
                    $("#wrapper").toggleClass("toggled-" + side);
                    $.post('http://invninjv1.local/save_sidebar_state?show_' + side + '=' + storage);
                }, 200);
            }
        }
    }

            setupSidebar('left');
        setupSidebar('right');
    
    // auto select focused nav-tab
    if (window.location.hash) {
        setTimeout(function() {
            $('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
        }, 1);
    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (isStorageSupported() && /\/settings\//.test(location.href)) {
            var target = $(e.target).attr("href") // activated tab
            if (history.pushState) {
                history.pushState(null, null, target);
            }
            if (isStorageSupported()) {
                localStorage.setItem('last:settings_page', location.href.replace(location.hash, ''));
            }
        }
    });

    // set timeout onDomReady
    setTimeout(delayedFragmentTargetOffset, 500);

    // add scroll offset to fragment target (if there is one)
    function delayedFragmentTargetOffset(){
        var offset = $(':target').offset();
        if (offset) {
            var scrollto = offset.top - 180; // minus fixed header height
            $('html, body').animate({scrollTop:scrollto}, 0);
        }
    }

  });

</script>



    <script type="text/javascript">

    var currencies = [{"id":94,"name":"Albanian Lek","symbol":"L ","precision":"2","thousand_separator":".","decimal_separator":",","code":"ALL","swap_currency_symbol":false,"exchange_rate":null},{"id":90,"name":"Algerian Dinar","symbol":"DA","precision":"2","thousand_separator":",","decimal_separator":".","code":"DZD","swap_currency_symbol":false,"exchange_rate":null},{"id":82,"name":"Angolan Kwanza","symbol":"Kz","precision":"2","thousand_separator":".","decimal_separator":",","code":"AOA","swap_currency_symbol":false,"exchange_rate":null},{"id":23,"name":"Argentine Peso","symbol":"$","precision":"2","thousand_separator":".","decimal_separator":",","code":"ARS","swap_currency_symbol":false,"exchange_rate":null},{"id":97,"name":"Armenian Dram","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"AMD","swap_currency_symbol":false,"exchange_rate":null},{"id":40,"name":"Aruban Florin","symbol":"Afl. ","precision":"2","thousand_separator":" ","decimal_separator":".","code":"AWG","swap_currency_symbol":false,"exchange_rate":null},{"id":12,"name":"Australian Dollar","symbol":"$","precision":"2","thousand_separator":",","decimal_separator":".","code":"AUD","swap_currency_symbol":false,"exchange_rate":null},{"id":98,"name":"Azerbaijan Manat","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"AZN","swap_currency_symbol":false,"exchange_rate":null},{"id":77,"name":"Bahraini Dinar","symbol":"BD ","precision":"2","thousand_separator":",","decimal_separator":".","code":"BHD","swap_currency_symbol":false,"exchange_rate":null},{"id":24,"name":"Bangladeshi Taka","symbol":"Tk","precision":"2","thousand_separator":",","decimal_separator":".","code":"BDT","swap_currency_symbol":false,"exchange_rate":null},{"id":71,"name":"Barbadian Dollar","symbol":"$","precision":"2","thousand_separator":",","decimal_separator":".","code":"BBD","swap_currency_symbol":false,"exchange_rate":null},{"id":100,"name":"Belarusian Ruble","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"BYN","swap_currency_symbol":false,"exchange_rate":null},{"id":93,"name":"Bolivian Boliviano","symbol":"Bs","precision":"2","thousand_separator":",","decimal_separator":".","code":"BOB","swap_currency_symbol":false,"exchange_rate":null},{"id":99,"name":"Bosnia and Herzegovina Convertible Mark","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"BAM","swap_currency_symbol":false,"exchange_rate":null},{"id":68,"name":"Botswana Pula","symbol":"P","precision":"2","thousand_separator":",","decimal_separator":".","code":"BWP","swap_currency_symbol":false,"exchange_rate":null},{"id":20,"name":"Brazilian Real","symbol":"R$","precision":"2","thousand_separator":".","decimal_separator":",","code":"BRL","swap_currency_symbol":false,"exchange_rate":null},{"id":2,"name":"British Pound","symbol":"\u00a3","precision":"2","thousand_separator":",","decimal_separator":".","code":"GBP","swap_currency_symbol":false,"exchange_rate":null},{"id":72,"name":"Brunei Dollar","symbol":"B$","precision":"2","thousand_separator":",","decimal_separator":".","code":"BND","swap_currency_symbol":false,"exchange_rate":null},{"id":39,"name":"Bulgarian Lev","symbol":"","precision":"2","thousand_separator":" ","decimal_separator":".","code":"BGN","swap_currency_symbol":false,"exchange_rate":null},{"id":9,"name":"Canadian Dollar","symbol":"C$","precision":"2","thousand_separator":",","decimal_separator":".","code":"CAD","swap_currency_symbol":false,"exchange_rate":null},{"id":88,"name":"Cape Verdean Escudo","symbol":"","precision":"2","thousand_separator":".","decimal_separator":"$","code":"CVE","swap_currency_symbol":false,"exchange_rate":null},{"id":86,"name":"CFP Franc","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"XPF","swap_currency_symbol":false,"exchange_rate":null},{"id":62,"name":"Chilean Peso","symbol":"$","precision":"0","thousand_separator":".","decimal_separator":",","code":"CLP","swap_currency_symbol":false,"exchange_rate":null},{"id":32,"name":"Chinese Renminbi","symbol":"RMB ","precision":"2","thousand_separator":",","decimal_separator":".","code":"CNY","swap_currency_symbol":false,"exchange_rate":null},{"id":30,"name":"Colombian Peso","symbol":"$","precision":"2","thousand_separator":".","decimal_separator":",","code":"COP","swap_currency_symbol":false,"exchange_rate":null},{"id":47,"name":"Costa Rican Col\u00f3n","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"CRC","swap_currency_symbol":false,"exchange_rate":null},{"id":43,"name":"Croatian Kuna","symbol":"kn","precision":"2","thousand_separator":".","decimal_separator":",","code":"HRK","swap_currency_symbol":true,"exchange_rate":null},{"id":51,"name":"Czech Koruna","symbol":"K\u010d","precision":"2","thousand_separator":" ","decimal_separator":",","code":"CZK","swap_currency_symbol":true,"exchange_rate":null},{"id":5,"name":"Danish Krone","symbol":"kr","precision":"2","thousand_separator":".","decimal_separator":",","code":"DKK","swap_currency_symbol":true,"exchange_rate":null},{"id":61,"name":"Dominican Peso","symbol":"RD$","precision":"2","thousand_separator":",","decimal_separator":".","code":"DOP","swap_currency_symbol":false,"exchange_rate":null},{"id":37,"name":"East Caribbean Dollar","symbol":"EC$","precision":"2","thousand_separator":",","decimal_separator":".","code":"XCD","swap_currency_symbol":false,"exchange_rate":null},{"id":29,"name":"Egyptian Pound","symbol":"E\u00a3","precision":"2","thousand_separator":",","decimal_separator":".","code":"EGP","swap_currency_symbol":false,"exchange_rate":null},{"id":3,"name":"Euro","symbol":"\u20ac","precision":"2","thousand_separator":".","decimal_separator":",","code":"EUR","swap_currency_symbol":false,"exchange_rate":null},{"id":92,"name":"Fijian Dollar","symbol":"FJ$","precision":"2","thousand_separator":",","decimal_separator":".","code":"FJD","swap_currency_symbol":false,"exchange_rate":null},{"id":73,"name":"Georgian Lari","symbol":"","precision":"2","thousand_separator":" ","decimal_separator":",","code":"GEL","swap_currency_symbol":false,"exchange_rate":null},{"id":38,"name":"Ghanaian Cedi","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"GHS","swap_currency_symbol":false,"exchange_rate":null},{"id":101,"name":"Gibraltar Pound","symbol":"GIP","precision":"2","thousand_separator":",","decimal_separator":".","code":"","swap_currency_symbol":false,"exchange_rate":null},{"id":104,"name":"Gibraltar Pound","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"GIP","swap_currency_symbol":false,"exchange_rate":null},{"id":18,"name":"Guatemalan Quetzal","symbol":"Q","precision":"2","thousand_separator":",","decimal_separator":".","code":"GTQ","swap_currency_symbol":false,"exchange_rate":null},{"id":83,"name":"Haitian Gourde","symbol":"G","precision":"2","thousand_separator":",","decimal_separator":".","code":"HTG","swap_currency_symbol":false,"exchange_rate":null},{"id":75,"name":"Honduran Lempira","symbol":"L","precision":"2","thousand_separator":",","decimal_separator":".","code":"HNL","swap_currency_symbol":false,"exchange_rate":null},{"id":26,"name":"Hong Kong Dollar","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"HKD","swap_currency_symbol":false,"exchange_rate":null},{"id":69,"name":"Hungarian Forint","symbol":"Ft","precision":"0","thousand_separator":".","decimal_separator":",","code":"HUF","swap_currency_symbol":true,"exchange_rate":null},{"id":63,"name":"Icelandic Kr\u00f3na","symbol":"kr","precision":"2","thousand_separator":".","decimal_separator":",","code":"ISK","swap_currency_symbol":true,"exchange_rate":null},{"id":11,"name":"Indian Rupee","symbol":"Rs. ","precision":"2","thousand_separator":",","decimal_separator":".","code":"INR","swap_currency_symbol":false,"exchange_rate":null},{"id":27,"name":"Indonesian Rupiah","symbol":"Rp","precision":"2","thousand_separator":",","decimal_separator":".","code":"IDR","swap_currency_symbol":false,"exchange_rate":null},{"id":6,"name":"Israeli Shekel","symbol":"NIS ","precision":"2","thousand_separator":",","decimal_separator":".","code":"ILS","swap_currency_symbol":false,"exchange_rate":null},{"id":81,"name":"Jamaican Dollar","symbol":"$","precision":"2","thousand_separator":",","decimal_separator":".","code":"JMD","swap_currency_symbol":false,"exchange_rate":null},{"id":45,"name":"Japanese Yen","symbol":"\u00a5","precision":"0","thousand_separator":",","decimal_separator":".","code":"JPY","swap_currency_symbol":false,"exchange_rate":null},{"id":65,"name":"Jordanian Dinar","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"JOD","swap_currency_symbol":false,"exchange_rate":null},{"id":103,"name":"Kazakhstani Tenge","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"KZT","swap_currency_symbol":false,"exchange_rate":null},{"id":8,"name":"Kenyan Shilling","symbol":"KSh ","precision":"2","thousand_separator":",","decimal_separator":".","code":"KES","swap_currency_symbol":false,"exchange_rate":null},{"id":89,"name":"Kuwaiti Dinar","symbol":"KD","precision":"2","thousand_separator":",","decimal_separator":".","code":"KWD","swap_currency_symbol":false,"exchange_rate":null},{"id":96,"name":"Lebanese Pound","symbol":"LL ","precision":"2","thousand_separator":",","decimal_separator":".","code":"LBP","swap_currency_symbol":false,"exchange_rate":null},{"id":59,"name":"Macanese Pataca","symbol":"MOP$","precision":"2","thousand_separator":",","decimal_separator":".","code":"MOP","swap_currency_symbol":false,"exchange_rate":null},{"id":91,"name":"Macedonian Denar","symbol":"\u0434\u0435\u043d","precision":"2","thousand_separator":",","decimal_separator":".","code":"MKD","swap_currency_symbol":false,"exchange_rate":null},{"id":19,"name":"Malaysian Ringgit","symbol":"RM","precision":"2","thousand_separator":",","decimal_separator":".","code":"MYR","swap_currency_symbol":false,"exchange_rate":null},{"id":46,"name":"Maldivian Rufiyaa","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"MVR","swap_currency_symbol":false,"exchange_rate":null},{"id":87,"name":"Mauritian Rupee","symbol":"Rs","precision":"2","thousand_separator":",","decimal_separator":".","code":"MUR","swap_currency_symbol":false,"exchange_rate":null},{"id":28,"name":"Mexican Peso","symbol":"$","precision":"2","thousand_separator":",","decimal_separator":".","code":"MXN","swap_currency_symbol":false,"exchange_rate":null},{"id":102,"name":"Moldovan Leu","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"MDL","swap_currency_symbol":false,"exchange_rate":null},{"id":80,"name":"Moroccan Dirham","symbol":"MAD ","precision":"2","thousand_separator":",","decimal_separator":".","code":"MAD","swap_currency_symbol":false,"exchange_rate":null},{"id":56,"name":"Mozambican Metical","symbol":"MT","precision":"2","thousand_separator":".","decimal_separator":",","code":"MZN","swap_currency_symbol":true,"exchange_rate":null},{"id":66,"name":"Myanmar Kyat","symbol":"K","precision":"2","thousand_separator":",","decimal_separator":".","code":"MMK","swap_currency_symbol":false,"exchange_rate":null},{"id":53,"name":"Namibian Dollar","symbol":"$","precision":"2","thousand_separator":",","decimal_separator":".","code":"NAD","swap_currency_symbol":false,"exchange_rate":null},{"id":85,"name":"Nepalese Rupee","symbol":"Rs. ","precision":"2","thousand_separator":",","decimal_separator":".","code":"NPR","swap_currency_symbol":false,"exchange_rate":null},{"id":35,"name":"Netherlands Antillean Guilder","symbol":"","precision":"2","thousand_separator":".","decimal_separator":",","code":"ANG","swap_currency_symbol":false,"exchange_rate":null},{"id":15,"name":"New Zealand Dollar","symbol":"$","precision":"2","thousand_separator":",","decimal_separator":".","code":"NZD","swap_currency_symbol":false,"exchange_rate":null},{"id":22,"name":"Nigerian Naira","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"NGN","swap_currency_symbol":false,"exchange_rate":null},{"id":14,"name":"Norske Kroner","symbol":"kr","precision":"2","thousand_separator":".","decimal_separator":",","code":"NOK","swap_currency_symbol":true,"exchange_rate":null},{"id":57,"name":"Omani Rial","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"OMR","swap_currency_symbol":false,"exchange_rate":null},{"id":48,"name":"Pakistani Rupee","symbol":"Rs ","precision":"0","thousand_separator":",","decimal_separator":".","code":"PKR","swap_currency_symbol":false,"exchange_rate":null},{"id":64,"name":"Papua New Guinean Kina","symbol":"K","precision":"2","thousand_separator":",","decimal_separator":".","code":"PGK","swap_currency_symbol":false,"exchange_rate":null},{"id":67,"name":"Peruvian Sol","symbol":"S\/ ","precision":"2","thousand_separator":",","decimal_separator":".","code":"PEN","swap_currency_symbol":false,"exchange_rate":null},{"id":10,"name":"Philippine Peso","symbol":"P ","precision":"2","thousand_separator":",","decimal_separator":".","code":"PHP","swap_currency_symbol":false,"exchange_rate":null},{"id":49,"name":"Polish Zloty","symbol":"z\u0142","precision":"2","thousand_separator":" ","decimal_separator":",","code":"PLN","swap_currency_symbol":true,"exchange_rate":null},{"id":74,"name":"Qatari Riyal","symbol":"QR","precision":"2","thousand_separator":",","decimal_separator":".","code":"QAR","swap_currency_symbol":false,"exchange_rate":null},{"id":42,"name":"Romanian New Leu","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"RON","swap_currency_symbol":false,"exchange_rate":null},{"id":55,"name":"Russian Ruble","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"RUB","swap_currency_symbol":false,"exchange_rate":null},{"id":33,"name":"Rwandan Franc","symbol":"RF ","precision":"2","thousand_separator":",","decimal_separator":".","code":"RWF","swap_currency_symbol":false,"exchange_rate":null},{"id":44,"name":"Saudi Riyal","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"SAR","swap_currency_symbol":false,"exchange_rate":null},{"id":95,"name":"Serbian Dinar","symbol":"din","precision":"2","thousand_separator":".","decimal_separator":",","code":"RSD","swap_currency_symbol":false,"exchange_rate":null},{"id":13,"name":"Singapore Dollar","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"SGD","swap_currency_symbol":false,"exchange_rate":null},{"id":4,"name":"South African Rand","symbol":"R","precision":"2","thousand_separator":",","decimal_separator":".","code":"ZAR","swap_currency_symbol":false,"exchange_rate":null},{"id":79,"name":"South Korean Won","symbol":"W ","precision":"2","thousand_separator":".","decimal_separator":",","code":"KRW","swap_currency_symbol":false,"exchange_rate":null},{"id":50,"name":"Sri Lankan Rupee","symbol":"LKR","precision":"2","thousand_separator":",","decimal_separator":".","code":"LKR","swap_currency_symbol":true,"exchange_rate":null},{"id":76,"name":"Surinamese Dollar","symbol":"SRD","precision":"2","thousand_separator":".","decimal_separator":",","code":"SRD","swap_currency_symbol":false,"exchange_rate":null},{"id":7,"name":"Swedish Krona","symbol":"kr","precision":"2","thousand_separator":".","decimal_separator":",","code":"SEK","swap_currency_symbol":true,"exchange_rate":null},{"id":17,"name":"Swiss Franc","symbol":"","precision":"2","thousand_separator":"'","decimal_separator":".","code":"CHF","swap_currency_symbol":false,"exchange_rate":null},{"id":60,"name":"Taiwan New Dollar","symbol":"NT$","precision":"2","thousand_separator":",","decimal_separator":".","code":"TWD","swap_currency_symbol":false,"exchange_rate":null},{"id":34,"name":"Tanzanian Shilling","symbol":"TSh ","precision":"2","thousand_separator":",","decimal_separator":".","code":"TZS","swap_currency_symbol":false,"exchange_rate":null},{"id":21,"name":"Thai Baht","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"THB","swap_currency_symbol":false,"exchange_rate":null},{"id":36,"name":"Trinidad and Tobago Dollar","symbol":"TT$","precision":"2","thousand_separator":",","decimal_separator":".","code":"TTD","swap_currency_symbol":false,"exchange_rate":null},{"id":54,"name":"Tunisian Dinar","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"TND","swap_currency_symbol":false,"exchange_rate":null},{"id":41,"name":"Turkish Lira","symbol":"TL ","precision":"2","thousand_separator":".","decimal_separator":",","code":"TRY","swap_currency_symbol":false,"exchange_rate":null},{"id":70,"name":"Ugandan Shilling","symbol":"USh ","precision":"2","thousand_separator":",","decimal_separator":".","code":"UGX","swap_currency_symbol":false,"exchange_rate":null},{"id":58,"name":"Ukrainian Hryvnia","symbol":"","precision":"2","thousand_separator":",","decimal_separator":".","code":"UAH","swap_currency_symbol":false,"exchange_rate":null},{"id":25,"name":"United Arab Emirates Dirham","symbol":"DH ","precision":"2","thousand_separator":",","decimal_separator":".","code":"AED","swap_currency_symbol":false,"exchange_rate":null},{"id":52,"name":"Uruguayan Peso","symbol":"$","precision":"2","thousand_separator":".","decimal_separator":",","code":"UYU","swap_currency_symbol":false,"exchange_rate":null},{"id":1,"name":"US Dollar","symbol":"$","precision":"2","thousand_separator":",","decimal_separator":".","code":"USD","swap_currency_symbol":false,"exchange_rate":null},{"id":78,"name":"Venezuelan Bolivars","symbol":"Bs.","precision":"2","thousand_separator":".","decimal_separator":",","code":"VES","swap_currency_symbol":false,"exchange_rate":null},{"id":16,"name":"Vietnamese Dong","symbol":"","precision":"0","thousand_separator":".","decimal_separator":",","code":"VND","swap_currency_symbol":false,"exchange_rate":null},{"id":31,"name":"West African Franc","symbol":"CFA ","precision":"2","thousand_separator":",","decimal_separator":".","code":"XOF","swap_currency_symbol":false,"exchange_rate":null},{"id":84,"name":"Zambian Kwacha","symbol":"ZK","precision":"2","thousand_separator":",","decimal_separator":".","code":"ZMW","swap_currency_symbol":false,"exchange_rate":null}];
    var currencyMap = {};
    for (var i=0; i<currencies.length; i++) {
        var currency = currencies[i];
        currencyMap[currency.id] = currency;
        currencyMap[currency.code] = currency;
    }

    var countries = [{"id":4,"iso_3166_2":"AF","iso_3166_3":"AFG","name":"Afghanistan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":248,"iso_3166_2":"AX","iso_3166_3":"ALA","name":"\u00c5land Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":8,"iso_3166_2":"AL","iso_3166_3":"ALB","name":"Albania","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":12,"iso_3166_2":"DZ","iso_3166_3":"DZA","name":"Algeria","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":16,"iso_3166_2":"AS","iso_3166_3":"ASM","name":"American Samoa","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":20,"iso_3166_2":"AD","iso_3166_3":"AND","name":"Andorra","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":24,"iso_3166_2":"AO","iso_3166_3":"AGO","name":"Angola","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":660,"iso_3166_2":"AI","iso_3166_3":"AIA","name":"Anguilla","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":10,"iso_3166_2":"AQ","iso_3166_3":"ATA","name":"Antarctica","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":28,"iso_3166_2":"AG","iso_3166_3":"ATG","name":"Antigua and Barbuda","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":32,"iso_3166_2":"AR","iso_3166_3":"ARG","name":"Argentina","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":51,"iso_3166_2":"AM","iso_3166_3":"ARM","name":"Armenia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":533,"iso_3166_2":"AW","iso_3166_3":"ABW","name":"Aruba","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":36,"iso_3166_2":"AU","iso_3166_3":"AUS","name":"Australia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":40,"iso_3166_2":"AT","iso_3166_3":"AUT","name":"Austria","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":31,"iso_3166_2":"AZ","iso_3166_3":"AZE","name":"Azerbaijan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":44,"iso_3166_2":"BS","iso_3166_3":"BHS","name":"Bahamas","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":48,"iso_3166_2":"BH","iso_3166_3":"BHR","name":"Bahrain","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":50,"iso_3166_2":"BD","iso_3166_3":"BGD","name":"Bangladesh","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":52,"iso_3166_2":"BB","iso_3166_3":"BRB","name":"Barbados","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":112,"iso_3166_2":"BY","iso_3166_3":"BLR","name":"Belarus","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":56,"iso_3166_2":"BE","iso_3166_3":"BEL","name":"Belgium","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":84,"iso_3166_2":"BZ","iso_3166_3":"BLZ","name":"Belize","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":204,"iso_3166_2":"BJ","iso_3166_3":"BEN","name":"Benin","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":60,"iso_3166_2":"BM","iso_3166_3":"BMU","name":"Bermuda","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":64,"iso_3166_2":"BT","iso_3166_3":"BTN","name":"Bhutan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":68,"iso_3166_2":"BO","iso_3166_3":"BOL","name":"Bolivia, Plurinational State of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":535,"iso_3166_2":"BQ","iso_3166_3":"BES","name":"Bonaire, Sint Eustatius and Saba","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":70,"iso_3166_2":"BA","iso_3166_3":"BIH","name":"Bosnia and Herzegovina","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":72,"iso_3166_2":"BW","iso_3166_3":"BWA","name":"Botswana","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":74,"iso_3166_2":"BV","iso_3166_3":"BVT","name":"Bouvet Island","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":76,"iso_3166_2":"BR","iso_3166_3":"BRA","name":"Brazil","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":86,"iso_3166_2":"IO","iso_3166_3":"IOT","name":"British Indian Ocean Territory","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":96,"iso_3166_2":"BN","iso_3166_3":"BRN","name":"Brunei Darussalam","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":100,"iso_3166_2":"BG","iso_3166_3":"BGR","name":"Bulgaria","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":854,"iso_3166_2":"BF","iso_3166_3":"BFA","name":"Burkina Faso","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":108,"iso_3166_2":"BI","iso_3166_3":"BDI","name":"Burundi","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":116,"iso_3166_2":"KH","iso_3166_3":"KHM","name":"Cambodia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":120,"iso_3166_2":"CM","iso_3166_3":"CMR","name":"Cameroon","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":124,"iso_3166_2":"CA","iso_3166_3":"CAN","name":"Canada","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":",","decimal_separator":"."},{"id":132,"iso_3166_2":"CV","iso_3166_3":"CPV","name":"Cape Verde","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":136,"iso_3166_2":"KY","iso_3166_3":"CYM","name":"Cayman Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":140,"iso_3166_2":"CF","iso_3166_3":"CAF","name":"Central African Republic","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":148,"iso_3166_2":"TD","iso_3166_3":"TCD","name":"Chad","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":152,"iso_3166_2":"CL","iso_3166_3":"CHL","name":"Chile","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":156,"iso_3166_2":"CN","iso_3166_3":"CHN","name":"China","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":",","decimal_separator":"."},{"id":162,"iso_3166_2":"CX","iso_3166_3":"CXR","name":"Christmas Island","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":166,"iso_3166_2":"CC","iso_3166_3":"CCK","name":"Cocos (Keeling) Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":170,"iso_3166_2":"CO","iso_3166_3":"COL","name":"Colombia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":174,"iso_3166_2":"KM","iso_3166_3":"COM","name":"Comoros","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":178,"iso_3166_2":"CG","iso_3166_3":"COG","name":"Congo","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":180,"iso_3166_2":"CD","iso_3166_3":"COD","name":"Congo, the Democratic Republic of the","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":184,"iso_3166_2":"CK","iso_3166_3":"COK","name":"Cook Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":188,"iso_3166_2":"CR","iso_3166_3":"CRI","name":"Costa Rica","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":384,"iso_3166_2":"CI","iso_3166_3":"CIV","name":"C\u00f4te d'Ivoire","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":191,"iso_3166_2":"HR","iso_3166_3":"HRV","name":"Croatia","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":192,"iso_3166_2":"CU","iso_3166_3":"CUB","name":"Cuba","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":531,"iso_3166_2":"CW","iso_3166_3":"CUW","name":"Cura\u00e7ao","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":196,"iso_3166_2":"CY","iso_3166_3":"CYP","name":"Cyprus","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":203,"iso_3166_2":"CZ","iso_3166_3":"CZE","name":"Czech Republic","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":208,"iso_3166_2":"DK","iso_3166_3":"DNK","name":"Denmark","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":262,"iso_3166_2":"DJ","iso_3166_3":"DJI","name":"Djibouti","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":212,"iso_3166_2":"DM","iso_3166_3":"DMA","name":"Dominica","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":214,"iso_3166_2":"DO","iso_3166_3":"DOM","name":"Dominican Republic","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":218,"iso_3166_2":"EC","iso_3166_3":"ECU","name":"Ecuador","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":818,"iso_3166_2":"EG","iso_3166_3":"EGY","name":"Egypt","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":222,"iso_3166_2":"SV","iso_3166_3":"SLV","name":"El Salvador","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":226,"iso_3166_2":"GQ","iso_3166_3":"GNQ","name":"Equatorial Guinea","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":232,"iso_3166_2":"ER","iso_3166_3":"ERI","name":"Eritrea","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":233,"iso_3166_2":"EE","iso_3166_3":"EST","name":"Estonia","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":" ","decimal_separator":null},{"id":231,"iso_3166_2":"ET","iso_3166_3":"ETH","name":"Ethiopia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":238,"iso_3166_2":"FK","iso_3166_3":"FLK","name":"Falkland Islands (Malvinas)","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":234,"iso_3166_2":"FO","iso_3166_3":"FRO","name":"Faroe Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":242,"iso_3166_2":"FJ","iso_3166_3":"FJI","name":"Fiji","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":246,"iso_3166_2":"FI","iso_3166_3":"FIN","name":"Finland","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":250,"iso_3166_2":"FR","iso_3166_3":"FRA","name":"France","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":254,"iso_3166_2":"GF","iso_3166_3":"GUF","name":"French Guiana","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":258,"iso_3166_2":"PF","iso_3166_3":"PYF","name":"French Polynesia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":260,"iso_3166_2":"TF","iso_3166_3":"ATF","name":"French Southern Territories","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":266,"iso_3166_2":"GA","iso_3166_3":"GAB","name":"Gabon","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":270,"iso_3166_2":"GM","iso_3166_3":"GMB","name":"Gambia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":268,"iso_3166_2":"GE","iso_3166_3":"GEO","name":"Georgia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":276,"iso_3166_2":"DE","iso_3166_3":"DEU","name":"Germany","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":288,"iso_3166_2":"GH","iso_3166_3":"GHA","name":"Ghana","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":292,"iso_3166_2":"GI","iso_3166_3":"GIB","name":"Gibraltar","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":300,"iso_3166_2":"GR","iso_3166_3":"GRC","name":"Greece","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":304,"iso_3166_2":"GL","iso_3166_3":"GRL","name":"Greenland","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":308,"iso_3166_2":"GD","iso_3166_3":"GRD","name":"Grenada","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":312,"iso_3166_2":"GP","iso_3166_3":"GLP","name":"Guadeloupe","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":316,"iso_3166_2":"GU","iso_3166_3":"GUM","name":"Guam","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":320,"iso_3166_2":"GT","iso_3166_3":"GTM","name":"Guatemala","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":831,"iso_3166_2":"GG","iso_3166_3":"GGY","name":"Guernsey","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":324,"iso_3166_2":"GN","iso_3166_3":"GIN","name":"Guinea","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":624,"iso_3166_2":"GW","iso_3166_3":"GNB","name":"Guinea-Bissau","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":328,"iso_3166_2":"GY","iso_3166_3":"GUY","name":"Guyana","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":332,"iso_3166_2":"HT","iso_3166_3":"HTI","name":"Haiti","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":334,"iso_3166_2":"HM","iso_3166_3":"HMD","name":"Heard Island and McDonald Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":336,"iso_3166_2":"VA","iso_3166_3":"VAT","name":"Holy See (Vatican City State)","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":340,"iso_3166_2":"HN","iso_3166_3":"HND","name":"Honduras","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":344,"iso_3166_2":"HK","iso_3166_3":"HKG","name":"Hong Kong","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":348,"iso_3166_2":"HU","iso_3166_3":"HUN","name":"Hungary","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":352,"iso_3166_2":"IS","iso_3166_3":"ISL","name":"Iceland","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":356,"iso_3166_2":"IN","iso_3166_3":"IND","name":"India","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":360,"iso_3166_2":"ID","iso_3166_3":"IDN","name":"Indonesia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":364,"iso_3166_2":"IR","iso_3166_3":"IRN","name":"Iran, Islamic Republic of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":368,"iso_3166_2":"IQ","iso_3166_3":"IRQ","name":"Iraq","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":372,"iso_3166_2":"IE","iso_3166_3":"IRL","name":"Ireland","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":",","decimal_separator":"."},{"id":833,"iso_3166_2":"IM","iso_3166_3":"IMN","name":"Isle of Man","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":376,"iso_3166_2":"IL","iso_3166_3":"ISR","name":"Israel","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":380,"iso_3166_2":"IT","iso_3166_3":"ITA","name":"Italy","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":388,"iso_3166_2":"JM","iso_3166_3":"JAM","name":"Jamaica","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":392,"iso_3166_2":"JP","iso_3166_3":"JPN","name":"Japan","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":832,"iso_3166_2":"JE","iso_3166_3":"JEY","name":"Jersey","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":400,"iso_3166_2":"JO","iso_3166_3":"JOR","name":"Jordan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":398,"iso_3166_2":"KZ","iso_3166_3":"KAZ","name":"Kazakhstan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":404,"iso_3166_2":"KE","iso_3166_3":"KEN","name":"Kenya","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":296,"iso_3166_2":"KI","iso_3166_3":"KIR","name":"Kiribati","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":408,"iso_3166_2":"KP","iso_3166_3":"PRK","name":"Korea, Democratic People's Republic of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":410,"iso_3166_2":"KR","iso_3166_3":"KOR","name":"Korea, Republic of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":414,"iso_3166_2":"KW","iso_3166_3":"KWT","name":"Kuwait","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":417,"iso_3166_2":"KG","iso_3166_3":"KGZ","name":"Kyrgyzstan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":418,"iso_3166_2":"LA","iso_3166_3":"LAO","name":"Lao People's Democratic Republic","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":428,"iso_3166_2":"LV","iso_3166_3":"LVA","name":"Latvia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":422,"iso_3166_2":"LB","iso_3166_3":"LBN","name":"Lebanon","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":426,"iso_3166_2":"LS","iso_3166_3":"LSO","name":"Lesotho","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":430,"iso_3166_2":"LR","iso_3166_3":"LBR","name":"Liberia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":434,"iso_3166_2":"LY","iso_3166_3":"LBY","name":"Libya","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":438,"iso_3166_2":"LI","iso_3166_3":"LIE","name":"Liechtenstein","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":440,"iso_3166_2":"LT","iso_3166_3":"LTU","name":"Lithuania","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":442,"iso_3166_2":"LU","iso_3166_3":"LUX","name":"Luxembourg","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":446,"iso_3166_2":"MO","iso_3166_3":"MAC","name":"Macao","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":807,"iso_3166_2":"MK","iso_3166_3":"MKD","name":"Macedonia, the former Yugoslav Republic of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":450,"iso_3166_2":"MG","iso_3166_3":"MDG","name":"Madagascar","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":454,"iso_3166_2":"MW","iso_3166_3":"MWI","name":"Malawi","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":458,"iso_3166_2":"MY","iso_3166_3":"MYS","name":"Malaysia","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":462,"iso_3166_2":"MV","iso_3166_3":"MDV","name":"Maldives","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":466,"iso_3166_2":"ML","iso_3166_3":"MLI","name":"Mali","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":470,"iso_3166_2":"MT","iso_3166_3":"MLT","name":"Malta","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":",","decimal_separator":"."},{"id":584,"iso_3166_2":"MH","iso_3166_3":"MHL","name":"Marshall Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":474,"iso_3166_2":"MQ","iso_3166_3":"MTQ","name":"Martinique","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":478,"iso_3166_2":"MR","iso_3166_3":"MRT","name":"Mauritania","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":480,"iso_3166_2":"MU","iso_3166_3":"MUS","name":"Mauritius","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":175,"iso_3166_2":"YT","iso_3166_3":"MYT","name":"Mayotte","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":484,"iso_3166_2":"MX","iso_3166_3":"MEX","name":"Mexico","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":583,"iso_3166_2":"FM","iso_3166_3":"FSM","name":"Micronesia, Federated States of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":498,"iso_3166_2":"MD","iso_3166_3":"MDA","name":"Moldova, Republic of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":492,"iso_3166_2":"MC","iso_3166_3":"MCO","name":"Monaco","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":496,"iso_3166_2":"MN","iso_3166_3":"MNG","name":"Mongolia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":499,"iso_3166_2":"ME","iso_3166_3":"MNE","name":"Montenegro","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":500,"iso_3166_2":"MS","iso_3166_3":"MSR","name":"Montserrat","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":504,"iso_3166_2":"MA","iso_3166_3":"MAR","name":"Morocco","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":508,"iso_3166_2":"MZ","iso_3166_3":"MOZ","name":"Mozambique","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":104,"iso_3166_2":"MM","iso_3166_3":"MMR","name":"Myanmar","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":516,"iso_3166_2":"NA","iso_3166_3":"NAM","name":"Namibia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":520,"iso_3166_2":"NR","iso_3166_3":"NRU","name":"Nauru","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":524,"iso_3166_2":"NP","iso_3166_3":"NPL","name":"Nepal","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":528,"iso_3166_2":"NL","iso_3166_3":"NLD","name":"Netherlands","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":540,"iso_3166_2":"NC","iso_3166_3":"NCL","name":"New Caledonia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":554,"iso_3166_2":"NZ","iso_3166_3":"NZL","name":"New Zealand","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":558,"iso_3166_2":"NI","iso_3166_3":"NIC","name":"Nicaragua","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":562,"iso_3166_2":"NE","iso_3166_3":"NER","name":"Niger","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":566,"iso_3166_2":"NG","iso_3166_3":"NGA","name":"Nigeria","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":570,"iso_3166_2":"NU","iso_3166_3":"NIU","name":"Niue","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":574,"iso_3166_2":"NF","iso_3166_3":"NFK","name":"Norfolk Island","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":580,"iso_3166_2":"MP","iso_3166_3":"MNP","name":"Northern Mariana Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":578,"iso_3166_2":"NO","iso_3166_3":"NOR","name":"Norway","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":512,"iso_3166_2":"OM","iso_3166_3":"OMN","name":"Oman","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":586,"iso_3166_2":"PK","iso_3166_3":"PAK","name":"Pakistan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":585,"iso_3166_2":"PW","iso_3166_3":"PLW","name":"Palau","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":275,"iso_3166_2":"PS","iso_3166_3":"PSE","name":"Palestinian Territory, Occupied","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":591,"iso_3166_2":"PA","iso_3166_3":"PAN","name":"Panama","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":598,"iso_3166_2":"PG","iso_3166_3":"PNG","name":"Papua New Guinea","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":600,"iso_3166_2":"PY","iso_3166_3":"PRY","name":"Paraguay","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":604,"iso_3166_2":"PE","iso_3166_3":"PER","name":"Peru","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":608,"iso_3166_2":"PH","iso_3166_3":"PHL","name":"Philippines","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":612,"iso_3166_2":"PN","iso_3166_3":"PCN","name":"Pitcairn","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":616,"iso_3166_2":"PL","iso_3166_3":"POL","name":"Poland","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":620,"iso_3166_2":"PT","iso_3166_3":"PRT","name":"Portugal","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":630,"iso_3166_2":"PR","iso_3166_3":"PRI","name":"Puerto Rico","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":634,"iso_3166_2":"QA","iso_3166_3":"QAT","name":"Qatar","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":638,"iso_3166_2":"RE","iso_3166_3":"REU","name":"R\u00e9union","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":642,"iso_3166_2":"RO","iso_3166_3":"ROU","name":"Romania","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":643,"iso_3166_2":"RU","iso_3166_3":"RUS","name":"Russian Federation","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":646,"iso_3166_2":"RW","iso_3166_3":"RWA","name":"Rwanda","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":652,"iso_3166_2":"BL","iso_3166_3":"BLM","name":"Saint Barth\u00e9lemy","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":654,"iso_3166_2":"SH","iso_3166_3":"SHN","name":"Saint Helena, Ascension and Tristan da Cunha","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":659,"iso_3166_2":"KN","iso_3166_3":"KNA","name":"Saint Kitts and Nevis","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":662,"iso_3166_2":"LC","iso_3166_3":"LCA","name":"Saint Lucia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":663,"iso_3166_2":"MF","iso_3166_3":"MAF","name":"Saint Martin (French part)","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":666,"iso_3166_2":"PM","iso_3166_3":"SPM","name":"Saint Pierre and Miquelon","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":670,"iso_3166_2":"VC","iso_3166_3":"VCT","name":"Saint Vincent and the Grenadines","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":882,"iso_3166_2":"WS","iso_3166_3":"WSM","name":"Samoa","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":674,"iso_3166_2":"SM","iso_3166_3":"SMR","name":"San Marino","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":678,"iso_3166_2":"ST","iso_3166_3":"STP","name":"Sao Tome and Principe","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":682,"iso_3166_2":"SA","iso_3166_3":"SAU","name":"Saudi Arabia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":686,"iso_3166_2":"SN","iso_3166_3":"SEN","name":"Senegal","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":688,"iso_3166_2":"RS","iso_3166_3":"SRB","name":"Serbia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":690,"iso_3166_2":"SC","iso_3166_3":"SYC","name":"Seychelles","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":694,"iso_3166_2":"SL","iso_3166_3":"SLE","name":"Sierra Leone","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":702,"iso_3166_2":"SG","iso_3166_3":"SGP","name":"Singapore","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":534,"iso_3166_2":"SX","iso_3166_3":"SXM","name":"Sint Maarten (Dutch part)","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":703,"iso_3166_2":"SK","iso_3166_3":"SVK","name":"Slovakia","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":705,"iso_3166_2":"SI","iso_3166_3":"SVN","name":"Slovenia","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":90,"iso_3166_2":"SB","iso_3166_3":"SLB","name":"Solomon Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":706,"iso_3166_2":"SO","iso_3166_3":"SOM","name":"Somalia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":710,"iso_3166_2":"ZA","iso_3166_3":"ZAF","name":"South Africa","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":239,"iso_3166_2":"GS","iso_3166_3":"SGS","name":"South Georgia and the South Sandwich Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":728,"iso_3166_2":"SS","iso_3166_3":"SSD","name":"South Sudan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":724,"iso_3166_2":"ES","iso_3166_3":"ESP","name":"Spain","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":144,"iso_3166_2":"LK","iso_3166_3":"LKA","name":"Sri Lanka","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":729,"iso_3166_2":"SD","iso_3166_3":"SDN","name":"Sudan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":740,"iso_3166_2":"SR","iso_3166_3":"SUR","name":"Suriname","swap_postal_code":false,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":744,"iso_3166_2":"SJ","iso_3166_3":"SJM","name":"Svalbard and Jan Mayen","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":748,"iso_3166_2":"SZ","iso_3166_3":"SWZ","name":"Swaziland","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":752,"iso_3166_2":"SE","iso_3166_3":"SWE","name":"Sweden","swap_postal_code":true,"swap_currency_symbol":true,"thousand_separator":null,"decimal_separator":null},{"id":756,"iso_3166_2":"CH","iso_3166_3":"CHE","name":"Switzerland","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":760,"iso_3166_2":"SY","iso_3166_3":"SYR","name":"Syrian Arab Republic","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":158,"iso_3166_2":"TW","iso_3166_3":"TWN","name":"Taiwan, Province of China","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":762,"iso_3166_2":"TJ","iso_3166_3":"TJK","name":"Tajikistan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":834,"iso_3166_2":"TZ","iso_3166_3":"TZA","name":"Tanzania, United Republic of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":764,"iso_3166_2":"TH","iso_3166_3":"THA","name":"Thailand","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":626,"iso_3166_2":"TL","iso_3166_3":"TLS","name":"Timor-Leste","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":768,"iso_3166_2":"TG","iso_3166_3":"TGO","name":"Togo","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":772,"iso_3166_2":"TK","iso_3166_3":"TKL","name":"Tokelau","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":776,"iso_3166_2":"TO","iso_3166_3":"TON","name":"Tonga","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":780,"iso_3166_2":"TT","iso_3166_3":"TTO","name":"Trinidad and Tobago","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":788,"iso_3166_2":"TN","iso_3166_3":"TUN","name":"Tunisia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":792,"iso_3166_2":"TR","iso_3166_3":"TUR","name":"Turkey","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":795,"iso_3166_2":"TM","iso_3166_3":"TKM","name":"Turkmenistan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":796,"iso_3166_2":"TC","iso_3166_3":"TCA","name":"Turks and Caicos Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":798,"iso_3166_2":"TV","iso_3166_3":"TUV","name":"Tuvalu","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":800,"iso_3166_2":"UG","iso_3166_3":"UGA","name":"Uganda","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":804,"iso_3166_2":"UA","iso_3166_3":"UKR","name":"Ukraine","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":784,"iso_3166_2":"AE","iso_3166_3":"ARE","name":"United Arab Emirates","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":826,"iso_3166_2":"GB","iso_3166_3":"GBR","name":"United Kingdom","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":840,"iso_3166_2":"US","iso_3166_3":"USA","name":"United States","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":",","decimal_separator":"."},{"id":581,"iso_3166_2":"UM","iso_3166_3":"UMI","name":"United States Minor Outlying Islands","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":858,"iso_3166_2":"UY","iso_3166_3":"URY","name":"Uruguay","swap_postal_code":true,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":860,"iso_3166_2":"UZ","iso_3166_3":"UZB","name":"Uzbekistan","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":548,"iso_3166_2":"VU","iso_3166_3":"VUT","name":"Vanuatu","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":862,"iso_3166_2":"VE","iso_3166_3":"VEN","name":"Venezuela, Bolivarian Republic of","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":704,"iso_3166_2":"VN","iso_3166_3":"VNM","name":"Viet Nam","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":92,"iso_3166_2":"VG","iso_3166_3":"VGB","name":"Virgin Islands, British","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":850,"iso_3166_2":"VI","iso_3166_3":"VIR","name":"Virgin Islands, U.S.","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":876,"iso_3166_2":"WF","iso_3166_3":"WLF","name":"Wallis and Futuna","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":732,"iso_3166_2":"EH","iso_3166_3":"ESH","name":"Western Sahara","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":887,"iso_3166_2":"YE","iso_3166_3":"YEM","name":"Yemen","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":894,"iso_3166_2":"ZM","iso_3166_3":"ZMB","name":"Zambia","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null},{"id":716,"iso_3166_2":"ZW","iso_3166_3":"ZWE","name":"Zimbabwe","swap_postal_code":false,"swap_currency_symbol":false,"thousand_separator":null,"decimal_separator":null}];
    var countryMap = {};
    for (var i=0; i<countries.length; i++) {
        var country = countries[i];
        countryMap[country.id] = country;
    }

    fx.base = 'EUR';
    fx.rates = {"ALL":1,"DZD":1,"AOA":1,"ARS":1,"AMD":1,"AWG":1,"AUD":1,"AZN":1,"BHD":1,"BDT":1,"BBD":1,"BYN":1,"BOB":1,"BAM":1,"BWP":1,"BRL":1,"GBP":1,"BND":1,"BGN":1,"CAD":1,"CVE":1,"XPF":1,"CLP":1,"CNY":1,"COP":1,"CRC":1,"HRK":1,"CZK":1,"DKK":1,"DOP":1,"XCD":1,"EGP":1,"EUR":1,"FJD":1,"GEL":1,"GHS":1,"":1,"GIP":1,"GTQ":1,"HTG":1,"HNL":1,"HKD":1,"HUF":1,"ISK":1,"INR":1,"IDR":1,"ILS":1,"JMD":1,"JPY":1,"JOD":1,"KZT":1,"KES":1,"KWD":1,"LBP":1,"MOP":1,"MKD":1,"MYR":1,"MVR":1,"MUR":1,"MXN":1,"MDL":1,"MAD":1,"MZN":1,"MMK":1,"NAD":1,"NPR":1,"ANG":1,"NZD":1,"NGN":1,"NOK":1,"OMR":1,"PKR":1,"PGK":1,"PEN":1,"PHP":1,"PLN":1,"QAR":1,"RON":1,"RUB":1,"RWF":1,"SAR":1,"RSD":1,"SGD":1,"ZAR":1,"KRW":1,"LKR":1,"SRD":1,"SEK":1,"CHF":1,"TWD":1,"TZS":1,"THB":1,"TTD":1,"TND":1,"TRY":1,"UGX":1,"UAH":1,"AED":1,"UYU":1,"USD":1,"VES":1,"VND":1,"XOF":1,"ZMW":1};

    var NINJA = NINJA || {};
        NINJA.primaryColor = "";
    NINJA.secondaryColor = "";
    NINJA.fontSize = 9;
    NINJA.headerFont = "Roboto";
    NINJA.bodyFont = "Roboto";
    
    function formatMoneyInvoice(value, invoice, decorator, precision) {
        var company = invoice.company;
        var client = invoice.client;

        return formatMoneyAccount(value, company, client, decorator, precision);
    }

    function formatMoneyAccount(value, company, client, decorator, precision) {
        var currencyId = false;
        var countryId = false;

        if (client && client.currency_id) {
            currencyId = client.currency_id;
        } else if (company && company.currency_id) {
            currencyId = company.currency_id;
        }

        if (client && client.country_id) {
            countryId = client.country_id;
        } else if (company && company.country_id) {
            countryId = company.country_id;
        }

        if (company && ! decorator) {
            decorator = parseInt(company.show_currency_code) ? 'code' : 'symbol';
        }

        return formatMoney(value, currencyId, countryId, decorator, precision)
    }

    function formatAmount(value, currencyId, precision) {
        if (!value) {
            return '';
        }

        if (!currencyId) {
            currencyId = 1;
        }

        if (!precision) {
            precision = 2;
        }

        var currency = currencyMap[currencyId];
        var decimal = currency.decimal_separator;

        value = roundToPrecision(NINJA.parseFloat(value), precision) + '';

        if (decimal == '.') {
            return value;
        } else {
            return value.replace('.', decimal);
        }
    }

    function formatMoney(value, currencyId, countryId, decorator, precision) {
        value = NINJA.parseFloat(value);

        if (!currencyId) {
            currencyId = 1;
        }

        var currency = currencyMap[currencyId];

        if (!currency) {
            currency = currencyMap[1];
        }

        if (!decorator) {
            decorator = 'symbol';
        }

        if (decorator == 'none') {
            var parts = (value + '').split('.');
            precision = parts.length > 1 ? Math.min(4, parts[1].length) : 0;
        } else if (!precision) {
            precision = currency.precision;
        } else if (currency.precision == 0) {
            precision = 0;
        }

        var thousand = currency.thousand_separator;
        var decimal = currency.decimal_separator;
        var code = currency.code;
        var swapSymbol = currency.swap_currency_symbol;

        if (countryId && currencyId == 3) {
            var country = countryMap[countryId];
            swapSymbol = country.swap_currency_symbol;
            if (country.thousand_separator) {
                thousand = country.thousand_separator;
            }
            if (country.decimal_separator) {
                decimal = country.decimal_separator;
            }
        }

        value = accounting.formatMoney(value, '', precision, thousand, decimal);
        var symbol = currency.symbol;

        if (decorator == 'none') {
            return value;
        } else if (decorator == 'code' || ! symbol) {
            return value + ' ' + code;
        } else if (swapSymbol) {
            return value + ' ' + symbol.trim();
        } else {
            return symbol + value;
        }
    }

    function convertCurrency(amount, fromCurrencyId, toCurrencyId) {
        return fx.convert(amount, {
            from: currencyMap[fromCurrencyId].code,
            to: currencyMap[toCurrencyId].code,
        });
    }

</script>

</head>

<body class="body">



<nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="height:60px;">

    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="#" id="left-menu-toggle" class="menu-toggle" title="Toggle Navigation">
          <div class="navbar-brand">
                <i class="fa fa-bars hide-phone" style="width:32px;padding-top:2px;float:left"></i>
                
                <img src="/images/invoiceninja-logo.png" width="193" height="25" style="float:left"/>
          </div>
      </a>
    </div>

    <a id="right-menu-toggle" class="menu-toggle hide-phone pull-right" title="Toggle History" style="cursor:pointer">
      <div class="fa fa-bars"></div>
    </a>

    <div class="collapse navbar-collapse" id="navbar-collapse-1">
      <div class="navbar-form navbar-right">

                          
        <div class="btn-group user-dropdown">
          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
            <div id="myAccountButton" class="ellipsis" style="max-width:130px;">
                                    Second company
                              <span class="caret"></span>
            </div>
          </button>
          <ul class="dropdown-menu user-accounts">
                                                                                                            <li style="margin-top: 4px; margin-bottom: 4px; min-width: 220px; cursor: pointer">
             
            <a href="/settings/company_details">
            
                    <div class="pull-left" style="width: 40px; min-height: 40px; margin-right: 16px">&nbsp;</div>
        
                    <b>
        
        <div class="company" style="padding-right:90px">Second company</div>
        <div class="user" style="padding-right:90px">Norman Osborn</div>

                    
            </b>
            </a>

</li>                                                                                                <li style="margin-top: 4px; margin-bottom: 4px; min-width: 220px; cursor: pointer">
                        <a href="/switch_account/1">
            
                    <div class="pull-left" style="width: 40px; min-height: 40px; margin-right: 16px">&nbsp;</div>
        
        
        <div class="company" style="padding-right:90px">Localhost company</div>
        <div class="user" style="padding-right:90px">Norman Osborn</div>

            </a>

</li>                                                                                                <li class="divider"></li>
                                            <li><a href="#" onclick="showSignUp()">Add CompanyPlan</a></li>
                                      <li><a href="#" onclick="logout()">Log Out</a></li>
          </ul>
        </div>

      </div>

      <form accept-charset="utf-8" class="form-horizontal navbar-form navbar-right" id="search-form" role="search" method="POST" action="/handle_command">
        <div class="form-group has-feedback">
          <input type="text" name="command" id="search" style="width: 280px;padding-top:0px;padding-bottom:0px;margin-right:12px;"
            class="form-control" placeholder="Search: shortcut is /"/>
                    </div>
      <input type="hidden" name="_token" value="frPRPZtD0NIePrpcFHDC2ELJ89NvFzlG0dzIkrWt"></form>

      
      <ul class="nav navbar-nav hide-non-phone" style="font-weight: bold">
                                        <li><a href="/dashboard">Dashboard</a></li>
                                                      <li><a href="/clients">Clients</a></li>
                                                      <li><a href="/products">Products</a></li>
                                                      <li><a href="/invoices">Invoices</a></li>
                                                      <li><a href="/payments">Payments</a></li>
                                                      <li><a href="/recurring_invoices">Recurring</a></li>
                                                      <li><a href="/credits">Credits</a></li>
                                                      <li><a href="/quotes">Quotes</a></li>
                                                      <li><a href="/proposals">Proposals</a></li>
                                                      <li><a href="/projects">Projects</a></li>
                                                      <li><a href="/tasks">Tasks</a></li>
                                                      <li><a href="/expenses">Expenses</a></li>
                                                      <li><a href="/vendors">Vendors</a></li>
                                                      <li><a href="/reports">Reports</a></li>
                                                      <li class="active"><a href="/settings">Settings</a></li>
                            </ul>
    </div><!-- /.navbar-collapse -->

</nav>

<div id="wrapper" class=' toggled-right'>

    <!-- Sidebar -->
    <div id="left-sidebar-wrapper" class="hide-phone">
        <ul class="sidebar-nav sidebar-nav-dark">
                                                <li class="nav-dashboard ">

        
    <a href="/dashboard"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-tachometer" style="width:46px; padding-right:10px"></i>
        Dashboard
        
    </a>

</li>
                                                                <li class="nav-clients ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/clients/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/clients"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-users" style="width:46px; padding-right:10px"></i>
        Clients
        
    </a>

</li>
                                                                <li class="nav-products ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/products/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/products"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-cube" style="width:46px; padding-right:10px"></i>
        Products
        
    </a>

</li>
                                                                <li class="nav-invoices ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/invoices/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/invoices"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-file-pdf-o" style="width:46px; padding-right:10px"></i>
        Invoices
        
    </a>

</li>
                                                                <li class="nav-payments ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/payments/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/payments"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-credit-card" style="width:46px; padding-right:10px"></i>
        Payments
        
    </a>

</li>
                                                                <li class="nav-recurring_invoices ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/recurring_invoices/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/recurring_invoices"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-files-o" style="width:46px; padding-right:10px"></i>
        Recurring
        
    </a>

</li>
                                                                <li class="nav-credits ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/credits/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/credits"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-credit-card" style="width:46px; padding-right:10px"></i>
        Credits
        
    </a>

</li>
                                                                <li class="nav-quotes ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/quotes/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/quotes"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-file-text-o" style="width:46px; padding-right:10px"></i>
        Quotes
        
    </a>

</li>
                                                                <li class="nav-proposals ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/proposals/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/proposals"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-th-large" style="width:46px; padding-right:10px"></i>
        Proposals
        
    </a>

</li>
                                                                <li class="nav-projects ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/projects/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/projects"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-briefcase" style="width:46px; padding-right:10px"></i>
        Projects
        
    </a>

</li>
                                                                <li class="nav-tasks ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/tasks/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/tasks"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-clock-o" style="width:46px; padding-right:10px"></i>
        Tasks
        
    </a>

</li>
                                                                <li class="nav-expenses ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/expenses/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/expenses"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-file-image-o" style="width:46px; padding-right:10px"></i>
        Expenses
        
    </a>

</li>
                                                                <li class="nav-vendors ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/vendors/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/vendors"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-building" style="width:46px; padding-right:10px"></i>
        Vendors
        
    </a>

</li>
                                                                <li class="nav-tickets ">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/tickets/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/tickets"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-life-ring" style="width:46px; padding-right:10px"></i>
        Tickets
        
    </a>

</li>
                                        
                            <li class="nav-reports ">

            <a type="button" class="btn btn-default btn-sm pull-right" title="Calendar"
            href="/reports/calendar">
            <i class="fa fa-calendar" style="width:20px"></i>
        </a>
    
    <a href="/reports"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
        <i class="fa fa-th-list" style="width:46px; padding-right:10px"></i>
        Reports
        
    </a>

</li>
                        <li class="nav-settings active">

            <a type="button" class="btn btn-default btn-sm pull-right" title="User Guide: Settings &gt; Localization"
            href="https://invoice-ninja.readthedocs.io/en/latest/settings.html#localization" target="_blank">
            <i class="fa fa-info-circle" style="width:20px"></i>
        </a>
    
    <a href="/settings"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link active">
        <i class="fa fa-cog" style="width:46px; padding-right:10px"></i>
        Settings
        
    </a>

</li>
            <li style="width:100%;">
                <div class="nav-footer">
                                            <a href="javascript:showContactUs()" title="Contact Us">
                            <i class="fa fa-envelope"></i>
                        </a>
                                        <a href="https://www.invoiceninja.com/forums/forum/support/" target="_blank" title="support forum">
                        <i class="fa fa-list-ul"></i>
                    </a>
                    <a href="javascript:showKeyboardShortcuts()" title="Help">
                        <i class="fa fa-question-circle"></i>
                    </a>
                    <a href="https://www.facebook.com/invoiceninja" target="_blank" title="Facebook">
                        <i class="fa fa-facebook-square"></i>
                    </a>
                    <a href="https://twitter.com/invoiceninja" target="_blank" title="Twitter">
                        <i class="fa fa-twitter-square"></i>
                    </a>
                    <a href="https://github.com/invoiceninja/invoiceninja/" target="_blank" title="GitHub">
                        <i class="fa fa-github-square"></i>
                    </a>
                </div>
            </li>
        </ul>
    </div>
    <!-- /#left-sidebar-wrapper -->

    <div id="right-sidebar-wrapper" class="hide-phone" style="overflow-y:hidden">
        <ul class="sidebar-nav sidebar-nav-dark">
            <li style="margin-top: 0px"><a type="button" class="btn btn-primary btn-sm pull-right" href="/invoices/create/2">
                                    <i class="fa fa-plus-circle" style="width:20px" title="Create Invoice"></i>
                                </a><a href="/clients/2"><div><i class="fa fa-users" style="width:32px"></i> Stevie Gleichner</div></a></li><li style="text-align:right; padding-right:18px;"><a href="/tasks/37">Task: Sunt aliquid rep... <i class="fa fa-clock-o" style="width:24px"></i></a></li><li style="text-align:right; padding-right:18px;"><a href="/projects/2">Project: Dolor in voluptatem. <i class="fa fa-briefcase" style="width:24px"></i></a></li><li style="text-align:right; padding-right:18px;"><a href="/tasks/103">Task: #103 <i class="fa fa-clock-o" style="width:24px"></i></a></li><li style="text-align:right; padding-right:18px;"><a href="/tasks/102">Task: #102 <i class="fa fa-clock-o" style="width:24px"></i></a></li><li style="text-align:right; padding-right:18px;"><a href="/tasks/101">Task: #101 <i class="fa fa-clock-o" style="width:24px"></i></a></li><li style="margin-top: 16px"><a type="button" class="btn btn-primary btn-sm pull-right" href="/invoices/create/9">
                                    <i class="fa fa-plus-circle" style="width:20px" title="Create Invoice"></i>
                                </a><a href="/clients/9"><div><i class="fa fa-users" style="width:32px"></i> Solon Schoen DVM</div></a></li><li style="text-align:right; padding-right:18px;"><a href="/projects/9">Project: Saepe expedita dolore et. <i class="fa fa-briefcase" style="width:24px"></i></a></li><li style="margin-top: 16px"><a type="button" class="btn btn-primary btn-sm pull-right" href="/invoices/create/1">
                                    <i class="fa fa-plus-circle" style="width:20px" title="Create Invoice"></i>
                                </a><a href="/clients/1"><div><i class="fa fa-users" style="width:32px"></i> Mr. Brian Carroll</div></a></li><li style="text-align:right; padding-right:18px;"><a href="/proposals/1">Proposal: 0017 <i class="fa fa-th-large" style="width:24px"></i></a></li><li style="margin-top: 16px"><a type="button" class="btn btn-primary btn-sm pull-right" href="/invoices/create/15">
                                    <i class="fa fa-plus-circle" style="width:20px" title="Create Invoice"></i>
                                </a><a href="/clients/15"><div><i class="fa fa-users" style="width:32px"></i> Alfonso Hammes I</div></a></li>
        </ul>
    </div>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="container-fluid">

          <div class="alert alert-warning" style="display:none" id="keepAliveDiv">
    This page will expire soon, <a href="#" onclick="keepAlive()">click here</a> to keep working
</div>

<script type="text/javascript">
    var redirectTimer = null;
    function startWarnSessionTimeout() {
        var oneMinute = 1000 * 60;
        var threeMinutes = oneMinute * 3;
        var waitTime = oneMinute * 60 * 4; // 4 hours

        setTimeout(function() {
            warnSessionExpring();
        }, (waitTime - threeMinutes));
    }

    function warnSessionExpring() {
        $("#keepAliveDiv").fadeIn();
        redirectTimer = setTimeout(function() {
            NINJA.formIsChanged = false;
            window.location = 'http://invninjv1.local/dashboard';
        }, 1000 * 60);
    }

    // keep the token cookie valid to prevent token mismatch errors
    function keepAlive() {
        clearTimeout(redirectTimer);
        $('#keepAliveDiv').fadeOut();
        $.get('http://invninjv1.local/keep_alive');
        startWarnSessionTimeout();
    }

    $(function() {
        if ($('form.warn-on-exit, form.form-signin').length > 0) {
            startWarnSessionTimeout();
        }
    });
</script>

          
          
          
          <div class="pull-right">
                        </div>

                      <ol class="breadcrumb"></ol>
          
              

    <form enctype="multipart/form-data" accept-charset="utf-8" class="form-horizontal warn-on-exit" method="POST">
    
    
    

    <script type="text/javascript">
    $(function() {
        if (isStorageSupported() && /\/settings\//.test(location.href)) {
            localStorage.setItem('last:settings_page', location.href);
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            if (history.pushState) {
                history.pushState(null, null, target);
            }
        });

    })
</script>

<div class="row">
    <div class="col-md-3">
                    <div class="panel panel-default">
                <div class="panel-heading" style="color:white">
                    Basic Settings
                                    </div>
                <div class="list-group">
                                                                        <a href="/settings/company_details" class="list-group-item "
                                style="width:100%;text-align:left">CompanyPlan Details</a>
                                                                                                <a href="/settings/user_details" class="list-group-item "
                                style="width:100%;text-align:left">User Details</a>
                                                                                                <a href="/settings/localization" class="list-group-item selected"
                                style="width:100%;text-align:left">Localization</a>
                                                                                                <a href="/settings/online_payments" class="list-group-item "
                                style="width:100%;text-align:left">Online Payments</a>
                                                                                                <a href="/settings/tax_rates" class="list-group-item "
                                style="width:100%;text-align:left">Tax Rates</a>
                                                                                                <a href="/settings/products" class="list-group-item "
                                style="width:100%;text-align:left">Products</a>
                                                                                                <a href="/settings/notifications" class="list-group-item "
                                style="width:100%;text-align:left">Notifications</a>
                                                                                                <a href="/settings/import_export" class="list-group-item "
                                style="width:100%;text-align:left">Import | Export</a>
                                                                                                <a href="/settings/account_management" class="list-group-item "
                                style="width:100%;text-align:left">company Management</a>
                                                                                </div>
            </div>
                    <div class="panel panel-default">
                <div class="panel-heading" style="color:white">
                    Advanced Settings
                                    </div>
                <div class="list-group">
                                                                        <a href="/settings/invoice_settings" class="list-group-item "
                                style="width:100%;text-align:left">Invoice Settings</a>
                                                                                                <a href="/settings/invoice_design" class="list-group-item "
                                style="width:100%;text-align:left">Invoice Design</a>
                                                                                                <a href="/settings/client_portal" class="list-group-item "
                                style="width:100%;text-align:left">Client Portal</a>
                                                                                                <a href="/settings/email_settings" class="list-group-item "
                                style="width:100%;text-align:left">Email Settings</a>
                                                                                                <a href="/settings/templates_and_reminders" class="list-group-item "
                                style="width:100%;text-align:left">Templates &amp; Reminders</a>
                                                                                                <a href="/settings/bank_accounts" class="list-group-item "
                                style="width:100%;text-align:left">Credit Cards &amp; Banks</a>
                                                                                                <a href="/settings/tickets" class="list-group-item "
                                style="width:100%;text-align:left">Tickets</a>
                                                                                                <a href="/settings/data_visualizations" class="list-group-item "
                                style="width:100%;text-align:left">Data Visualizations</a>
                                                                                                <a href="/settings/api_tokens" class="list-group-item "
                                style="width:100%;text-align:left">API Tokens</a>
                                                                                                <a href="/settings/user_management" class="list-group-item "
                                style="width:100%;text-align:left">User Management</a>
                                                                                        <a href="/settings/system_settings" class="list-group-item "
                            style="width:100%;text-align:left">System Settings</a>
                                    </div>
            </div>
        
            </div>

    <div class="col-md-9">

    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Localization</h3>
            </div>
                <div class="panel-body form-padding-right">

                <div class="form-group"><label for="currency_id" class="control-label col-lg-4 col-sm-4">Currency</label><div class="col-lg-8 col-sm-8"><select class="form-control" onchange="updateCurrencyCodeRadio()" id="currency_id" name="currency_id"><option value="94">Albanian Lek</option><option value="90">Algerian Dinar</option><option value="82">Angolan Kwanza</option><option value="23">Argentine Peso</option><option value="97">Armenian Dram</option><option value="40">Aruban Florin</option><option value="12">Australian Dollar</option><option value="98">Azerbaijan Manat</option><option value="77">Bahraini Dinar</option><option value="24">Bangladeshi Taka</option><option value="71">Barbadian Dollar</option><option value="100">Belarusian Ruble</option><option value="93">Bolivian Boliviano</option><option value="99">Bosnia and Herzegovina Convertible Mark</option><option value="68">Botswana Pula</option><option value="20">Brazilian Real</option><option value="2">British Pound</option><option value="72">Brunei Dollar</option><option value="39">Bulgarian Lev</option><option value="86">CFP Franc</option><option value="9">Canadian Dollar</option><option value="88">Cape Verdean Escudo</option><option value="62">Chilean Peso</option><option value="32">Chinese Renminbi</option><option value="30">Colombian Peso</option><option value="47">Costa Rican Colón</option><option value="43">Croatian Kuna</option><option value="51">Czech Koruna</option><option value="5">Danish Krone</option><option value="61">Dominican Peso</option><option value="37">East Caribbean Dollar</option><option value="29">Egyptian Pound</option><option value="3">Euro</option><option value="92">Fijian Dollar</option><option value="73">Georgian Lari</option><option value="38">Ghanaian Cedi</option><option value="101">Gibraltar Pound</option><option value="104">Gibraltar Pound</option><option value="18">Guatemalan Quetzal</option><option value="83">Haitian Gourde</option><option value="75">Honduran Lempira</option><option value="26">Hong Kong Dollar</option><option value="69">Hungarian Forint</option><option value="63">Icelandic Króna</option><option value="11">Indian Rupee</option><option value="27">Indonesian Rupiah</option><option value="6">Israeli Shekel</option><option value="81">Jamaican Dollar</option><option value="45">Japanese Yen</option><option value="65">Jordanian Dinar</option><option value="103">Kazakhstani Tenge</option><option value="8">Kenyan Shilling</option><option value="89">Kuwaiti Dinar</option><option value="96">Lebanese Pound</option><option value="59">Macanese Pataca</option><option value="91">Macedonian Denar</option><option value="19">Malaysian Ringgit</option><option value="46">Maldivian Rufiyaa</option><option value="87">Mauritian Rupee</option><option value="28">Mexican Peso</option><option value="102">Moldovan Leu</option><option value="80">Moroccan Dirham</option><option value="56">Mozambican Metical</option><option value="66">Myanmar Kyat</option><option value="53">Namibian Dollar</option><option value="85">Nepalese Rupee</option><option value="35">Netherlands Antillean Guilder</option><option value="15">New Zealand Dollar</option><option value="22">Nigerian Naira</option><option value="14">Norske Kroner</option><option value="57">Omani Rial</option><option value="48">Pakistani Rupee</option><option value="64">Papua New Guinean Kina</option><option value="67">Peruvian Sol</option><option value="10">Philippine Peso</option><option value="49">Polish Zloty</option><option value="74">Qatari Riyal</option><option value="42">Romanian New Leu</option><option value="55">Russian Ruble</option><option value="33">Rwandan Franc</option><option value="44">Saudi Riyal</option><option value="95">Serbian Dinar</option><option value="13">Singapore Dollar</option><option value="4">South African Rand</option><option value="79">South Korean Won</option><option value="50">Sri Lankan Rupee</option><option value="76">Surinamese Dollar</option><option value="7">Swedish Krona</option><option value="17">Swiss Franc</option><option value="60">Taiwan New Dollar</option><option value="34">Tanzanian Shilling</option><option value="21">Thai Baht</option><option value="36">Trinidad and Tobago Dollar</option><option value="54">Tunisian Dinar</option><option value="41">Turkish Lira</option><option value="1" selected="selected">US Dollar</option><option value="70">Ugandan Shilling</option><option value="58">Ukrainian Hryvnia</option><option value="25">United Arab Emirates Dirham</option><option value="52">Uruguayan Peso</option><option value="78">Venezuelan Bolivars</option><option value="16">Vietnamese Dong</option><option value="31">West African Franc</option><option value="84">Zambian Kwacha</option></select></div></div>
                <div class="form-group currrency_radio"><label for="show_currency_code" class="control-label col-lg-4 col-sm-4">&nbsp;</label><div class="col-lg-8 col-sm-8"><label for="show_currency_code2" class="radio-inline"><input value="0" id="show_currency_code2" type="radio" name="show_currency_code" checked="checked">Symbol: <span id="currency_symbol_example"/></label><label for="show_currency_code3" class="radio-inline"><input value="1" id="show_currency_code3" type="radio" name="show_currency_code">Code: <span id="currency_code_example"/></label></div></div>
                <br/>

                <div class="form-group"><label for="language_id" class="control-label col-lg-4 col-sm-4">Language</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="language_id" name="language_id"><option value=""></option><option value="18">Albanian</option><option value="31">Bulgarian</option><option value="28">Chinese - Taiwan</option><option value="17">Croatian</option><option value="16">Czech</option><option value="9">Danish</option><option value="6">Dutch</option><option value="1" selected="selected">English</option><option value="29">English - Australia</option><option value="20">English - United Kingdom</option><option value="23">Finnish</option><option value="4">French</option><option value="13">French - Canada</option><option value="3">German</option><option value="19">Greek</option><option value="2">Italian</option><option value="10">Japanese</option><option value="14">Lithuanian</option><option value="27">Macedonian</option><option value="8">Norwegian</option><option value="15">Polish</option><option value="5">Portuguese - Brazilian</option><option value="21">Portuguese - Portugal</option><option value="24">Romanian</option><option value="30">Serbian</option><option value="22">Slovenian</option><option value="7">Spanish</option><option value="12">Spanish - Spain</option><option value="11">Swedish</option><option value="26">Thai</option><option value="25">Turkish - Turkey</option></select><span class="help-block">Help improve our translations with <a href="https://www.transifex.com/invoice-ninja/invoice-ninja" target="_blank">Transifex.com</a></span></div></div>
                <br/>&nbsp;<br/>

                <div class="form-group"><label for="timezone_id" class="control-label col-lg-4 col-sm-4">Timezone</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="timezone_id" name="timezone_id"><option value=""></option><option value="1">(GMT-11:00) Midway Island</option><option value="2">(GMT-11:00) Samoa</option><option value="3">(GMT-10:00) Hawaii</option><option value="4">(GMT-09:00) Alaska</option><option value="5" selected="selected">(GMT-08:00) Pacific Time (US &amp; Canada)</option><option value="6">(GMT-08:00) Tijuana</option><option value="7">(GMT-07:00) Arizona</option><option value="8">(GMT-07:00) Mountain Time (US &amp; Canada)</option><option value="9">(GMT-07:00) Chihuahua</option><option value="10">(GMT-07:00) Mazatlan</option><option value="11">(GMT-06:00) Mexico City</option><option value="12">(GMT-06:00) Monterrey</option><option value="13">(GMT-06:00) Saskatchewan</option><option value="14">(GMT-06:00) Central Time (US &amp; Canada)</option><option value="15">(GMT-05:00) Eastern Time (US &amp; Canada)</option><option value="16">(GMT-05:00) Indiana (East)</option><option value="17">(GMT-05:00) Bogota</option><option value="18">(GMT-05:00) Lima</option><option value="19">(GMT-04:00) Caracas</option><option value="20">(GMT-04:00) Atlantic Time (Canada)</option><option value="21">(GMT-04:00) La Paz</option><option value="22">(GMT-04:00) Santiago</option><option value="23">(GMT-03:30) Newfoundland</option><option value="24">(GMT-03:00) Buenos Aires</option><option value="25">(GMT-03:00) Greenland</option><option value="26">(GMT-02:00) Stanley</option><option value="27">(GMT-01:00) Azores</option><option value="28">(GMT-01:00) Cape Verde Is.</option><option value="29">(GMT) Casablanca</option><option value="30">(GMT) Dublin</option><option value="31">(GMT) Lisbon</option><option value="32">(GMT) London</option><option value="33">(GMT) Monrovia</option><option value="34">(GMT+01:00) Amsterdam</option><option value="35">(GMT+01:00) Belgrade</option><option value="36">(GMT+01:00) Berlin</option><option value="37">(GMT+01:00) Bratislava</option><option value="38">(GMT+01:00) Brussels</option><option value="39">(GMT+01:00) Budapest</option><option value="40">(GMT+01:00) Copenhagen</option><option value="41">(GMT+01:00) Ljubljana</option><option value="42">(GMT+01:00) Madrid</option><option value="43">(GMT+01:00) Paris</option><option value="44">(GMT+01:00) Prague</option><option value="45">(GMT+01:00) Rome</option><option value="46">(GMT+01:00) Sarajevo</option><option value="47">(GMT+01:00) Skopje</option><option value="48">(GMT+01:00) Stockholm</option><option value="49">(GMT+01:00) Vienna</option><option value="50">(GMT+01:00) Warsaw</option><option value="51">(GMT+01:00) Zagreb</option><option value="52">(GMT+02:00) Athens</option><option value="53">(GMT+02:00) Bucharest</option><option value="54">(GMT+02:00) Cairo</option><option value="55">(GMT+02:00) Harare</option><option value="56">(GMT+02:00) Helsinki</option><option value="57">(GMT+02:00) Istanbul</option><option value="58">(GMT+02:00) Jerusalem</option><option value="59">(GMT+02:00) Kyiv</option><option value="60">(GMT+02:00) Minsk</option><option value="61">(GMT+02:00) Riga</option><option value="62">(GMT+02:00) Sofia</option><option value="63">(GMT+02:00) Tallinn</option><option value="64">(GMT+02:00) Vilnius</option><option value="65">(GMT+03:00) Baghdad</option><option value="66">(GMT+03:00) Kuwait</option><option value="67">(GMT+03:00) Nairobi</option><option value="68">(GMT+03:00) Riyadh</option><option value="69">(GMT+03:30) Tehran</option><option value="70">(GMT+04:00) Moscow</option><option value="71">(GMT+04:00) Baku</option><option value="72">(GMT+04:00) Volgograd</option><option value="73">(GMT+04:00) Muscat</option><option value="74">(GMT+04:00) Tbilisi</option><option value="75">(GMT+04:00) Yerevan</option><option value="76">(GMT+04:30) Kabul</option><option value="77">(GMT+05:00) Karachi</option><option value="78">(GMT+05:00) Tashkent</option><option value="79">(GMT+05:30) Kolkata</option><option value="80">(GMT+05:45) Kathmandu</option><option value="81">(GMT+06:00) Ekaterinburg</option><option value="82">(GMT+06:00) Almaty</option><option value="83">(GMT+06:00) Dhaka</option><option value="84">(GMT+07:00) Novosibirsk</option><option value="85">(GMT+07:00) Bangkok</option><option value="86">(GMT+07.00) Ho Chi Minh</option><option value="87">(GMT+07:00) Jakarta</option><option value="88">(GMT+08:00) Krasnoyarsk</option><option value="89">(GMT+08:00) Chongqing</option><option value="90">(GMT+08:00) Hong Kong</option><option value="91">(GMT+08:00) Kuala Lumpur</option><option value="92">(GMT+08:00) Perth</option><option value="93">(GMT+08:00) Singapore</option><option value="94">(GMT+08:00) Taipei</option><option value="95">(GMT+08:00) Ulaan Bataar</option><option value="96">(GMT+08:00) Urumqi</option><option value="97">(GMT+09:00) Irkutsk</option><option value="98">(GMT+09:00) Seoul</option><option value="99">(GMT+09:00) Tokyo</option><option value="100">(GMT+09:30) Adelaide</option><option value="101">(GMT+09:30) Darwin</option><option value="102">(GMT+10:00) Yakutsk</option><option value="103">(GMT+10:00) Brisbane</option><option value="104">(GMT+10:00) Canberra</option><option value="105">(GMT+10:00) Guam</option><option value="106">(GMT+10:00) Hobart</option><option value="107">(GMT+10:00) Melbourne</option><option value="108">(GMT+10:00) Port Moresby</option><option value="109">(GMT+10:00) Sydney</option><option value="110">(GMT+11:00) Vladivostok</option><option value="111">(GMT+12:00) Magadan</option><option value="112">(GMT+12:00) Auckland</option><option value="113">(GMT+12:00) Fiji</option></select></div></div>
                <div class="form-group"><label for="date_format_id" class="control-label col-lg-4 col-sm-4">Date Format</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="date_format_id" name="date_format_id"><option value=""></option><option value="1">31/Dec/2020</option><option value="2">31-Dec-2020</option><option value="3">31/December/2020</option><option value="4">31-December-2020</option><option value="5">Dec 31, 2020</option><option value="6">December 31, 2020</option><option value="7">Thu Dec 31, 2020</option><option value="8">2020-12-31</option><option value="9">31-12-2020</option><option value="10">12/31/2020</option><option value="11">31.12.2020</option><option value="12">31. Dec. 2020</option><option value="13">31. December 2020</option></select></div></div>
                <div class="form-group"><label for="datetime_format_id" class="control-label col-lg-4 col-sm-4">Date/Time Format</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="datetime_format_id" name="datetime_format_id"><option value=""></option><option value="1">31/Dec/2020 12:00 am</option><option value="2">31-Dec-2020 12:00 am</option><option value="3">31/December/2020 12:00 am</option><option value="4">31-December-2020 12:00 am</option><option value="5">Dec 31, 2020 12:00 am</option><option value="6">December 31, 2020 12:00 am</option><option value="7">Thu Dec 31st, 2020 12:00 am</option><option value="8">2020-12-31 12:00 am</option><option value="9">31-12-2020 12:00 am</option><option value="10">12/31/2020 12:00 am</option><option value="11">31.12.2020 12:00 am</option><option value="12">31. Dec. 2020 12:00 am</option><option value="13">31. December 2020 12:00 am</option></select></div></div>
                <div class="form-group"><label for="military_time" class="control-label col-lg-4 col-sm-4">24 Hour Time</label><div class="col-lg-8 col-sm-8"><div class="checkbox"><label for="military_time" class=""><input type="hidden" name="military_time" value="0"><input id="military_time" type="checkbox" name="military_time" value="1">Enable</label></div></div></div>

                <br/>&nbsp;<br/>

                <div class="form-group"><label for="start_of_week" class="control-label col-lg-4 col-sm-4">First Day of the Week</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="start_of_week" name="start_of_week"><option value=""></option><option value="0">Sunday</option><option value="1" selected="selected">Monday</option><option value="2">Tuesday</option><option value="3">Wednesday</option><option value="4">Thursday</option><option value="5">Friday</option><option value="6">Saturday</option></select><span class="help-block">Used by <b>date</b> selectors</span></div></div>

                <div class="form-group"><label for="financial_year_start" class="control-label col-lg-4 col-sm-4">First Month of the Year</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="financial_year_start" name="financial_year_start"><option value=""></option><option value="2000-01-01" selected="selected">January</option><option value="2000-02-01">February</option><option value="2000-03-01">March</option><option value="2000-04-01">April</option><option value="2000-05-01">May</option><option value="2000-06-01">June</option><option value="2000-07-01">July</option><option value="2000-08-01">August</option><option value="2000-09-01">September</option><option value="2000-10-01">October</option><option value="2000-11-01">November</option><option value="2000-12-01">December</option></select><span class="help-block">Used by <b>date range</b> selectors</span></div></div>


                </div>
            </div>
        </div>
    </div>

    <center>
        <button type='submit' class='btn btn-success btn-lg'>Save <span class='glyphicon glyphicon-floppy-disk'></span></button>
    </center>

    <input type="hidden" name="_token" value="frPRPZtD0NIePrpcFHDC2ELJ89NvFzlG0dzIkrWt"></form>

    <script type="text/javascript">

        function updateCurrencyCodeRadio() {
            var currencyId = $('#currency_id').val();
            var currency = currencyMap[currencyId];
            var symbolExample = '';
            var codeExample = '';

            if ( ! currency || ! currency.symbol) {
                $('.currrency_radio').hide();
            } else {
                symbolExample = formatMoney(1000, currencyId, 840, 'symbol');
                codeExample = formatMoney(1000, currencyId, 840, 'code');
                $('.currrency_radio').show();
            }

            $('#currency_symbol_example').text(symbolExample);
            $('#currency_code_example').text(codeExample);
        }

    </script>

          <br/>
          <div class="row">
            <div class="col-md-12">

                              Powered by


<a href="https://www.invoiceninja.com/?utm_source=powered_by" target="_blank" title="Created by Hillel Coren">Invoice Ninja</a> -
<a href="https://github.com/invoiceninja/invoiceninja/releases" target="_blank" title="Trello Roadmap">v4.5.19</a> |

  <a href="#" onclick="showWhiteLabelModal()">White label</a>

  <div class="modal fade" id="whiteLabelModal" tabindex="-1" role="dialog" aria-labelledby="whiteLabelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">White Label</h4>
        </div>

        <div class="container" style="width: 100%; padding-bottom: 0px !important">
        <div class="panel panel-default">
        <div class="panel-body">
          <p>Purchase a ONE YEAR white label license for $30 to remove the Invoice Ninja branding from the invoice and client portal.</p>
          <div class="row">
              <div class="col-md-6">
                  <h4>Before</h4>
                  <img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="/images/pro_plan/white_label_before.png" width="100%" alt="before">
              </div>
              <div class="col-md-6">
                  <h4>After</h4>
                  <img src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="/images/pro_plan/white_label_after.png" width="100%" alt="after">
              </div>
          </div>
          <br/>
          <p>Note: the white-label license is intended for personal use, please email us at <a href="&#109;&#x61;ilt&#111;&#x3a;&#x63;o&#x6e;ta&#x63;&#x74;&#64;&#105;&#x6e;vo&#x69;&#99;&#x65;&#x6e;i&#x6e;&#x6a;&#x61;&#46;&#x63;&#x6f;&#x6d;">&#x63;o&#x6e;ta&#x63;&#x74;&#64;&#105;&#x6e;vo&#x69;&#99;&#x65;&#x6e;i&#x6e;&#x6a;&#x61;&#46;&#x63;&#x6f;&#x6d;</a> if you'd like to resell the app.</p>
        </div>
        </div>
        </div>

        <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close </button>
          <!-- <button type="button" class="btn btn-primary" onclick="showRecoverLicense()">Recover </button> -->
          <button type="button" class="btn btn-primary" onclick="showApplyLicense()">Apply </button>
          <button type="button" class="btn btn-success" onclick="buyWhiteLabel()">Purchase </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="applyWhiteLabelModal" tabindex="-1" role="dialog" aria-labelledby="applyWhiteLabelModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Apply White Label License</h4>
          </div>

          <div class="container" style="width: 100%; padding-bottom: 0px !important">
          <div class="panel panel-default">
          <div class="panel-body">
              <form accept-charset="utf-8" class="form-horizontal" method="POST">
              <div class="form-group required"><label for="white_label_license_key" class="control-label col-lg-4 col-sm-4">License Key</label><div class="col-lg-8 col-sm-8"><input required pattern=".{24,}" maxlength="24" class="form-control" id="white_label_license_key" type="input" name="white_label_license_key"></div></div>
              <input type="hidden" name="_token" value="frPRPZtD0NIePrpcFHDC2ELJ89NvFzlG0dzIkrWt"></form>
          </div>
          </div>
          </div>

          <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close </button>
            <button type="button" class="btn btn-success" onclick="applyLicense()">Submit </button>
          </div>
        </div>
      </div>
  </div>

  <div class="modal fade" id="recoverWhiteLabelModal" tabindex="-1" role="dialog" aria-labelledby="recoverWhiteLabelModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Recover White Label License</h4>
          </div>

          <div class="container" style="width: 100%; padding-bottom: 0px !important">
          <div class="panel panel-default">
          <div class="panel-body">
              <form accept-charset="utf-8" class="form-horizontal" method="POST">
              <div class="form-group required"><label for="white_label_license_email" class="control-label col-lg-4 col-sm-4">Email</label><div class="col-lg-8 col-sm-8"><input required class="form-control" id="white_label_license_email" type="email" name="white_label_license_email"></div></div>
              <input type="hidden" name="_token" value="frPRPZtD0NIePrpcFHDC2ELJ89NvFzlG0dzIkrWt"></form>
          </div>
          </div>
          </div>

          <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close </button>
            <button type="button" class="btn btn-success" onclick="applyLicense()">Submit </button>
          </div>
        </div>
      </div>
  </div>

<script type="text/javascript">

    function showWhiteLabelModal() {
        loadImages('#whiteLabelModal');
        $('#whiteLabelModal').modal('show');
    }

    function buyWhiteLabel() {
        buyProduct('92D2J5', '3');
    }

    function buyProduct(affiliateKey, productId) {
        location.href = "/white_label/purchase";
    }

    function showApplyLicense() {
        $('#whiteLabelModal').modal('hide');
        $('#applyWhiteLabelModal').modal('show');
    }

    function showRecoverLicense() {
        $('#whiteLabelModal').modal('hide');
        $('#recoverWhiteLabelModal').modal('show');
    }

    function applyLicense() {
        var license = $('#white_label_license_key').val();
        window.location = "/dashboard?license_key=" + license + "&product_id=3";
    }

</script>
                          </div>
        </div>
    </div>

                    <!-- /#page-content-wrapper -->
</div>

<form accept-charset="utf-8" class="form-vertical contact-us-form" onsubmit="return onContactUsFormSubmit()" method="POST">

<div class="modal fade" id="contactUsModal" tabindex="-1" role="dialog" aria-labelledby="contactUsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Contact Us</h4>
      </div>

      <div class="container" style="width: 100%; padding-bottom: 0px !important">
      <div class="panel panel-default">
      <div class="panel-body">
          <div class="input-div">
              <div class="form-group required"><label for="" class="control-label">From</label><div required class="form-control-static" id="contact_us_from">Norman Osborn &lt;normansecond@rideafide.local&gt;</div></div>

              <div class="form-group required"><label for="contact_us_message" class="control-label">Message</label><textarea required class="form-control" rows="10" id="contact_us_message" name="contact_us_message"></textarea></div>

                                    <div class="form-group"><div class="checkbox"><label for="include_errors" class=""><input type="hidden" name="include_errors" value="0"><input id="include_errors" type="checkbox" name="include_errors" value="1">Include <a href="/errors" target="_blank">recent errors</a> from storage/logs/laravel-error.log</label></div></div>
                          </div>
          <div class="response-div" style="display: none; font-size: 16px">
              Thank you for your message! We&#039;ll try to respond as soon as possible.
          </div>
      </div>
      </div>
      </div>

      <div class="modal-footer">
        <div class="input-div">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
        <div class="response-div" style="display: none;">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>

<input type="hidden" name="_token" value="frPRPZtD0NIePrpcFHDC2ELJ89NvFzlG0dzIkrWt"></form>

<script type="text/javascript">

    function showContactUs() {
        $('#contactUsModal').modal('show');
    }

    $(function() {
        $('#contactUsModal').on('shown.bs.modal', function() {
            var message = '';
                            message = '\n\n' + "App Version: v4.5.19\nWhite Label: No - hcm2o7uvejuorwwsaqqxhjeigivwqwbo\nServer OS: Linux 5.3.0-64-generic\nPHP Version: 7.3.19\nMySQL Version: 5.6.48";
                        $('#contactUsModal .input-div').show();
            $('#contactUsModal .response-div').hide();
            $("#contact_us_message").val(message).focus().selectRange(0, 0);
        })
    })

    function onContactUsFormSubmit() {
        $('#contactUsModal .modal-footer button').attr('disabled', true);

        $.post("/contact_us", $('.contact-us-form').serialize(), function(data) {
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
<script type="text/javascript">

$(function() {

    validateSignUp();

    $('#signUpModal').on('shown.bs.modal', function () {
        trackEvent('/company', '/view_sign_up');
        // change the type after page load to prevent errors in Chrome console
        $('#new_password').attr('type', 'password');
        $(['first_name','last_name','email','password']).each(function(i, field) {
            var $input = $('form.signUpForm #new_'+field);
            if (!$input.val()) {
                $input.focus();
                return false;
            }
        });
    })

    
    // Ensure terms is checked for sign up form
        setSignupEnabled(false);
    $("#terms_checkbox, #privacy_checkbox").change(function() {
        setSignupEnabled($('#terms_checkbox').is(':checked') && $('#privacy_checkbox').is(':checked'));
    });
    
});


function showSignUp() {
    if (location.href.indexOf('/dashboard') == -1) {
        location.href = "/dashboard?sign_up=true";
    } else {
        $('#signUpModal').modal('show');
    }
}

function hideSignUp() {
    $('#signUpModal').modal('hide');
}

function setSignupEnabled(enabled) {
    $('.signup-form input[type=text]').prop('disabled', !enabled);
    $('.signup-form input[type=password]').prop('disabled', !enabled);
    if (enabled) {
        $('.signup-form a.btn').removeClass('disabled');
    } else {
        $('.signup-form a.btn').addClass('disabled');
    }
}

function validateSignUp(showError) {
    var isFormValid = true;
    $(['first_name','last_name','email','password']).each(function(i, field) {
        var $input = $('form.signUpForm #new_'+field),
        val = $.trim($input.val());
        var isValid = val && val.length >= (field == 'password' ? 8 : 1);

        if (field == 'password') {
            var score = scorePassword(val);
            if (isValid) {
                isValid = score > 50;
            }

            showPasswordStrength(val, score);
        }

        if (isValid && field == 'email') {
            isValid = isValidEmailAddress(val);
        }
        if (isValid) {
            $input.closest('div.form-group').removeClass('has-error').addClass('has-success');
        } else {
            isFormValid = false;
            $input.closest('div.form-group').removeClass('has-success');
            if (showError) {
                $input.closest('div.form-group').addClass('has-error');
            }
        }
    });

    if (! $('#terms_checkbox').is(':checked') || ! $('#privacy_checkbox').is(':checked')) {
        isFormValid = false;
    }

    $('#saveSignUpButton').prop('disabled', !isFormValid);

    return isFormValid;
}

function validateServerSignUp() {
    if (!validateSignUp(true)) {
        return;
    }

    $('#signUpDiv, #signUpFooter').hide();
    $('#working').show();

    $.ajax({
        type: 'POST',
        url: 'http://invninjv1.local/signup/validate',
        data: 'email=' + $('form.signUpForm #new_email').val(),
        success: function(result) {
            if (result == 'available') {
                submitSignUp();
            } else {
                $('#errorTaken').show();
                $('form.signUpForm #new_email').closest('div.form-group').removeClass('has-success').addClass('has-error');
                $('#signUpDiv, #signUpFooter').show();
                $('#working').hide();
            }
        }
    });
}

function submitSignUp() {
    $.ajax({
        type: 'POST',
        url: 'http://invninjv1.local/signup/submit',
        data: 'new_email=' + encodeURIComponent($('form.signUpForm #new_email').val()) +
        '&new_password=' + encodeURIComponent($('form.signUpForm #new_password').val()) +
        '&new_first_name=' + encodeURIComponent($('form.signUpForm #new_first_name').val()) +
        '&new_last_name=' + encodeURIComponent($('form.signUpForm #new_last_name').val()) +
        '&go_pro=' + $('#go_pro').val(),
        success: function(result) {
            if (result) {
                                hideSignUp();
                NINJA.formIsChanged = false;
                location.href = "/dashboard";
                            }
        }
    });
}

function handleSignedUp() {
    if (isStorageSupported()) {
        localStorage.setItem('guest_key', '');
    }
    fbq('track', 'CompleteRegistration');
    trackEvent('/company', '/signed_up');
}

</script>


<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Log Out</h4>
      </div>

      <div class="container" style="width: 100%; padding-bottom: 0px !important">
      <div class="panel panel-default">
      <div class="panel-body">
        <h3>Are you sure?</h3><br/>
        <p>Your company is not registered, this will permanently erase your data.</p>
      </div>
      </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="logout(true)">Log Out/Delete company</button>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
  .help-panel {
      margin-left: 14px;
      margin-right: 14px;
  }

  .help-panel .col-md-2 div,
  .help-panel .col-md-3 div {
      background-color:#777;
      color:#fff;
      width:28px;
      text-align:center;
      padding-top:2px;
      padding-bottom:2px;
      font-weight:bold;
      font-size: 18px;
      float: left;
      margin-left: 12px;
      margin-top: 4px;
      margin-bottom: 4px;
  }
  .help-panel .key-label {
      padding-top: 10px;
  }
</style>

<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Help</h4>
      </div>

      <div class="container" style="width: 100%; padding-bottom: 0px !important">
      <div class="panel panel-default">
      <div class="panel-body help-panel">

          
          <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="keyboard_shortcuts">

                  <div class="row">
                      <div class="col-md-3"><div>?</div></div>
                      <div class="col-md-3 key-label">Help</div>
                      <div class="col-md-3"><div>N</div><div>C</div></div>
                      <div class="col-md-3 key-label">New Client</div>
                  </div>
                  <div class="row">
                      <div class="col-md-3"><div>/</div></div>
                      <div class="col-md-3 key-label">Search</div>
                      <div class="col-md-3"><div>N</div><div>I</div></div>
                      <div class="col-md-3 key-label">New Invoice</div>
                  </div>
                  <div class="row">
                      <div class="col-md-3"><div>M</div></div>
                      <div class="col-md-3 key-label">Menu</div>
                      <div class="col-md-3"><div>N</div><div>...</div></div>
                      <div class="col-md-3 key-label">New ...</div>
                  </div>
                  <div class="row">
                      <div class="col-md-3"><div>H</div></div>
                      <div class="col-md-3 key-label">History</div>
                  </div>
                  <div class="row">
                      <div class="col-md-3"></div>
                      <div class="col-md-3"></div>
                      <div class="col-md-3"><div>L</div><div>C</div></div>
                      <div class="col-md-3 key-label">List Clients</div>
                  </div>
                  <div class="row">
                      <div class="col-md-3"><div>G</div><div>D</div></div>
                      <div class="col-md-3 key-label">Dashboard</div>
                      <div class="col-md-3"><div>L</div><div>I</div></div>
                      <div class="col-md-3 key-label">List Invoices</div>
                  </div>
                  <div class="row">
                      <div class="col-md-3"><div>G</div><div>S</div></div>
                      <div class="col-md-3 key-label">Settings</div>
                      <div class="col-md-3"><div>L</div><div>...</div></div>
                      <div class="col-md-3 key-label">List ...</div>
                  </div>

              </div>

              <div role="tabpanel" class="tab-pane" id="voice_commands">
                  <div class="row">
                      <p>
                          Sample commands:
                      </p>
                      <p>
                          <ul>
                              <li>Go to the dashboard</li>
                              <li>List active and deleted tasks</li>
                              <li>Find &lt;client name&gt;</li>
                              <li>Show me &lt;client name&gt;'s past due invoices</li>
                              <li>New invoice for &lt;client name&gt;</li>
                              <li>Create payment for invoice &lt;invoice number&gt;</li>
                          </ul>
                      </p>
                      <p>
                          We're actively working to improve this feature, if there's a command you'd like us to support please email us at <a href="&#109;&#x61;&#x69;&#108;to&#x3a;&#x0;">&#x0;</a>.
                      </p>
                  </div>
              </div>
          </div>

      </div>
      </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <a class="btn btn-primary" href="https://www.invoiceninja.com/knowledge-base/" target="_blank">Knowledge Base</a>
        <a class="btn btn-primary" href="https://invoice-ninja.readthedocs.io/en/latest/settings.html#localization" target="_blank">User Guide</a>
        <a class="btn btn-primary" href="https://www.youtube.com/channel/UCXAHcBvhW05PDtWYIq7WDFA/videos" target="_blank">YouTube Videos</a>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

    function showKeyboardShortcuts() {
        $('#helpModal').modal('show');
    }

    $(function() {

        var settingsURL = 'http://invninjv1.local/settings/company_details';
        if (isStorageSupported()) {
            settingsURL = localStorage.getItem('last:settings_page') || settingsURL;
        }
        // if they're on the last viewed settings page link to main settings page
        if ('http://invninjv1.local/settings/localization' != settingsURL) {
            $('.nav-settings .nav-link').attr("href", settingsURL);
        }

        Mousetrap.bind('?', function(e) {
            showKeyboardShortcuts();
        });

        Mousetrap.bind('/', function(e) {
            event.preventDefault();
            $('#search').focus();
        });

        Mousetrap.bind('g d', function(e) {
            location.href = "/dashboard";
        });

        /*
        Mousetrap.bind('g r c', function(e) {
            location.href = "/reports/calendar";
        });
        */

        Mousetrap.bind('g r', function(e) {
            location.href = "/reports";
        });

        Mousetrap.bind('g s', function(e) {
            location.href = settingsURL;
        });

        Mousetrap.bind('h', function(e) {
            $('#right-menu-toggle').trigger('click');
        });

        Mousetrap.bind('m', function(e) {
            $('#left-menu-toggle').trigger('click');
        });

        
                    Mousetrap.bind('n i', function(e) {
                var link = "/invoices/create";
                                    if (location.pathname.indexOf('/clients/') >= 0) {
                        var matches = location.pathname.match(/\d+/g);
                        if (matches.length) {
                            link += '/' + matches[0];
                        }
                    }
                                location.href = link;
            });
            Mousetrap.bind('l i', function(e) {
                location.href = "/invoices";
            });
                    Mousetrap.bind('n p', function(e) {
                var link = "/payments/create";
                                    if (location.pathname.indexOf('/clients/') >= 0) {
                        var matches = location.pathname.match(/\d+/g);
                        if (matches.length) {
                            link += '/' + matches[0];
                        }
                    }
                                location.href = link;
            });
            Mousetrap.bind('l p', function(e) {
                location.href = "/payments";
            });
                    Mousetrap.bind('n e', function(e) {
                var link = "/expenses/create";
                                location.href = link;
            });
            Mousetrap.bind('l e', function(e) {
                location.href = "/expenses";
            });
                    Mousetrap.bind('n t', function(e) {
                var link = "/tasks/create";
                                    if (location.pathname.indexOf('/clients/') >= 0) {
                        var matches = location.pathname.match(/\d+/g);
                        if (matches.length) {
                            link += '/' + matches[0];
                        }
                    }
                                location.href = link;
            });
            Mousetrap.bind('l t', function(e) {
                location.href = "/tasks";
            });
                    Mousetrap.bind('n c', function(e) {
                var link = "/clients/create";
                                location.href = link;
            });
            Mousetrap.bind('l c', function(e) {
                location.href = "/clients";
            });
                    Mousetrap.bind('n q', function(e) {
                var link = "/quotes/create";
                                location.href = link;
            });
            Mousetrap.bind('l q', function(e) {
                location.href = "/quotes";
            });
                    Mousetrap.bind('n v', function(e) {
                var link = "/vendors/create";
                                    if (location.pathname.indexOf('/clients/') >= 0) {
                        var matches = location.pathname.match(/\d+/g);
                        if (matches.length) {
                            link += '/' + matches[0];
                        }
                    }
                                location.href = link;
            });
            Mousetrap.bind('l v', function(e) {
                location.href = "/vendors";
            });
                    Mousetrap.bind('n r', function(e) {
                var link = "/recurring_invoices/create";
                                    if (location.pathname.indexOf('/clients/') >= 0) {
                        var matches = location.pathname.match(/\d+/g);
                        if (matches.length) {
                            link += '/' + matches[0];
                        }
                    }
                                location.href = link;
            });
            Mousetrap.bind('l r', function(e) {
                location.href = "/recurring_invoices";
            });
        
                    Mousetrap.bind('g c d', function(e) {
                location.href = "/settings/company_details";
            });
                    Mousetrap.bind('g u d', function(e) {
                location.href = "/settings/user_details";
            });
                    Mousetrap.bind('g l', function(e) {
                location.href = "/settings/localization";
            });
                    Mousetrap.bind('g o p', function(e) {
                location.href = "/settings/online_payments";
            });
                    Mousetrap.bind('g t x', function(e) {
                location.href = "/settings/tax_rates";
            });
                    Mousetrap.bind('g p', function(e) {
                location.href = "/settings/products";
            });
                    Mousetrap.bind('g n', function(e) {
                location.href = "/settings/notifications";
            });
                    Mousetrap.bind('g i e', function(e) {
                location.href = "/settings/import_export";
            });
                    Mousetrap.bind('g a m', function(e) {
                location.href = "/settings/account_management";
            });
                    Mousetrap.bind('g i s', function(e) {
                location.href = "/settings/invoice_settings";
            });
                    Mousetrap.bind('g i d', function(e) {
                location.href = "/settings/invoice_design";
            });
                    Mousetrap.bind('g c p', function(e) {
                location.href = "/settings/client_portal";
            });
                    Mousetrap.bind('g e', function(e) {
                location.href = "/settings/email_settings";
            });
                    Mousetrap.bind('g t r', function(e) {
                location.href = "/settings/templates_and_reminders";
            });
                    Mousetrap.bind('g c c', function(e) {
                location.href = "/settings/bank_accounts";
            });
                    Mousetrap.bind('g v', function(e) {
                location.href = "/settings/data_visualizations";
            });
                    Mousetrap.bind('g a t', function(e) {
                location.href = "/settings/api_tokens";
            });
                    Mousetrap.bind('g u m', function(e) {
                location.href = "/settings/user_management";
            });
        

    });
</script>


</div>

<p>&nbsp;</p>



<script type="text/javascript">
    NINJA.formIsChanged = false;

    $(function () {
        $('form.warn-on-exit input, form.warn-on-exit textarea, form.warn-on-exit select').change(function () {
            NINJA.formIsChanged = true;
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        
        $('[data-toggle="tooltip"]').tooltip();

            });
    $('form').submit(function () {
        NINJA.formIsChanged = false;
    });
    $(window).on('beforeunload', function () {
        if (NINJA.formIsChanged) {
            return "You have unsaved changes";
        } else {
            return undefined;
        }
    });
    function openUrl(url, track) {
        trackEvent('/view_link', track ? track : url);
        window.open(url, '_blank');
    }
</script>

</body>

</html>
