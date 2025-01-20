<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Source: https://github.com/invoiceninja/invoiceninja -->
    <!-- Version: 4.5.19 -->
            <meta charset="utf-8">

            <title>Invoice Design | Invoice Ninja</title>
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
    <meta name="csrf-token" content="IH4RfjHyCuiL4BTW0mJXyy0httbYIu2gFF50M3a6">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="canonical" href="https://app.invoiceninja.com/settings/invoice_design"/>

    	    <link href="/css/built.css?no_cache=4.5.19" rel="stylesheet" type="text/css"/>

    

	<style type="text/css">
		.label-group {
			display: none;
		}
	</style>

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
        weekStart: 0
    });

    if (isStorageSupported()) {
          }

    $('ul.navbar-settings, ul.navbar-search').hover(function () {
        if ($('.user-accounts').css('display') == 'block') {
            $('.user-accounts').dropdown('toggle');
        }
    });

    
    
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
            <script src="/js/vfs_fonts/roboto.js" type="text/javascript"></script>
        <script src="/pdf.built.js?no_cache=4.5.19" type="text/javascript"></script>
    <script src="/js/lightbox.min.js" type="text/javascript"></script>
    <link href="/css/lightbox.css" rel="stylesheet" type="text/css"/>

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
                                    Norman Osborn
                              <span class="caret"></span>
            </div>
          </button>
          <ul class="dropdown-menu user-accounts">
                            <li style="margin-top: 4px; margin-bottom: 4px; min-width: 220px; cursor: pointer">
             
            <a href="/settings/company_details">
            
                    <div class="pull-left" style="width: 40px; min-height: 40px; margin-right: 16px">&nbsp;</div>
        
                    <b>
        
        <div class="company" style="padding-right:90px">Untitled</div>
        <div class="user" style="padding-right:90px">Norman Osborn</div>

                    
            </b>
            </a>

</li>                        <li class="divider"></li>
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
      <input type="hidden" name="_token" value="IH4RfjHyCuiL4BTW0mJXyy0httbYIu2gFF50M3a6"></form>

      
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

            <a type="button" class="btn btn-default btn-sm pull-right" title="User Guide: Invoice Design "
            href="https://invoice-ninja.readthedocs.io/en/latest/invoice_design.html" target="_blank">
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
                                                                                                <a href="/settings/localization" class="list-group-item "
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
                                                                                                <a href="/settings/invoice_design" class="list-group-item selected"
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
    <script type="text/javascript">

function ViewModel(data) {
    var self = this;

    self.invoice_fields = ko.observableArray();
    self.client_fields = ko.observableArray();
    self.account_fields1 = ko.observableArray();
    self.account_fields2 = ko.observableArray();
    self.product_fields = ko.observableArray();
    self.task_fields = ko.observableArray();
    window.field_map = [];

    self.addField = function(section, field, label) {
        if (self[section].indexOf(field) < 0) {
            self[section].push(field);
        }
    }

    self.resetInvoiceFields = function() {
        self.invoice_fields.removeAll();
        self.client_fields.removeAll();
        self.account_fields1.removeAll();
        self.account_fields2.removeAll();
    }

    self.resetProductFields = function() {
        self.product_fields.removeAll();
        self.task_fields.removeAll();
    }

    self.onChange = function() {
        self.updateSelects();
        refreshPDF();
        NINJA.formIsChanged = true;
    }

    self.updateSelects = function() {
        var usedFields = [].concat(
            self.invoice_fields(),
            self.client_fields(),
            self.account_fields1(),
            self.account_fields2());
        var selects = [
            'invoice_fields',
            'client_fields',
            'account_fields1',
            'account_fields2',
            'product_fields',
            'task_fields',
        ];

        for (var i=0; i<selects.length; i++) {
            var select = selects[i];
            $('#' + select + '_select > option').each(function() {
                if (select == 'product_fields') {
                    usedFields = self.product_fields();
                } else if (select == 'task_fields') {
                    usedFields = self.task_fields();
                }
                var isUsed = usedFields.indexOf(this.value) >= 0;
                $(this).css('color', isUsed ? '#888' : 'black');
            });
        }
    }

    self.onDragged = function() {
        self.onChange();
    }

    self.removeInvoiceFields = function(item) {
        self.invoice_fields.remove(item);
        self.onChange();
    }
    self.removeClientFields = function(item) {
        self.client_fields.remove(item);
        self.onChange();
    }
    self.removeAccountFields1 = function(item) {
        self.account_fields1.remove(item);
        self.onChange();
    }
    self.removeAccountFields2 = function(item) {
        self.account_fields2.remove(item);
        self.onChange();
    }
    self.removeProductFields = function(item) {
        self.product_fields.remove(item);
        self.onChange();
    }
    self.removeTaskFields = function(item) {
        self.task_fields.remove(item);
        self.onChange();
    }
}

function addField(section) {
    var $select = $('#' + section + '_select');
    var field = $select.val();
    var label = $select.find('option:selected').text();
    window.model.addField(section, field, label);
    window.model.onChange();
    $select.val(null).blur();
}

$(function() {
    window.model = new ViewModel();

    var selectedFields = {"invoice_fields":{"invoice.invoice_number":"Invoice Number","invoice.po_number":"PO Number","invoice.invoice_date":"Invoice Date","invoice.due_date":"Due Date","invoice.balance_due":"Balance Due","invoice.partial_due":"Partial Due"},"client_fields":{"client.client_name":"Client Name","client.id_number":"ID Number","client.vat_number":"VAT Number","client.address1":"Street","client.address2":"Apt\/Suite","client.city_state_postal":"City\/State\/Postal","client.country":"Country","client.email":"Contact Email"},"account_fields1":{"company.company_name":"CompanyPlan Name","company.id_number":"ID Number","company.vat_number":"VAT Number","company.website":"Website","company.email":"Email","company.phone":"Phone"},"account_fields2":{"company.address1":"Street","company.address2":"Apt\/Suite","company.city_state_postal":"City\/State\/Postal","company.country":"Country"},"product_fields":{"product.item":"Item","product.description":"Description","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field","product.unit_cost":"Unit Cost","product.quantity":"Quantity","product.tax":"Tax","product.line_total":"Line Total"},"task_fields":{"product.service":"Service","product.description":"Description","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field","product.rate":"Rate","product.hours":"Hours","product.tax":"Tax","product.line_total":"Line Total"}};
    var allFields = {"invoice_fields":{"invoice.invoice_number":"Invoice Number","invoice.po_number":"PO Number","invoice.invoice_date":"Invoice Date","invoice.due_date":"Due Date","invoice.invoice_total":"Invoice Total","invoice.balance_due":"Balance Due","invoice.partial_due":"Partial Due","invoice.outstanding":"Outstanding","invoice.custom_text_value1":"Custom Field","invoice.custom_text_value2":"Custom Field",".blank":"Blank"},"client_fields":{"client.client_name":"Client Name","client.id_number":"ID Number","client.vat_number":"VAT Number","client.website":"Website","client.work_phone":"Phone","client.address1":"Street","client.address2":"Apt\/Suite","client.city_state_postal":"City\/State\/Postal","client.postal_city_state":"Postal\/City\/State","client.country":"Country","client.contact_name":"Contact Name","client.email":"Contact Email","client.phone":"Contact Phone","client.custom_value1":"Custom Field","client.custom_value2":"Custom Field","contact.custom_value1":"Custom Field","contact.custom_value2":"Custom Field",".blank":"Blank"},"account_fields":{"company.company_name":"CompanyPlan Name","company.id_number":"ID Number","company.vat_number":"VAT Number","company.website":"Website","company.email":"Email","company.phone":"Phone","company.address1":"Street","company.address2":"Apt\/Suite","company.city_state_postal":"City\/State\/Postal","company.postal_city_state":"Postal\/City\/State","company.country":"Country","company.custom_value1":"Custom Field","company.custom_value2":"Custom Field",".blank":"Blank"},"product_fields":{"product.item":"Item","product.description":"Description","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field","product.unit_cost":"Unit Cost","product.quantity":"Quantity","product.discount":"Discount","product.tax":"Tax","product.line_total":"Line Total"},"task_fields":{"product.service":"Service","product.description":"Description","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field","product.rate":"Rate","product.hours":"Hours","product.discount":"Discount","product.tax":"Tax","product.line_total":"Line Total"}};

    loadFields(selectedFields);
    loadMap(allFields);

    model.updateSelects();
    ko.applyBindings(model);
})

function resetInvoiceFields() {
    var defaultFields = {"invoice_fields":{"invoice.invoice_number":"Invoice Number","invoice.po_number":"PO Number","invoice.invoice_date":"Invoice Date","invoice.due_date":"Due Date","invoice.balance_due":"Balance Due","invoice.partial_due":"Partial Due"},"client_fields":{"client.client_name":"Client Name","client.id_number":"ID Number","client.vat_number":"VAT Number","client.address1":"Street","client.address2":"Apt\/Suite","client.city_state_postal":"City\/State\/Postal","client.country":"Country","client.email":"Contact Email"},"account_fields1":{"company.company_name":"CompanyPlan Name","company.id_number":"ID Number","company.vat_number":"VAT Number","company.website":"Website","company.email":"Email","company.phone":"Phone"},"account_fields2":{"company.address1":"Street","company.address2":"Apt\/Suite","company.city_state_postal":"City\/State\/Postal","company.country":"Country"},"product_fields":{"product.item":"Item","product.description":"Description","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field","product.unit_cost":"Unit Cost","product.quantity":"Quantity","product.tax":"Tax","product.line_total":"Line Total"},"task_fields":{"product.service":"Service","product.description":"Description","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field","product.rate":"Rate","product.hours":"Hours","product.tax":"Tax","product.line_total":"Line Total"}};
    window.model.resetInvoiceFields();
    loadFields(defaultFields, 'invoice');
    window.model.onChange();
}

function resetProductFields() {
    var defaultFields = {"invoice_fields":{"invoice.invoice_number":"Invoice Number","invoice.po_number":"PO Number","invoice.invoice_date":"Invoice Date","invoice.due_date":"Due Date","invoice.balance_due":"Balance Due","invoice.partial_due":"Partial Due"},"client_fields":{"client.client_name":"Client Name","client.id_number":"ID Number","client.vat_number":"VAT Number","client.address1":"Street","client.address2":"Apt\/Suite","client.city_state_postal":"City\/State\/Postal","client.country":"Country","client.email":"Contact Email"},"account_fields1":{"company.company_name":"CompanyPlan Name","company.id_number":"ID Number","company.vat_number":"VAT Number","company.website":"Website","company.email":"Email","company.phone":"Phone"},"account_fields2":{"company.address1":"Street","company.address2":"Apt\/Suite","company.city_state_postal":"City\/State\/Postal","company.country":"Country"},"product_fields":{"product.item":"Item","product.description":"Description","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field","product.unit_cost":"Unit Cost","product.quantity":"Quantity","product.tax":"Tax","product.line_total":"Line Total"},"task_fields":{"product.service":"Service","product.description":"Description","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field","product.rate":"Rate","product.hours":"Hours","product.tax":"Tax","product.line_total":"Line Total"}};
    window.model.resetProductFields();
    loadFields(defaultFields, 'product');
    window.model.onChange();
}

function loadMap(allFields) {
    for (var section in allFields) {
        if ( ! allFields.hasOwnProperty(section)) {
            continue;
        }
        var fields = allFields[section];
        for (var field in fields) {
            if ( ! fields.hasOwnProperty(field)) {
                continue;
            }
            var label = fields[field];
            window.field_map[field] = label;
        }
    }
}

function loadFields(selectedFields, filter) {
    for (var section in selectedFields) {
        if ( ! selectedFields.hasOwnProperty(section)) {
            continue;
        }

        if (filter == 'invoice' && (section == 'product_fields' || section == 'task_fields')) {
            continue;
        } else if (filter == 'product' && (section != 'product_fields' && section != 'task_fields')) {
            continue;
        }

        var fields = selectedFields[section];
        for (var field in fields) {
            if ( ! fields.hasOwnProperty(field)) {
                continue;
            }
            var label = fields[field];
            model.addField(section, field, label);
        }
    }
}

</script>


<style type="text/css">

.field-list {
    width: 100%;
    margin-top: 12px;
}
.field-list tr {
    width: 100%;
    cursor: pointer;
    border-bottom: solid 1px #CCC;
}

.field-list td {
    width: 100%;
    background-color: white;
    padding-top: 10px;
    padding-bottom: 10px;
}

.field-list td i {
    float: left;
    width: 18px;
    padding-top: 2px;
}

.field-list td div {
    float: left;
    xwidth: 146px;
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.field-list tr:hover .fa {
    visibility: visible;
}

.field-list tr:hover div {
    width: 120px;
}

.field-list .fa {
    visibility: hidden;
}

.field-list .fa-close {
    color: red;
}

</style>

  <script>
    var invoiceDesigns = [{"id":1,"name":"Clean","javascript":"{\"content\":[{\"columns\":[{\"image\":\"$companyLogo\",\"fit\":[120,80]},{\"stack\":\"$companyDetails\",\"margin\":[7,0,0,0]},{\"stack\":\"$companyAddress\"}]},{\"text\":\"$entityTypeUC\",\"margin\":[8,30,8,5],\"style\":\"entityTypeLabel\"},{\"table\":{\"headerRows\":1,\"widths\":[\"auto\",\"auto\",\"*\"],\"body\":[[{\"table\":{\"body\":\"$invoiceDetails\"},\"margin\":[0,0,12,0],\"layout\":\"noBorders\"},{\"stack\":\"$clientDetails\"},{\"text\":\"\"}]]},\"layout\":{\"hLineWidth\":\"$firstAndLast:.5\",\"vLineWidth\":\"$none\",\"hLineColor\":\"#D8D8D8\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:6\",\"paddingBottom\":\"$amount:6\"}},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$notFirst:.5\",\"vLineWidth\":\"$none\",\"hLineColor\":\"#D8D8D8\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:14\",\"paddingBottom\":\"$amount:14\"}},{\"columns\":[\"$notesAndTerms\",{\"table\":{\"widths\":[\"*\",\"40%\"],\"body\":\"$subtotals\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"defaultStyle\":{\"font\":\"$bodyFont\",\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"footer\":{\"columns\":[{\"text\":\"$invoiceFooter\",\"alignment\":\"left\"}],\"margin\":[40,-20,40,0]},\"styles\":{\"entityTypeLabel\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLargest\",\"color\":\"$primaryColor:#37a3c6\"},\"primaryColor\":{\"color\":\"$primaryColor:#37a3c6\"},\"accountName\":{\"color\":\"$primaryColor:#37a3c6\",\"bold\":true},\"invoiceDetails\":{\"margin\":[0,0,8,0]},\"accountDetails\":{\"margin\":[0,2,0,2]},\"clientDetails\":{\"margin\":[0,2,0,2]},\"notesAndTerms\":{\"margin\":[0,2,0,2]},\"accountAddress\":{\"margin\":[0,2,0,2]},\"odd\":{\"fillColor\":\"#fbfbfb\"},\"productKey\":{\"color\":\"$primaryColor:#37a3c6\",\"bold\":true},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLarger\"},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLarger\",\"color\":\"$primaryColor:#37a3c6\"},\"invoiceNumber\":{\"bold\":true},\"tableHeader\":{\"bold\":true,\"fontSize\":\"$fontSizeLarger\"},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"taxTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\"},\"invoiceLineItemsTable\":{\"margin\":[0,16,0,16]},\"clientName\":{\"bold\":true},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"lineTotal\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\"},\"termsLabel\":{\"bold\":true},\"fullheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"},\"invoiceDocuments\":{\"margin\":[7,0,7,0]},\"invoiceDocument\":{\"margin\":[0,10,0,10]}},\"pageMargins\":[40,40,40,60],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":2,"name":"Bold","javascript":"{\"content\":[{\"columns\":[{\"width\":380,\"stack\":[{\"text\":\"$yourInvoiceLabelUC\",\"style\":\"yourInvoice\"},\"$clientDetails\"],\"margin\":[60,100,0,10]},{\"canvas\":[{\"type\":\"rect\",\"x\":0,\"y\":0,\"w\":225,\"h\":\"$invoiceDetailsHeight\",\"r\":0,\"lineWidth\":1,\"color\":\"$primaryColor:#36a498\"}],\"width\":10,\"margin\":[-10,100,0,10]},{\"table\":{\"body\":\"$invoiceDetails\"},\"layout\":\"noBorders\",\"margin\":[0,110,0,0]}]},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:14\",\"paddingBottom\":\"$amount:14\"}},{\"columns\":[{\"width\":46,\"text\":\" \"},\"$notesAndTerms\",{\"table\":{\"widths\":[\"*\",\"40%\"],\"body\":\"$subtotals\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"footer\":[{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":0,\"x2\":600,\"y2\":0,\"lineWidth\":100,\"lineColor\":\"$secondaryColor:#292526\"}]},{\"columns\":[{\"text\":\"$invoiceFooter\",\"margin\":[40,-40,40,0],\"alignment\":\"left\",\"color\":\"#FFFFFF\"}]}],\"header\":[{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":0,\"x2\":600,\"y2\":0,\"lineWidth\":200,\"lineColor\":\"$secondaryColor:#292526\"}],\"width\":10},{\"columns\":[{\"image\":\"$companyLogo\",\"fit\":[120,60],\"margin\":[30,16,0,0]},{\"stack\":\"$companyDetails\",\"margin\":[0,16,0,0],\"width\":140},{\"stack\":\"$companyAddress\",\"margin\":[20,16,0,0]}]}],\"defaultStyle\":{\"font\":\"$bodyFont\",\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"styles\":{\"primaryColor\":{\"color\":\"$primaryColor:#36a498\"},\"accountName\":{\"bold\":true,\"margin\":[4,2,4,1],\"color\":\"$primaryColor:#36a498\"},\"accountDetails\":{\"margin\":[4,2,4,1],\"color\":\"#FFFFFF\"},\"accountAddress\":{\"margin\":[4,2,4,1],\"color\":\"#FFFFFF\"},\"clientDetails\":{\"margin\":[0,2,0,1]},\"odd\":{\"fillColor\":\"#ebebeb\"},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"$primaryColor:#36a498\",\"bold\":true},\"invoiceDetails\":{\"color\":\"#ffffff\"},\"invoiceNumber\":{\"bold\":true},\"tableHeader\":{\"fontSize\":12,\"bold\":true},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"taxTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\",\"margin\":[0,0,40,0]},\"firstColumn\":{\"margin\":[40,0,0,0]},\"lastColumn\":{\"margin\":[0,0,40,0]},\"productKey\":{\"color\":\"$primaryColor:#36a498\",\"bold\":true},\"yourInvoice\":{\"font\":\"$headerFont\",\"bold\":true,\"fontSize\":14,\"color\":\"$primaryColor:#36a498\",\"margin\":[0,0,0,8]},\"invoiceLineItemsTable\":{\"margin\":[0,26,0,16]},\"clientName\":{\"bold\":true},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"lineTotal\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\",\"margin\":[0,0,40,0]},\"termsLabel\":{\"bold\":true,\"margin\":[0,0,0,4]},\"fullheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"},\"invoiceDocuments\":{\"margin\":[47,0,47,0]},\"invoiceDocument\":{\"margin\":[0,10,0,10]}},\"pageMargins\":[0,80,0,40],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":3,"name":"Modern","javascript":"{\"content\":[{\"columns\":[{\"image\":\"$companyLogo\",\"fit\":[120,80],\"margin\":[0,60,0,30]},{\"stack\":\"$clientDetails\",\"margin\":[0,60,0,0]}]},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$notFirst:.5\",\"vLineWidth\":\"$notFirstAndLastColumn:.5\",\"hLineColor\":\"#888888\",\"vLineColor\":\"#FFFFFF\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:8\",\"paddingBottom\":\"$amount:8\"}},{\"columns\":[\"$notesAndTerms\",{\"table\":{\"widths\":[\"*\",\"40%\"],\"body\":\"$subtotalsWithoutBalance\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]},{\"columns\":[{\"canvas\":[{\"type\":\"rect\",\"x\":0,\"y\":0,\"w\":515,\"h\":26,\"r\":0,\"lineWidth\":1,\"color\":\"$secondaryColor:#403d3d\"}],\"width\":10,\"margin\":[0,10,0,0]},{\"text\":\"$balanceDueLabel\",\"style\":\"subtotalsBalanceDueLabel\",\"margin\":[0,16,0,0],\"width\":370},{\"text\":\"$balanceDue\",\"style\":\"subtotalsBalanceDue\",\"margin\":[0,16,8,0]}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"footer\":[{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":0,\"x2\":600,\"y2\":0,\"lineWidth\":100,\"lineColor\":\"$primaryColor:#f26621\"}],\"width\":10},{\"columns\":[{\"width\":350,\"stack\":[{\"text\":\"$invoiceFooter\",\"margin\":[40,-40,40,0],\"alignment\":\"left\",\"color\":\"#FFFFFF\"}]},{\"stack\":\"$companyDetails\",\"margin\":[0,-40,0,0],\"width\":\"*\"},{\"stack\":\"$companyAddress\",\"margin\":[0,-40,0,0],\"width\":\"*\"}]}],\"header\":[{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":0,\"x2\":600,\"y2\":0,\"lineWidth\":200,\"lineColor\":\"$primaryColor:#f26621\"}],\"width\":10},{\"columns\":[{\"text\":\"$companyName\",\"bold\":true,\"font\":\"$headerFont\",\"fontSize\":30,\"color\":\"#ffffff\",\"margin\":[40,20,0,0],\"width\":350}]},{\"width\":300,\"table\":{\"body\":\"$invoiceDetails\"},\"layout\":\"noBorders\",\"margin\":[400,-40,0,0]}],\"defaultStyle\":{\"font\":\"$bodyFont\",\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"styles\":{\"primaryColor\":{\"color\":\"$primaryColor:#299CC2\"},\"accountName\":{\"margin\":[4,2,4,2],\"color\":\"$primaryColor:#299CC2\"},\"accountDetails\":{\"margin\":[4,2,4,2],\"color\":\"#FFFFFF\"},\"accountAddress\":{\"margin\":[4,2,4,2],\"color\":\"#FFFFFF\"},\"clientDetails\":{\"margin\":[0,2,4,2]},\"invoiceDetails\":{\"color\":\"#FFFFFF\"},\"invoiceLineItemsTable\":{\"margin\":[0,0,0,16]},\"productKey\":{\"bold\":true},\"clientName\":{\"bold\":true},\"tableHeader\":{\"bold\":true,\"color\":\"#FFFFFF\",\"fontSize\":\"$fontSizeLargest\",\"fillColor\":\"$secondaryColor:#403d3d\"},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"taxTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\"},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"#FFFFFF\",\"alignment\":\"right\",\"bold\":true},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"#FFFFFF\",\"bold\":true,\"alignment\":\"right\"},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"lineTotal\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\"},\"termsLabel\":{\"bold\":true,\"margin\":[0,0,0,4]},\"invoiceNumberLabel\":{\"bold\":true},\"invoiceNumber\":{\"bold\":true},\"fullheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"},\"invoiceDocuments\":{\"margin\":[7,0,7,0]},\"invoiceDocument\":{\"margin\":[0,10,0,10]}},\"pageMargins\":[40,120,40,50],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":4,"name":"Plain","javascript":"{\"content\":[{\"columns\":[{\"stack\":\"$companyDetails\"},{\"stack\":\"$companyAddress\"},[{\"image\":\"$companyLogo\",\"fit\":[120,80]}]]},{\"columns\":[{\"width\":340,\"stack\":\"$clientDetails\",\"margin\":[0,40,0,0]},{\"width\":200,\"table\":{\"body\":\"$invoiceDetails\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"hLineColor\":\"#E6E6E6\",\"paddingLeft\":\"$amount:10\",\"paddingRight\":\"$amount:10\"}}]},{\"canvas\":[{\"type\":\"rect\",\"x\":0,\"y\":0,\"w\":515,\"h\":25,\"r\":0,\"lineWidth\":1,\"color\":\"#e6e6e6\"}],\"width\":10,\"margin\":[0,30,0,-43]},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$notFirst:1\",\"vLineWidth\":\"$none\",\"hLineColor\":\"#e6e6e6\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:8\",\"paddingBottom\":\"$amount:8\"}},{\"columns\":[\"$notesAndTerms\",{\"width\":160,\"style\":\"subtotals\",\"table\":{\"widths\":[60,60],\"body\":\"$subtotals\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:10\",\"paddingRight\":\"$amount:10\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"footer\":{\"columns\":[{\"text\":\"$invoiceFooter\",\"alignment\":\"left\",\"margin\":[0,0,0,12]}],\"margin\":[40,-20,40,40]},\"defaultStyle\":{\"font\":\"$bodyFont\",\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"styles\":{\"primaryColor\":{\"color\":\"$primaryColor:#299CC2\"},\"accountDetails\":{\"margin\":[0,2,0,1]},\"accountAddress\":{\"margin\":[0,2,0,1]},\"clientDetails\":{\"margin\":[0,2,0,1]},\"tableHeader\":{\"bold\":true},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\"},\"invoiceLineItemsTable\":{\"margin\":[0,16,0,16]},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"lineTotal\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\"},\"termsLabel\":{\"bold\":true,\"margin\":[0,0,0,4]},\"terms\":{\"margin\":[0,0,20,0]},\"invoiceDetailBalanceDueLabel\":{\"fillColor\":\"#e6e6e6\"},\"invoiceDetailBalanceDue\":{\"fillColor\":\"#e6e6e6\"},\"subtotalsBalanceDueLabel\":{\"fillColor\":\"#e6e6e6\"},\"subtotalsBalanceDue\":{\"fillColor\":\"#e6e6e6\"},\"fullheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"},\"invoiceDocuments\":{\"margin\":[7,0,7,0]},\"invoiceDocument\":{\"margin\":[0,10,0,10]}},\"pageMargins\":[40,40,40,60],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":5,"name":"Business","javascript":"{\"content\":[{\"columns\":[{\"image\":\"$companyLogo\",\"fit\":[120,80]},{\"width\":300,\"stack\":\"$companyDetails\",\"margin\":[140,0,0,0]},{\"width\":150,\"stack\":\"$companyAddress\"}]},{\"columns\":[{\"width\":120,\"stack\":[{\"text\":\"$invoiceIssuedToLabel\",\"style\":\"issuedTo\"},\"$clientDetails\"],\"margin\":[0,20,0,0]},{\"canvas\":[{\"type\":\"rect\",\"x\":20,\"y\":0,\"w\":174,\"h\":\"$invoiceDetailsHeight\",\"r\":10,\"lineWidth\":1,\"color\":\"$primaryColor:#eb792d\"}],\"width\":30,\"margin\":[200,25,0,0]},{\"table\":{\"widths\":[70,76],\"body\":\"$invoiceDetails\"},\"layout\":\"noBorders\",\"margin\":[200,34,0,0]}]},{\"canvas\":[{\"type\":\"rect\",\"x\":0,\"y\":0,\"w\":515,\"h\":32,\"r\":8,\"lineWidth\":1,\"color\":\"$secondaryColor:#374e6b\"}],\"width\":10,\"margin\":[0,20,0,-45]},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$notFirst:1\",\"vLineWidth\":\"$notFirst:.5\",\"hLineColor\":\"#FFFFFF\",\"vLineColor\":\"#FFFFFF\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:12\",\"paddingBottom\":\"$amount:12\"}},{\"columns\":[\"$notesAndTerms\",{\"stack\":[{\"style\":\"subtotals\",\"table\":{\"widths\":[\"*\",\"35%\"],\"body\":\"$subtotalsWithoutBalance\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}},{\"canvas\":[{\"type\":\"rect\",\"x\":60,\"y\":20,\"w\":198,\"h\":30,\"r\":7,\"lineWidth\":1,\"color\":\"$secondaryColor:#374e6b\"}]},{\"style\":\"subtotalsBalance\",\"table\":{\"widths\":[\"*\",\"45%\"],\"body\":\"$subtotalsBalance\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"footer\":{\"columns\":[{\"text\":\"$invoiceFooter\",\"alignment\":\"left\"}],\"margin\":[40,-20,40,0]},\"defaultStyle\":{\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"styles\":{\"primaryColor\":{\"color\":\"$primaryColor:#299CC2\"},\"accountName\":{\"bold\":true},\"accountDetails\":{\"color\":\"#AAA9A9\",\"margin\":[0,2,0,1]},\"accountAddress\":{\"color\":\"#AAA9A9\",\"margin\":[0,2,0,1]},\"even\":{\"fillColor\":\"#E8E8E8\"},\"odd\":{\"fillColor\":\"#F7F7F7\"},\"productKey\":{\"bold\":true},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"#ffffff\",\"bold\":true},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true,\"color\":\"#ffffff\",\"alignment\":\"right\",\"noWrap\":true},\"invoiceDetails\":{\"color\":\"#ffffff\"},\"tableHeader\":{\"color\":\"#ffffff\",\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"secondTableHeader\":{\"color\":\"$secondaryColor:#374e6b\"},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"taxTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\"},\"issuedTo\":{\"margin\":[0,2,0,1],\"bold\":true,\"color\":\"#374e6b\"},\"clientDetails\":{\"margin\":[0,2,0,1]},\"clientName\":{\"color\":\"$primaryColor:#eb792d\"},\"invoiceLineItemsTable\":{\"margin\":[0,10,0,10]},\"invoiceDetailsValue\":{\"alignment\":\"right\"},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"lineTotal\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\"},\"subtotalsBalance\":{\"alignment\":\"right\",\"margin\":[0,-25,0,0]},\"termsLabel\":{\"bold\":true,\"margin\":[0,0,0,4]},\"fullheader\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subheader\":{\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"}},\"pageMargins\":[40,40,40,40],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":6,"name":"Creative","javascript":"{\"content\":[{\"columns\":[{\"stack\":\"$clientDetails\"},{\"stack\":\"$companyDetails\"},{\"stack\":\"$companyAddress\"},{\"image\":\"$companyLogo\",\"fit\":[120,80],\"alignment\":\"right\"}],\"margin\":[0,0,0,20]},{\"columns\":[{\"text\":[{\"text\":\"$entityTypeUC\",\"style\":\"header1\"},{\"text\":\" #\",\"style\":\"header2\"},{\"text\":\"$invoiceNumber\",\"style\":\"header2\"}],\"width\":\"*\"},{\"width\":200,\"table\":{\"body\":\"$invoiceDetails\"},\"layout\":\"noBorders\",\"margin\":[16,4,0,0]}],\"margin\":[0,0,0,20]},{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":5,\"x2\":515,\"y2\":5,\"lineWidth\":3,\"lineColor\":\"$primaryColor:#AE1E54\"}]},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"hLineColor\":\"$primaryColor:#E8E8E8\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:8\",\"paddingBottom\":\"$amount:8\"}},{\"columns\":[\"$notesAndTerms\",{\"style\":\"subtotals\",\"table\":{\"widths\":[\"*\",\"40%\"],\"body\":\"$subtotalsWithoutBalance\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]},{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":20,\"x2\":515,\"y2\":20,\"lineWidth\":3,\"lineColor\":\"$primaryColor:#AE1E54\"}],\"margin\":[0,-8,0,-8]},{\"text\":\"$balanceDueLabel\",\"style\":\"subtotalsBalanceDueLabel\"},{\"text\":\"$balanceDue\",\"style\":\"subtotalsBalanceDue\"},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"footer\":{\"columns\":[{\"text\":\"$invoiceFooter\",\"alignment\":\"left\"}],\"margin\":[40,-20,40,0]},\"defaultStyle\":{\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"styles\":{\"primaryColor\":{\"color\":\"$primaryColor:#AE1E54\"},\"accountName\":{\"margin\":[4,2,4,2],\"color\":\"$primaryColor:#AE1E54\",\"bold\":true},\"accountDetails\":{\"margin\":[4,2,4,2]},\"accountAddress\":{\"margin\":[4,2,4,2]},\"odd\":{\"fillColor\":\"#F4F4F4\"},\"productKey\":{\"bold\":true},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLargest\",\"margin\":[320,20,0,0]},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"$primaryColor:#AE1E54\",\"bold\":true,\"margin\":[0,-10,10,0],\"alignment\":\"right\"},\"invoiceDetailBalanceDue\":{\"bold\":true,\"color\":\"$primaryColor:#AE1E54\"},\"invoiceDetailBalanceDueLabel\":{\"bold\":true},\"tableHeader\":{\"bold\":true,\"color\":\"$primaryColor:#AE1E54\",\"fontSize\":\"$fontSizeLargest\"},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"taxTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\"},\"clientName\":{\"bold\":true},\"clientDetails\":{\"margin\":[0,2,0,1]},\"header1\":{\"bold\":true,\"margin\":[0,30,0,16],\"fontSize\":42},\"header2\":{\"margin\":[0,30,0,16],\"fontSize\":42,\"italics\":true,\"color\":\"$primaryColor:#AE1E54\"},\"invoiceLineItemsTable\":{\"margin\":[0,4,0,16]},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"lineTotal\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\"},\"termsLabel\":{\"bold\":true,\"margin\":[0,0,0,4]},\"fullheader\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subheader\":{\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"}},\"pageMargins\":[40,40,40,40],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":7,"name":"Elegant","javascript":"{\"content\":[{\"image\":\"$companyLogo\",\"fit\":[120,80],\"alignment\":\"center\",\"margin\":[0,0,0,30]},{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":5,\"x2\":515,\"y2\":5,\"lineWidth\":2}]},{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":3,\"x2\":515,\"y2\":3,\"lineWidth\":1}]},{\"columns\":[{\"width\":120,\"stack\":[{\"text\":\"$invoiceToLabel\",\"style\":\"header\",\"margin\":[0,0,0,6]},\"$clientDetails\"]},{\"width\":10,\"canvas\":[{\"type\":\"line\",\"x1\":-2,\"y1\":18,\"x2\":-2,\"y2\":80,\"lineWidth\":1,\"dash\":{\"length\":2}}]},{\"width\":120,\"stack\":\"$companyDetails\",\"margin\":[0,20,0,0]},{\"width\":110,\"stack\":\"$companyAddress\",\"margin\":[0,20,0,0]},{\"stack\":[{\"text\":\"$detailsLabel\",\"style\":\"header\",\"margin\":[0,0,0,6]},{\"width\":180,\"table\":{\"body\":\"$invoiceDetails\"},\"layout\":\"noBorders\"}]}],\"margin\":[0,20,0,0]},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$notFirst:.5\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:12\",\"paddingBottom\":\"$amount:12\"}},{\"columns\":[\"$notesAndTerms\",{\"style\":\"subtotals\",\"table\":{\"widths\":[\"*\",\"40%\"],\"body\":\"$subtotalsWithoutBalance\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]},{\"canvas\":[{\"type\":\"line\",\"x1\":270,\"y1\":20,\"x2\":515,\"y2\":20,\"lineWidth\":1,\"dash\":{\"length\":2}}]},{\"text\":\"$balanceDueLabel\",\"style\":\"subtotalsBalanceDueLabel\"},{\"text\":\"$balanceDue\",\"style\":\"subtotalsBalanceDue\"},{\"canvas\":[{\"type\":\"line\",\"x1\":270,\"y1\":20,\"x2\":515,\"y2\":20,\"lineWidth\":1,\"dash\":{\"length\":2}}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"footer\":[{\"columns\":[{\"text\":\"$invoiceFooter\",\"alignment\":\"left\"}],\"margin\":[40,-20,40,0]},{\"canvas\":[{\"type\":\"line\",\"x1\":35,\"y1\":5,\"x2\":555,\"y2\":5,\"lineWidth\":2,\"margin\":[30,0,0,0]}]},{\"canvas\":[{\"type\":\"line\",\"x1\":35,\"y1\":3,\"x2\":555,\"y2\":3,\"lineWidth\":1,\"margin\":[30,0,0,0]}]}],\"defaultStyle\":{\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"styles\":{\"accountDetails\":{\"margin\":[0,2,0,1]},\"clientDetails\":{\"margin\":[0,2,0,1]},\"accountAddress\":{\"margin\":[0,2,0,1]},\"clientName\":{\"bold\":true},\"accountName\":{\"bold\":true},\"odd\":{},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"$primaryColor:#5a7b61\",\"margin\":[320,20,0,0]},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"$primaryColor:#5a7b61\",\"style\":true,\"margin\":[0,-14,8,0],\"alignment\":\"right\"},\"invoiceDetailBalanceDue\":{\"color\":\"$primaryColor:#5a7b61\",\"bold\":true},\"fullheader\":{\"font\":\"$headerFont\",\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"header\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"tableHeader\":{\"bold\":true,\"color\":\"$primaryColor:#5a7b61\",\"fontSize\":\"$fontSizeLargest\"},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"taxTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\"},\"invoiceLineItemsTable\":{\"margin\":[0,40,0,16]},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"lineTotal\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\"},\"termsLabel\":{\"bold\":true,\"margin\":[0,0,0,4]},\"subheader\":{\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"}},\"pageMargins\":[40,40,40,40],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":8,"name":"Hipster","javascript":"{\"content\":[{\"columns\":[{\"width\":10,\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":0,\"x2\":0,\"y2\":75,\"lineWidth\":0.5}]},{\"width\":120,\"stack\":[{\"text\":\"$fromLabelUC\",\"style\":\"fromLabel\"},\"$companyDetails\"]},{\"width\":120,\"stack\":[{\"text\":\" \"},\"$companyAddress\"],\"margin\":[10,0,0,16]},{\"width\":10,\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":0,\"x2\":0,\"y2\":75,\"lineWidth\":0.5}]},{\"stack\":[{\"text\":\"$toLabelUC\",\"style\":\"toLabel\"},\"$clientDetails\"]},[{\"image\":\"$companyLogo\",\"fit\":[120,80]}]]},{\"text\":\"$entityTypeUC\",\"margin\":[0,4,0,8],\"bold\":\"true\",\"fontSize\":42},{\"columnGap\":16,\"columns\":[{\"width\":\"auto\",\"text\":[\"$invoiceNoLabel\",\" \",\"$invoiceNumberValue\"],\"bold\":true,\"color\":\"$primaryColor:#bc9f2b\",\"fontSize\":10},{\"width\":\"auto\",\"text\":[\"$invoiceDateLabel\",\" \",\"$invoiceDateValue\"],\"fontSize\":10},{\"width\":\"auto\",\"text\":[\"$dueDateLabel?\",\" \",\"$dueDateValue\"],\"fontSize\":10},{\"width\":\"*\",\"text\":[\"$balanceDueLabel\",\" \",{\"text\":\"$balanceDue\",\"bold\":true,\"color\":\"$primaryColor:#bc9f2b\"}],\"fontSize\":10}]},{\"margin\":[0,26,0,0],\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$amount:.5\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:8\",\"paddingBottom\":\"$amount:8\"}},{\"columns\":[{\"stack\":\"$notesAndTerms\",\"width\":\"*\",\"margin\":[0,12,0,0]},{\"width\":200,\"style\":\"subtotals\",\"table\":{\"widths\":[\"*\",\"36%\"],\"body\":\"$subtotals\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$notFirst:.5\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:12\",\"paddingBottom\":\"$amount:4\"}}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"footer\":{\"columns\":[{\"text\":\"$invoiceFooter\",\"alignment\":\"left\"}],\"margin\":[40,-20,40,0]},\"defaultStyle\":{\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"styles\":{\"accountName\":{\"bold\":true},\"clientName\":{\"bold\":true},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"$primaryColor:#bc9f2b\",\"bold\":true},\"tableHeader\":{\"bold\":true,\"fontSize\":\"$fontSizeLargest\"},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"taxTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\"},\"fromLabel\":{\"color\":\"$primaryColor:#bc9f2b\",\"bold\":true},\"toLabel\":{\"color\":\"$primaryColor:#bc9f2b\",\"bold\":true},\"accountDetails\":{\"margin\":[0,2,0,1]},\"accountAddress\":{\"margin\":[0,2,0,1]},\"clientDetails\":{\"margin\":[0,2,0,1]},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"lineTotal\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\"},\"termsLabel\":{\"bold\":true,\"margin\":[0,16,0,4]},\"fullheader\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subheader\":{\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"}},\"pageMargins\":[40,40,40,40],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":9,"name":"Playful","javascript":"{\"content\":[{\"columns\":[{\"image\":\"$companyLogo\",\"fit\":[120,80]},{\"canvas\":[{\"type\":\"rect\",\"x\":0,\"y\":0,\"w\":190,\"h\":\"$invoiceDetailsHeight\",\"r\":5,\"lineWidth\":1,\"color\":\"$primaryColor:#009d91\"}],\"width\":10,\"margin\":[200,0,0,0]},{\"width\":400,\"table\":{\"body\":\"$invoiceDetails\"},\"layout\":\"noBorders\",\"margin\":[210,10,10,0]}]},{\"margin\":[0,18,0,0],\"columnGap\":50,\"columns\":[{\"width\":212,\"stack\":[{\"text\":\"$invoiceToLabel:\",\"style\":\"toLabel\"},{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":4,\"x2\":150,\"y2\":4,\"lineWidth\":1,\"dash\":{\"length\":3},\"lineColor\":\"$primaryColor:#009d91\"}],\"margin\":[0,0,0,4]},\"$clientDetails\",{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":9,\"x2\":150,\"y2\":9,\"lineWidth\":1,\"dash\":{\"length\":3},\"lineColor\":\"$primaryColor:#009d91\"}]}]},{\"width\":\"*\",\"stack\":[{\"text\":\"$fromLabel:\",\"style\":\"fromLabel\"},{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":4,\"x2\":250,\"y2\":4,\"lineWidth\":1,\"dash\":{\"length\":3},\"lineColor\":\"$primaryColor:#009d91\"}],\"margin\":[0,0,0,4]},{\"columns\":[\"$companyDetails\",\"$companyAddress\"],\"columnGap\":4},{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":9,\"x2\":250,\"y2\":9,\"lineWidth\":1,\"dash\":{\"length\":3},\"lineColor\":\"$primaryColor:#009d91\"}]}]}]},{\"canvas\":[{\"type\":\"rect\",\"x\":0,\"y\":0,\"w\":515,\"h\":35,\"r\":6,\"lineWidth\":1,\"color\":\"$primaryColor:#009d91\"}],\"width\":10,\"margin\":[0,30,0,-30]},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$notFirst:.5\",\"vLineWidth\":\"$none\",\"hLineColor\":\"$primaryColor:#009d91\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:8\",\"paddingBottom\":\"$amount:8\"}},{\"columns\":[\"$notesAndTerms\",{\"stack\":[{\"style\":\"subtotals\",\"table\":{\"widths\":[\"*\",\"35%\"],\"body\":\"$subtotalsWithoutBalance\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}},{\"canvas\":[{\"type\":\"rect\",\"x\":50,\"y\":20,\"w\":208,\"h\":30,\"r\":4,\"lineWidth\":1,\"color\":\"$primaryColor:#009d91\"}]},{\"style\":\"subtotalsBalance\",\"table\":{\"widths\":[\"*\",\"50%\"],\"body\":\"$subtotalsBalance\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"footer\":[{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":38,\"x2\":68,\"y2\":38,\"lineWidth\":6,\"lineColor\":\"#009d91\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":68,\"y1\":0,\"x2\":135,\"y2\":0,\"lineWidth\":6,\"lineColor\":\"#1d766f\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":135,\"y1\":0,\"x2\":201,\"y2\":0,\"lineWidth\":6,\"lineColor\":\"#ffb800\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":201,\"y1\":0,\"x2\":267,\"y2\":0,\"lineWidth\":6,\"lineColor\":\"#bf9730\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":267,\"y1\":0,\"x2\":333,\"y2\":0,\"lineWidth\":6,\"lineColor\":\"#ac2b50\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":333,\"y1\":0,\"x2\":399,\"y2\":0,\"lineWidth\":6,\"lineColor\":\"#e60042\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":399,\"y1\":0,\"x2\":465,\"y2\":0,\"lineWidth\":6,\"lineColor\":\"#ffb800\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":465,\"y1\":0,\"x2\":532,\"y2\":0,\"lineWidth\":6,\"lineColor\":\"#009d91\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":532,\"y1\":0,\"x2\":600,\"y2\":0,\"lineWidth\":6,\"lineColor\":\"#ac2b50\"}]},{\"text\":\"$invoiceFooter\",\"alignment\":\"left\",\"margin\":[40,-60,40,0]}],\"header\":[{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":0,\"x2\":68,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#009d91\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":68,\"y1\":0,\"x2\":135,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#1d766f\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":135,\"y1\":0,\"x2\":201,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#ffb800\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":201,\"y1\":0,\"x2\":267,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#bf9730\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":267,\"y1\":0,\"x2\":333,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#ac2b50\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":333,\"y1\":0,\"x2\":399,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#e60042\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":399,\"y1\":0,\"x2\":465,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#ffb800\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":465,\"y1\":0,\"x2\":532,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#009d91\"}]},{\"canvas\":[{\"type\":\"line\",\"x1\":532,\"y1\":0,\"x2\":600,\"y2\":0,\"lineWidth\":9,\"lineColor\":\"#ac2b50\"}]}],\"defaultStyle\":{\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"styles\":{\"accountName\":{\"color\":\"$secondaryColor:#bb3328\"},\"accountDetails\":{\"margin\":[0,2,0,1]},\"accountAddress\":{\"margin\":[0,2,0,1]},\"clientDetails\":{\"margin\":[0,2,0,1]},\"clientName\":{\"color\":\"$secondaryColor:#bb3328\"},\"even\":{\"fillColor\":\"#E8E8E8\"},\"odd\":{\"fillColor\":\"#F7F7F7\"},\"productKey\":{\"color\":\"$secondaryColor:#bb3328\"},\"lineTotal\":{\"alignment\":\"right\"},\"tableHeader\":{\"bold\":true,\"fontSize\":\"$fontSizeLargest\",\"color\":\"#FFFFFF\"},\"secondTableHeader\":{\"color\":\"$primaryColor:#009d91\"},\"costTableHeader\":{\"alignment\":\"right\"},\"qtyTableHeader\":{\"alignment\":\"right\"},\"lineTotalTableHeader\":{\"alignment\":\"right\"},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"#FFFFFF\",\"bold\":true},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true,\"color\":\"#FFFFFF\",\"alignment\":\"right\"},\"invoiceDetails\":{\"color\":\"#FFFFFF\"},\"invoiceLineItemsTable\":{\"margin\":[0,0,0,16]},\"invoiceDetailBalanceDueLabel\":{\"bold\":true},\"invoiceDetailBalanceDue\":{\"bold\":true},\"fromLabel\":{\"color\":\"$primaryColor:#009d91\"},\"toLabel\":{\"color\":\"$primaryColor:#009d91\"},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"subtotals\":{\"alignment\":\"right\"},\"subtotalsBalance\":{\"alignment\":\"right\",\"margin\":[0,-25,0,0]},\"termsLabel\":{\"bold\":true,\"margin\":[0,0,0,4]},\"fullheader\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"subheader\":{\"fontSize\":\"$fontSizeLarger\"},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"}},\"pageMargins\":[40,40,40,40],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null},{"id":10,"name":"Photo","javascript":"{\"content\":[{\"columns\":[{\"image\":\"$companyLogo\",\"fit\":[120,80]},{\"text\":\"\",\"width\":\"*\"},{\"width\":180,\"table\":{\"body\":\"$invoiceDetails\"},\"layout\":\"noBorders\"}]},{\"image\":\"data:image\\\/jpeg;base64,\\\/9j\\\/4AAQSkZJRgABAQEAYABgAAD\\\/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT\\\/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT\\\/wAARCAEZA4QDASIAAhEBAxEB\\\/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL\\\/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6\\\/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL\\\/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6\\\/9oADAMBAAIRAxEAPwD0kT6iVJXXdaC++rXH\\\/wAcpY59U+9\\\/bmtED\\\/qKXA\\\/9nqmJuPlOR6Af\\\/XpUuHRCD8o9CM1jqaWL5vb5+usa2p\\\/7C1x\\\/8XUbXOpQddd1pgf+opc\\\/\\\/F1Thulx1B57ipzIoH3sfVR\\\/hRqFiy11qP8A0G9aXj\\\/oKXP9Xpst9qLfd1nWSe+dVuP\\\/AIuq6XJjzl\\\/M+rHj86ljuTnlwn4E0ahYkW81HIxretEjqDqtwP8A2pUp1PUFH\\\/Ib1oH\\\/ALCc\\\/wD8XVQyqMmWHavZhhc0PtYDapPsGo1CxpDUtSA+XWdZc\\\/8AYUn\\\/APiqaNX1A5U63q6\\\/9xOY\\\/wDs9Uwcj5WOfRTzUABDHOB7nFGoWNRdQ1Numtaxjrk6jP8A\\\/F1MdX1BYwF1rV947\\\/2hPj\\\/0Os3KvGFUqzemMVD5whbknjjAxj86Wo7I1DrGqj5v7Z1b6nUZ\\\/wD4upY9c1Qr\\\/wAhrVS3p\\\/aE3\\\/xVZJuAU3BcH+8TikS6GQMhpPTg\\\/rRqBr\\\/27qvT+2dVH11GX\\\/4ulGt6sWA\\\/tnVSPX7fN\\\/8AFVlmd8ZZdq+o\\\/wD1UhmV12s42nrRqFkbX9t6mqZOs6kCP+ojPn\\\/0KmnXtVCk\\\/wBs6qR1\\\/wCP+b\\\/4qsXfGg2ocnsN1Kk7KuNu0dTxmlqFjaj8R6mykHVtV3Z6i\\\/l4\\\/wDH6cNd1VcA63qjHt\\\/p8v8A8VWTHdfKQGwKcWZ\\\/u7XHtRqFjXTXdWHXWdT9s30v\\\/wAVTh4k1dQf+JvqLfS\\\/kP8A7NWPG4UESZU9gP8A9VIZPKI4IB\\\/uGjUDZHiPWsYOr6muPW8l\\\/wDiqcvifWG\\\/5jOoJ7fa5ef\\\/AB41lfaUf+IH6U2AomcyIc+wP9aNQNf\\\/AISTWe2taifpdSn+tTnxTrSAY1i+Pt9sf+rVhCYHo3\\\/juKPtYTopJ\\\/2WH+NO4G9\\\/wlmrr11nUfwvW\\\/xpB4z1cMQNX1FuehupB\\\/I1giQMclT+JpWkTHdP8\\\/hSA6H\\\/AIS7WTh\\\/7Zv+ewu34\\\/Wm\\\/wDCW61jP9s354\\\/5+n\\\/xrCVuATkjseaa8odDgk0Aa7+LdcJx\\\/bWoDtn7W\\\/r9aRvF2tgEf2zqAPOD9qf\\\/ABrn2uC7k8dfpmlnkAj5f5T05\\\/SncDpdP8X65HqVp\\\/xOb6U+cnym6cg8jqM9K96\\\/aD8R3mj\\\/AAN8Q3tpPNaXf2TaksUhV1YkDhhyOtfN3hhs+IdOUqWU3CjH1PSvo79pD7LD8C\\\/EMdwuRJbBIwf75I2\\\/ripd7j6H5r+KPiv4yhuXEXivXI8KBhdRm9P96uHk+Lvjdpc\\\/8Jn4gA9Bqs\\\/\\\/AMXR4uu\\\/Nu50TAG7FcjtAfB6k4zXSYnaR\\\/Ffxxt\\\/5HLxDk\\\/9RSf\\\/AOLqKT4teOFOP+Ez8QEA\\\/wDQVn\\\/+KrmkxtI7gciopyVYZAz6UAd7afF3xoLQv\\\/wmGvHA5J1Ocn\\\/0Ks+6+LvjdiSvjLXwe\\\/8AxNZ\\\/\\\/i65mzkJjkjP3faqsn3zjnnJJoA6j\\\/hbvjk8Hxl4g6f9BWf\\\/AOLqZPiz44BH\\\/FZ+Ic55\\\/wCJpP8A\\\/FVx\\\/Qe3rihW3Px07EDqKAOuf4t+OCWx4z8Q9f8AoKT5\\\/wDQqWL4teOB18ZeIT\\\/3FZ\\\/\\\/AIuuTGSrY6Z701pMD\\\/CgDrn+Lfjlj8vjLxBg\\\/wDUUn\\\/+LqM\\\/FnxyOP8AhM\\\/EPoT\\\/AGpPz\\\/4\\\/XKDO4n24BFPJAOcgY6UAdWfiz45C5PjPxD0\\\/6Ck\\\/\\\/wAVUY+LPjkgY8Z+IiP+wrPn\\\/wBDrl3dSeB9eajHB657kCgDrf8AhbfjkjA8Z+IQfX+1J\\\/8A4uhvi545PI8Z+If\\\/AAaT8f8Aj9cox44zgU0A4PJIzQB1p+LXjnd\\\/yOniEDH\\\/AEFJ+v8A33TV+Lfjk9PGfiHr\\\/wBBWf8A+LrlACV5GO4xSHIzgZOeMjrQB1Y+Lfjof8zp4h\\\/8Gs\\\/\\\/AMXQfi345Rs\\\/8Jn4hPbH9qz+v+\\\/XJ5U89D70jctwQD+lAHW\\\/8Lb8dcZ8Z+Ic+2qT8f8Aj1TRfFvxuUP\\\/ABWfiDP\\\/AGFJ\\\/wD4uuNOCeB26VYt8fN3oA67\\\/hbPjgL\\\/AMjl4hz0z\\\/ak\\\/wD8XSj4s+OWjLDxlr5AOONUn5\\\/8erkJTzgfKB0p9ucQli2MngE0AdQnxX8cs2T408Qge2qTn\\\/2elf4teOFGR4z8Qbv+wpP\\\/APF1yUYLHAPHXk9KkkZQhVdpJoA6T\\\/hbnjndz4y8QdP+grP\\\/APF0J8WvHOB\\\/xWniE\\\/8AcUn\\\/APi65XqT245+tNY7iDnAoA7Fvi545IGPGXiAf9xWf\\\/4unRfFnxwAzHxnr+7\\\/ALCk\\\/wD8XXIrgoDuOAe1IXwRk4oA6g\\\/FzxwW48aeIP8AwaT\\\/APxdMHxb8dcg+M\\\/EOPUapP8A\\\/F1y7LkjHOfzppGAT0xQB1n\\\/AAtvxycf8Vp4h6dP7Vn\\\/APi6T\\\/hbfjr\\\/AKHTxBx\\\/1FZ\\\/\\\/iq5Xdkc5U9fSkAHHTHvQB1y\\\/Fzxzjnxn4gBA6\\\/2rP8A\\\/FUjfFvx1\\\/0OniE\\\/9xSf\\\/wCLrk0Hbj8KR2DA9\\\/egDqx8WPHWT\\\/xWniL\\\/AMGs\\\/wD8VS\\\/8Lb8ckf8AI5+Icf8AYVn\\\/APi65LkDvinYIIOcjv7UAdbH8XfHB\\\/5nPxACRk\\\/8TSc\\\/+z00\\\/FzxxuGfGfiHA7f2rP8A\\\/FVyyozPsGc+nep7PT59QvobWCJpZ5nCIiclj0xQB7Jb+OPGFz4UbU\\\/+Eu12Nkh4QapPyemfv+4NeweAdCvPib4o16PW\\\/irrfhwWNrZrDawahKXlZrdCWwXAwD19zXIeNPhxp3gL4F6bcT38n\\\/CRzNsvdKljw1sAepHX0\\\/OvOvFlhp3iDxFcarpvjHTLZJ0iCxytNG64jVSDhO201F77FWsVPG3jnxn4T8Y6no8HxC1nU4bOdoVu4NUn2SgHgjL19O+E\\\/hjfa34M0JLzxz4ntte1XSX1BZX12ZWRgoI2xAkMvIydw9q+SR4CjkYsvifQpGzyTeEZP4qP1rttK8UfEHR9MttO034gWCWVtG0UMKatF8iEYKgt29ulJ3toCaW56D4ff7J8FbHxv4n8eeNla41OSw8vTtSc9AcH5nHTBPWuh8NfD7Ur6+8H6bf\\\/ABI8ZfbfE9pJf20tvfyeVDEBuUPl+WIPOOBXgs2l+LZ\\\/C0Hht9a0y40S3uTdxWi6pblVkIILD5s9zX1Z8OPG3hnwL4V09TrI1OSwtRFbWhuYJbiJmUeYu44CqDnhX6AVMm0tGUrM8z8MeDvEF\\\/a+F4dT+JniuHUPE93Pb6ebW9leOJY2K7pMyc5OOBWX4b+HPxR1S78WSap491\\\/StF8OvPHNqQvbmRZ2jZgREocZPy\\\/rWb4PvviloXkabpwtJbGG6eW0u7kQzNZl8hnjOSUyDkgZrsfjB4f8QWHwz0fwT4WsdR1uWadtR1vVIYnH2i4YfdBOCwySfwFF2na4tDzjxDB4+0fwT4V8RWnj\\\/wAQaiPENxPb29ol9cCQeW+wH7\\\/O7jj3rofFngv4heDtPcaj8VNQt9YjsU1GTTp9SuYzsbqiSM215BkEqKwnn+JK+A9N8L3PgWS4ttL8w2F41lMLm2Z2LMyurAA5xjjsKl8U+PviF4k0iS31XwX5+oS2iWEmpT2E0kpjUAZVWJVHOBllAJxVXYaGp4r8IfFbwh4ZbxLH8Tp9R8O\\\/ZvPXU7PW53jaTgCAc\\\/6wk4x9fSvMdJ+NPxMv7yG1tPGfiKa5lYJHEl\\\/MxZicAAZ5JPFdrB8Z\\\/Fen6Dc+Gr3wZDN4OmtVtn0Y20kaqR\\\/y1V+SJM8lufpXkdhcaj4d16HVNOgnsZ7WcXFvvUs0ZVty\\\/MQM4x1xTV+pLt0PcpvFXx0+HXi7w1Z+K9a8SafBqVwiJFfXL7Jl3AMOT6EZHUZFefeP\\\/il42s\\\/EF7bweLtehtEuJ1gRdTmGEWZ1UZ3c8D9K6ef40+JPjJ4+8F2uvbVjtNSjdVQN8zsy5Y5Pt2ry74hKTrrS7i4leZwCen+kS9Py7U0N+RMfi345PTxn4hA\\\/7Ck5\\\/wDZ6T\\\/hbPjvkf8ACZ+If\\\/BpP\\\/8AF1yu35gPbr0oC7s55BqiTqx8WvHAbB8Z+If\\\/AAaz\\\/wDxVL\\\/wtrxzyf8AhM\\\/EOPQapP8A\\\/F1yjAIOvPpUa5LYxt47CgDrn+LfjkjI8Z+IRz\\\/0FJ\\\/\\\/AIqj\\\/hbfjkj\\\/AJHLxBnrj+1Z\\\/wD4uuUjG0+o96kRBu5A5oA6j\\\/ha\\\/joYz408QE\\\/9hSf\\\/AOLpU+LXjoLj\\\/hM\\\/EOR2\\\/tSfn\\\/x6uVnID8Dvio1k\\\/izkfSgDrn+LPjrcSvjLxDt4\\\/wCYpP8A\\\/F0w\\\/Fvx1\\\/0OfiEn\\\/sKz\\\/wDxdc0kvG0qMetRuPn469R2NAHUr8WfHP8A0OniEH\\\/sKz\\\/\\\/ABdPX4ueOA4P\\\/CZ+IOf+opP\\\/APF1ybgdsH1NNiBJGT06ZoA7F\\\/ir44wGXxl4hPv\\\/AGrP\\\/wDF0yT4t+OBhf8AhM\\\/EC+\\\/9qz\\\/\\\/ABdc2TgKAQv0qvdMxc8g49KAOqT4teOiePGXiDPr\\\/ak\\\/\\\/wAXTf8AhbfjoHnxn4h+n9qT\\\/wDxdcxEGI4+maRT8w4yAfXFAHXSfFvxygX\\\/AIrLxCAQef7Un\\\/8Aiqif4t+OOCfGniH3\\\/wCJpP8A\\\/Ff5zXNStuUEkn0AqCT5jkjB9KAOpPxd8dYwfGniH8NVn\\\/8Ai6QfF3xyAD\\\/wmniE8\\\/8AQVn\\\/APiq5PqRn+dKv3s9qAOs\\\/wCFueOjyvjTxCOOB\\\/as\\\/wD8XSD4ueOTjPjPxFgeuqz\\\/APxVcpx0wc0cY5INAHWj4u+OV\\\/5nTxDgk\\\/8AMVn\\\/APi6P+FueOSf+R08Q4x\\\/0FZ\\\/\\\/i65IrkcGlPC8gD07GgDqm+LvjpTj\\\/hM\\\/EJ\\\/7is\\\/\\\/wAXRXK5UZ3Lk+9FAH22dzj7mffP\\\/wBapYEKxnG4Y9+P5U1CAQPnxnsSRT2jDZKuVx2DYFZGoI28Zyn\\\/AALGakc5HUj6DH8qqr5g\\\/iz75zTstxuYP\\\/vc4oAkgmZt29wcdN3NSEsBgv8AmwqBUOT1P1B\\\/wpvmOB87F\\\/QelAFmWRSq7MK3c1MjBVBZicj1AqtE5J+62KimkdP4QQT0Y0AaQ+f+79aa7YHrz3qiXMigOFAHT\\\/IFSLLIv+7260AWGk3rtGQfYU0u4GCcL7kVHl+pOM\\\/3s4pPM7BVz\\\/fAOP5UAPMrpzuDKOwPNKtyWwC2F\\\/u96rnyw5Zid3pt4pyy7XG1QB6gEGgCwZwjZUN+INAuBM20kDPY5zVaTcZN5II6fNk\\\/pSoCxB+Xb6KMGkBa\\\/wBX0xgejc\\\/lSiZGPKknpzVUsqTD5W+pOTUruGOcZx03LRYCfzI1+QgBj0\\\/yTUgYRAgsqnthg38qqKGdTkLn6UgYx8E4J6Bs0WAtK+8HMu3HtSI2z\\\/VnGeuTiq5fb98Y9Nn9aXz8\\\/ecKe3NFhNliSUqfmcH6im+cX+58nqACM\\\/nVYjd987iO4JGKkBiH3irH\\\/ZH\\\/ANaiwx73ix44x9R\\\/9amC5kUk9j0yMfzqIuT985HbjNRSXRAHU\\\/T5aLAaBnYKCxU\\\/pUQu9rcufpmq6z+YAC2O\\\/HWomuI9xXauR36GgC\\\/9oO3cQwB+vNK04YYwCPXPas03IOQJFwP4Rjio1uc5yQvP5e1FhXNZbr5l54zzzTRMBxwTWclySB0z\\\/P3qUtkk8DsPrRYZ6T8DdPg1bx\\\/YCUKRExkGR3AJH611H7enjE+F\\\/hRptpGdrX16A3OCVVGOPzxWT+zhZC48aCXONkbZPrxjFcp\\\/wU23ReFfBmDhDdTA+n3BUfaB7H5\\\/T3L3Vw8jMTk5OTnrURiB6dj6U215Ygj8KsFsMMHmukyGpCWTLYUD1qvMSzf496mnuCAVHpwMcVTyScdqALEBwpI55596lcAxhiPzpLWLzEYE9TyKLsiMhFbgdRQBAeCcgZPOaarAPjocUEjJzwe1Mxg9MAdKAJy6hc45xTHbdzjBHfNHfPUYzkUmARQAuMlcjnPGacxxxweOtGCF5OSO9R7gR7ZoAGIJHGD3oUgn\\\/Z44H+fpTm4OQcD86Z0Hp9KAFU59fqKX0JAOKavB\\\/wAKCcg55zQAO2M9TntSglsj3pvXtn1ozznGKAAZOTzj1pBwDzu460vO0EDtk0oU9uOfzoAaQec8VZhASJifx4qsefqKsx\\\/Kh5zngUAEmVOeuelA4jGMnrxURbccZJ\\\/z61aVMxrzkA0AIzbUJxzj8qrE\\\/PnJ49RxUsz5AHIXHWmiPoT39BQApGw881GTu6E4qe44Xr254qsCS3PA\\\/nQBLswgP3hTMhScd\\\/xqdiMKecEVGFyRt659PrQAiL16g4710\\\/gf4eav8R9TNjo8AeRV3SSudscY9WY8AVzRIX5VyDjBr2DR\\\/FkXw08FaTaRjf8A2rMLnUERtrvECMICOmcNSY0UPHH7O3ibwNo8OqSta6jasdrPYyF9h9+K8ve2kjJDIy9sEe9fd1h+1z8MrbwjBbRfD4nTI1WJ\\\/N5XdjucHJ964G+8S\\\/AvxVqVrOthdaf50wMsEM+UQE\\\/7QB\\\/I1mpPqiml0Z8qWWm3d9cpBbQSXEzHAjjXcT+VdBq\\\/wy8UaFJHHf6JeWryxiZBJERvQ9xX3d8NtJ+CkfjGCDRZZtN1C2USR37Ou4naCcqwII69PSvcfG\\\/wOsfHVkuq2eqy3WqRxnyJwU2yL12kgcex7Zo59dh8vmfkF\\\/Y90JZIzA4Mf3l2nK\\\/WrWn+G73VZ\\\/ItbeSWb+6q5Nfeup2N18Ilng03w7aaXqFxKZb+41mKO4EyDqUyMY6HINfO3iXxhP478bDUp9NS10Z5yJrLSUFp5qDgMxUHk9faqUmyWrHk7aHp\\\/hmWWLWJ\\\/OukH\\\/HnZkNg\\\/wC1J0HvjNdh8B9F0vV\\\/GSXN9rK+H\\\/scguLZjCJSzAkhcnAH1Net6x+zx8OPGmitfeF\\\/EN\\\/o2tCIvJp2r4kRiBk4kCj26181tDJpG+MyL5schhOw5HHfPcdaaakLY9k+MHxR0XxFqmrypd3OoXl4cTXbxgbwDjgZAA\\\/CvGVTRXBLPMD\\\/ANcx\\\/jWbJM8vyn5s+gqJYJCAdhz24ppWVg3Nd7XQsDFzMoP\\\/AEzz\\\/WnHTtHZsf2gwzxkxniskWrgDCN+VAtpHH3SPTApiNZdL0vzCv8AaYx\\\/eEbU\\\/wDsbTV4GrRg9fuMMn8qp2Oh3mpTpFDbyySMRhUXOa90+Hf7G3jLxeYZr+IaLaSjdvuR+8I9k6\\\/nipcktxpN7HjiaDZkjbrUPT+62P5UsugxwSjydahJznKswxX2PafsHeHNKhRtS1nUbiXIAEISMH8CCaS\\\/\\\/Yq8GNEPLv8AVLVscvJNHjP\\\/AAJBWftYl8kj5AjsL1WIi8RopHTFyy\\\/1q1AviNBui8TuvP8ADqLD\\\/wBmr2nx7+xZq+kxLN4f1AaojZIhnHlOfQK33Tn8K+efEfhbVfC2ovZ6nZz2VwpIMcqEfiD3HvVpqWxLTR1BvPGcDDy\\\/FN0c9NupsR\\\/6FUy6v4+Vd6+JLyT6X5b+teds7tnLk+lAkZf4iD6DjNVYk9ETxF8QkZJE1e9aQHKuJQWB9j1pdU+G2u+IbfTZ9P0+7v2jtlSbyk3nzN7u2e\\\/8Qrzr7TKp4kZenAatjRfFOpaLcRTWt5PEwOQVkIwcj0+lFuwEHiDw5eeH7g2+oWclhOqg+VOpQkH2NZC\\\/I3TPHPevqr9p7W7X4l2XgS1mhU+IW8OQ3MdwmA0smSXjb1yoyPcY718qFTFlSCCDgqRzmkndXG1ZiO3y4C8HikVdo4JAx9KHJb2FPQlT2xjpiqEHIz6\\\/SpYiRnI5qPzMr79OKWNjjB7Z6mgBkzAuTjg8c0q44J6E+lI6ZIYgk9eeaAcEKOOcn6UAOGAcZ+XpwaYww2TyPU04Ody4wOajcnK45oAl4fBGM05htXI69qi6kc9KlDl1YAE45oAUPlA2QSO9Qu3PI\\\/KnRjoT1NOuArONuMfWgCOFm4x1p8q54A6\\\/rUPKHJPHQEGpjl413AFSetADS3yAdulRuM5znr2p5wM9gfXmmdAQOCTgYHFADM88YGOc0uMHkhiOSelISc4wKU478H0xQAdMAdR7UcbuvFKOBgc59KUc9B0oAMZABAPamk9dtKWOecfWgn0GT1oAFOB1\\\/KilPXg0UAfcn2MqcBR9QabJD5bAFyp7DOa62TR8Ngj9f\\\/rU3+yEA5Rfq3NYXNTk3tJnGQCQBzzUcMT\\\/ADbAR69v6V1v9lkfdVSO+FoOk89C305xRcDlngc427k+hzmjyHTqG\\\/76rqptKiG3aFTPoKhfSsAYyP8AdWi4rHMPbStxGOffIqbyH2gfMx7gEHH510aaU0hwB09M019J6blP6Ci4zBjj8okhGyetJIrkZbp25NdDHphPBG76DNK2njOAMH0\\\/yaLhY5tY3Q5J+X64\\\/XFOWMh93Dg\\\/w5FdCNNTdyoz7innTDj5Yx7HFFwMEKWXlSAf4dxxUbQMX9I\\\/7o5\\\/Wt4Wwjk2kDI9amWxjcbmA9yRxRcDnDbHblVKj+9\\\/9akFuSOFJfs3T+tdCbFFn4K7Mfwnj8qc+nggsqk+4xSuBzgtCp3OhLDtn\\\/65oa2LvlYiB0rfFi2RlMj1PWpBp6spyM\\\/rTuBzzWzp\\\/wAs8D6A01bZpOQgGP71dLFpaMhOChz0HFL\\\/AGWMEnIPpwc0XA5l4HJGUA+gNJ9lVPu7UPtnmujFgCPmBX8c1GumqP4jID6Y4ouBzxtXbG5yf94EUvlPGOAy59Oa6NNKQZwhb3Apw04t1yfSi4WOSNsFPR1z7VH5BP3uR9K6waNtJ5FMj0vax2BGPei4HNmEoo2oM\\\/7AOagZJQxOQeencV1SaaFdtq5PfcOKa+knO7YCSem00XCxzDx5UHysMOS1RSRMcDGD06V1i6M5OWVQp6Y7VXbRjheGGB0p3CxyyhlySPmJ6elTB9\\\/94Y9q220fC\\\/OvH1pY9Ey\\\/3SPTPcUXEdn8ANSnsviHYpF80coZHAI6YzVn\\\/gpFp6Xfwp8Pzuv7+PVFVGz0BifP8h+Vb3wK0JI\\\/HFrOQp2xsQPf2rnP+Ck+oJF4I8HaeCMz6m8hX1CREf8As4qFrK43sfnH5TWrk54NSIcgsQMe5q1qMaJcFeMA8iqN1KMbVAx3IHIrpMivM+45GeBnOKYvBGeR6inqd2M\\\/dPt+dPKhV7jJoAlsZdhZT355qO4+aX8KbCDvOO1OZgT83A5\\\/CgCEZLd+vA9qV+Ae\\\/wBaVjgDv2zSPgAn37UAJ91cEcdMU+IgAYJx71GPmyTyfSlPAxgCgBztzz0xwabgHHc+lByTnrn09acxxxjJ9hQAHjAOf51Gw3ZPY8c96cCeh60hAzzn0FAAOT0+bvSHgZPPtTycggmmjIYg4PrQAmdo4BFIecg+vel7gZ4pqkb\\\/AJufxoAcFJ4zgYz0oY7gT1U5pq9+Mf0pwIHJGcetABkkjPGBVhV\\\/EjpVZR82R261YjzkDt3oAcYtke48M3Sn2xMybB0J6Ypk7gtjoPWkiPlozZJI7YoASVMyHjg1Iqsyg456CmOfM29QCccVL\\\/qFGep60AVnLMSDz1\\\/Smfdx39sVK5AHDZHtTFwBk9e3FAEo5UYwD3qSIEZJwTkVEZRgjIxShio5PXpmgAb\\\/AFgGM89q9D+K9qirouyPymFqibTxggen415\\\/YWz3l\\\/BEiF2dgB6nmvadVtUvvE1xqmpxK9ppEQQI33WcL8q9x2\\\/SgDgPEjNofg3TNJZNsszfa5SDn733Rj6fzrjAxViwByOhzWl4j1ibXNWubuRi29jt7cfSsxgMkZNAHReGtav5tStAlyEkh4h3nb+Ga+vvgl8dvElloqfZdTeGWFissDgMjYPcYxXxFGMrnPNbmh+LNV8OzB7C+kgA5Kg5U49R3qWrjTsfo34o\\\/aCt9Z0fyPFfh7TdWtEIYRzISN3r\\\/OuY074ieBtWieTSPAGgxyEdie3qBXyr4M+JPiHx54n03SLqa0SKVtru0eBt68847U2L4j3vhnxDqUdilvCIpmSMrHnGDjPJxWfIXzHb\\\/tJfGeS6t7PRNFS10eBlLXdtYWwj3H+H95jJHXgHFfO1hIJo2VhnBLHnnp\\\/9avV9B1ez13wl48utX0yLVNUeFTBfzKpa3JlTlR26np615RbKRJMwwBtJrSOmhDd9SOBlMyYHGO3pV44IIB57VQgx56YyDt6DtV\\\/B7Z\\\/CqEKE3kDbknk10Hhvw\\\/Nrt\\\/BaW0DXEsrhFRFyST0wB1rEi+ZwOeK+yf2NPhhHHHP4pvoSTnyrMyICM4+dunUcAYPXNRKXKrjSu7HqPwR\\\/Z60r4Z2EeoarbQ3uvEbhIp3CH\\\/ZQHq3vXdeN\\\/i5ofw+0432qXUdtGoIMRYF3H8OOMk+3bvXIfGf4p2fw60K41q4YtLGhitbUNhWcg4\\\/HIPPBHzelfnf4++IOsfEHXJ7+\\\/unnmkc4jDHbGD\\\/Co7AZrmjBzd2bykoaI+pPHn7dyTytDo+lOYF5WSWXYT+Azn8a5TTP24dXt7pDdaak0WeQly0ZIz\\\/s4\\\/WvDPC\\\/wa8YeM7c3GlaHd3sI48xY\\\/k9\\\/mPFQ+Kfg94t8IxedqmhXltCp5lMZKD8RxW6jDYycpbn298Nv2nPCXxCuf7PYtpF\\\/ORiC5ChWb\\\/eGFYnjhh+NdX8QvhXoXxE0prbUrNZWCEQyqfnibnleffO05B6gkV+ZVtcSWkoaMkFT696+yf2Ufj1LrLJ4R8Q3QkkCYsLiUnc+P8AliWPfup7EfnnKny+9EuMr6M+evib8NL74fa9LYXkYdcZiuE5SRfUHH5jtXFi1RtxKgem7jFfoX+0F8N4vG\\\/gW+EcGb+BDPaOqDBcDO0E8qHUdB3HPQY\\\/Pm5BimkjZdrA4IPY+lawlzozkrMqi3TB+QDB+lVpl2Soq4UYHvnnrVzgg8gdfWqE5zcgZB6VoSeqfG6\\\/uINY8HKkpjkg0K0CuvVTgkEfQn9K4DxjYl7i11UbVOoIZJEXAKyqxV+nqRu\\\/4FXoHxKtoH8b6RJcspgg06037idoHlj+ua+jfgHZ\\\/BX4rfDu38H+IrW1tvE0ks7x3oUJKQznaVkxxgH7p44qL8qHa58LLjH6U5IwPTHTNejfHr4PX3wS+IV94duX8+EATWl12mhY\\\/K314IPuK85xx7Hoaq9xETsqsQFHPTmkRiox+lNDbpO+Peng\\\/N7ZxxTAeGynHb8qkjiWXOfrioG4HAz7mpoWAYAnBoAjnUI30\\\/SoepAHJPepJypc9jTI8Ejn3oAM7Tg5P1qSKUDoCR796a6ds4BpI1yw7885FAEyxfODk8+tRvgyYUY+lWWXKbhxjqKp53OTg8+lAD5VBAGQD7UoyAAemfpmkcAlc8H1FOY4XGckUAMyNvTtjimB8A8d\\\/WlYDA6\\\/j3ppJA5GfwoATGcYwO9Gfm4HHbJo+7jnHsKCevp\\\/KgBS2M5A6cYNG44yOPWkJOMd+tA9OaAAnt1OfSkY4GdxJ5FKMk49PUUuDg460AAfA5BooyO6hvfGaKAP1AksxtJJfGOmCaqrYKx4RvqT\\\/wDWrrPsYB5O0+gBFNktV3j7xPYjGK4bnVY5SXTiSNrFB6AZzSppaAHPBP8AcGK6z7GT\\\/AW+i002YTrETnuoxRcVjl20raBujK\\\/VhzUi6Sx+7uH14\\\/pXSvY7MY3HPoc\\\/ypfspb+EH60XHY5T+xmBJTAbuc4pr6Gz\\\/fYe3Oa6o2UYz5jYHbApRp4XlSSD0zRzCscf\\\/YzDpGG+tKujeWS21CT2C12n2RIxkuV+nFBslYA9vU0+YLHGDS8HO1RTTphZiuVA9xgV1zWK7j8uR79P0pfsWPuxqvuAaOYLHHtpLDOTlfbOKP7N2oQBkex5rr1swW+YfUYofTkdiAuM980cwWORj01QM7HLe5pTaqW2FWX3B6fpXVf2WoO3AI\\\/EUh0tS2wKQPXGaLhY5gWSqwVcn3LUj2A342BiR1INdQdLEJ4yWH+zx\\\/Kmm1dpAGAXP0FHMFjmf7NIH3WVe+1TilGl7uUIIHXd1\\\/pXUnTdpGN\\\/4Hikex3NnaOP7opXCxy0mnqCNylj2Ix\\\/hQunJz8pH511P2Mt7Un2Be0RH+8MU+YLHLiw8rgLnP8AdWiTTiOkefoa6g6aScjI+vNLNYsxGUQ\\\/Q0rhY5MaZ6qE+oxSrpUjn5cD6viusuLAkLsH5Uw6dgAsD\\\/KnzBY5X+zZ4+3meyij+ynYZEYDd811a6eAeRx6daP7NGSVDH6CjmCxyn9nM2EZQoHc0j6duBJXGB1JrrhpJI6HJ7EVE2k4IGOBz9afMFjipNP+XCx4FA03BUhOQOufrXZS6aDkbcKPbio\\\/7KIOQn0J6mjmCx0PwSsvK8RMzH5whK5PavFv+CmLSR\\\/8IExz5Hm3AyPXCV718OY2t9diwoGeCe4HNeT\\\/APBSKygufh14VlfIuU1U+WB\\\/dMT7v5LVQepMlZH53akwknZuo9h2rLlb94cDAFamoKEHc8dDWd5QlYY6n1rp2MBsRyd3Hp+NSScLuOTxT0tHywI4FDqdpBz16GmBXixux+tSeSzZOPl9+KbCP3ygirVy2IwB24\\\/xoApSHB4+nrTCTxnn6dh70Dk5JxilzkdQcjpQAnBB9+1KCRn68c0mdx7mkwDjGfegBckcfzqQuQRyPY1GQAAQTn0FKOvuT1oAGBDE+tISfpTjnnA7Z5ppOTjjn0oAFUdWA6cUEkEjGM+opSD6Zz7dKTByQc4z270AB9\\\/wpNuRnAz9KP4T0FABBGeOOnpQAm8dj2pQMDPb60dCMDnPQUDOBk8fyoAcvJ46+v8An6VMnLk9hzgdqjhz6DjualtlUAnkc9c\\\/59KAGynGcAjPSlTCwjPQnvTJMNjByM9qmjUNCQfXPtQAsYBQHPH61FLKHbK5YU\\\/cVjxgHng4qJRngYJ6YFACxkbQDgn2phY7jz+tOVcqc4xnFRtwdvb0oAk+Vfm+tKeRjOMGlUDy8cgg8ZphHTj8cUAdF8PLcz+KLV8lVgJmPfAUFv6V2PjzXHtPCVvZ5dLm\\\/me4lLdWU9M\\\/rXO+CIWtLPUb0xsQiCI7e248\\\/oKoeNtYbXNUDbiY4kWNPTgUAZOoKtutukeOYwzH1Y\\\/5\\\/Sqe3cRnrUk1yZooldAxj4B7496iToD2HtigCUJ26ewNKgx0\\\/wD1UB\\\/vEAjk0xiAc\\\/pQBoaRez6dercW7lJEBAYds8f1ok1GWW6kldss7Fi3rnvUdrbXE9ndzxRlooUDSOP4VLAfzIqrvBBB5Y\\\/pQB6Foni60t\\\/h94i04QKtzPEoEv8AEx81D\\\/IGuNhYRGUnvH1HvVCGcpDMnB3AD9RVm5LREKVI+Toe9ICOFgs4boMVeEhx0zxmqEOTOvPUA5xzVsENkk9PU0wNHTwJJkyO4GOa\\\/SjwJp3\\\/AAj3wh0S0skEdy1nCPm3NlnAZs45xyfzr8z9Nm26hExAAyM1+qHw2mj1D4fadJhZF+xRMCwBx8lc9bZGtPqfEH7W\\\/jq41\\\/xydHWTdZ2CL+73ZBkI6\\\/lt\\\/Wqf7K3wXg+Kvjgf2ipOlWQE90BwWXso+pFcD8U5JL3x7rMjsSxuCCc556V9W\\\/sCyRJpviSMAGcmE89Svz1cvdhoStZan1jaaLpejabFZafbRWlnCoVIUXAA9hXJaxoC6oJYJo45rdsh1cZBXnrXVXJ3E549+xrA1KdzbOFBGTjOe1cSZ1WR8FftR\\\/BK18BX8et6TEsWmXR2vboDiJwO3sa8T8M6vcaHq1te2rtFNBIsqOpwQwOc\\\/pX3Z+0zoyah8JtUllkAMQV13gdRXwfbWjQyZweBXdDWOpyyVmfp3pniAeIvBtrqscgCzWyXAUdMsnmDH4rIPbcfSvzx+MGmDR\\\/iR4ghEflA3LSBAQdu\\\/wCYAf8AfX6V9jfs46pcaj8MLC1ljLRw2+xXJz2uD+fP6V8m\\\/tF3Yl+LuujG0oUQ5GOQoHas4aSaKlqkzzgHkj\\\/Jqm2PtQJxncMgcd6kL4YHPHeq6tvuF6\\\/eFdBkd38a74t4yaGMeWgtrf5QcgHyl9a47RdWuNK1O2u4ZSkkLhgVOO+cV0vxfmE\\\/jq86jbHCmT14jWuZ0bT\\\/ALfqNtbL96WQL+ZpdBn0Z+2L4ibxTpfw41OSNSZNNkQTA5ZwChCk+27\\\/AMeNfNG4Ku7nHavZfjjdb\\\/h98P4CAZIYJVVs9RhAf5CvFeoGSQOORSirIHuB5OcfTvTgeMcdacpXAyQCfWpBHGR83BxiqEQkllPb8KdEyj5iTmo3+90zTQCpGS2D+FAD5ARk9ulM4GDjPXjFT7tykYHTFRFmXjHTrxQA8fPnPapNg2ccFj3qEY6Z56YHSnr8yHJ4HPFAErECM7W\\\/A1Vbjg5571NGh689CeT1qNlDM35ZFADx93Jxz2HWo1U5\\\/vHpUikoMZ601lGDzgjgmgBjYK+m3rnmkyNvAzj1oY7cUmM8cHPrQAu3jByO1KAN2OPfPemEAkng0vQnoMc9KAAls8nPFHH0BNGcdCOnOKNu4Afn9aADjGR+vrSkjJ7ZpAuCR2zTsYOOmeaAGnGfu5opdq9yDRQB+vj2QZtxTH4809LUbCAq8\\\/3jzW0lgCMjgehqVLPKkgMAOw6V5h22Oa+xlTgryac1gGPzIDj2roVstxycD6Ch7UcfcOf7v\\\/1xQFjnBZq33F2+uTmhrDI5DfjXRLYqv3g3PqaDZhj8mfzphY53+z8fwk\\\/Wl+yE8DPHbFbz2YAGCzH0TH9TSNaDA+Q575WkFjFXT2TkqSD0701dMy5+TH15\\\/St4225QHUAds5\\\/pSC0TPAP4UwMRtOBGBkGmpprI+5ThvUDn+dbXlEuVBC49qPszg8HcfzpBYxnsmIO4\\\/N6sKQWpC7e3qpNbi22\\\/5Tx64z\\\/hSnTlHRst6YP+FAWMH7ASM7T9Tn+tOXTn25zx\\\/d6GtxbQK2G4\\\/MfoakEJUDaAV9zTAwPsH+zj680osMDorH1HFb32fc2\\\/A\\\/AUpgBGTjPp\\\/kUAYSWTFcAED2JxSrYsoOFU+5Fbf2LzGDbM47jj9Kc8QTjAOR1IxSAwTaMcEqP+Ar\\\/9ehrLzMYLPjru7VuRW4AOwfrmkFuc4ZTzQFjD\\\/s8L0Gc\\\/SnjTdmdij34xW2bHB4+X8RUhsfL6MWz68UAc8tishOE3EetPhtkjY5XcPQtjFbEln5fJAOfTNOWyJzuyw7AHpQBgpZK0jHaG6nC54pDZIGPBHsa3jpoXklgD\\\/d60hsA4wuSfpz+tAGILNuMISvrjik+wAHBUZPQitv7EVBHUjtTfsuc9vw60wMF9PBJHXPWk+w7SOM10C2hK8DP1pGsse\\\/4GgBnhO0EOtROi\\\/lXhv\\\/BQ6bz9D8HW\\\/JP2mdxg8DEYFfQegWxj1GInjPb0rwT\\\/AIKE6YYfB\\\/hXVMk+XftbEf78bHP\\\/AI5j8a1p7mU9j86NZZhcsueecin6LZmU7sZAGeOag1WNmuX4LHPOK2vDcWLdiwxgE8iu05yrqbpECADkce9YryFzjktnvV\\\/Wd0k5ABK881TgtmlKgdDgH2oAIICULuCADjk+9JNLnICnFaN0qQwKgIPviqbQeZjBwM54oArEErgDgDnmkyQOQFHqakLAfLt4HeomJPB5oACOMEc+tA25xyCKB7np2xSjHXIzQAw8DnPPFKGIwcEnvinFc9DzSdwB0HqKAEDHaM\\\/lQBkk9T1NBxjnk+g7Ui8AEflQAoyehIGOlK3IwBjHWms3Unn8KX7vI556CgBAOeeSOvFKCSMDjJznNJ9QfpQuTjGPQUADtjAHP49KQkhSMdKXAI565oUZI46DigB8Y4Y9B6ipYvuMQSOaiTIA4PIp8TDB9zzmgCSCNdzZzxRCS0jDk5pCML1685otm2Ox\\\/Qj+VADpUKRgnrzUKL8o7Zp8jmQ\\\/iaAuAT+FACMpQ4H\\\/AALHeo1U98etOLZPbPrSZZR2oAfjzDinxruOevI5FRq20YOeantY2nmjjQEuzBRigDs9v9m+BhuAV7tvMLnnIHC4FcK24rwT6nvXa+NZhZ21rYB1zCio21uMgVxjctkjp6UAM5yTjoAeKVFywHXnFTQ2slyyxxRmR3bAUAk\\\/lXr3gj9lP4k+NbZbqy8OXNvbsOJbseUD7jdzSbS3GlfY8icBMjr6c9KgKl2AHBPHvX0RffsO\\\/E62tyfsNq5\\\/urcAmvPvE37Pfj3wcjyX+gXBjXkvBiQfpS5k+oWZw9jqcmn6ZqFoqZF5Gsbc9MMrf+y1lgnB7fhVq4hlgZo5o2jdTgq4wR+dViuOf596oQighhjuRWjqLg3TqvZVHH0qvaoC6jA65z3qzqJIvJMjB4I46cCgCGElZjg5OMc8VZU4PrjqKhXHmHJ69ambIQnHbGKAHQt5UofkAc4HWv0M+AHiaLxd8GrFDO\\\/nWkQhmwSpwmQRx1+Qsfwr87w2ZMkfSvdP2XPi+Ph94pOn38mdN1IojFj8sb9mwT781lNcyLg7MwPj\\\/wCFbjw38Q7szR7Euvn3ZyN44b9RkexFbP7PHxim+Efi1Lx0aXT58Q3USgcoT1HuDyK+lPjp8IoPiT4d36c2buMB7WXAYNj+HIGSQOMd1CkZxXw5qejXvhrUJbK\\\/he2uImxiQEdPT1HvTi1NWYO8XofqjoXjLTPGOmRX+jXsV7Yy\\\/ddeo9QR1BFaVrpsd8kjyghB27Cvy38OeNtW8PP5mn6ncWTH+K3mKfng109z8aPFup2j2t34g1CWFxtaM3LhT+AOKxdHszT2h7n+1n8QdLvoI\\\/DOh3ou0WTdeSRn92COiZ7nnnHTpXynNZSTzxW8ILz3DhVVe\\\/Iq7e6qJN29i8mOFU8mvZ\\\/2evg3d6rqsfiPWonRUx9lgI+YnqCB2b0z9TwBnbSETP4mfQvw40RPAfw8gjk+SK2tvMlZjjnbj9f3p\\\/L1r89fiB4hPijxtrmqFt32q7kkBxtyCxxx24xX1\\\/8AtY\\\/FeHwb4TfwrYTh9S1CMrKIiAIk4B9wMDaPYe9fDmSQDnk85zU019pjm+hKhO0jk56ZpsRAmQkHO4fhTsnAHPTkVJp9lNeXsMcUbO7SABVGT1rYzPQvix4Vkmmn8Q2m+4sxcC1upQOIn2KyA9+RkZ\\\/2a5Lwmjwaj9sVf+PWNpRnpnHH86978M\\\/DLx7N4h1VbPwve6jpF0dk1ndQMtvdIR0ycDKnBB6iqF3+zN8RNOub5LTwVf29jO24Rr++KDOQNw6\\\/lUKS7jszzb4sasb7T\\\/C9pni2tXOOvVsf+y155jnnn8K9c+KXwq8Yxa7ufwxq0dpbW8cKyPZSBSQvzHOMdSa85k8ManDIUksriMjgq0bVSaAyGAOfUdeKfk7R\\\/LNaZ8M6nKpKWVwcekTcfpVSW0lhZlkjZG\\\/2lwRTEUFPzcdfWn8lRzux7YpWRkIG04pD83H64xQA9Tngj8ajkyXYj0p6YYc8MOlNbgkAZb0x1oAQHDZ6fpz0qaIgkqx4P\\\/16hK7fQmnQjDgnkZ70ASqdm\\\/bkKOntTIcMTnr1xUszKVJHB9KigfZkD6c0AMUHBOeKDyDkZI70oAXODupmPmH+NACZA\\\/wFABJ9B70ZAJOOvWkZjyBgY5zQAAcdsZoPytjjP6UuFYjsetJjGB6UAIvJyOKXOR79KUHaOOaOgxzj19KADg5\\\/WlXkZ9OKQg7\\\/AMqABngcd6ADax\\\/i\\\/QUU0xljkAkdqKAP2r8puoXKdyc0CEN9w8e2ak4A+6R+VCmJgQcg9twrzDtEEe3g9\\\/Rf\\\/r1G0aRdC3PvViOJdpyyn6ik+6Rzu9+aAK6x5\\\/8Ar5qXYyj5V2\\\/U1OwDfeyPqMfzoxs5P8qAIzE6gFSmT\\\/eP\\\/wBaoPKyT1z3weKu7QevzfX\\\/APXTWyeMqPwoAqvEGUZYcegpoXYfkYZ9xirqgjrIf+BEYpphOSc8f7PNAFIws3JwfoaUrhcbMf7W6rQQZ4x\\\/wI0pRTwFQN6gc0AUvLZuOKkjjMZB4OOw61b8r5ecA+pNG0gYwGHqKAKcsRkfeFwfQjmnJAeCR+fFWzGAvCnPpmgKNmcAH0J5oAqta5O\\\/BGO46UnlAnlVYf3gtXUAKbSgOe+OKaYwGxwoPYHigCqYUz8r49iKcIsg\\\/KG984qw8aIeevoOaEhEwJwVA6hqAKvkIOq4+vNKqLHnBJJ7VO0K\\\/wACKo796WKKNchTj2OaAIj+7GGBXPoM0BC+cBR\\\/unNWRG46BU\\\/HNL5Sxjs+fagCmItx+Y\\\/9881KEJ6Fv+BDNSiLHTA+uaaUGfmJFAEflNn5SM+wxTmjLDHf2NThMD72fr2o2A9Tn2oAqiHJxs\\\/Ekc0nkKMcDJ6VbwD8uwKB\\\/FnrQRg5A5z\\\/ADoApPDxx3zkHpTktxk4xmrDId3T8KXAIJIHsaADTkEF1G\\\/Awe1cZ+1p4Dj8e\\\/BLV4iha408DUbcg8howc\\\/mpYfjXbxEKynqQfpVrxr\\\/AKZ4B1qJcbpLGZeeQMxmtIOxEtT8WvLWfVzGMMGbr7VpXcjwOttbryeGHtTNMVbY3U7AbkYqDnvT9LnEUUt3KSxbODmu85Qlt0gT96AXIrBkuY4nZUGOcetWtT1b7QxIPPTHtWI7fN1Bz60ATvOH78+ppskpIODx+VQA8Y\\\/HNLwf4j0oAQ9cnp+Ypw57ZWkVgByuacsmOSuT6gUAD53L1GTxTRgNnqe\\\/FNLg5GM+9Lx7k0AP4ALHOPamFyQSM9OtLuI6Dj1poxnigBdvCnrnnmg4GcHHqB60hznOMg+1DEdP0H6UALlieDmkZvXn3oC9znn16UDg+g60AAILYJ6dMUvIOM8dPwpvfg9evpQeDjt60ADEg9MelOVx0HWjrjHSkXGSMcnvQBJzt5FLuOMk8j1700sWAwPwoTJPPftmgB8hGM\\\/lSwnlgWz2wKjYkEc9OaIzhhnt1OaAHovz54NPkYqDxknrgUzooIJ4HNNZiy5yc460AIvzMRjJpCMcjsKmWF2ACIWPsCambTLt1O21lIPcIf8AP\\\/6qAKeMHGcexrofBUSnXInbkQgyY9cc1Ss\\\/Dl7PIB9naIY\\\/5aDb+Ndro3h1fD9leX8sgkfy\\\/LAHQZoA5XxPefbNWlccc9qyoYmmfaDkngClupjPPI\\\/Tec8dq9z\\\/AGP\\\/AIRj4nfFC0+0xB9N09lubnK5DAHhT9aTdldjSvofQ\\\/7G\\\/wCyrBbWdl418T2aSyPHvsbSUZAB6SEfyr7S8sImFAVQOg7U2zgis4IoIEWKKNQqIvAUDgCpM88dDxj0rjbvqdCVitNAJEwwGO\\\/Ga4PxxoEV5YygqAcHt1r0dl+Xgg+1YmuaZ9tt3XHOOKhrqNM\\\/Pn46fC6xv5ppzbKkwPyzRDDc+uOtfK+saNJol48ErZIOVYdCK+5Pj5puo+GdWzdRSfY7g\\\/JIq\\\/KfYntXyV41torqORQMvGcqc84rtg7o55bnF2Dr5xJPHqata26yXEZAwAuKylyj8np34qzcztLtbJOO3rVkiwkmZs9hU5bC5IwMc1Wh4kPP4VLg+uQaAHlSw68ds0+NZFcleSvIPcfhVzS9NfUZgiIzbjgYHX6V9m\\\/s5\\\/sWf8JFa2niDxYGt9OcLJFZbSHlHXJz0FS2luNJs439m\\\/4\\\/X8SJ4a8RQTXViFIhvArFkwcgMV5wDzuzkV7N40+E3hj4sRh5FS4mcF47qAjzQxGclSQG7fdIPqpNdx8cdM+HngXwUdPis7bTH\\\/5YwWCKJXb6Dlia+ePB\\\/wAP\\\/iRrkr3Hhuxl0PTXYGN9YkZDICeP3Y\\\/xrn0fvLQ1V1o9Tjdc\\\/ZN1KK8dNM1W3ZhnMNxII3X\\\/AL72H\\\/x2su0\\\/ZV8UNOF1C9s7SAH7\\\/wBojbP0G8V9Z6Z4e8caRbBNU1jTriUDkKHX9DxUd7p\\\/jUh202XSXmYDCyFxt\\\/FR\\\/On7R9w5UeWfD39mTRvDZi1K6L30kbBo5ZvkiBHfkAn6Krf71aHxa+PmifCrS3stHmS91oKYkRV+VB3+gz3ySccnisjWdf8AGGl6\\\/HH44gvbKyZv+PrT1MtqAD\\\/GcBgPevT9T+BXgL4yeCoUVYUvDHm31ayKmVTjjOOGX2P6VLet5DXZH5y+JfEd\\\/wCLNbn1PVJ2ubudixdh0yc4HoKzghcbR2\\\/zzXr\\\/AI\\\/\\\/AGY\\\/GPgvxtF4eWwfVJLk7rS5so2aKdPUHHBHGQele9fCf9hiG1FtfeMbo3EzYc6dZ\\\/dB9HbjP0H51u5xSuZKLZ85fCf4I+JPizqHk6VabbWNlE15IcRxA+\\\/c8dBX3p8Gf2WvCvwyjgu2tBqmrquXvblQcN1Oxei\\\/zr0Twx4RtvCljHa2NjFYWUWAkMKBQPw\\\/rXVwlRGPT19a5p1HLY3jBLccjmEKFX5QMcVoWV4TIqt096ypJ0XCscfypqXyRN8rZz71kaWOuRUdScZ7c1UudF0+5bMtlbyMP4niBP5kU6xvFcA542AkVaDBgcHJHb0rW6ZmZ6aPZRcJaxKv+ygFeafFT9mTwJ8W7eZ9S0uOy1VlKpqVmoSVTjALY4YfWvUZ2K5PrVNb1kIBywz371Kdh2ufkD8aPg\\\/rHwZ8XXeh6xHypLQTp92aLJCyD646dQc156sKBSMHOSCK\\\/Wn9p34MQ\\\/Gf4eTtZwQt4h09Gls5HXJcYy0eevPb3r8qNc0e70LVbiyvYGiuoTseN1KlCOoIOOa64S5kc8lZmRKAmcHAHtUagk4ADetLJGxY54HXmmcMccjmtCSUjd69elHcDApFYZxUk6bSpHGe4oAZIxZffpTI8Bj2PQ4p7sVAUnA9qjDbCOM9896AFYAuTxz6U0HJHYHqacxGDzt+lNxt9v60AH8Pt1z60ZCjPHJzmkAz\\\/EM9vSlxkYHNACDJA7++aM4HofTFAAHbvSgY6jA6k0ANxgfX2p643cDPvSE7cEd+lOA5POMUAKELJk8Zpqr6jHHSldjgDpz0FKqluilqADA78ewFFO2SKP4vwFFAH7XINq7VHH5\\\/qKD8pA8wL7ZppuAvVvm9sChLncp5yPVmrzDtJVTcDj5\\\/cdqYFZeFHB9aaZXX7nzL3xQsu4cL9cmgCVk2Ab1LZ\\\/vc0wuvTIH\\\/AAGkE4boAmKR3I6ZP+9QA9DgnOR6YGc099qgE4we5qJHZMlsYx705JFcn5h\\\/wHrQA7IUZyT9OaCN3RQ30GDUYkidiGUcf7WKmQqPuqPxNAAN2MYb6Hn+tMVgJdo4ceuKewJHHyn1BpCUVQTs3dznBoAUtjpnf6gcU5G3L8zc96i8w\\\/eHI7Y5pVIJ3HIb0FAEjAA8fMPbg0g2hs9\\\/TPNNZ167mLf3etKpJIJ3H\\\/ZJH8qABmYvnHye5p2FPzKAT24oLheCMfzpNwPIHHqaAAYkIL4D9gBTzI8fykkE9sVGdx5Vm2jqB0\\\/lR9\\\/nI49DQAKhXOVx+NAwDwfyo80\\\/xAA07AH3QqjvQAoBPcj86bKSCOcfWnMFJBB59uKduY\\\/fz7bcUAMH7vkc59R\\\/9ekRNhJjKsT1p4K+o\\\/nSE47E\\\/pQAhC+mT3FKVYgZYAelPDK3DYSlUhDnJx+FADFRlOT0HIpQhySfwoVsyEc89Of6U9z8oPTNADDHycDAPJGKZs+bkA\\\/hUgOAOp7UoUZ4IoAaEwenJPQ960btFm8PXaMOGgdf\\\/HSKpIm0Y6GrmsyR6Z4Xv7mYiOOK2eR2Y4AAUnk1cSJH5D+I9CXSV1OGX5Cbl8jPoxGK4jUbnyYUhiPycnjvXa\\\/EHWItQhuJyf3s8rygg9ixI\\\/nXmTys3XPXv1rvRyiSuWzk8+tRM2D7HpSswOQST7imkcYIz\\\/OmAAANxgADp70uQ5Ge9Aw56EfWrEFvlgWBGfXtQBDsLKeOvGKCmMZ464xU0jYYqO2ah3HnHPpzQAHI+h74pvOcfhT44zM2Ogz17f55qzPbpbBeQSRgjNAFQqeCelOJCqMgZ6cCgsxzwc9TxQE\\\/dD1HrQAn3uAOD1oMRXGBnjin5AJH3cVJChdwD8vNAEBUsAQuR9elNVDgEAgVdNvxwc9+OamgEMZ2su5gO\\\/0oAz1gfaeDinLbMO+MnvirV1ImdqlTx1BqJYpHXIBJ9cZoArlcE880hUg9unr2qV7Z14ZeT270ht5FXIXdxQAzp9AemKaOckDp\\\/KnEEE8EDpkUsUDzkBELHOMCgABwOnNCKXYKBye2eauwaDe3EoAhdQfUcV3Hh7wUIwJrjCIhy0j8L9KAOV0vw1c37hdpOTxjmuos\\\/CekWMRe9nMsvURRjP4Zq3eaqmxYNNUwQA4aZh8z1mDyBKI2O9jnJFAGvY39ppwZ47eKKPBAz8xxTJvGeFMdtC8hPAITr9KqBraBCoCk9PmOSKgj1SGBj80aKpyMYoASe\\\/1a7bKW4jzyWc4FW9dvJtP8JfZ55d087lmCiqcviSCWRUQNKx6bab8SJPLmtIAwJ8tSVBHBwM\\\/59qAOLDE4Az16HjFfop\\\/wT70C30b4c6jq7Jtub65KbsfwJ\\\/8ArNfnQBk5A9ua\\\/Rn9jTUJbb4MWMhyVF1IpPT0rGr8JpDc+tIrpZOhIz0qyMkHn61xFlr6iQq7j6Z6109tfrNGPmHIHtmuVO2hvY1MBgOSQD1qjeXEUbbCwDsOKp6nq5soAc\\\/M3Ari9f8AGkNtMUbB29CCMihu+wWsbPiDRrPXbKW2vbWC7hY\\\/NFMgYH86+Kvj\\\/wDsmT281xrHg5HlUF3m012BwOv7s\\\/0P4V9Oz+PknmWONixYZYjoBVpNUN\\\/KTwQ\\\/XmhOUHcTSe5+Reo2ktneSQTRvFLGxVo3XDAjsQaVeY2weQc\\\/Sv0D+Pv7LmmfFFH1XSiuma+qn59vyTYBwHA7+9fBOs6Je+GtUu9M1C2ktby2kMckUgIKkGu2ElJHPKLiVI+47H1q5axrM6LjAz8xz27VShbBfJJHcGuk8I6HNr+sWWn2ymSe7mWJEHU5OBx9TVkn1V+xh8AIvFt+PE+uW27RbNisEbdJpRjjHoAcmvrn4nfFH\\\/hE7S20rR7V73Vro+TDbQJ93HU56AAfy4rM0WPSvhH4BttHtJUhh0y3JkaT5CzYy7kH3zzXMfDa1S5S\\\/wDHWpPNO+o7XtLacbTGn8ChfU56981xOV3c6ErKw7Rvhjpmg6k3iTxLPJrmvSqWQ3O0CNe+0fdRR6mub+JP7R+heBQ0F9qIWVgf9DswScdhgYJ+rMo9BivP\\\/wBo\\\/wDaBPhO1nsrKZbnWLkEeYp4Ucjf1+6D90dyM18N6zrd5rF\\\/Nd3k8lxcSsWeWQ5ZietaQp82shOXLoj6g1P9tmW3dk0fw9bR24PDXDLvP1AX+pq34d\\\/bZik1C3Ou+HYpIQwYvauhYH1wy\\\/1FfIhJbqTnPU0u49ug9619nHsZczP0r8J\\\/GjSfikEi0y6tdctijm5srlPJvIuOAiHIYEk5OSAMVPa+FLnwNrltqfhKdI9PvJ1F3YSH5MMTlwP4WHPTg455FfnF4c8Tah4a1OC+0+7ltbuFg8c0TFWUj0P6V9o\\\/B342XPxN0kxrEp8R2Ua\\\/ardCAL2IkDzUHGHDYzyANxNZSg47bGilfc+x9M0xbrbNNmbjueFPsPyroUs4oUUqg2j9K8f8J\\\/FKDyxDNIrTRHZKquCG9xivRLHxRa6haLLbTJMnQ7WBZfqK57W3NdzbuUjnhKNjIGAR2rjLi8aCSSM\\\/IFPH0rVl1+FshJFMnXaDzWHqGo2kS7pZVMh68iluMilvgE67U75NYc2u+VceWDvIPas3W\\\/F1vasVEgz7Yrlr3x3Z2iyTzfdAxzgfzquVibsew6R4jYJvkYKPU8cVv6f4njmOPMDZ6V84+L\\\/iTb6H4Ri1CKcwPM5RU3DLYGSf5V5fp\\\/7RkttMB5ryMxwcN\\\/OqVOXQhzSPu2bVoyhKNuPfmsC61QLLkMeMHHt618\\\/aH8cTfomZTlx8wByRXXaZ4yOr3yMjDbjnP8qlxa3LUk9j2zRdVVx83APGa+b\\\/ANsH9nuDV9Hu\\\/GXh\\\/TUm1KIGa9iRctIoABcD1GBkV7bo8+4REEkBsnA6\\\/wD1q7aBkvLYowDKw5UjjHvTjKzCSTR+QmmyaPqUkUF3YW6lmGHbI\\\/Cq2qeH\\\/C811KkkF5p+GOJI1LKCO\\\/evsT9qz9mrw1Y+Hb\\\/xBoVuul38ObhreL7rnqdo7fhXxj4X8SypcvHcTLvzgLcnCfQmu2MuZXOZqzsUJPht9q+fStShvAeiOdjfkaoXHgbWIEZZrRo9vViQcV6TKIWCvKlowc7gkTFSPTBq\\\/BNhjIjybCn3GO9c0yTw2TR7vzjH5RxnGc1et\\\/C15OoICk9MbhnFezS+HdK1tcvtguQNxaP5c\\\/hXNeIvh\\\/d6bG09nceaijovNAzzTUNCvbFj5sDbem4cis9oJUGChX6iunv9R1KxZobmKRCp6svBFT6RfMxSW6RZIN\\\/zKy9vWmI48q4wSOKltdOnnU+XGXH+eK9abT9F1C1eO2SNpCNwxjmsMwy6QDvg2xDgsRQBxcWi3LAkxEY61N\\\/Ys0zgJGwPof8APtXomi32nagskbpskbOWyOawNVM2m3rqUIj6rIBwfegDIPhi6ihLeSWCjk8UyxgUTFHgwT7ZrsPD3iCCa3ltZG2uw6sRVK9gezV5fLEqZGCgoAzU0i1nb5o9rE\\\/rVR7BbO4ZF6Z5BrZ03ULfK5XkHGO9Z3iO3ltr77TEC0LgE47UAPaxU4IUdOflorNi12WJAuzHsc0UAfsKsyD+ID2IpJJwp+7n3GSKzRcZIPzA+g6U43W4gnj9K8w7S79qB\\\/iA9hxUnn+nH+6f8Kzjdj0z+NMa78zk7xj1wKANYT56\\\/J9VzmmyznA2AE96zDd5+5sHrtANAnUZ2HB7\\\/wCcUAabTMyjAyfrihZMdMj1yTVDzmABUZPsaX7QABzz3GaAL5usfekB9iKd9qjx9\\\/b78is5psAbUOfpmnjlQc\\\/gRQBoG6CqCrkmn7w0YYlgT3LEVnIcN8x49lqUXAYbdpC+uKALokATAy3uOaRZ2U8sAvfJqj5iBsBefU08Nuwc\\\/gOtAF37QM7sjHqOtKXwPMVz\\\/KqYkI+X5j7HrSBsN0T\\\/AHSeaALguAwydrP68GnLLuTaRye3SqbP\\\/FgKB2HIpBIGO4ED2FAF9SI+GH5GnMwz8gBHfIFUkumII2Z9zz\\\/KpI2dkY8YHrkUAWhKP936Gm5Y+h9dtVVnDdvzOaepVDyoXPvQBZTaAcMB9RQsqN0O76D\\\/AOvUDNj7rLj601Zy3I3J+X9KALSzkE9RRGWUk56\\\/3sVX+1GTv0oDM\\\/3WGR14\\\/wDrUAWjIF5JT6f5FOzuwV5J9M1VV2Jxu59uaUOMnK49wKALPmsRtYYA6E\\\/\\\/AKqUMGPJIHt3qsZECnao3etSq\\\/HrjtQBYz26EelIoySMZH9ajEmSB360\\\/wAwAt0P1oAs2oDzICc5P5157+1742TwR8A\\\/EE2\\\/ZNfoNOhx\\\/elyP5Zr0SwXfcIOgznNfOf\\\/AAUTnVPhLokDN80urIwX12xSVtTMpn5ueIL95p+G4AwB6Vj7sZHGccjFWb9w0jswwfzNViQvT9a7TnGgDcO+KeqmTG3qamtLMzK0hGI1HJA600uq528dRjFAFiOCNIQ5+Zjxj0pHuSR82AfXiq4uG2lex557U0ZbIX9OgoAQKZn+U5J9K07fS1wTKe\\\/SiygFsGkkGDjjP86iuLwzMQCQueRQBO2yFGCEY74HNZ0hMr7epz19anMchhGASTVm1tlgAeU5J6A0AKtksMGWHzHByRiqLRNPIUjXJyeP5VpX0uUIB6jpSaRZOxL7sKfagCsunMGG8dPSriac+zKrwPUd6uumWCggfpTmjIQKrHgde1AGJO3kZU561S3l2L9j+tXb92MhRh6c1paRpcUkaySKW9j2oAyobFpG3EYXrVz7U0PyRr0yOlaupWvlx4jTBPoO+KlsdMVFDNg4BOD2oA52Q3U0ofysiuj07T\\\/tESh4xkDkYxV2aOIQ7QVQAjsOaii1HznMUJyqgjp3oAiudCsokLMOTxj0rX8PeGPtEyx28Y3d2YcCqtno8+p6jFDFmaaQgAKM4zX0F4T+HR0jThEWRpyoeWQ8Accik3YaOEs\\\/BqW8D3V84isrcbpJQMYFed+KvFf\\\/AAkd4LWwQQWEJ2qFyMj1PvWx8aPH\\\/wBtuJNHsJCtpC+19jf6xh3P0rzGG+S1hZCCXY5JX+VJdwNbUNX8s+UiBUjxgDqTiqlvDqFwWaGNgWH3sVni7leTeqjI5x1ratdWmWE+bNtxwAB2qhCweHbqeUNc3QiGOfm6Vfh8PaSjDzZ5JmB6JyKzH1W2AJLNIx67jgVWbWdvEKDOeCoxQB2eiwWUeqxpBbJGpbq2Oe9cv451A3usP8gG0EfKPcmtHwjHez38l5IreXFEzAMD1IwD+tcrqUxl1CZzk\\\/McUAVkwznP6Gv06\\\/ZD0VB8C9PgkTBuC7k98k9a\\\/MeMb5VXPPYV+sX7L+nNY\\\/CLRISu1hECQfU81hW+E1prUZ4ue88MSJPIjGBcLvHX2NdL4S8Ww6np6zI+8AgZB6V0Ov6DBrVpJbzqCCOvQCvnjWVu\\\/hTrJldHOnSn53jB2dep9DXMkpGzdme761rUFw4G\\\/JUFtoNeQeLZxNK7K2XboKS88Yx3FvHdwymWGaMFWB6Dg4Nefap4gN1PguwQdVB+97ZrSMWiZNNWOy0gJC4Bbcz8swFdxo8zeWMqVyOM8cV5PpWvELgjCL3brXZabrKysqLIOeFFEkJM7p9ZhtVYuV3KNxA54rxb47fBXRfjRpL32myRReIbZGME0ZA83vsf8uD2r23wloMd7ALuZRJCc7VZc+Z\\\/tfT0rXuvB1nIoeJfInH3ZE4P\\\/wCqs0+V3RbVz8htZ0S98O6td6ZqMDW15buUlicYKmvZP2P9FGu\\\/HLw6jr+6tWa6YE9QiFh+oFet\\\/thfBQXWny+J7SELqlqo+0FEOLiIZGf94cfhXkH7IeryaR8Wo54n2v8AZJsfiAP8a7ObmjdHPblkfa\\\/x61W6ubCx0y1EZe7uoYZPNQONjN8xI7jAPFXfHd\\\/D4Y8Kwxq3lRWFtuAAwAxwi\\\/kCx\\\/CvMfiFr66r4w8EveTuhOqqEKqCpYRkAHPTgnnmug\\\/aY1GK28Ba4PJTzmtXMc3n4YMofGE7jBbn3rlStZM2vuz8+viN4tn8ZeKL7VJmwssmIhg4WNeAB+FZmgeHZtduURNqIW2734H1P6VQnXknhua7nwBetp1m52bhICFbH3fp9f6V29DmOv1b4J6bo3h03JvjeXZIC7JAoJ74Xk49zXmep+E7hYZZYI3eGIEsxHQDgn+Vepy6jc31ms7FsF8AuvBqlq73TwfZbdl2SjMjbSO3Q8UkB4sylH5+X3rs\\\/hd4vn8C+NNJ1aIlVimAlTs8ZOHU+oKk1i63oU+lXUSyhMPkqUOc44qKOLymjcjPI6c03sNH398UfDt9ofhW51\\\/SgzPGHlTzHXEqAblxtGFAXdx9PSvEtI\\\/aEu7MxvulgkKgsNzAgYB\\\/qK+jY5GvfBlt5iOUGjRo+4HaGMRPXOOjemeDXBfCj4aWN54HivxLYXUzHi3VBJIowBk55HTI9iK54tWszVraxzI\\\/avURhVu0U456A1iaj+0mtwxL3ikg5zvPT1\\\/nXyvr1omm61f2qMWSCd4gWHUKxGT+VVUB9iORgitlCJnzM+mX\\\/aJ0kzHzrppBnLMsbGuN8WfH8apdotpDJJbx5KhztBPqRXiWTkE8\\\/UU8Eg+vOMCnZCudt4g+KOreJnjNzJ5UUS7Y4kJ2qO\\\/41Fpt\\\/kqXlcswzjFckgJAyckVt6Spk2AHnpmqEeq+FNWukliijctk449K+g\\\/AN5ejyZXH7pHwxzy3r9a8T+EukyTyqz5S3DANnktx\\\/Kvq7w5p9vcaVbQwQhdvPIOGHsR\\\/WsJuyNYq56zoBX7LDJuK5QfKRXbaVLlF4AXtXnugWLSeSgYNCnCc5GOwNeh6dCYwDyOMHk1ydTfocP8AtB+EX8WfDTWBb5F7BbvJHjjIA5Br8k54X0bxIY7kBRHJ8wPIzX7YXUaXFrPC6ho5UKFcdQetfmB+1r8ILjwR4zuZI4G+ySkPC6oQrD6+vrXVTl0MZrqeUpok+pStPY6krhTkLkjb6DmpDYeKbGVQka3BXJDDmsnw5rAtt0bRyBAvzEHAB+tdPZ+JbaNg8F1MNvIUMGx6jBroMTPg8U32nvtvNPkV9mzjuc10mlfEmxQIj7kKsdyyjjmol1r7Y48p4ZnY5\\\/e4BB9Kkkk0u\\\/f\\\/AE7SkiJG1ti9fU5FA0dLdS6b4qtsbYWl2H0IxXPaXowSxljNgDHExAyMllrIbwpaG6L6NqjWZAyI2c4Pt1pun65rXh7V0S8BdJOPNXlX\\\/wDr0CJraKzudSU6cHsryMZNvKMK\\\/ar9pr0N+ZtO1GBYzwMsM4PrXC+MLzUI\\\/E096iyRZIZccY6Ulp4h+1yB7lfnHJcdTQBd8X6VPokqyWiEJvIBXoRWba67fbDHcW5uIewK5xXe+HvFdnJ5dpfhZrd2xl+oPFbGu6DZWUyS6eI2STlVPIOfelcDyHVJ4Comg3wzZ+6elMstW1EsqJLuQno1ehXWh6Zq3nIiLFd44Vu5rjItFk+0yrGuGU7Sc8A0wGas\\\/wBnijlaMxT8E7elRweK5TFsZA+ex6VoXCS2YjjnjE0Trz3AOasQeGrK5t5Wt5FM23cEoAxZNQWVy32BD+FFSLDJHlXjBZeDmigD9Vxc7Tgkfkad9rKcAAjuQM\\\/rVJ3Zm4AwRjGaFQAZIwR0wcV51jtZaN2D0fH0OKXz2fGCGA9TVVCHGWByPpSeYT3xUk3LvnA+h\\\/3ef6Cn+aQeCx+tZpO3+Ld+A4qTzm7HH+9mqsUXo7gysVVeR68U9nPY8jrkVnq4kYjOPUgULJgkB1GPVgKQGgboJ2Vv0pPNyc7gfbPSqaTbSejZ\\\/uqD\\\/MUhmdieQoz2HNIDUS5TACjDeoqRrmTZjgr6FazA5AG45H+yRmk8zYc\\\/Nj6UAaf2k7RjANCynO7Lf0qhHMHIHI9zj+VK0gBwpUn6\\\/wBKANIXHPU7vYUvnbmxjJ\\\/Ws5ZiOCuD\\\/eVaeHYfvNxZfTofyoAvedsOCSn+yaeLjIyrDPpWeJmPzBcD0qRZSULccds0AXfPkYgkFfqDTzO4PKmT3YniqMdxGwzuKHPC461IJdw7D2zQBdNwB\\\/AP+Aj\\\/AApv2nb9xuvrzVQdD1\\\/lSRlkzk7vTOBigC3HOwBy6k+1Sm6DY3YH05\\\/lVJ7hc\\\/Nx9KI5Q2fm\\\/IUAXmudmD0z6nFKJR3P5KKo+dt\\\/iB+oqTzD2cR+7Ec\\\/nQBcEm7ofyp3nMeOmPbFUo5WydzhhTjKFOcBfcc0AXFk55cfTFTLICPU9qzkmG8Y4I7kVMkysMZzgd6ALyOSAc8A8CpY5cE84A4qgk3J7L14qaOQjGCMdaANrSZVNwCTjPSvkX\\\/go7q+3\\\/hD7LfmLFzKVzxnCqD+pr6x0yYG6UZ+h\\\/Cviz\\\/gpRayLqng28jYkGOeMr2\\\/hOa2pbmc9mfDM7HzD9c4qMdTzn+lSSxsGPPP86lsrZppgoUsCcmu05jQuS1rpMUWMeZ82axgpI5PNbGtzGaQIPuooGM8Vn2cBkfr8vf3oAhK9cHOD+dXbNDAxkZeo4461a2QIjdyOhqhPdOcqOmegNAEl5ftMccAD0HFV4UaeUDGQTURbn1Fa2n3aW0GCuW7HFAGhtS2hUHr9KohhNdBc5XP5VWuL1ruQBeAT0NXLLT2gJkdhn60AS3FsqNubJ9Aau6UFkjyeEFZN00k8\\\/lggkgDGc1pMo0+yCFhuI65oAZe3IWbCnIwefzqG2mknTCfO2cBfSsy4uPPZgD1P6Vf02X7Mh3d+mKAGzWReXcfulupNbdkwKARMuFGDxXPXWpFyRkNk9RUukzMBJMzkLjjOaANy8YKpdjj0z3pdKulkVmZQAOOO9YF1fPdXCqCWBOOeTV55l02x8tnBlb5j24oAfqOro6vGikc4FO0lHjiaYoNznj1\\\/Ksyw8u4nMshConOCa9g+BPw4f4l+Isk7dJsyXmkYYHHRaTdlcaVz0D4GfDN0tH17UInWSUBYQRgAd2FXfjn8Srfwl4en0qzYLqFym1sDlV7817X4r1HTvAfhWaZvLht7aEhQOOAOAK\\\/Pb4geLJPFviC5vHclHc4z2HYVlH33cuWhz0kj3ly8jEuznJJqxLEka7QAT1z6mm6dbrPIxJ2KvOT0q55lrEvzRlyOQzHGa2MyrGzNIAnLnoMdasRaPdXbAuNgPcn8qqyXYBzCuw9eO1S298QuHmYA+lAGjFodpESs0uXHbPWtGC1tbYosEAkl5PPGKxBqMEJDRx7245Y5pjajc3Eu2NXJI7f\\\/WoA7O2uG\\\/sfUC4+dQFIjbAA571548mXJIyc569a7bTo7ix8IXrToFMzg5YdgPX8a4g\\\/McjgdARQBueBtHPiLxZpVgn\\\/AC3uFUkdcZ5r9aPgtCtt4Ot4UOAjMo56DNfmZ+zToP8AbvxY0tc5WDdOT\\\/uiv0n+D9yv\\\/CORKcZPPX1rmrM2pnpEiAodvX3rhvG3h6HWbCaKWIOjKVIYcCu5RgehzkfWszUrcyI3AYduMVzbGx8Y+JbC5+FUs8ZR5tCnfevfyWPpz0rJ0\\\/UbPXoftNlPkIeVYEFW9CK+j\\\/HnheDUrOeC8t1e2k6g84r4\\\/wDH3he++GmrSzafKRZSnKEchfZgeorqhLmRjJW1PS7eZ9ixqu8E447e9dHoZMl8kVwzRozBMngEd\\\/0FeE+F\\\/jbZ2l9HbaxE1o+donXlCf6V7hHqFj4jtIprSeJpU+aKRW3AnHqO3anJEpn0DpOtwx2kUEOCqqD8vYe1dDFeC4VCp4XHPrXh3h\\\/xQhAiciKdTh492foR7V3Nt4ph06zeRpQDjI5rlcWbpkXxmu7OLwxdx3IUxiJi5PYEGvz3\\\/Z\\\/sr64+L1tc6XAZ7WFpmnw4ULCVYE8nsOcD0r6+13xr4c8Z6lqWleIdSjt7FbSSQxNKFLkDAUDIJ5PavmH4JWkVt4quE01mjVJnwQ3zeXyBkV001ZWMZu7Vj2P4uag3h2x07VUiScaffQzlpOdqnKsR6HmvV\\\/ixYjxf4SguId7WuoWhXIjXbtlTIYvkEAHPAzn0rgvFmlw+J\\\/DNxZzqW8yMo\\\/tx1A9e9XP2bvGsHibwxd+CNdSObUtAbyxFMc+fCCcEA9cY\\\/I4qXtcpb2Pgyewktbu4s5lZJ4nKFTwQQcEV2HgbVrFNNuNMvF8q5J3202Thj\\\/dPp\\\/8AXr1T9p\\\/4V3aa\\\/d+KtOsDapId11ZxAuVwMebkcc8ZA+teCw+TdkZIWQdVPc10Rd1cxasz1HS74alAsPl\\\/MjAhsDrmvUIfgnNe6Ymp3d7FA7ReaQSFRAAeSe3HNfPekanqmjzJ9ku9hU5AdFfH5iuq1bxv4i8T2iW2p6rLNbKB+5XaienIUDP40NPoGnU5nxBZreavIiTC6hhPlpNg4Ye2ah8P+HpfEfi7SNEtIy81zMkZx2BPJ+gGfyp9\\\/qMVm+xB5twwwqrzj619Ffs6fCafwtbyeJ9cQJqN7EqwwH78UT87T6O+Mf7K5NKUrIErs9m8Ya9daJ4F1QRqrPb2WLaOOPkF9wjXjrhCh\\\/PtXD3caeCvhRql5IqLLa6c6iXHO4JsHP8AvYqz4gvrrxZ4q0\\\/R7M7ra3l+03c6MCrzY+RPXC\\\/exwAFxz24f9sDxfbeH\\\/AemeE7SXF7dus04B5EKDv\\\/ALzY\\\/wC+TWKXQ1Z8dSFpXZnyxPzEtySTTov9YOOcGo\\\/XPXrUsI2tu6ArjmukxK5UtnHFOyAo4yTXefCX4TXnxO1aSOOUWun2xU3M57ZzgAdycVzPirSI\\\/D\\\/iXUtPikMsVtO8aOepAJAov0AoW6FpFBz1rp9J09pZo0TljjHPvXN2OTKMkHH58V6\\\/8M9Bjv8AFxJjI4XI+770gPWPA2mxafo0UIXfOfmz057\\\/AIV9A\\\/Dy\\\/NtpsbSM0UKDp69K8R0m7t7WZIUBcKeVQZaQ17P4L0y91eRJrqPyLZTlLY8EfWsJ6o2ie2+HilxZxSBQdwBJ6c\\\/T8a6y1AEYbbjAA4rktDkEUEabvlAHy11liymMgHgjiuZGzJpc9umPyrgvjR8MdO+J\\\/gq8029T95tLRTL95G9jXoJPPXJxVa5P7iTIG0g8Gq2Efi9daWuh+ItW0W+aUGG4aBwgHQMRmtKX4eWN0c22pshPOJFIHbjNbfx0mW0+NHisxKJI\\\/tr7tvHHf9a5qz1iyuNuI3jbcDlZD0967lqjkK934Q13Sw8sYW6gXpIjAg\\\/h16VmRa1e2DL5sTxuhB4yMV00WuzQyEwXRZXJBilXAGPerkerJeB2uLVJUxtYhQwyaYGFbeLoZ43NwA8rqAMrjac9c1rN4ggm0+GBW3oHx5bHJHuDVe707RL9ZF+z\\\/ZpAQPMQ4x+Fc\\\/q\\\/h240lfOt5zPCh\\\/hPSgDo55TpzpMMXEMy7WEgzt\\\/wrnfEenpZ+VeWqlYJx930PcUy31n7XA0Eny5xz7+9dBaQNqOjy2G9WZW3oT1\\\/A0Ac5Y3rwx73XGMZJGQa7a58Qtqegxi0YGeBhhEBzj0rhLhm02d45VZSQRjHStSzv4007ER2TxfPuXjcPQ0rAdFaXbXqwXGSl3HzKjcHitqXw+l9C91bEeYw3Oi9M96yLXVrbXrFnEfl6hHHu3r\\\/ABgdiKk8N6lviVo7jypF+Voh0b160AYKaq2J7W4QMFJXjqKzdPuJNO1JgMsnrntVfxPBPBrdzIAwVn3Z7VnRXLNjDHcec0wO8Fi9yTIrcMfQUVyUWt3cCBFmIA9DRQB+qcqq8oJOG9l4p+CgOWP0A\\\/xqo0ygEbsD0AqITqrDEhX\\\/AGfWvPOwuCQsRtX8xmpThcc8\\\/WqzXJHt7ZzTFnDfxiP2VhzQItFhJ\\\/GR+FR+bj+Db7\\\/\\\/AKqYZ\\\/M6MVx7ZpFuAc5x+NAE8CFmOSF+rAVKNrEj5iR1NVpLjCjbEXPoB\\\/8AWpRK7Dpt9iaTAnLN2fGPTj+tLFhWyo3t35zVUOinLAn6E1KH7kbF7HbQO5Nw7EHKn36UKTu2kYX+9uqJXLnaQsi+meaYjhZyNuzHpSsItYAPA\\\/HinCZQAp2n64NRCVeuDu9dwz+VDO0gwshGexbmiw0yyk3IVQmPTilMoZtnRvZQap7WQbSQT+FGGHIxn3xTsMvhgi8nkdyMUCX5SxIb8KqLIAvzk7vRTR5o+6uQT60rAWknV1J4U+wA\\\/pSCTdyWPHvVYFkB3Zz7Himh9\\\/LEZHT5qANA3oPqv4mgXAlOQ5O314rPZ92MjH0anxTbScdDRYDRMxkzgBvrxUQcHqxX6ZquZwh65+oFOkuy5H3h9TmiwFpbhf4WHvT0n2k7zkdu9Z5mB7Z+lDuuPl\\\/d\\\/TmiwGh54Y4WPHuP\\\/rCnLIM8uD\\\/s9SKzvtHHysQfU05JSpyWHPvRYDSSckkZwO2aUXG3gnNZ32jOMEe5yKe0+BjJwPypAaYn5B\\\/A1KlwDwTgfyrF+04PB5\\\/SpYpzyehAoA37S6CTxljgZ4rx79ubwDbeKfg42r7gt7o0n2mJxzlSCGU\\\/Xj8q9HjuWmKoo+92zXCftcz3Vv8AAHWtjfK6BGx\\\/dNXB2aJlqj8u2O+Tscn06V1kFhHpmjJMMGRwc4xXOabZG5vYlA6mtrW5HitmjDfKp24r0DkOevZvNmY45J6ehpkFx5LAKc54xUErmUn1FIhAkGeRnPHFAFq4mHA\\\/MgYqqCW46fhzSyMHbKjj2NXIrMSxk4yxPXrQBTht2mYbRk1rxadIsIYr+BpdJtxDcEt0Heta6uwI8gcYwOelAHPpGkbnJwRyB6Ul3eSEbQ34VHesGl3KCGx64p9pps93gqCQOS2OKAFsYZZnZ1J6cse1Q3U0gdkMhcA\\\/WtKeA2tqY42w38eD+fFY8qEsdwye2KAJ7EJvLyEcdiKsS3MYTIAzis2MbWwe57VahsZrl8Ipz3PpQAyHa9wNx2qT8xx2rUury2S22QYCgY4FQrpDxhmZcsPeqjwKcKeG9KAEt7lhc5RdzDpVi6tHY75CTIwzg9q1NK0yO1Xz5156qM0s81vK7Nswc9QetAGZpumSX93FbrjMrBRgZr9DPg74Msvht8MLWwQj7bdAS3D9yT2zXy7+zX8PbTxL4q\\\/tK+\\\/48bTDgEcM2eBX1N451+Pw54Pvrpgpt4o9ynODx\\\/kVhUd3yo1hpqfPf7VfxMaa6Tw5aSgxQktMQf4uw\\\/I\\\/rXy+zBz1BOc4FbPizWZtb1e5upZGkeR2YljnqaxEXkY457VrFWVjNu7LvmhLfYhKn+dQTSliec4pNxw\\\/GcU3GByOh9aoQ4MSCP8AJqSCJJZFVmwOp\\\/Kq6jkjPHtUsb7HDY6HNAF+KxgiZurAfjWrbmSNAIo0iU5y7HpWMl+w+VF9Txz\\\/AJ61LGLu7KYBHuxoA73xQv2P4faarOJHn3SMwHuQK8w2jcSGP5V3\\\/j\\\/UHk0XSrPyvJjghjXls7jjJP515+Rgd8elAHv37HVqH8falcFgvk2EmD9cCvtb4N6qr+H7TDhsrjjpxXwH+zdr39k\\\/EGK3dgiXsbQnjocZH8hX2X8F7421pfabIdr2kxUZ646jFc9RbmsGfS1hKSg78etXnhDLk8E9u1c14auxcRqCSQRya6psEAE5\\\/H2rlOg53W9LS7hZSgIOOtfPHxc+HwubKcBN8ZU8NX1Bc26upAGF7YHSuM8ReHUvI3LruJHGeeKafKyWuZH5a+P\\\/AAxJo9xJGyssYY7f6Yrn9A8b634UlDWF9LDsOdhbKn6ivtT4v\\\/CG2v0ncQ75CflP9a+MPHHhK58M37q6Hy8nnFd0ZKSOVqx6Fp37TN7NEiappySzRrhbm2cxuP0o8QftK6rdQeTao6oQMB2I59z3rw+Rdr+oz17VLIpJTuDzVcqC5c1bXL7W9QkurqZ3mfqQxxj0pNE16+8Patb6jp9w9tdQOGWRD6evt14qogyRntx1pGTAwOPWiwj608AftI6L4ht7Cw1SIaZq8jbHkY5hlJ+6Qf4ewwTS+NtG1HTPEUHirwzN9m1mzbeNvCzL\\\/dbH6H3xXyrpE9raapaS3sLXNokqtLCj7S6gjIB7ZHGa+n5Pj54Ku9Xt7awtZtL0q5iRY4ZnMn2f5QpV2PJ6dai1noVc9n8DfEfw78cdHjtbhl03XoWJuNMdtj5HUKTyVPoP\\\/rV5l4\\\/\\\/AGUbbWrq7vbM\\\/wBh3eA7bCptpZGJ+VFJBU8Z49cAVka94Gg1iddR0+VrW8A3Q31o+HX056MPr+dbuhfGf4heFojY63Z2njCyRBEGlIhmKD1Pfj3NZ8rT90u6e541ffADx\\\/o1wY47MTrn76ybB09HANXdH\\\/Z38fa7Osd0kenwH70jNvIHriMHNfY3wb1e3+LENzLH4f1Dw1BbnYTLMVVm67UC4z716k3wz01MC5Ml1ERgrLIWGfcEmpdVrRlKCPlf4Y\\\/s66D4MuxqMhfWNVh5jllC7Ub\\\/AGVGVTHqxJHZQa3fFXj\\\/AAzaT4da21DUlISZxJ+6tI2zuYt\\\/E3H1J7dq9S+JHwJ1\\\/wATsi6T4iNjpSJtfTYIvKaQf3RKCcD8K8x1fw5pHwR0ea61e2NhbRvuGELF5MdQTkuxwfmJ\\\/KhO+oWtsJoNvovwm8J3WtahI0Vvbh5WkmfdLI7Hkn1djwB2GBXxL8S\\\/Ht18SPGmo67dqU899sUQORHGOFX8Bj8a6D4x\\\/GPUfidquwB7HRYG\\\/cWe7nP99z3b+VebhTng7T6ZreMbamTd9EIxIU+h55p+evOaaTgcdaFwN3pjp+NWSe8\\\/s6eIo\\\/DvhXxPcOyqd8ZA7n5WrxPxBfHUtcvrs43TTMxx7mr2k+I5dK8O3tjFkG4cMxHQgDisFiXOck59aVtbjLOnjdMiFcljivZ\\\/BWqS2dvHBAm5zwqjjvXjeknddoSO\\\/UfSvffhhp6RyC5mUs+PkXHOaAR7B4F0CPS1S7nHnX0oDM2P9WPQV7j4UivLxI\\\/KCjPU5rzPwT4R1nWnilFs0cDEESScAj6V9C+F\\\/CVzpMEaIVkGOQzc5rkqM6Io1tI8OukaSTXDbuDtTpXSwjyRjg44pthDLsw4AIHQd6nWNgdrdefaskW2SxOXTIOR7dqyPE+orpulXUrsFVELEn6Vsx8R4PY9cYrxn9p3xYvhf4Za7ciTZIINikEAktxV9iT8w\\\/HOrDVfHWtXtwjN9quZHGxucFzVX\\\/hFrSRJHt7mVSMcOnTPvWPqlzJ9ulZslS3BB6irdnrUcCkRrJGeuVk612rRHKTnw9qtmEkglSdTk4DdD6YNVnnv7GX99bSIQckAEc1d\\\/wCEibyok8w5XJBZevetD+2zLCrIyyBzlhnnjrwaYGOviLd5qyAFHG3DDJz\\\/AJNW11e1n0yW2YlS2MOpzRfzWV2pMtr5ZJyDjnHbpWZfaZbwwK8LFT6Z96AM6Qi3nIRty+oNdDouoJtG5skMCQpwQAa52JGjlL9QvJ962NCubOR5Fu0DszBR2IGeTmgDR+IUUJNlPbggMhLZ6k+9cnBdPFE4\\\/hIxmuw8Q20Nx5fkMDCMqoc8j2964ogxsUK9DggUAdD4a1CW2uwI8FjhVLHg5rvbOzhtgZLiBYpsk5AyprymykxdRgHbgjJNehSX93NokkVt\\\/pJkPy47Z9KAI\\\/EOnI92hRlKSjqeRXB3Nm9tdyIcKA2B+fFdRHqP2u3jgaPbLGduD1FN1vT\\\/AD4Wm8vDouOtAHPpCAo+br60VnMXLHacjNFAH6stIWPU01yc8gH3IJpSWU8sT+FJtMxDA4x\\\/eFeedYbt33SPfBzQdqjnH\\\/AqSRWyOn4Jj+dLGRzkE+lADS5Q\\\/wAJ+jVJucDnIHsaQs5++px22nP9KbtlHUYoAsK2QN8jEeivSmbAG5jjt81QCNv4M579TTsnoyn8qALJkV1GGOaEnbOM\\\/lVfp1BYemKU\\\/KMht3+zjpQBbEjn77Ap2HQ\\\/pTVkUPx09zmo4ic5xt98YqZF3tjcT7NgigCQuDFkZ\\\/A01JGyORt9D1oztbZkcds4pdoduX2Z7nkUABcF8HAH1z\\\/OnF1QZIBjHU8\\\/\\\/qpohCnht\\\/8AtDpSOiYOVy3ryaAJo50MfyY2\\\/WgzDG0Dr7kmoURcYwAfXbQFCsFyefQUATKWUHPA\\\/wBomhWUgkPjHbFQyx7WGCc4pyA4OcZ7c5pWAd5+ccDPuc0srsCpIwPoR\\\/WmEYHzSc9uKZu3D5izHtimBYS5HOMAe3\\\/6qDMh6Sn8gf5VSCuOx\\\/PFSEB8bwGx0zQO5Y+1beuP1NLKwUAq+0nr1qmJWbrk49TTjISMIMeuOaBFpiQgJJHuBilEoIA3H9aovM7DCtk+gFOBAALZyeuBmgdy39oz8oA4\\\/iz1pzT\\\/ACAbsZqDewj+\\\/kdhmmmTcAMce9KwXLAk+Y5Ofp3qx5vlpgnBIFZyuQeD05yecUplJOCc4HPaiwXNKG5KSqR69e9Zf7SWlt4m+BevQxg5S2MuB\\\/EVBNSrJtKk8EelbN95eseFLyxmO9ZIGQqfcEf1prR3E9UflnoenfZ71h\\\/GBwO9YWrXTtLPE55BJ59a6\\\/xBpdxo3i6+tGUxm3mePbjoATj9K4bWIz9ulJOBuPSvQOQoRKGfDcckcU6VO4454qUQhIwT98mq7sxx6g9qAJrK28wksDtB5rRNwIIwgHA71n2140GMdD1461NPLkIuM\\\/yoAla8JdVBzn0qxeysCIwTzg9elZ6YglVn5A9KtyuLu6LrkFuuO1ADbyycxKwBz1xV\\\/TJZ7XT2LcMeMUTXEaqA3YVSutUJIiQYUGgCvdzsXbqc81W2uwGASOnvTrtsvnBHue1WrAjaS2MKaAIrDyhKEkU9c1stfCCPy7dQo6k1ms8IBOOeearGdnlChsDPftQBpxXkspxkH196qRkpe7niyD2NLZSLFdbWIx0+lX5byG0G4gNIRye1ADL67ik5JIPZR0qjZy+beKin5WYAcVWSczTOTzkE49K7f4L+F08V\\\/ECwt5lBtUYyOD3AGf6Ck3YD6y+FvhuPwP4HtojCHuXUTSgDnJHQ\\\/nXmv7SXjpk0OHTYZCBO4eSHPIA55\\\/SvW9Z1hG09zpfyXUIwY26Nj0r4\\\/wDjD4kuNe8TzPcRiKZQIiB6jviso6u5beh5\\\/JlmyQcHnmmINvXg9qegYAcZpmPmxxu9Sa2IAqW5HTHNNJLA8ZI9BSrlm7jPWrMbKijBwfWgCGFSMkjgilDfvQOPriiSTIPYGmqeQQOT3zQBq2qbR8kYPpxV23cvOikhSSBjOSTWNDNNMNseSo61p6Jpck2qWgZwMzJ0+tAHRfFGJ7Q2FuxBZIlG3uOB1rg2Puev6V2nxRlI1xo2kDlSw4GCPY1xir1HcGgDY8GawdC8VaXfDhYZ0Yn8a+6PDt2lj8Q\\\/PgJa1vo0kGG4\\\/wA81+fpyNpzz3NfWvwX8Zt4g8P6JdPlp7GT7JISe3G39KzmtCo7n2x4UvVNwiKQowMt3NehQ7PKBIAJ6GvKvAyi4eGUDhlBJFekm7SNVzgHtiuLY6iwwVm4APOcmq13arJGRtB69qfBP5nzEgY7U25uRHlQRuPGKGCOB8VeG47uN12Ddyc9K+Ufjj8L1ntJ28vIAYg4\\\/wA96+2LpVnTlN5HpzXmHj\\\/wxFfWsyGIDKkEVcJcrFJXR+U+rWEmmX8tu45U4qCRcBCO4AzjmvVP2g\\\/Br+GfE5kVNsEnKkDvzXlO7hcHgV2p3OXYkQZQjr9aXHGCOPUcmliwq5OSPbrTyAeD0NMREqjgNyuOoprDOe2KlwMZzgH88UjjHTr7GgDo\\\/CPxL1zwXOrWd60luDk205LxHp2zx+FeoaZ+0XY3iBNX0p7diOZLZg65\\\/wB09Pzrwkgjnoev60m0jJPOD2FKyA\\\/Tf9nLxrpus+BrW40qVXhLuGGNrK245yOx6V7pZ3q3CfOd3HOTX5M\\\/Bj4u6p8KvEUVxbTudNkcC6tQflde5x6jsa\\\/SHwL42i17SrXUYG32dxbidHHIKkZ\\\/P2rkqQs7nTCV1Y9Zt5gBlTx0x0qDVtKsNcspbS\\\/s4L22kUh4biJZEYHg5B61i6Beed+9kkBXqB29q1hqkZkKx5xWJpY+Tvjr+wfpeuRXWreBCLDUcb\\\/7JdgIJT3CE\\\/cPXgkj6V8OeKvAmt+CtXn07WtPuNPvIWIaK4Qgn3HqD6iv2WN8h6Nn3\\\/z+Nc34y+HHhT4mRQweJtFg1WKAny2l3K6E+jKQRx2zW0arW5k4X2PxuaJvTB\\\/ummEnoOw9a\\\/RL4j\\\/8E9vD2vSNceEtTbRXbJNreZmj9gHzuX8c147qn\\\/BO\\\/wAeQhza6hpFxjp++ZM\\\/mtdCqRZi4tHyaW4BoHTg19GD9g\\\/4px3sdu2n2Yic\\\/wDHyLxPLXHrzn9K9E0H\\\/gmzr11Cjap4psLMkZIt7d5sH05K0OcV1Dll2PlHwRoc\\\/iDxBb2VtG0ssjYAQZNfe\\\/wT+BDadDFcX67mxkJIAcZ9q6r4M\\\/sWaL8K5nupNRbV9RY\\\/LcNB5e0dMAbj7175p3heOwRAGLAD0xisZzvpE1hG2rM7SPDUVpEoVPlAACgcV0VtYpEBsHC9qkW2NuB049RU8QUsQeW9PWsUtS2x0aBWx0J6UsiZOCMD61JtyeDz9aX765GQB61pYkrs+xGyeOuc18H\\\/ALfXj8rpVto0Tjfd3G4pnkog\\\/wASK+2\\\/EOo\\\/Y9PmbcFbBX6V+UX7V\\\/jMeLvizfxQSebaWGIEx6gfN+uacFeQpaRPLVeK7tNkqlXjbO5TjIpi6fbFsJK4bn3FVPP3KxC4PqfWo0lVWywBAPO3iuswNKbRghQwTiUH14Oahe0u4MN5ZdexXmmwXeGJDEN2ycirsF2w4Do2eMMMYoAoR3k0DMTu56g06TUPtaFXQbs53KOat3Mm9yrKCobJz\\\/Ks2RYnnxHlATj2FADXYhOhJ+uaiH7pweRz2qR1w+3sp49qJ41WP\\\/a6fT3oA2oJ\\\/tVqgZ8iIcZNY18266ckYz6VpaZ5ixuxGVx0IrJm5mYnJ5oAmskV5+u1iODXQ6HrdzZalDb\\\/AHirYwD+lcsh5yOCPQ1bsJCl0G7+vf8Az1oA6HxDayWXiAOilJJiHwfWujS3luLR4yFMrDoG61h6oRqfkTM+5wuAT\\\/hVG31021yRubegwKAK9xarHM6sgVgeQcUVYnjN9IZ8kF+Tg4ooA\\\/UhLJgOV5pG04uwJXHrzXQtYjOec+vWgwopxIVDdge9ebc7LGB\\\/ZwJ4Gfz\\\/AMKebJxjJQfWtxrISEHbtx7U2SzGRkAn3ouFjDl04xgY3c+vFK9tvAAQcepxW29scDcv60SWrADAU\\\/UUXGYZs8gbVGe+DSS2qKi85PfHat1LPP3l3j0Aoe0BAx8n40XFYwktmJ43j60xLc72yhP1X\\\/8AXXQNB5YBywz3pqxjcSRjPf1p3CxifZwOe\\\/pyP6UzyV3nh2PoAP51u\\\/Z1BJAAPsaPsq5zz9SP\\\/rUrhYwzASPlQhvUkH+VKYJfLPH4ZrZMO5tuD9QcUC0DfIVJz6n+tO4zESIqMspB9M04x7hwG3fga1\\\/svlvt2bV9etDQEE7QT7jrSAx\\\/LdWwSQP7pAFBTac7iuOxNaj25OdykN7mmrZR7fmUbv72aaYGdkPyWXPvQVI\\\/hY+68VoLbGJSqcg+9MNs4HUD6DH9aLk2KBgZvvNn680eUYhgKefXmryRNzvYZ7E0zyXXrJuz6\\\/8A1qLhYp7DHwQDn+7xSBdnbb9DirMkCqRuI9sLUjQAj5QU\\\/wB7v+tFwKLljjCq35Cotu0+n+8P8KtNDu+6+KcsYP3g2PXBpgUslDkFT\\\/u\\\/\\\/XpGdgPlHP1zVlbVdx4x9KYYtzEZPB6EUBYi8zKAlW3e\\\/FRsxwBVgxFSRuGPaq0w4woJB9KAF8wq3XdT\\\/NJyTgA1XOQRkE4PY0ZLD8KALSyB1wOBn8a2tElxcBHIKt13DisOIcEjn8c\\\/hV+2Vn+bPNJgj5w\\\/bG+E66RdJ4y0eJirDbdwouQfR+K+L7+dZ52YZOfXsa\\\/W++0u28SaXPaXqiZJE8sqx7V+Y\\\/x1+Hj\\\/AA6+IWqaasJisjIZLb\\\/cPOPwziuqlK+jMakbO5575h3jBzTGyCDn6k0nrgHPrTmPGcYPqK3MhVTfk9hyKmB81kI7YqSxj3Jg\\\/wAVSRQBLvPbsCeaANGe1SeFFJGSKda2yW67pHHfAqndTbiOSMcD8KoXN3JLtVzx0BPpQBZvpQxJHQkgc1QcEDdjJB7d6knbIyxyccE1Lp6JJkEemKAK8rmYgnrjqKlT93CGOcnvVk2sSc8j\\\/PSqrzA4GOe4oAikk3Ee\\\/pRGNjhuTjvTC5Bzz9a2tLhglt8P1FAFSyjM0pOCQOc066ZA5GDz1BHWrchhsLVth57j+lZk0xmXdxkmgCMqS2V4yO1e6fs86fBpllf6xcRu05IihGO3GTXhtm652nHPFe9eDNUOmeE7SG2AlJy7L6Z6Gkxo7XxHratBJNDcG3MYJ64IOD1\\\/SvmDxNfyX2qyzyvvldy7MO5Ney+OdXnl8PzTmEZIww7jI6+9eD3blp23ZJFJAy4ZFS0A7nis44bJJ4\\\/nUylplx2XpURU7s9cVQhv3cg05WO0j8Bn0pm0k\\\/N+VPAKqTz9AaAE6DJOKfAR5gB5\\\/rURI9hTlwnPT6UAaMLxRbiQR3wD1rX8JSxz+JdOUkKGmQFnPA56mudjTewLHAxXS+D7WJNdtpGG8pluT7UAM+IEgm8S3TBlYb2+6eOtc0MHpzz6961vEbLJqtw4BALHrx+FZgyMA8AmgACMx6ZA7mvWv2dda+z69faQzcXsRaMFv+Wi8jFeWRDKEA4HPWtHwlr8nhnxVp+qR43QSqzZ6EZ5FJjR+mXwd8WxS6Ukc77ZoQY5AT3Fdrc+JxcXJKvkY4UH+lfK2l+JpIZFv7KTzLS+jWSMD3\\\/rXRad8Smt7t7e6LxkhcFgBnk\\\/41yunrc3Uz6OtfGJCdvVeeamsddGoy7hIQTxn0rx7T9chvBG8c5MoJGM8AYruvCkyhCMg89u3+eaxasaJ3PS4kM0Kgdx1J6e1Z2r6OJIGG0MxBwCKt6fdo2BgBcdhnFa7FZV4wTjqKkZ8e\\\/tI\\\/AHW\\\/HXh6eTS7KKS8iIkiTdtZ8Z4Ga+N9W+CHjbQbYz6j4evbeIE53Rn5cdc1+wU1ukjcDdntUNzpEc8ZUxjaRz71tGq46Gbgmfivc2NxY5E1vJF2y4I5qvuDbuB9c1+jv7QP7OvhPxVY3VybRdP1VvmS7gJB3YONw6GvkrSP2Q\\\/iRrV3KkOlRw2gbal3czKiSL2YDk4\\\/CulTTVzFxaPFfO2jATpz+NTWsEt5KI44ZJJGwAqLuJPsBX1fo3\\\/BPvWp4kfUvElrav1ZYLdpQPxLD+VfRHwf8A2cPDPwogN2xbUtUwMXlxxs9lUcD+dS6sVsNQbPijwF+yv438aosz6edJsyM+ffAoxHsuN354r2PSP2HdLsoBJq+sXdxJgFkt1WNffqCa+sNT8RwWOERdzck7RzXB6\\\/40gDl\\\/ODKOoUcisvaSlsackVueRSfsyeB9DXizedgP+W0uSP6Vp+HPFOl\\\/DFRpkLrFppkz5bSnCepAzx9KxPG\\\/xIuCs8gYKqjG7jtXiN5q\\\/wDaN+91fP50qt8ig8KDz+daqPMtTNtJ6H6Aab4ghudPjltJPMgdco4bOcis7V\\\/GT2MkaBxknJPPSvl\\\/4YfGweHohp92xNmM7Hxnb6\\\/hzXpF541s9aiM8c6srjbGc5BHrWPs7M05z1nSviAl0wDsOeeTXVWfi2E\\\/8tF9znrXzfpurbC7OTGRgY3Z\\\/GtyHxKdoZST6Env9KHAakfSEPii3cDEgUY6A\\\/yq0niCN8AYKn3r58sfE0itGGkIBPUV0en+LJYWG8sIz1J6Y71m4tFKVz26C9WXoc9+K1LaVSikgYI7V5lp3iaOSPzFYhAMk4rptN1lVCqzfP1K+lSnYq1zrlYDjI57VLGwAx+hrBl1RYlWRmHHJ+nrVV\\\/EUSjJPsOevOKvmIsdNMyMjDp\\\/Ws+S68p8jB561mjXreaMtv3bR16ADvWXqviGCFGyVw2RkHnH+f5UnIdjr0vFKltw9atxOGi4\\\/DFeZQeKo48KZQVIzkdO\\\/wD9augtPFdubQtkggZpqVgaPGf2u\\\/i2nw28AXbRSqmp3itBbLnDbmGC2PYc1+W0tzJNO80rNJKxJZnOSSe9e+fto\\\/FAfED4pSWcExe00hWtgpXgS7jvIP5D8K8CeJyFbBwwzmuqmrK5zzd2WBKuR+7XAGeR2p7Jbyvwnl467TiquJMHKHpx7UnnHbyMjvWpBObFN+0ORnmkayZcLvz6mhbpMDcpJ9RUnnxMf4sH3oAhlEyZOcHuQarbypPceueaszyIV+VjjJPNVx6qfm9aAHr1+YHPqan8kTQhRgY71AgLEdWHt61oxNGCFHMuPyoAsWWoeVC0ZwF24JFYTHcxb15xVmUiORlBOP5VXYckZyTQAn8PIIz3605GAkU9KZg4PGVpy\\\/L3O4HigDatNRSBFLcuB0NN1aFGh8+NNuTgmskvuIIyG7\\\/5\\\/GtNJhJppjc9\\\/wA6AC3nPlLkUUzzGhCrhunbFFAH7JLENvzGgQrg45H0oRNyEjr6nihELcsoPvmvMO0Y8WSMKAPTFARewCfj1qV228BtvsDnNEQ3A5Kj2FADHhAxx+XFRxRqxPB\\\/4DUkpZMdVz6ikBEg6gEdytAAYh2wP97NMMBb\\\/a\\\/A1YRCTwdvuvNMcEfeG760AR+Tv4Zzx7UxoAf4Bx39atCJpe\\\/H+yaaY16EdO+KAKT2yDkdfccU1YOe4988VfKoB8gOfejYFXcVIPrgYoAovFlSvG3+9nmmrAwHHT1JNXGIYdMD1oRBjgA\\\/lmgCoV28MCfcYxSeWDzlQPrV9sGPYcken\\\/16idUCFSAo9CaAKLW4L5zn\\\/azmmPbjOS+T7girgRcYUj2xSCPJG5gPUf5NAFD7IrguWPHYGlTAUgA4960TbK6koRt+lMjhXaRtY++f\\\/rUAZ6xAg7gKhe2IK7SD68YrTNmgxhc\\\/QUfZ152AD1oAziACNzMD7U6UCXGBjHoDV5bQc7gx+pqIIrZ+Qt\\\/wL\\\/61AGatugPyqCfalNsHHz7WA6AAjH61daH02j8aSaEMoCjHrxQBnPAOysfYGo2t1Kj5efcVpCJeyFD3IPWoJISzEHIXPBxTuBnyW67SAFB9QOfxqjJH85A5+tbn2Qjnse5WoJbPAyQaLisYq2\\\/zE5I4xz3qYRBgB3HYVa+zkYJ4444p\\\/wBmH3uoHb\\\/P1p3CxWjiKHaQcZq\\\/BE2MgfQYzT4rcEKOOucitGG0wAQMZ9qVwQadFiRQTwTya8S\\\/bP8Ag0nir4fHW7GIvqGnHzmKqMsm07h\\\/X8K98hgOBjGBzV\\\/UbKPWtFuLG4TfFLE0bKRngjFOLs7g1dWPxijty0hVvlPTP4VHJBsYjtjOPevUvj58LZ\\\/hX47vNPdW+zTN5tq\\\/qpPI\\\/CvLixJYNXoJ3VzkatoOjdYY8DqeeKZJcsM8ADGM1E+5SeRn3oGHAOeT2xTETwzNK3JOaY43g7cn6VGoKtkZx705JCHyD+JoAY5LDaxxgYqxZEqjHoMVG\\\/JJYZNIJdoGM47YoAnklZvUjt6VUJzIDgnnt0qyXyvHFRFd4JGD7CgCNzk5wAemBVq2kIi9D1qFYGPGcCn7\\\/JxySOme1ADrgtIMZJB55qJfkXYw6d6d5gIyBgdsUoVGXJIGCOnagBlnEbi6hjGQXYDj3r25FXSoEiSRUdIwAF6HgeleS+HtNN5qsEcPMpYYX3r0mWK4tsJeRSnB70gMXx3qj\\\/YUg858seVB4xXnaL5khzxjjPrXV+Nr6C6aFYySyZyecVycBy\\\/BwM5zQBPhYt3Tnv61C5CtkcD0FOYbzjv0OaTASM7gc8Y4pgM34HPHoTTS+7IAGen9Kdj5SDjFN2AHj8jQAgUZxnFPiQyNk4H1pF+8e49RUokVRgYJz1HrQA\\\/zwuRgE\\\/Suk8CXZbWFjCBnKMAWOAOK5QDc\\\/PI68Cuo8EsDrDr9zELnt0xzQBka07NqlyHOW3kHB4461R2kA9AKs6hhr6ZiAMuSD+NV9uG5PI5wDyKAJGjZ4VYYAzjrzTGhaMks249cd6es2EGclQfpUZdepBx7mgD2\\\/wCBPj3zY4vD14RuiJe2djz2yvP419M6j4DsvFmko6LslAPzIOQa\\\/Puwv5NPvIri3cpKjb1YdjX6Kfs3+J4\\\/H\\\/g6z1Dd+\\\/QmG4XphgP6isammqNIaux5BqDa38Prwpue6tVfG4A5+v6V6h4B+J9teOI3kAlzkqzevtXXfErwtDeqwCc+u3\\\/69fPXiHQ5dDvhd25a3kX5lPZsVKtNaj+E+v8AQPE6SRrhww7HrXbafqnmKVD7uhPPNfKnw78cm\\\/tkhnYR3Cn5lJ78dPb\\\/ABr23SvEkcaIysdzLu4PX68VhKLRspJnqcEo27i3HPfmpnkBjIxgGuT0rXluIEYEshPbuadqfiJbSNcfe67c+1ZlFK+0caz4gM1381lbgFUYZDv2\\\/AVaub2JC0a\\\/Lt444rj9S8bzEOq\\\/cHbvUXhWafxbqhjRzDFHh5HP8Iz0Huaqwro7GXVd8LKmGYDI21zOu6zcW9q4MTqOSeDzXV61qlh4asiltEskmeVJ+ZvfNc1D8SdLvZ3gkHlv91o36g0khnkHi3x1JYRtMGB2xseTj8K8R8S\\\/FFJruSVZMpIuRtbJBr6y8TfDvwz47tXjngClv+WkDFGH5Gvnzx1+xrqFu7TeHtZSeHqlvfDDD23Dg9e4FdMZRMJKR4LrfjSa9iZd4K4\\\/M\\\/nXJal4njhZgG3N6L\\\/n1ra+IHwq8ZeCZiNU0maCDOPtCEPGc\\\/7SkgfjiuSs\\\/DU11Iu4EdyT2rdNdDIrXfiS8ucrGxhXvtJycepp+g+LNY8Pzb7G6lVc8xuxZG+orrtK+HqyAGQHPbI4rp7bwDY2sQZ4wx6e3amBN4O+NE12yQX9u8Up+UToSU\\\/HvXrOm6xHewiaKTdgZ2q3NeQRaXb6cW2xqJDzyOnNTDxJLpXzwybCp7DANS9Que\\\/6NqweVWlwFU9a7O51JWgSKGMSN0J6896+atH+L1lBtjvv3L9mX7p9D0r1TTPHumNYxSxXQl\\\/djDKe\\\/c\\\/nWbiaJnpen+JJbN1WQeXBGN7sc\\\/gP8+lbunfEu3unctKP3as2c9q8P174kwTWEdokokdvmfb\\\/ACrDs\\\/EsdlEyuwKEh3A5zjnaPqQPyqfZ33HzWPoTVvH7vZIm9455Bgq54zxzx9aqR+P5RbrufftByu\\\/pXisnjmMRPNczpAhBOGIGOlcnqnxe0+1hkhtpXmY91yf1\\\/Gj2Yuc+iNT+JqWXyifAAwCHyBXB6l8a5FkZTMNvLY3fdwOnXoeK8FvvGsmoQNIzyPKxykYXAHTrXN3FxeTyMzblycn61apoTmz6b0r4sHUryCJJNw4U4J57\\\/wCFeoa74+i8EfC\\\/VfEl22RDCdiZ++\\\/RQP8AgRr59+B3wy1vxJcQ3UkH2axUhjNKcDGOw9eap\\\/ti\\\/E62toLP4f6QQUtCJrxweM7cqn65\\\/KpcU5WRSbSuz5a1bUZdZ1a5vrg7prmVpXPuxyf501DtTbuXHfnpTIkWZiCdhx35qQWIxw4GOoroMQMzZGMe\\\/NPEu6I5UMTyDxUbWbg5LgkDpUYhdB+vBoAsxiB1w6gEHrgjNNe2hJyrEDrVfLoDweO3rTRISNuMCgA2neQD3wD1pOA2BwOgx1pznG0DknpTGGBwOfUdTQBJbt+8BPrmr1ud9y0oIPNZqEDBOPqKt2mQ4Pbrj1oAlvo1SQt3Y\\\/rVGT5mz1z1\\\/OrF2wY5Axj+dVW6Enj60AIw98UYx7\\\/SgbhkEcCgEAnnIxjPpQA7nHXaTVi2m8tlLDK8ZBqqGyuOvNStKSgXGR0oAvmZZSWIIz2FFVY5cIMqfzxRQB+zZbLYAJH+zxT1j6HBGPWq5uiHC4Vgf4sEmn\\\/atoIzjPYqa8w7SV\\\/3hBQkj2NOI2\\\/d2CqyT9ssvsopzyeXwQefXn+QoAnb58bhJx7U0kD7w2iqobOc8\\\/nTpJhgckfQ0AWVAQ5XBz\\\/dH\\\/1qbtyxJ69eKgaR4gGU5z6mlE4xls5PoKALKOqn5owfqc\\\/1qNZVZyCgUf7tM84N1VnHs2KRJd7EJnPoDyKALDNxgqCtNBw2SQF9AuCKiY\\\/LyCT6Cm\\\/aT9wbRjtjJoAsByX43bfWhvvcAlvcVCJtoyDg+tPSc8MSSPY0AH3m+br6gcUvl4+ZRk+pFRuxkbKE59DTCXU4cg+oK\\\/1oAlYsHycA+wwKcWVgQ33j24pse0pkDDeoPH5UrYwQxz+FAAsYVSRwPQ5zSA7hn5h7E0sKjYSMEZ74oZm7AN7igCJiHPPGO2etOCCToAuP739KbIq5HmcHttpUZnByOnYHFACMMY4BPuKRVYg7kDe4GKdsLAkjZ7LzQjc8up\\\/CgCIwpxlsfXApn3P72PyqdF253Ae2M0eUrf3T7NigCsI85KjafdajKkHuD6ngVYO5ehApGfH8IY980AVjBn7zB89sHA\\\/Go5LYEdOe9WYyWd8Nj\\\/Z5wKV1yuMZ9c0AZEsI5I609UXvgE+tWpIRIo45pRDkqG54z+NAEVuhZgehB71pQxdO3H5VWiXnIHGc5zmtKCMHsSDxQBJChHr65q\\\/AgVjjjI6dqhjABII5PcVaiXkH09aAPA\\\/2uPg0PiN4KkurKDfqlkvmQlRknplfx5r8z9Rs3s7mSOSMxyIxVkcYwR1FftlLapdQtG65VhtOa+Av2uf2Z7\\\/S9Wu\\\/E\\\/h21NxYzMZbi3iHzIepYDv9K6aU7aMxnHqj5AUbyAeR60NGYzjaeKlBKsVYFWU9DxinSSBsk8nvXUYDYotynjgUilEbJGCOKPN+TAXnpULyZ5OAfSgBZWBIOfxphJXJ7HpntSfUE9gKcR14755oAUcDjkn8qWOT2x60+PDqwPOPxpHYJg0ALJKQR1OfTtUT5ILc\\\/WleTn1zTd3GT370APyNmPz9qIwWYrwv0pUQMnv0zThiNuDk\\\/lQBveC4mOqbkZl8tdwZR0PrXbz6rO7Lul+0Mncn+dcv4Cit\\\/KupJbowPgBMDIY1pSyCJzuJVfXHDUAcz4qvlv7xpEj8sHg46fWsKA7Bux+ArR1ecTXksiDapPA71mFyuAenrQBMXDKf6VZSGN4FPBXHIqlEOTk5HWrAyI+CSR0zQBFMFRhtxUJxnk\\\/gKViSvv1pCozyMZPegBDnLDGPT3p\\\/PIGQeo9KZ15AxinFtuMdfXFACh8E9z6eldD4KV31KaRfvJA+FzjORiubz1yRz3xXUeA5UF9dmRTKGtnVUBIznjrjtQBiXQDTSbjwCTnrUR6dMD1xzUl0As8ijghjwf5UwHnngjPegBD856HA5FJhVGAuQfxocEKMH64qMBicZx2wRQBKoGzG3FfVH7D\\\/AI\\\/h0bUNZ0O5nWMz7Z4EY\\\/eIyGA\\\/DFfKvzKhAOat6Lq954e1O3v7KdoLqBw6OvUGpkuZWGnZ3P1B1\\\/XbeXJJXB5OO1eOeMprS6ZwroAR2P8AOvF\\\/Dfxi8R+M7OVIoWuLm2jBmWJgGK5+8Afw6VieJviHqUylZbaa3OBkMuCcVlGFi3K528s7WNwJbdykig8g4zXqPgr4jSXMSQTOiXCEA5bgj1FfJEvjO8aR8swj6AE5xVvTvHV1BdJcJIVljYEEVo43JTsfopoHiFjbFlO5ScfL6\\\/4Vd1LUYMEvITn+LGfwr5w+EPxntdaWO0upPIu1B+Rhw3HUf4V6PqfjeziikEkwAiXccjrnIH9a5XCzN1LQd4n8Ux2Ym+zjEi\\\/xuwAarvwy+I9tpXhrUb24mWN3uCm44PRR\\\/j+pr5x+KvxLW6kC2TlR0BA+teSnxjq9q7PFdyRq3LpnKt9R9K29ndWMubW59oXXxMa8vnmkbGfuhn5xWXq2v2GrosjPsnXlXUgHNfI9t8UbiKcCWfJHBYDIPSu28PeO5btg7OJUXoQQapQtsHOfR\\\/hXxvcabMsU0nOccnqK9XsvFyXMCc5YjJ\\\/GvjrTPFkt1qyyCTqQM9eK9Y0fxzbpEqyvyfu+prOUClI9uuxa6nEUnjRkYYIIyK8v1\\\/8AZ78OazeSXEEb2Esh3Yt8Bc\\\/7uP5Va0\\\/x1ZXThBMTgZbOa63StdhnIIYMBj8PSs7SjsXozyPWPgJqOkxu9lPDcjjCuCp\\\/wrz\\\/AF3wj4g0gHdpFyy55aJd44OO3tX2FHfQypsbDZ5B61TvdNtrmLIUA5\\\/Gmqj6icOx+fviTVLuyc+dZzQuB\\\/y0jK4\\\/MVy0UOteJpWj0\\\/T5rhWPVIyV\\\/PpX6D6z4Ns9RhZZYEkUjBDqCK5Wb4fRWMHl2kQijXgJGMAfhWyqJkcjPkOx+BPiC8VZL27t7IEZKElmH1wMVaX4P3OlvkazcoSMfuRtB\\\/WvqKfwfIVAX5W287hnNUj4Ca44KqzdzjtT5xcp8+W3h64sD5aTSXTL0eXk\\\/hxWhB4W1S\\\/3KN6qeSyg8e1fQel\\\/CQtLueMHJ7+n+c12ukfDCK1RTIgAx24\\\/SpdRAoXPkOX4U6hcsfMMh4HzODzQPhPMhGVZ8dOMcV9j3XgqFYgEHyrw3A5rmtQ8PRANtXDE52gcY70lUvsPksfK6eAriC8VGiYgnnH1r1r4V\\\/CC21K\\\/iurmISIGyI5AMcH\\\/AOtXY6voFtbTRtGgVX5bHr\\\/n+Veh\\\/Du1Szt0MaqylucevHr9f50TnoEY6nV+JY7T4cfDPV9aaFEg0+zefZjAO1eB+JwK\\\/JbxJ4gu\\\/FWv32rXzb7y7lMsjc4yew\\\/Sv0b\\\/AG4viTF4Y+BTaKHDXuuypboF7IpDSH9APxr81EYFhldx9+1FFaXCo9bEkLiMkk8+tSLcIMfKc46VKVgwp2ZJ7E0skEDRgiPB7kE10GRA0wGdufXrSGUY+8cj8jT2WN8AJ0+tNMC5HHfmgBjSAE4PHuc5qPOW4\\\/AUOCpK9yOp6U1RznIx6igCYrnbz09qawwfQ56ZpN+OpIx1psjFh3OR09aADByR6d\\\/QVajIMJOdrZwPWqi5OACT61IhyvB6AdsYoAmmJ8hRjkcdagCjHfGKdJMcY4GfT1qMZwQfpQAdCBik2k+mRS4BxxnvSN8p\\\/HpQAD1HPehSW64zijGeBxSrnr2HOaALcdlLKgYdD70VH9plwMyMPTiigD9jt43AhialSfAIYhSfUc1TVio2k8\\\/X+lPVeMkM2O4O39K8w7Sw5II+bd9KVlYcD\\\/x6qe\\\/zSDnfju1SmQg9Nv8AWgCdoxDgrsYn0NR4Jzlf50rswxyV\\\/rQSH6gH9aAHb3jGSDjp8oyaQMGJ3Y\\\/A80RlSSMZ\\\/HFNWMFjzn6GgCTBYAKOnoCaRSwPCAe5p3lPHgggfX\\\/9VR7WJJyDQA7c2eX49A2f0phJDHkkehFSbCAO3uKA\\\/YEkigAByg4P8hSLLztwMeu7JpTknn9RTcKD0BPpzmgBxcK2SST6DrQWVh94g+hODSMqlfunP+fWlEQKcEj2oAaHCOBliPzFK5Z2yuQPQdKNmD\\\/X\\\/IpwUdPlJ9xQA0yuJApV+fcYqVoyzDkj6GkRCAVZV57gUuzyCBkHP+zQA8KYxyQCfxpWXOBkHPXmlLqeicd8c0weS\\\/8AqwR68df0oAdsVBwQfrURzxnA+pp\\\/C9AV\\\/A00SGX\\\/AGsepzQAS5iwUAbPXjGKia4YDksv0xVny\\\/UbfqMVA0KycbOh780AIfmHIAz3FAY5ww+X161KoCgZGB7EVGQwYnaCp6dM0ACn5uAMdutNYAZ45z24NPEmQRyMUijr7880AR7QQT39aXbhsdOMUoUo2R8oI55pw5PHp370AOQfkPSrcQyOMdvwqqgCnOPwqeKQAdMHGSKAL8b46cf1qynIHIzWfDIc8Hn36Vbjkxkbu9AF6MgBc1Df6fb6nbPDOqurDBDAU1JfmA6U8O24Z5PTNAHxb+0n+yPDqTT6z4bgW3vACzQRrhZOfYcGviLVtIvNEv5bK\\\/tpLW6hba8co2sPzr9qpoYr0MkgBXGBmvn\\\/APaB\\\/Zi0j4kWX2uGBbfU0OROgwenQ10QqW0ZjKHVH5jOGIA9O9NycmvbvGf7Lfi3w2sskMKXsEeT8jANgegrxq\\\/025064eG5heCdDgo64INdSaexha25X6sMfnTsAqW9+\\\/pSgBl6YpoJz82RxxTAfG21Sen9aY5yR3pM5Bx19+9NLfMR3x64oAD90cdKB8p6ZFBDMowO\\\/c09cFRxnj1oAF4zwR6ihsswOM57UjY3DPB9aQMTzgfUUAdz4Uj0+HSf9KkKSSOWG3pxWjdhYjtWUzWuew5ArOsLqG00+3guLYOAm4Hvk96Z9t8sO0Qyn9wigDl9SIW4kKnK5OMiqLc9+ParF3L5szNjGTkj8aq8DPUn270AT26h5Am3rnvVh4vJQ5GfbpVeF9jLkYI\\\/nU9wXaLGevPNAFQkkkjHPApEH59KVRwc4\\\/E0hbHbmgBz\\\/kAeopoAO05x\\\/WnsAAp\\\/Ooi24YwOOg680AHJ9OPXtXZfDFN+r3pwGIs5No9+AP1rjiRwcV1nw9cJdag4HItmGc4AyRyf0oAwr5Nl3OuApDnIqHouQOvc0+7C\\\/aJFUhhkjI6Go+cnnjHOeaAGS\\\/Lg4655NMLMccDj2p8mSAMYxzUZJ4IwB1oAUOScEcmgZ6kFfajcARgZPrTNxXAHWgDrvhh4zPgrxjZ6iVDWvMUyk8FDwfy\\\/pX1D4x0PSfEFol5aiF0dMo8ZB3cdQa+L92Dntn8q7nwd8T77w9pr6e8peAf6ssNwTPUfSk1d3GmdLr\\\/hBIZJim3CtzgZFcRd2DQvkcHPQ9a1Z\\\/HEt08hLbjKcnn+Q7Vl3F3PcYLcHpz0piCCeaxlV4pGjZOQyNgivY\\\/Al5rfxB03UERWluLWFd0m\\\/DScnHHr1rxIyhXBOS3cV3fwu+KC+Bp78SxyMlzGqDZj5WB4P05P50mNDde0a8sLpory2uI51PMciHI9s1zF\\\/A8+Y1Rkx1yME16Vq3imPXW+1SlyJeQ7DFYVwlvc7cYY4wKEB53JpTqCSOgzwKbbveaZIHhdwOpArs20zbcGMAD0BP8AhTrzw88YUgKc9hTESeFfHUKYjuAIZh3Ydfxrop\\\/FhUiRTgkZGD90egrhrjw4sik457HtTRpGo28YCNvReitz+ApWA9A0bxndW4aQOWMjZHuK9U8H\\\/EeQO5lkO3ds\\\/Svm6C4uYZS1wjBsYLA10Ft4nMK8bvlI+7xx\\\/nNJxTGm0fXdj8QUwpa4Bz3z07VuWXj2KX5DIOB94nvXxvZ+MZ4Osr7R0wcZ+tdHp\\\/j2VQxWXk44JrN00Wpn1zbeL4p3RfNUjOOTWnHqVvcyr+8QjH3d2K+Y9E8etLGP3hE\\\/QNnvXU6H42H20Ca4OFGSazcC+Y+hhYwTKJCFwcDC1ah0W2RN4KEEcHFec6F47tnbElwwRgAeOMe9bGqeNLLTtPM63AaFiI8rwyt0GfUdKy5WXdHe2q2scZRcHimzatDbPt3L7EdfyNeOp8S4vKkcylH3EA9u+c+3FZ+p\\\/Fixs4N08xbAPIPcDgjNPkbDnR6zf60rhtsnyjOOnP4VwGr+LrSylYySAlmIwTjj1\\\/z7V4V4r+PN1dPJDpm9pSNpkcYA7fjVn4eeG7jx9dldYu7iVnO8eS5Qg568VqocquzNzvsdP4i+KWniQIJ1Lbs8sPl5rS0P4y2Wi2H2u7uYrexjwWmd+N3Uj3Pt7V4H+0\\\/4W0v4V+L9M0vQri4llltPtNyLmTzNhZiFAP0BP414lfa1e6jGsVzctJEhysecKD6gVoopoi7TPQvj78aLz40+MDftvh0qzUwWNsxzhM53kf3m4J\\\/CvNUjdTjaemMAUyPlvT6Vaa5PzbzzjqT3rRJJWJbbGL5p7HPbigmcoAVYYoN02Rn6Ugm+YsTjA5piEDOoPGD9KGmkAxggjsaDcdOTj2pWlJHI565FACZ81WypZvUDpQtuNuSAc1f07XZ9Psr2ziSNlu1CSF0BIGc8E9KrzsEiQDG0UAVXKngdj3PShIgcMDnFKuGYkccdvWrkaqY8Z+btj+VAFIqAOeO9LHHuOc4xzihiWJ35yp5qeFV3HIOOM80AMFuDng\\\/WoPLO7p7c1bnYwFSPun1qNHUNnGM8UAMNqwBOSPf1pvlkHBIPfp2q2ziSMjIyP1qszngk5x2oAfbweYDnr0FOmh8pTj8jwc06GZY1BwM561Jcf6Q4ZOT049aAKoU4HOKK2bbw9qVxEHitJpE\\\/vIhINFAH63+WC424A\\\/u5pziQN02jvkU13w2OPwNSI0YRuQD\\\/ALteYdo1II2HBzjuRmnrEvPzA\\\/QVF5\\\/B+fP0FMWV2znH4UAWkZuc\\\/o2aYkqqT8wJ9G4\\\/lVdbgnPlMw9cGnM4HXAP4UAWDKG7KT\\\/tCgHOcYX8arswAGWZh6Y6VLHKEGSSAaAHgH\\\/lmzE98iiPKMcsM\\\/71AQNyXJHXgGl+VjgMOPWgBzqrDn5hSps2gABR9KgmXavyr82fwpyvIIhjP0ycUASjcX24AT+9TvL2DIIIHcZFQlmdMEZPpnihW2jBXn0FAEwUsQxOB7808uEjOGBI7Dr\\\/ACqOMGTthf7pp5UhSuF20AAlLpnaM\\\/XmkV\\\/73B96BgDGVpTGGGRgn2oAlWQBCAAQe5HNMRiB8u0\\\/VajKSE8Lx35pdpB6tg\\\/3aAJHfJBbt07UjzFyNwDY9cU1odx+Uk\\\/lStE6YyTz0oAkWQsCVwvsaZCxlB4PH90Um3y\\\/vnk9MU54ufn4+pxQAjtyMjd9BjFKmAepX6D\\\/ABpSSAOW\\\/CgB26bs+7EUAMOznnP50wgY+UHP0qRY2Qk7c\\\/U\\\/\\\/Xoyw6tj2FACBflB39e2elI5JOByc8HqacitvyQQD3zTwuSaAISpbBI5\\\/WpQq45PJHr704IVAOevSkI4GT270AROnzZB5pqu2TnBA79KeAGJ68j\\\/ADzQgz1yfp3oAmjc8fw++atwvtI556VSA28VYVjtyOSe9AF7zsYIPWhXzgc49qrIcjrT06jGM+lAFgtk5z\\\/gKa8gckNjg1GWGcduahmbBLAkN7UAV7\\\/S7O9BWSJTnPavIfid+zX4c8dWcwlskWdslXjXDIcccivXVkLZ3HPHFPWQjIIyKpNrYTSe5+VXxS+AviH4catcRG1mvbBclJ44ycDPQ4HWvNntGj4ZWRh\\\/CwxX6++IfCmn+IY3W4hSTcMHIryLxF+yr4X18ySPYJvk43Lwa6Y1VbUxdN9D815Yhls5BB4JoVTgN3719WfEL9ijU9OaefQJWlRckRyt+OK+Z9b0S98OarcadfwmC6gba8b9j\\\/nFbKSlsZNNbmXtC84z\\\/wDrozj6ds1PtBYcAEHjFRFNwzk47iqEMYdx1PanJhnGeOccUALjkYP96prSItcxgdWYDp0oA7eW4FzHHE0ahVUKD0yMVn3YcBiDtA4x7Vbu7dopCWXg\\\/wAQNZVwHSF2J3jpmgDnZ+ZM9T3NNIGevXnNOYZfIyM00RFhnHtmgCbavXIHtT5XG31+gqFRgEMfpTTknHABOKAAtkg+\\\/btSAYPDZ9ADQT8pz\\\/jTRkIeSfbtQA5m4BJ6d+1ITlc5x2puOeefWgnkk8DP50ADZx1wfTvXa\\\/Dq3tZk1iS5u0tlFuVO5ckgkdK4sDAJ6rXVeCnK2+rMVDR\\\/Z8HnocigDDlwHbaRtJPP+NRHAXgkdakfO4nI4JP1pvDdMn0oAhmBba3IGO1R7g3b8u9TXOeFBJA6GoW+negBFIDZ6D0pA20Z\\\/kaXGOcHj9KTA6gcCgA5pd3POfbNGMlunNBGRkDoO9ABv+TAHvxWjY6y0AKT5lj9e4rN52EZ70YABJ5NAG893aMpII\\\/4F2qlNcxZG0lvSs9V8wgY4J4qR43gZgwKMOobg0Aa+meJ7mwCo+JbfGNjn+VdTpur29+N1q6xSgfcc85rz0NkdB7UsMzwSLIjbHU5yKAPVLIPJOskwDDqea3Li6ijiXhTkYJZuRXKeF7+HXbcx7il0F5UH73vUuoNNaMyMPM7cnjFAGrG0dzclFbdgZJz3rQt7FNoYLuJ75rlNEufKkGWySTwBgmumXVFjAQSgtnDKB0\\\/HvSYGimmRzRbWhVs\\\/wB4cio28N285ZmiCY7KKkttZjAABGffrUi6rHL8xcE+g7e1IZnXPg+Byxjm288duaxLnTJ9PkYBQ6qeq11M98oj37gFbH\\\/1qpRyLc5JbgjPzelFxGfba86RbUVkZflCnr6Voxa5NZxcNvI+cncOuKnbRra5DcHzPXpmrfw98H6brPii7t9VWWe1t1WTZFJsJycEZH4UXsMteGvGVzdMIJMjfIpLf3VByTn8MV1eqa3f3byxW7s8LHKbxgD3+veuu8YaJ4Y8N+FbFdJs4bZmuHJlILSMu3G0seSB6Vw1prtrEW807VzwP6YqFZ6lbaFE2V22ZJrllAJJOcZ\\\/\\\/VWe2lK0hneViSuMNkj+VWfFHjmx0u1d2lGxVI\\\/3jxxivJdW+LV3cborKERwdAX5OO9WI7iPRx9tRflYEgkV7d4d8U+H\\\/hZof9t6rdRx+QCUiBG+V\\\/4VUd818dv491fzPMSZY5B3UVl6rrmo65MJb65lunHTzGyB9PSk43FexqfELxtefELxjqniC\\\/z515M0gQnIjTPyoPYDA\\\/CudQfN1\\\/Sm7m2\\\/0pw+Q4PBq0IkAHbjnoDSNweg+vrTM5X0PekwcYPFADyOD0NIOBwOvOSKbnOewFOXOeq89sUAJzyffkClJxgYHHfvTQ2T\\\/nrS9VyCR7H0oAcpDc9O\\\/wBacZCYyrHFR4Ck7SB3+tKcgY7elADi2MDORjFAkIUkHn88U0FlI3cLSgktjH+NACudzZPB9u9LFMQuMnJpoyeDkY7etKeVP9fWgCV2yoBO761EGbG0ZFM3kknI68Vt+HNPiv7kRSAnkmgDJVSrEg4A7etXbLSbrVJUjt4Xd2IAwM81a1qw+xXjKoOwngnuK6f4YSj+37RGIwXGR70AaHhD4Janrut2Vneq1qtw2BuWvuf4a\\\/sZ+FNL063lurWO6mGGMkiAk8ehry5EWLXNFkHIEq8j\\\/P0r7b8Lyf8AEogPUlQf5Vzzk+hvCKsYem\\\/CHw1ptnHbxafCEXoNgortQy\\\/X8M0VjYo8Gu\\\/Ed6rfKI8Y6BT\\\/AI1BD4wvV+Voock90Of51XuBkDA+lUZLcuSR16UlYrU2f+EqvG6LGCOwB5\\\/WnjxPdHlkiI9wf8a59VKHGeMVP5THbwPaiyC7NVvFF30jht+vXYen508+KL116RAeyn\\\/GshEIY7vXr\\\/n6VMsbMRjBAosguzT\\\/AOEhvV7R\\\/XH\\\/ANercGu3bcbYiT0wP\\\/r1kJCwxgfKfU1ftrdm4YgjOOtLQLs0o9TncZ2hsjoc4\\\/nU0d9O4GQox6ZqvFEwwCTxxkGrix7VHHIHepKGefL1IBOehFJJdyheDj1FOkGBgDP09Ka0Qxk+vXvSAYupTEbT9M96UX0wA6D3HWoihVxjp1zQIPm3AEcfrQBdivJOCWyMfxd6nW9ZSOEx9KzVDbgOQRxntVmIYZQd3WgC+LyTI4AU+2AaeL45ICqM1UzgAf8A681FJL8qt+fuKALp1ByTgJke1RNqU3PCfiDWa1x3yeenrTftG0\\\/L6UCuaZ1OdeyHPt\\\/9emjUZwDhEGfY1nfasuB2xjB7VL5pZB3460DL8d\\\/KMZI\\\/AVPJqDsPmxxz3rJErLgd84wPWpBLjk49TQBoJcEZyA3GTSm7dTn5fxzVITELjPHcVCJ9zYA470Abcc+8DcF99uaek+H+XB+vaslbwgKF6nnmp4pweCc4\\\/nQBpLtBaT+InqKUSDGeS39Koi83R4zjPvTkkJbtgCgC4z\\\/MOn1puRjceWxUUcgbABzj0pWfB\\\/iH0oANwVzxwTz\\\/APWp6yZU8ZzxVV5QdvtT1kVVGCOeemDQBOTtY8Y4FTJLtyM4+lUi4xycepNTRuGXqQPSgC7u5I9egFSrIMYI9eM1ACRgn0BNKZdoJPBPfFAD2fPHaoXORyck9803zQDyPl9qZJJ3yM\\\/1oAQnB64+tPKblGDj3pkbBgQRk570qkEYH\\\/1qAIn+XoDwetRGcpyBjtmnykfMenU8HtVWUF2\\\/zigDotPhje0Qugc\\\/e+bmvzF\\\/bPu7Sb49azFaIii3jiik2DgvsDH\\\/ANCH5V+mqy\\\/ZNPaTcFCR5+nFfkZ8btYbX\\\/iz4r1BsES6hIBn0U4H6AVvR3uZVNjiRISSCOM5qVYmlGB19jUIAZvXBxzxW1p6FYicc9812HOY7q6MFIIP0q5pwX7ZEGb5QecCkv0Amzj\\\/AOvVnRYEkv4gflAz1\\\/GgDXdY5pHaK4JLMch+KqahFJHbtlSQP4lOQalfauTtXuQQcVVu3mEDFXAUjoTmgDF5wAMn19qE44HQ8+tMdxv4B4weeOab5uQCfrQBNtwMdT71Gy\\\/\\\/AKj60iSdznHrSCUDJB49qAF7cgH1x2pMKpAznnOKa0mOBjnsTQBkY6elACDOQc9ecZp3GMDIPXPrSM20DPXuaQHjOOMUAKRz1GeT713Pw98uLStflMaSOtuoCOMjlh3z1rhCCTkdK7HwNdCPSdfjYtmSBQAOhww7\\\/wCe9IDn5W\\\/eNxncTwAcU0fMDxk5xjtRIn97Of8AaoZSSAcDA7UwIblApBI68Y9KrqODwRjpzVi8AKLjqKrsSqjHHfrxQAAkkkjr2oB46DHrQFyeMmjHOCP60AIcjtkHoaUYYn6d+aBhjgjn0oXkEZ\\\/WgA5IHYe3al3ccdup70gwB2HHWgEqp7qeaAJo5BGQ\\\/YHgCu78TaNJ4n0BPElu\\\/mSRIkdzEByuAAG4rhDEfJBAG4nGCa6v4ea89lqg0yVwbK+\\\/cyqegB7\\\/AK0AcgOCeQvuBRwfU+oroPHfhSbwb4im0+U7o\\\/vxODkMp6HNc6Mc5yOec96AJbS5ms5lmgkKSKeCDzXQN45ubmJVuI0dgMbhwTXNDGMDk0AAHPcd6AOwsNdtpnXe4jPo3FbL+RIoMVxhsZODkV5sMj2P0qSOeWJgysU9CD0oA78XsluQoYOv94GrNrq3mlVYlQpwGB61w1vr0sYG8B89SeuKsx+IUX\\\/lmXPXrQB3f9oRmQYOQeSM1ctrm2YqNwwGB+Y9TXnNv4oeOfc0KEYwNvBAqw\\\/ilZIdiREtjjJoA9Tk8VQwaWZJdqbgyjjrjvUXgfxJb2txc3k8yxNcMFAd9uQvc\\\/UmvHr\\\/AFm6ndVkmO1VAWMDgCqj3s0hKvKzEknHPWgZ6\\\/8AFD4ySXLQaXpMkcsMCMXuBz85xkL24AFeWz+JdUu2zLeysewzx+VZgbJx60hAGOPmJ7mlsIknnlvGBlkaRj\\\/E7ZqL7uQMnFLnpznPTNLgDGOhPSmADHQkEfzo4zx07YoPTHftTSMDPqemKAFAIzj+fNAPPYfSlI2gY4HbmkbPTueaAA\\\/T6UegwAOtCpg8nrz60AE8HnHSgAzwT9e9BG4Hv9Dmhgoxx+GKAQOSeKADJBoHf+LHalXJPcd8+tN25yR3oAUYBznjrkUpJz1z7elLgbec5ppHGcEgetABjJ6n\\\/GlwSB0J9e9KCc88DHANKoJfA4+lACdcc04jA4Ix60zAJx0p+AVyeSaAGMBzjFaWhymG9jOQoHY1n8MoyM\\\/hUtnKySqwO3B7dqAO28SaaboJMDxtyazfC8htNUidHMbKww9a9vc\\\/bNOZHzgjFY6wG2k2g8k9aAPrzR7lbvRtNuInEjI6NuHbmvs7wNLv0K3yf4fz4r4O+GF8s3gyGPd+9jXoD93mvuP4dSl\\\/Dtqc8lBz+FclTQ3gdhvJ5Xp9KKrgj1orG5pY8KuLcg9RjPbvSxqUGSOcZwKnuf8AV\\\/8AAv8AClj6H\\\/dH9aYFFrfJ4GDnGTVqC3YjkY7UH\\\/Vx\\\/wC9\\\/SrkX3n\\\/AM9qAKi2YPBJYH\\\/69PW26HOe30q2f9ZF9f6mm9l\\\/3qAI\\\/s2BuHWr1uhyMr17dBTIv9T+FX4f60mNIljiJ4CnFWFgyuSCpHvT4uj1NP8Af\\\/Ef0qRlGaIdMexqsQxBBAwDz7Vem6tUL\\\/6w\\\/hQBXCEfNkilCAsc5HfFWD\\\/q\\\/wAf6VHF\\\/F\\\/vf4UANEQAJIPvUsKY46ZHanS\\\/6lfqv9Kkj+8PxoAYylc88j9Kp3XL5PI61oH\\\/AFZ+v9aqXPU\\\/7g\\\/nQBl3Hyk4xtyTxVGW6eJsYHPf0q5N1FZ1x\\\/qx+H86skIrliQCMk+laUVwW+7xntWTF\\\/r0rWtOn4UgTHxy7jk8ZHApxmxzkE+maiXp+NQt\\\/rJPqP50FFvzgV4ORTlwW4I9\\\/Wqyf8ekP1H8qmH+sT6j+tFhXHPcbXAIz34NIbwFB8xxzxmqdx\\\/rX\\\/3TSf8ALJvx\\\/mKLBc0VvNy9e3ABqZbvGPmHPH1rLj\\\/j\\\/GrEf3D9P6UmFzThvAhIDA+9Sy3gJwcVlxfehpZPun8aQXLwuBu9M9jTluhkgnAz0zWeOq1Mf4fof60DL4nB7jJ71btn3c9j2rIH3H+n+NaVh\\\/x7D8KAL5m2qTjtSeaCOx+tQP8A8e7fQ0k3RfrQBJJLt9+Oxqq8+SevPpTZPur9KhH+rSgC0LkDGOM+lSCfJHoO9Zs3+t\\\/AVND0i\\\/GgCw8mQfTFQp88oUtzmppev4VV07\\\/j5T6igCx4x1JtK8J6nOqn5LZ2Htha\\\/H3Vbp77Ubq4kbfJLKzsx5ySSa\\\/XH4r\\\/APJN\\\/EH\\\/AF5S\\\/wDoLV+Qs3U\\\/Q11UdmYVB0C5mXgelbMByMdyc8VlWn3k\\\/D+Va1n\\\/AK0V0mJUu4W84jqf5Vc0aJDdN5iZwjH8cUyf\\\/Wy1NpX\\\/AC1\\\/3G\\\/lQBUMkQb5g20eh61DdESREqCBjnJpYerfQ\\\/zpLr+GgDLbls4GcZNNfGeO9L\\\/Gf96kb7p\\\/z60AGcHHYfpQB8vqSe9LJ0P0H9KVPuH\\\/AHh\\\/KgBu3PfJoIBYZAOTj260Sf6tqVf9b+P9BQA1QR0H45pMY4HanN9xv896G+8v4UAIMr3z7V1XgyJpLTVzlRH9nzuJPXIwOO9cr\\\/y1b6j+tdX4K\\\/5Buuf9c0\\\/9CoAxThA2Y8sT1Jpo4P1p7f8AHw\\\/1P9art94\\\/UfzoAkuYXaNWVSVHX2qhnnA47Vp3f3F\\\/H+VZ8fQ0ANJwccY6Ufe754ximxf6xvwpy\\\/6t\\\/wAf6UAGeQBScEZHI96Q\\\/wCu\\\/AVIv8X0oAavz8AY+tTWcv2e6ikZRIqtnDd6hj\\\/4+Pw\\\/oasCgDpfHWvL4p1x78WkOno0UaiG3QKi7VA6DvxXLwSlJY5AcFDnPpWzqH3pfqaw4\\\/vH\\\/d\\\/woA7DxX44i8S+H7G0ltib62AHnsckgVxgwA3XpUz\\\/AH3+pqB\\\/utQA4Z7dugpSdx6A\\\/WiP7zf71NX\\\/AFRoAVBxjdjNBAXnrnj6Ui9D9aaPvPQBICe\\\/b8BSEgEUkP8Aqm+n+NK33Y\\\/92gA6ZBwTU0UZJxkHnAJqKTr\\\/AMCP8qsfwxUAV5TukLYzRjpnIB5pF7fj\\\/OkXt9aAHA8E9R3NNbOBhvwpZP8AGkfr+VAChRjII4PSjbk5J59qdH99Poak7r9KAIQCM59PWgjBHPFKP9bSS\\\/w\\\/73+FAAM5PqfT2pV9DwfY0qfeP+e1MX7rfUUAOLYIPB9c0NkdecUR\\\/wAX1pF+6n0\\\/pQAp7dCaQ4PUjPapE\\\/1R+n+FQDqfoKAJQFyc+mc0Ke2CSfWkbt9KkH3V+tAERIz19hThg4PUUxfut9TUif6pP896AEHfnt2pyg99uT396IfvP9f8Klj\\\/AIv9+gCuMhvQZqRSMcd+oph6r\\\/u\\\/1p9v\\\/q2+tAEsibogxIHHJFV0J3A8881ZH\\\/Hoah\\\/5aH8aAOy8PzxtbhWxv96vahZbmVh0B\\\/Wue8Nf6411d32\\\/36APU\\\/hTdCPSriHJwBkD07196\\\/CW6W68MWbfeIjHJ+lfn98Kf9VP\\\/uj+tfePwS\\\/5FK2\\\/3F\\\/kK5avRm0D0URbucgfjRUbfeNFcxrc\\\/9k=\",\"margin\":[-40,16,0,0],\"width\":595},{\"margin\":[-20,-150,0,0],\"columnGap\":8,\"columns\":[{\"width\":\"auto\",\"text\":\"$toLabel:\",\"style\":\"bold\",\"color\":\"#cd5138\"},{\"width\":\"*\",\"stack\":\"$clientDetails\",\"margin\":[4,0,0,0]}]},{\"margin\":[-20,10,0,140],\"columnGap\":8,\"columns\":[{\"width\":\"auto\",\"text\":\"$fromLabel:\",\"style\":\"bold\",\"color\":\"#cd5138\"},{\"width\":\"*\",\"stack\":[{\"width\":150,\"stack\":\"$companyDetails\"},{\"width\":150,\"stack\":\"$companyAddress\"}]}]},{\"canvas\":[{\"type\":\"line\",\"x1\":0,\"y1\":5,\"x2\":515,\"y2\":5,\"lineWidth\":1.5}],\"margin\":[0,0,0,-30]},{\"style\":\"invoiceLineItemsTable\",\"table\":{\"headerRows\":1,\"widths\":\"$invoiceLineItemColumns\",\"body\":\"$invoiceLineItems\"},\"layout\":{\"hLineWidth\":\"$notFirst:.5\",\"vLineWidth\":\"$none\",\"hLineColor\":\"#000000\",\"paddingLeft\":\"$amount:8\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:10\",\"paddingBottom\":\"$amount:10\"}},{\"columns\":[\"$notesAndTerms\",{\"alignment\":\"right\",\"table\":{\"widths\":[\"*\",\"40%\"],\"body\":\"$subtotals\"},\"layout\":{\"hLineWidth\":\"$none\",\"vLineWidth\":\"$none\",\"paddingLeft\":\"$amount:34\",\"paddingRight\":\"$amount:8\",\"paddingTop\":\"$amount:4\",\"paddingBottom\":\"$amount:4\"}}]},\"$signature\",{\"stack\":[\"$invoiceDocuments\"],\"style\":\"invoiceDocuments\"}],\"defaultStyle\":{\"fontSize\":\"$fontSize\",\"margin\":[8,4,8,4]},\"footer\":{\"columns\":[{\"text\":\"$invoiceFooter\",\"alignment\":\"left\"}],\"margin\":[40,-20,40,0]},\"styles\":{\"accountDetails\":{\"margin\":[0,0,0,3]},\"accountAddress\":{\"margin\":[0,0,0,3]},\"clientDetails\":{\"margin\":[0,0,0,3]},\"productKey\":{\"color\":\"$primaryColor:#cd5138\"},\"lineTotal\":{\"alignment\":\"right\"},\"tableHeader\":{\"bold\":true,\"fontSize\":\"$fontSizeLarger\"},\"subtotalsBalanceDueLabel\":{\"fontSize\":\"$fontSizeLargest\"},\"subtotalsBalanceDue\":{\"fontSize\":\"$fontSizeLargest\",\"color\":\"$primaryColor:#cd5138\"},\"invoiceLineItemsTable\":{\"margin\":[0,0,0,16]},\"cost\":{\"alignment\":\"right\"},\"quantity\":{\"alignment\":\"right\"},\"tax\":{\"alignment\":\"right\"},\"termsLabel\":{\"bold\":true,\"margin\":[0,0,0,4]},\"fullheader\":{\"fontSize\":\"$fontSizeLargest\",\"bold\":true},\"help\":{\"fontSize\":\"$fontSizeSmaller\",\"color\":\"#737373\"}},\"pageMargins\":[40,30,40,30],\"background\":[{\"image\":\"$companyBackground\",\"alignment\":\"center\"}]}","pdfmake":null}];
    var invoiceFonts = [{"id":1,"name":"Roboto","folder":"roboto","css_stack":"'Roboto', Arial, Helvetica, sans-serif","css_weight":400,"google_font":"Roboto:400,700,900,100","normal":"Roboto-Regular.ttf","bold":"Roboto-Medium.ttf","italics":"Roboto-Italic.ttf","bolditalics":"Roboto-Italic.ttf","sort_order":100},{"id":2,"name":"Abril Fatface","folder":"abril_fatface","css_stack":"'Abril Fatface', Georgia, serif","css_weight":400,"google_font":"Abril+Fatface","normal":"AbrilFatface-Regular.ttf","bold":"AbrilFatface-Regular.ttf","italics":"AbrilFatface-Regular.ttf","bolditalics":"AbrilFatface-Regular.ttf","sort_order":200},{"id":3,"name":"Arvo","folder":"arvo","css_stack":"'Arvo', Georgia, serif","css_weight":400,"google_font":"Arvo:400,700","normal":"Arvo-Regular.ttf","bold":"Arvo-Bold.ttf","italics":"Arvo-Italic.ttf","bolditalics":"Arvo-Italic.ttf","sort_order":300},{"id":4,"name":"Josefin Sans","folder":"josefin_sans","css_stack":"'Josefin Sans', Arial, Helvetica, sans-serif","css_weight":400,"google_font":"Josefin Sans:400,700,900,100","normal":"JosefinSans-Regular.ttf","bold":"JosefinSans-Bold.ttf","italics":"JosefinSans-Italic.ttf","bolditalics":"JosefinSans-Italic.ttf","sort_order":400},{"id":5,"name":"Josefin Sans Light","folder":"josefin_sans_light","css_stack":"'Josefin Sans', Arial, Helvetica, sans-serif","css_weight":300,"google_font":"Josefin+Sans:300,700,900,100","normal":"JosefinSans-Light.ttf","bold":"JosefinSans-SemiBold.ttf","italics":"JosefinSans-LightItalic.ttf","bolditalics":"JosefinSans-LightItalic.ttf","sort_order":600},{"id":6,"name":"Josefin Slab","folder":"josefin_slab","css_stack":"'Josefin Slab', Arial, Helvetica, sans-serif","css_weight":400,"google_font":"Josefin Sans:400,700,900,100","normal":"JosefinSlab-Regular.ttf","bold":"JosefinSlab-Bold.ttf","italics":"JosefinSlab-Italic.ttf","bolditalics":"JosefinSlab-Italic.ttf","sort_order":700},{"id":7,"name":"Josefin Slab Light","folder":"josefin_slab_light","css_stack":"'Josefin Slab', Georgia, serif","css_weight":300,"google_font":"Josefin+Sans:400,700,900,100","normal":"JosefinSlab-Light.ttf","bold":"JosefinSlab-SemiBold.ttf","italics":"JosefinSlab-LightItalic.ttf","bolditalics":"JosefinSlab-LightItalic.ttf","sort_order":800},{"id":8,"name":"Open Sans","folder":"open_sans","css_stack":"'Open Sans', Arial, Helvetica, sans-serif","css_weight":400,"google_font":"Open+Sans:400,700,900,100","normal":"OpenSans-Regular.ttf","bold":"OpenSans-Semibold.ttf","italics":"OpenSans-Italic.ttf","bolditalics":"OpenSans-Italic.ttf","sort_order":900},{"id":9,"name":"Open Sans Light","folder":"open_sans_light","css_stack":"'Open Sans', Arial, Helvetica, sans-serif","css_weight":300,"google_font":"Open+Sans:300,700,900,100","normal":"OpenSans-Light.ttf","bold":"OpenSans-Regular.ttf","italics":"OpenSans-LightItalic.ttf","bolditalics":"OpenSans-LightItalic.ttf","sort_order":1000},{"id":10,"name":"PT Sans","folder":"pt_sans","css_stack":"'PT Sans', Arial, Helvetica, sans-serif","css_weight":400,"google_font":"PT+Sans:400,700,900,100","normal":"PTSans-Regular.ttf","bold":"PTSans-Bold.ttf","italics":"PTSans-Italic.ttf","bolditalics":"PTSans-Italic.ttf","sort_order":1100},{"id":11,"name":"PT Serif","folder":"pt_serif","css_stack":"'PT Serif', Georgia, serif","css_weight":400,"google_font":"PT+Serif:400,700,900,100","normal":"PTSerif-Regular.ttf","bold":"PTSerif-Bold.ttf","italics":"PTSerif-Italic.ttf","bolditalics":"PTSerif-Italic.ttf","sort_order":1200},{"id":12,"name":"Raleway","folder":"raleway","css_stack":"'Raleway', Arial, Helvetica, sans-serif","css_weight":400,"google_font":"Raleway:400,700,900,100","normal":"Raleway-Regular.ttf","bold":"Raleway-Medium.ttf","italics":"Raleway-Italic.ttf","bolditalics":"Raleway-Italic.ttf","sort_order":1300},{"id":13,"name":"Raleway Light","folder":"raleway_light","css_stack":"'Raleway', Arial, Helvetica, sans-serif","css_weight":300,"google_font":"Raleway:300,700,900,100","normal":"Raleway-Light.ttf","bold":"Raleway-Medium.ttf","italics":"Raleway-LightItalic.ttf","bolditalics":"Raleway-LightItalic.ttf","sort_order":1400},{"id":14,"name":"Titillium","folder":"titillium","css_stack":"'Titillium Web', Arial, Helvetica, sans-serif","css_weight":400,"google_font":"Titillium+Web:400,700,900,100","normal":"TitilliumWeb-Regular.ttf","bold":"TitilliumWeb-Bold.ttf","italics":"TitilliumWeb-Italic.ttf","bolditalics":"TitilliumWeb-Italic.ttf","sort_order":1500},{"id":15,"name":"Titillium Light","folder":"titillium_light","css_stack":"'Titillium Web', Arial, Helvetica, sans-serif","css_weight":300,"google_font":"Titillium+Web:300,700,900,100","normal":"TitilliumWeb-Light.ttf","bold":"TitilliumWeb-SemiBold.ttf","italics":"TitilliumWeb-LightItalic.ttf","bolditalics":"TitilliumWeb-LightItalic.ttf","sort_order":1600},{"id":16,"name":"Ubuntu","folder":"ubuntu","css_stack":"'Ubuntu', Arial, Helvetica, sans-serif","css_weight":400,"google_font":"Ubuntu:400,700,900,100","normal":"Ubuntu-Regular.ttf","bold":"Ubuntu-Bold.ttf","italics":"Ubuntu-Italic.ttf","bolditalics":"Ubuntu-Italic.ttf","sort_order":1700},{"id":17,"name":"Ubuntu Light","folder":"ubuntu_light","css_stack":"'Ubuntu', Arial, Helvetica, sans-serif","css_weight":300,"google_font":"Ubuntu:200,700,900,100","normal":"Ubuntu-Light.ttf","bold":"Ubuntu-Medium.ttf","italics":"Ubuntu-LightItalic.ttf","bolditalics":"Ubuntu-LightItalic.ttf","sort_order":1800},{"id":18,"name":"UKai - Chinese","folder":"ukai","css_stack":"","css_weight":400,"google_font":"","normal":"UKai.ttf","bold":"UKai.ttf","italics":"UKai.ttf","bolditalics":"UKai.ttf","sort_order":1800},{"id":19,"name":"GenshinGothic P - Japanese","folder":"gensha_gothic_p","css_stack":"","css_weight":400,"google_font":"","normal":"GenShinGothic-P-Regular.ttf","bold":"GenShinGothic-P-Regular.ttf","italics":"GenShinGothic-P-Regular.ttf","bolditalics":"GenShinGothic-P-Regular.ttf","sort_order":1800},{"id":20,"name":"GenshinGothic - Japanese","folder":"gensha_gothic","css_stack":"","css_weight":400,"google_font":"","normal":"GenShinGothic-Regular.ttf","bold":"GenShinGothic-Regular.ttf","italics":"GenShinGothic-Regular.ttf","bolditalics":"GenShinGothic-Regular.ttf","sort_order":1800}];
    var invoice = {"invoice_number":"0000","invoice_date":"Aug 8, 2020","company":{"id":1,"timezone_id":null,"date_format_id":null,"datetime_format_id":null,"currency_id":1,"created_at":"2020-08-08 18:20:19","updated_at":"2020-08-08 18:24:37","deleted_at":null,"name":null,"account_key":"hcm2o7uvejuorwwsaqqxhjeigivwqwbo","last_login":"2020-08-08 18:24:37","address1":null,"address2":null,"city":null,"state":null,"postal_code":null,"country_id":null,"invoice_terms":null,"industry_id":null,"size_id":null,"invoice_taxes":1,"invoice_item_taxes":0,"invoice_design_id":1,"work_phone":null,"work_email":null,"language_id":1,"custom_value1":null,"custom_value2":null,"fill_products":1,"update_products":1,"primary_color":null,"secondary_color":null,"hide_quantity":0,"hide_paid_to_date":0,"custom_invoice_taxes1":null,"custom_invoice_taxes2":null,"vat_number":null,"invoice_number_prefix":null,"invoice_number_counter":1,"quote_number_prefix":null,"quote_number_counter":1,"share_counter":1,"id_number":null,"token_billing_type_id":4,"invoice_footer":null,"pdf_email_attachment":0,"subdomain":null,"font_size":9,"invoice_labels":null,"custom_design1":null,"show_item_taxes":0,"iframe_url":null,"military_time":0,"recurring_hour":8,"invoice_number_pattern":null,"quote_number_pattern":null,"quote_terms":null,"website":null,"client_view_css":null,"header_font_id":1,"body_font_id":1,"auto_convert_quote":1,"all_pages_footer":0,"all_pages_header":0,"show_currency_code":0,"enable_portal_password":0,"send_portal_password":0,"recurring_invoice_number_prefix":"R","enable_client_portal":1,"invoice_fields":null,"devices":null,"logo":null,"logo_width":0,"logo_height":0,"logo_size":0,"invoice_embed_documents":0,"document_email_attachment":0,"enable_client_portal_dashboard":1,"company_id":1,"page_size":"A4","live_preview":1,"realtime_preview":1,"invoice_number_padding":4,"enable_second_tax_rate":0,"auto_bill_on_due_date":0,"start_of_week":0,"enable_buy_now_buttons":0,"include_item_taxes_inline":0,"financial_year_start":null,"enabled_modules":63,"enabled_dashboard_sections":7,"show_accept_invoice_terms":0,"show_accept_quote_terms":0,"require_invoice_signature":0,"require_quote_signature":0,"client_number_prefix":null,"client_number_counter":0,"client_number_pattern":null,"domain_id":1,"payment_terms":null,"reset_counter_frequency_id":null,"payment_type_id":null,"gateway_fee_enabled":0,"reset_counter_date":null,"tax_name1":null,"tax_rate1":"0.000","tax_name2":null,"tax_rate2":"0.000","quote_design_id":1,"custom_design2":null,"custom_design3":null,"analytics_key":null,"credit_number_counter":0,"credit_number_prefix":null,"credit_number_pattern":null,"task_rate":"0.0000","inclusive_taxes":0,"convert_products":0,"signature_on_pdf":0,"ubl_email_attachment":0,"auto_archive_invoice":0,"auto_archive_quote":0,"auto_email_invoice":1,"send_item_details":0,"custom_fields":{},"background_image_id":null,"custom_messages":{},"is_custom_domain":0,"allow_approve_expired_quote":0,"custom_fields_options":{},"client_view_js":"","show_product_notes":0,"require_approve_quote":1,"valid_until_days":null,"companyPlan":{"id":1,"plan":null,"plan_term":null,"plan_started":null,"plan_paid":null,"plan_expires":null,"payment_id":null,"trial_started":null,"trial_plan":null,"pending_plan":null,"pending_term":null,"created_at":"2020-08-08 18:20:19","updated_at":"2020-08-08 18:20:19","deleted_at":null,"plan_price":null,"pending_plan_price":null,"num_users":1,"pending_num_users":1,"utm_source":null,"utm_medium":null,"utm_campaign":null,"utm_term":null,"utm_content":null,"discount":0,"discount_expires":null,"promo_expires":null,"bluevine_status":null,"referral_code":null,"app_store_order_id":null},"language":{"id":1,"name":"English","locale":"en"},"country":null},"balance":100,"amount":100,"terms":"","invoice_footer":"","client":{"name":"Sample Client","address1":"10 Main St.","city":"New York","state":"NY","postal_code":"10000","work_phone":"(212) 555-0000","work_email":"sample@example.com","balance":100,"vat_number":"","id_number":"","contacts":[{"first_name":"Test","last_name":"Contact","email":"contact@gmail.com"}]},"invoice_items":[{"cost":100,"qty":1,"notes":"Notes","product_key":"Item","discount":10,"tax_name1":"Tax","tax_rate1":10}],"documents":[]};

    function getDesignJavascript() {
      var id = $('#invoice_design_id').val();
      if (id == '-1') {
        showMoreDesigns();
        $('#invoice_design_id').val(1);
        return invoiceDesigns[0].javascript;
      } else {
        var design = _.find(invoiceDesigns, function(design){ return design.id == id});
        return design ? design.javascript : '';
      }
    }

    function loadFont(fontId){
      var fontFolder = '';
      $.each(window.invoiceFonts, function(i, font){
        if(font.id==fontId)fontFolder=font.folder;
      });
      if(!window.ninjaFontVfs[fontFolder]){
        window.loadingFonts = true;
        jQuery.getScript("http:\/\/invninjv1.local\/js\/vfs_fonts\/%s.js".replace('%s', fontFolder), function(){window.loadingFonts=false;ninjaLoadFontVfs();refreshPDF()})
      }
    }

    function getPDFString(cb) {
      invoice.features = {
          customize_invoice_design:true,
          remove_created_by:false,
          invoice_settings:true
      };
      invoice.company.invoice_embed_documents = $('#invoice_embed_documents').is(":checked");
      invoice.company.hide_paid_to_date = $('#hide_paid_to_date').is(":checked");
      invoice.invoice_design_id = $('#invoice_design_id').val();
      invoice.company.page_size = $('#page_size option:selected').text();
      invoice.company.invoice_fields = ko.mapping.toJSON(model);

      NINJA.primaryColor = $('#primary_color').val();
      NINJA.secondaryColor = $('#secondary_color').val();
      NINJA.fontSize = parseInt($('#font_size').val());
      NINJA.headerFont = $('#header_font_id option:selected').text();
      NINJA.bodyFont = $('#body_font_id option:selected').text();

      var fields = ["address1","address2","amount","amount_paid","balance","balance_due","blank","city_state_postal","client_name","company_name","contact_name","country","credit_card","credit_date","credit_issued_to","credit_note","credit_number","credit_to","custom_value1","custom_value2","date","delivery_note","description","details","discount","due_date","email","from","gateway_fee_description","gateway_fee_discount_description","gateway_fee_item","hours","id_number","invoice","invoice_date","invoice_due_date","invoice_issued_to","invoice_no","invoice_number","invoice_to","invoice_total","item","line_total","method","outstanding","paid_to_date","partial_due","payment_date","phone","po_number","postal_city_state","product_key","quantity","quote","quote_date","quote_due_date","quote_issued_to","quote_no","quote_number","quote_to","rate","reference","service","statement","statement_date","statement_issued_to","statement_to","subtotal","surcharge","tax","tax_invoice","tax_quote","taxes","terms","to","total","unit_cost","valid_until","vat_number","website","work_phone","your_credit","your_invoice","your_quote","your_statement"];
      for (var i=0; i<fields.length; i++) {
        var field = fields[i];
        var val = $('#labels_' + field).val();
		if (val) {
			invoiceLabels[field + '_orig'] = invoiceLabels[field];
			invoiceLabels[field] = val;
		} else if (invoiceLabels[field + '_orig']) {
			invoiceLabels[field] = invoiceLabels[field + '_orig'];
			delete invoiceLabels[field + '_orig'];
		}
      }

      generatePDF(invoice, getDesignJavascript(), true, cb);
    }

	function updateFieldLabels() {
					if ($('#labels_address1').val()) {
				$('.address1-label-group').show();
			} else {
				$('.address1-label-group').hide();
			}
					if ($('#labels_address2').val()) {
				$('.address2-label-group').show();
			} else {
				$('.address2-label-group').hide();
			}
					if ($('#labels_amount').val()) {
				$('.amount-label-group').show();
			} else {
				$('.amount-label-group').hide();
			}
					if ($('#labels_amount_paid').val()) {
				$('.amount_paid-label-group').show();
			} else {
				$('.amount_paid-label-group').hide();
			}
					if ($('#labels_balance').val()) {
				$('.balance-label-group').show();
			} else {
				$('.balance-label-group').hide();
			}
					if ($('#labels_balance_due').val()) {
				$('.balance_due-label-group').show();
			} else {
				$('.balance_due-label-group').hide();
			}
					if ($('#labels_blank').val()) {
				$('.blank-label-group').show();
			} else {
				$('.blank-label-group').hide();
			}
					if ($('#labels_city_state_postal').val()) {
				$('.city_state_postal-label-group').show();
			} else {
				$('.city_state_postal-label-group').hide();
			}
					if ($('#labels_client_name').val()) {
				$('.client_name-label-group').show();
			} else {
				$('.client_name-label-group').hide();
			}
					if ($('#labels_company_name').val()) {
				$('.company_name-label-group').show();
			} else {
				$('.company_name-label-group').hide();
			}
					if ($('#labels_contact_name').val()) {
				$('.contact_name-label-group').show();
			} else {
				$('.contact_name-label-group').hide();
			}
					if ($('#labels_country').val()) {
				$('.country-label-group').show();
			} else {
				$('.country-label-group').hide();
			}
					if ($('#labels_credit_card').val()) {
				$('.credit_card-label-group').show();
			} else {
				$('.credit_card-label-group').hide();
			}
					if ($('#labels_credit_date').val()) {
				$('.credit_date-label-group').show();
			} else {
				$('.credit_date-label-group').hide();
			}
					if ($('#labels_credit_issued_to').val()) {
				$('.credit_issued_to-label-group').show();
			} else {
				$('.credit_issued_to-label-group').hide();
			}
					if ($('#labels_credit_note').val()) {
				$('.credit_note-label-group').show();
			} else {
				$('.credit_note-label-group').hide();
			}
					if ($('#labels_credit_number').val()) {
				$('.credit_number-label-group').show();
			} else {
				$('.credit_number-label-group').hide();
			}
					if ($('#labels_credit_to').val()) {
				$('.credit_to-label-group').show();
			} else {
				$('.credit_to-label-group').hide();
			}
					if ($('#labels_custom_value1').val()) {
				$('.custom_value1-label-group').show();
			} else {
				$('.custom_value1-label-group').hide();
			}
					if ($('#labels_custom_value2').val()) {
				$('.custom_value2-label-group').show();
			} else {
				$('.custom_value2-label-group').hide();
			}
					if ($('#labels_date').val()) {
				$('.date-label-group').show();
			} else {
				$('.date-label-group').hide();
			}
					if ($('#labels_delivery_note').val()) {
				$('.delivery_note-label-group').show();
			} else {
				$('.delivery_note-label-group').hide();
			}
					if ($('#labels_description').val()) {
				$('.description-label-group').show();
			} else {
				$('.description-label-group').hide();
			}
					if ($('#labels_details').val()) {
				$('.details-label-group').show();
			} else {
				$('.details-label-group').hide();
			}
					if ($('#labels_discount').val()) {
				$('.discount-label-group').show();
			} else {
				$('.discount-label-group').hide();
			}
					if ($('#labels_due_date').val()) {
				$('.due_date-label-group').show();
			} else {
				$('.due_date-label-group').hide();
			}
					if ($('#labels_email').val()) {
				$('.email-label-group').show();
			} else {
				$('.email-label-group').hide();
			}
					if ($('#labels_from').val()) {
				$('.from-label-group').show();
			} else {
				$('.from-label-group').hide();
			}
					if ($('#labels_gateway_fee_description').val()) {
				$('.gateway_fee_description-label-group').show();
			} else {
				$('.gateway_fee_description-label-group').hide();
			}
					if ($('#labels_gateway_fee_discount_description').val()) {
				$('.gateway_fee_discount_description-label-group').show();
			} else {
				$('.gateway_fee_discount_description-label-group').hide();
			}
					if ($('#labels_gateway_fee_item').val()) {
				$('.gateway_fee_item-label-group').show();
			} else {
				$('.gateway_fee_item-label-group').hide();
			}
					if ($('#labels_hours').val()) {
				$('.hours-label-group').show();
			} else {
				$('.hours-label-group').hide();
			}
					if ($('#labels_id_number').val()) {
				$('.id_number-label-group').show();
			} else {
				$('.id_number-label-group').hide();
			}
					if ($('#labels_invoice').val()) {
				$('.invoice-label-group').show();
			} else {
				$('.invoice-label-group').hide();
			}
					if ($('#labels_invoice_date').val()) {
				$('.invoice_date-label-group').show();
			} else {
				$('.invoice_date-label-group').hide();
			}
					if ($('#labels_invoice_due_date').val()) {
				$('.invoice_due_date-label-group').show();
			} else {
				$('.invoice_due_date-label-group').hide();
			}
					if ($('#labels_invoice_issued_to').val()) {
				$('.invoice_issued_to-label-group').show();
			} else {
				$('.invoice_issued_to-label-group').hide();
			}
					if ($('#labels_invoice_no').val()) {
				$('.invoice_no-label-group').show();
			} else {
				$('.invoice_no-label-group').hide();
			}
					if ($('#labels_invoice_number').val()) {
				$('.invoice_number-label-group').show();
			} else {
				$('.invoice_number-label-group').hide();
			}
					if ($('#labels_invoice_to').val()) {
				$('.invoice_to-label-group').show();
			} else {
				$('.invoice_to-label-group').hide();
			}
					if ($('#labels_invoice_total').val()) {
				$('.invoice_total-label-group').show();
			} else {
				$('.invoice_total-label-group').hide();
			}
					if ($('#labels_item').val()) {
				$('.item-label-group').show();
			} else {
				$('.item-label-group').hide();
			}
					if ($('#labels_line_total').val()) {
				$('.line_total-label-group').show();
			} else {
				$('.line_total-label-group').hide();
			}
					if ($('#labels_method').val()) {
				$('.method-label-group').show();
			} else {
				$('.method-label-group').hide();
			}
					if ($('#labels_outstanding').val()) {
				$('.outstanding-label-group').show();
			} else {
				$('.outstanding-label-group').hide();
			}
					if ($('#labels_paid_to_date').val()) {
				$('.paid_to_date-label-group').show();
			} else {
				$('.paid_to_date-label-group').hide();
			}
					if ($('#labels_partial_due').val()) {
				$('.partial_due-label-group').show();
			} else {
				$('.partial_due-label-group').hide();
			}
					if ($('#labels_payment_date').val()) {
				$('.payment_date-label-group').show();
			} else {
				$('.payment_date-label-group').hide();
			}
					if ($('#labels_phone').val()) {
				$('.phone-label-group').show();
			} else {
				$('.phone-label-group').hide();
			}
					if ($('#labels_po_number').val()) {
				$('.po_number-label-group').show();
			} else {
				$('.po_number-label-group').hide();
			}
					if ($('#labels_postal_city_state').val()) {
				$('.postal_city_state-label-group').show();
			} else {
				$('.postal_city_state-label-group').hide();
			}
					if ($('#labels_product_key').val()) {
				$('.product_key-label-group').show();
			} else {
				$('.product_key-label-group').hide();
			}
					if ($('#labels_quantity').val()) {
				$('.quantity-label-group').show();
			} else {
				$('.quantity-label-group').hide();
			}
					if ($('#labels_quote').val()) {
				$('.quote-label-group').show();
			} else {
				$('.quote-label-group').hide();
			}
					if ($('#labels_quote_date').val()) {
				$('.quote_date-label-group').show();
			} else {
				$('.quote_date-label-group').hide();
			}
					if ($('#labels_quote_due_date').val()) {
				$('.quote_due_date-label-group').show();
			} else {
				$('.quote_due_date-label-group').hide();
			}
					if ($('#labels_quote_issued_to').val()) {
				$('.quote_issued_to-label-group').show();
			} else {
				$('.quote_issued_to-label-group').hide();
			}
					if ($('#labels_quote_no').val()) {
				$('.quote_no-label-group').show();
			} else {
				$('.quote_no-label-group').hide();
			}
					if ($('#labels_quote_number').val()) {
				$('.quote_number-label-group').show();
			} else {
				$('.quote_number-label-group').hide();
			}
					if ($('#labels_quote_to').val()) {
				$('.quote_to-label-group').show();
			} else {
				$('.quote_to-label-group').hide();
			}
					if ($('#labels_rate').val()) {
				$('.rate-label-group').show();
			} else {
				$('.rate-label-group').hide();
			}
					if ($('#labels_reference').val()) {
				$('.reference-label-group').show();
			} else {
				$('.reference-label-group').hide();
			}
					if ($('#labels_service').val()) {
				$('.service-label-group').show();
			} else {
				$('.service-label-group').hide();
			}
					if ($('#labels_statement').val()) {
				$('.statement-label-group').show();
			} else {
				$('.statement-label-group').hide();
			}
					if ($('#labels_statement_date').val()) {
				$('.statement_date-label-group').show();
			} else {
				$('.statement_date-label-group').hide();
			}
					if ($('#labels_statement_issued_to').val()) {
				$('.statement_issued_to-label-group').show();
			} else {
				$('.statement_issued_to-label-group').hide();
			}
					if ($('#labels_statement_to').val()) {
				$('.statement_to-label-group').show();
			} else {
				$('.statement_to-label-group').hide();
			}
					if ($('#labels_subtotal').val()) {
				$('.subtotal-label-group').show();
			} else {
				$('.subtotal-label-group').hide();
			}
					if ($('#labels_surcharge').val()) {
				$('.surcharge-label-group').show();
			} else {
				$('.surcharge-label-group').hide();
			}
					if ($('#labels_tax').val()) {
				$('.tax-label-group').show();
			} else {
				$('.tax-label-group').hide();
			}
					if ($('#labels_tax_invoice').val()) {
				$('.tax_invoice-label-group').show();
			} else {
				$('.tax_invoice-label-group').hide();
			}
					if ($('#labels_tax_quote').val()) {
				$('.tax_quote-label-group').show();
			} else {
				$('.tax_quote-label-group').hide();
			}
					if ($('#labels_taxes').val()) {
				$('.taxes-label-group').show();
			} else {
				$('.taxes-label-group').hide();
			}
					if ($('#labels_terms').val()) {
				$('.terms-label-group').show();
			} else {
				$('.terms-label-group').hide();
			}
					if ($('#labels_to').val()) {
				$('.to-label-group').show();
			} else {
				$('.to-label-group').hide();
			}
					if ($('#labels_total').val()) {
				$('.total-label-group').show();
			} else {
				$('.total-label-group').hide();
			}
					if ($('#labels_unit_cost').val()) {
				$('.unit_cost-label-group').show();
			} else {
				$('.unit_cost-label-group').hide();
			}
					if ($('#labels_valid_until').val()) {
				$('.valid_until-label-group').show();
			} else {
				$('.valid_until-label-group').hide();
			}
					if ($('#labels_vat_number').val()) {
				$('.vat_number-label-group').show();
			} else {
				$('.vat_number-label-group').hide();
			}
					if ($('#labels_website').val()) {
				$('.website-label-group').show();
			} else {
				$('.website-label-group').hide();
			}
					if ($('#labels_work_phone').val()) {
				$('.work_phone-label-group').show();
			} else {
				$('.work_phone-label-group').hide();
			}
					if ($('#labels_your_credit').val()) {
				$('.your_credit-label-group').show();
			} else {
				$('.your_credit-label-group').hide();
			}
					if ($('#labels_your_invoice').val()) {
				$('.your_invoice-label-group').show();
			} else {
				$('.your_invoice-label-group').hide();
			}
					if ($('#labels_your_quote').val()) {
				$('.your_quote-label-group').show();
			} else {
				$('.your_quote-label-group').hide();
			}
					if ($('#labels_your_statement').val()) {
				$('.your_statement-label-group').show();
			} else {
				$('.your_statement-label-group').hide();
			}
			}

	function onFieldChange() {
		var $select = $('#label_field');
        var id = $select.val();
		$select.val(null).blur();
		$('.' + id + '-label-group').fadeIn();
		showUsedFields();
	}

	function showUsedFields() {
		$('#label_field > option').each(function(key, option) {
			var isUsed = $('#labels_' + option.value).is(':visible');
			$(this).css('color', isUsed ? '#888' : 'black');
		});
	}

    $(function() {
      var options = {
        preferredFormat: 'hex',
        disabled: false,
        showInitial: false,
        showInput: true,
        allowEmpty: true,
        clickoutFiresChange: true,
      };

      $('#primary_color').spectrum(options);
      $('#secondary_color').spectrum(options);
      $('#header_font_id').change(function(){loadFont($('#header_font_id').val())});
      $('#body_font_id').change(function(){loadFont($('#body_font_id').val())});

	  updateFieldLabels();
      refreshPDF();
	  setTimeout(function() {
		showUsedFields();
	  }, 1);

    });

  </script>


  <div class="row">
    <div class="col-md-12">

      <form accept-charset="utf-8" class="form-horizontal warn-on-exit" onchange="if(!window.loadingFonts)refreshPDF()" method="POST">

      
	  
      
      
      
      
      
      
      
      
      
      
	  

          
        <div style="display:none">
            <div class="form-group"><label for="invoice_fields_json" class="control-label col-lg-4 col-sm-4">invoice fields json</label><div class="col-lg-8 col-sm-8"><input class="form-control" data-bind="value: ko.mapping.toJSON(model)" id="invoice_fields_json" type="text" name="invoice_fields_json"></div></div>
		</div>


    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Invoice Design</h3>
      </div>

        <div class="panel-body">
            <div role="tabpanel">
                <ul class="nav nav-tabs" role="tablist" style="border: none">
                    <li role="presentation" class="active"><a href="#general_settings" aria-controls="general_settings" role="tab" data-toggle="tab">General Settings</a></li>
                    <li role="presentation"><a href="#invoice_labels" aria-controls="invoice_labels" role="tab" data-toggle="tab">Invoice Labels</a></li>
                    <li role="presentation"><a href="#invoice_fields" aria-controls="invoice_fields" role="tab" data-toggle="tab">Invoice Fields</a></li>
					<li role="presentation"><a href="#product_fields" aria-controls="product_fields" role="tab" data-toggle="tab">Product Fields</a></li>
                    <li role="presentation"><a href="#invoice_options" aria-controls="invoice_options" role="tab" data-toggle="tab">Invoice Options</a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="general_settings">
                    <div class="panel-body">

                      <div class="row">
                        <div class="col-md-6">

						  <div class="form-group"><label for="invoice_design_id" class="control-label col-lg-4 col-sm-4">Invoice Design</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="invoice_design_id" name="invoice_design_id"><option value="1" selected="selected">Clean</option><option value="2">Bold</option><option value="3">Modern</option><option value="4">Plain</option><option value="5">Business</option><option value="6">Creative</option><option value="7">Elegant</option><option value="8">Hipster</option><option value="9">Playful</option><option value="10">Photo</option></select></div></div>
						  <div class="form-group"><label for="quote_design_id" class="control-label col-lg-4 col-sm-4">Quote Design</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="quote_design_id" name="quote_design_id"><option value="1" selected="selected">Clean</option><option value="2">Bold</option><option value="3">Modern</option><option value="4">Plain</option><option value="5">Business</option><option value="6">Creative</option><option value="7">Elegant</option><option value="8">Hipster</option><option value="9">Playful</option><option value="10">Photo</option></select></div></div>
                          <div class="form-group"><label for="body_font_id" class="control-label col-lg-4 col-sm-4">Body Font</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="body_font_id" name="body_font_id"><option value="1" selected="selected">Roboto</option><option value="2">Abril Fatface</option><option value="3">Arvo</option><option value="4">Josefin Sans</option><option value="5">Josefin Sans Light</option><option value="6">Josefin Slab</option><option value="7">Josefin Slab Light</option><option value="8">Open Sans</option><option value="9">Open Sans Light</option><option value="10">PT Sans</option><option value="11">PT Serif</option><option value="12">Raleway</option><option value="13">Raleway Light</option><option value="14">Titillium</option><option value="15">Titillium Light</option><option value="16">Ubuntu</option><option value="17">Ubuntu Light</option><option value="18">UKai - Chinese</option><option value="19">GenshinGothic P - Japanese</option><option value="20">GenshinGothic - Japanese</option></select></div></div>
                          <div class="form-group"><label for="header_font_id" class="control-label col-lg-4 col-sm-4">Header Font</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="header_font_id" name="header_font_id"><option value="1" selected="selected">Roboto</option><option value="2">Abril Fatface</option><option value="3">Arvo</option><option value="4">Josefin Sans</option><option value="5">Josefin Sans Light</option><option value="6">Josefin Slab</option><option value="7">Josefin Slab Light</option><option value="8">Open Sans</option><option value="9">Open Sans Light</option><option value="10">PT Sans</option><option value="11">PT Serif</option><option value="12">Raleway</option><option value="13">Raleway Light</option><option value="14">Titillium</option><option value="15">Titillium Light</option><option value="16">Ubuntu</option><option value="17">Ubuntu Light</option><option value="18">UKai - Chinese</option><option value="19">GenshinGothic P - Japanese</option><option value="20">GenshinGothic - Japanese</option></select></div></div>

                        </div>
                        <div class="col-md-6">

                        
                        

                          <div class="form-group"><label for="page_size" class="control-label col-lg-6 col-sm-6">Page Size</label><div class="col-lg-6 col-sm-6"><select class="form-control" id="page_size" name="page_size"><option value="A0">A0</option><option value="A1">A1</option><option value="A2">A2</option><option value="A3">A3</option><option value="A4" selected="selected">A4</option><option value="A5">A5</option><option value="A6">A6</option><option value="A7">A7</option><option value="A8">A8</option><option value="A9">A9</option><option value="A10">A10</option><option value="B0">B0</option><option value="B1">B1</option><option value="B2">B2</option><option value="B3">B3</option><option value="B4">B4</option><option value="B5">B5</option><option value="B6">B6</option><option value="B7">B7</option><option value="B8">B8</option><option value="B9">B9</option><option value="B10">B10</option><option value="C0">C0</option><option value="C1">C1</option><option value="C2">C2</option><option value="C3">C3</option><option value="C4">C4</option><option value="C5">C5</option><option value="C6">C6</option><option value="C7">C7</option><option value="C8">C8</option><option value="C9">C9</option><option value="C10">C10</option><option value="RA0">RA0</option><option value="RA1">RA1</option><option value="RA2">RA2</option><option value="RA3">RA3</option><option value="RA4">RA4</option><option value="SRA0">SRA0</option><option value="SRA1">SRA1</option><option value="SRA2">SRA2</option><option value="SRA3">SRA3</option><option value="SRA4">SRA4</option><option value="Executive">Executive</option><option value="Folio">Folio</option><option value="Legal">Legal</option><option value="Letter">Letter</option><option value="Tabloid">Tabloid</option></select></div></div>

                          <div class="form-group"><label for="font_size" class="control-label col-lg-6 col-sm-6">Font Size</label><div class="col-lg-6 col-sm-6"><input class="form-control" min="0" step="1" id="font_size" type="number" name="font_size" value="9"></div></div>

                          <div class="form-group"><label for="primary_color" class="control-label col-lg-6 col-sm-6">Primary Color</label><div class="col-lg-6 col-sm-6"><input class="form-control" id="primary_color" type="text" name="primary_color"></div></div>
                          <div class="form-group"><label for="secondary_color" class="control-label col-lg-6 col-sm-6">Secondary Color</label><div class="col-lg-6 col-sm-6"><input class="form-control" id="secondary_color" type="text" name="secondary_color"></div></div>


                        
                        

                        </div>
                      </div>

                      <div class="help-block" style="padding-top:16px">
                        Note: the primary color and fonts are also used in the client portal and custom email designs.
                      </div>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="invoice_labels">
                    <div class="panel-body">

                      <div class="row">
                        <div class="col-md-6">
							<div class="form-group"><label for="label_field" class="control-label col-lg-4 col-sm-4">Label</label><div class="col-lg-8 col-sm-8"><select class="form-control" onchange="onFieldChange()" id="label_field" name="label_field"><option value="" disabled="disabled" selected="selected">Select Label</option><option value="address1">Street</option><option value="address2">Apt/Suite</option><option value="amount">Amount</option><option value="amount_paid">Amount Paid</option><option value="balance">Balance</option><option value="balance_due">Balance Due</option><option value="blank">Blank</option><option value="city_state_postal">City/State/Postal</option><option value="client_name">Client Name</option><option value="company_name">CompanyPlan Name</option><option value="contact_name">Contact Name</option><option value="country">Country</option><option value="credit_card">Credit Card</option><option value="credit_date">Credit Date</option><option value="credit_issued_to">Credit issued to</option><option value="credit_note">Credit Note</option><option value="credit_number">Credit Number</option><option value="credit_to">Credit to</option><option value="custom_value1">Custom Value</option><option value="custom_value2">Custom Value</option><option value="date">Date</option><option value="delivery_note">Delivery Note</option><option value="description">Description</option><option value="details">Details</option><option value="discount">Discount</option><option value="due_date">Due Date</option><option value="email">Email</option><option value="from">From</option><option value="gateway_fee_description">Gateway Fee Surcharge</option><option value="gateway_fee_discount_description">Gateway Fee Discount</option><option value="gateway_fee_item">Gateway Fee Item</option><option value="hours">Hours</option><option value="id_number">ID Number</option><option value="invoice">Invoice</option><option value="invoice_date">Invoice Date</option><option value="invoice_due_date">Due Date</option><option value="invoice_issued_to">Invoice issued to</option><option value="invoice_no">Invoice No.</option><option value="invoice_number">Invoice Number</option><option value="invoice_to">Invoice to</option><option value="invoice_total">Invoice Total</option><option value="item">Item</option><option value="line_total">Line Total</option><option value="method">Method</option><option value="outstanding">Outstanding</option><option value="paid_to_date">Paid to Date</option><option value="partial_due">Partial Due</option><option value="payment_date">Payment Date</option><option value="phone">Phone</option><option value="po_number">PO Number</option><option value="postal_city_state">Postal/City/State</option><option value="product_key">Product</option><option value="quantity">Quantity</option><option value="quote">Quote</option><option value="quote_date">Quote Date</option><option value="quote_due_date">Valid Until</option><option value="quote_issued_to">Quote issued to</option><option value="quote_no">Quote No.</option><option value="quote_number">Quote Number</option><option value="quote_to">Quote to</option><option value="rate">Rate</option><option value="reference">Reference</option><option value="service">Service</option><option value="statement">Statement</option><option value="statement_date">Statement Date</option><option value="statement_issued_to">Statement issued to</option><option value="statement_to">Statement to</option><option value="subtotal">Subtotal</option><option value="surcharge">Surcharge</option><option value="tax">Tax</option><option value="tax_invoice">Tax Invoice</option><option value="tax_quote">Tax Quote</option><option value="taxes">Taxes</option><option value="terms">Terms</option><option value="to">To</option><option value="total">Total</option><option value="unit_cost">Unit Cost</option><option value="valid_until">Valid Until</option><option value="vat_number">VAT Number</option><option value="website">Website</option><option value="work_phone">Phone</option><option value="your_credit">Your Credit</option><option value="your_invoice">Your Invoice</option><option value="your_quote">Your Quote</option><option value="your_statement">Your Statement</option></select></div></div>
						</div>
						<div class="col-md-6">
															<div class="form-group address1-label-group label-group"><label for="labels_address1" class="control-label col-lg-4 col-sm-4">Street</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_address1" type="text" name="labels_address1"></div></div>
															<div class="form-group address2-label-group label-group"><label for="labels_address2" class="control-label col-lg-4 col-sm-4">Apt/Suite</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_address2" type="text" name="labels_address2"></div></div>
															<div class="form-group amount-label-group label-group"><label for="labels_amount" class="control-label col-lg-4 col-sm-4">Amount</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_amount" type="text" name="labels_amount"></div></div>
															<div class="form-group amount_paid-label-group label-group"><label for="labels_amount_paid" class="control-label col-lg-4 col-sm-4">Amount Paid</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_amount_paid" type="text" name="labels_amount_paid"></div></div>
															<div class="form-group balance-label-group label-group"><label for="labels_balance" class="control-label col-lg-4 col-sm-4">Balance</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_balance" type="text" name="labels_balance"></div></div>
															<div class="form-group balance_due-label-group label-group"><label for="labels_balance_due" class="control-label col-lg-4 col-sm-4">Balance Due</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_balance_due" type="text" name="labels_balance_due"></div></div>
															<div class="form-group blank-label-group label-group"><label for="labels_blank" class="control-label col-lg-4 col-sm-4">Blank</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_blank" type="text" name="labels_blank"></div></div>
															<div class="form-group city_state_postal-label-group label-group"><label for="labels_city_state_postal" class="control-label col-lg-4 col-sm-4">City/State/Postal</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_city_state_postal" type="text" name="labels_city_state_postal"></div></div>
															<div class="form-group client_name-label-group label-group"><label for="labels_client_name" class="control-label col-lg-4 col-sm-4">Client Name</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_client_name" type="text" name="labels_client_name"></div></div>
															<div class="form-group company_name-label-group label-group"><label for="labels_company_name" class="control-label col-lg-4 col-sm-4">CompanyPlan Name</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_company_name" type="text" name="labels_company_name"></div></div>
															<div class="form-group contact_name-label-group label-group"><label for="labels_contact_name" class="control-label col-lg-4 col-sm-4">Contact Name</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_contact_name" type="text" name="labels_contact_name"></div></div>
															<div class="form-group country-label-group label-group"><label for="labels_country" class="control-label col-lg-4 col-sm-4">Country</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_country" type="text" name="labels_country"></div></div>
															<div class="form-group credit_card-label-group label-group"><label for="labels_credit_card" class="control-label col-lg-4 col-sm-4">Credit Card</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_credit_card" type="text" name="labels_credit_card"></div></div>
															<div class="form-group credit_date-label-group label-group"><label for="labels_credit_date" class="control-label col-lg-4 col-sm-4">Credit Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_credit_date" type="text" name="labels_credit_date"></div></div>
															<div class="form-group credit_issued_to-label-group label-group"><label for="labels_credit_issued_to" class="control-label col-lg-4 col-sm-4">Credit issued to</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_credit_issued_to" type="text" name="labels_credit_issued_to"></div></div>
															<div class="form-group credit_note-label-group label-group"><label for="labels_credit_note" class="control-label col-lg-4 col-sm-4">Credit Note</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_credit_note" type="text" name="labels_credit_note"></div></div>
															<div class="form-group credit_number-label-group label-group"><label for="labels_credit_number" class="control-label col-lg-4 col-sm-4">Credit Number</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_credit_number" type="text" name="labels_credit_number"></div></div>
															<div class="form-group credit_to-label-group label-group"><label for="labels_credit_to" class="control-label col-lg-4 col-sm-4">Credit to</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_credit_to" type="text" name="labels_credit_to"></div></div>
															<div class="form-group custom_value1-label-group label-group"><label for="labels_custom_value1" class="control-label col-lg-4 col-sm-4">Custom Value</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_custom_value1" type="text" name="labels_custom_value1"></div></div>
															<div class="form-group custom_value2-label-group label-group"><label for="labels_custom_value2" class="control-label col-lg-4 col-sm-4">Custom Value</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_custom_value2" type="text" name="labels_custom_value2"></div></div>
															<div class="form-group date-label-group label-group"><label for="labels_date" class="control-label col-lg-4 col-sm-4">Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_date" type="text" name="labels_date"></div></div>
															<div class="form-group delivery_note-label-group label-group"><label for="labels_delivery_note" class="control-label col-lg-4 col-sm-4">Delivery Note</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_delivery_note" type="text" name="labels_delivery_note"></div></div>
															<div class="form-group description-label-group label-group"><label for="labels_description" class="control-label col-lg-4 col-sm-4">Description</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_description" type="text" name="labels_description"></div></div>
															<div class="form-group details-label-group label-group"><label for="labels_details" class="control-label col-lg-4 col-sm-4">Details</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_details" type="text" name="labels_details"></div></div>
															<div class="form-group discount-label-group label-group"><label for="labels_discount" class="control-label col-lg-4 col-sm-4">Discount</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_discount" type="text" name="labels_discount"></div></div>
															<div class="form-group due_date-label-group label-group"><label for="labels_due_date" class="control-label col-lg-4 col-sm-4">Due Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_due_date" type="text" name="labels_due_date"></div></div>
															<div class="form-group email-label-group label-group"><label for="labels_email" class="control-label col-lg-4 col-sm-4">Email</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_email" type="text" name="labels_email"></div></div>
															<div class="form-group from-label-group label-group"><label for="labels_from" class="control-label col-lg-4 col-sm-4">From</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_from" type="text" name="labels_from"></div></div>
															<div class="form-group gateway_fee_description-label-group label-group"><label for="labels_gateway_fee_description" class="control-label col-lg-4 col-sm-4">Gateway Fee Surcharge</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_gateway_fee_description" type="text" name="labels_gateway_fee_description"></div></div>
															<div class="form-group gateway_fee_discount_description-label-group label-group"><label for="labels_gateway_fee_discount_description" class="control-label col-lg-4 col-sm-4">Gateway Fee Discount</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_gateway_fee_discount_description" type="text" name="labels_gateway_fee_discount_description"></div></div>
															<div class="form-group gateway_fee_item-label-group label-group"><label for="labels_gateway_fee_item" class="control-label col-lg-4 col-sm-4">Gateway Fee Item</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_gateway_fee_item" type="text" name="labels_gateway_fee_item"></div></div>
															<div class="form-group hours-label-group label-group"><label for="labels_hours" class="control-label col-lg-4 col-sm-4">Hours</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_hours" type="text" name="labels_hours"></div></div>
															<div class="form-group id_number-label-group label-group"><label for="labels_id_number" class="control-label col-lg-4 col-sm-4">ID Number</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_id_number" type="text" name="labels_id_number"></div></div>
															<div class="form-group invoice-label-group label-group"><label for="labels_invoice" class="control-label col-lg-4 col-sm-4">Invoice</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_invoice" type="text" name="labels_invoice"></div></div>
															<div class="form-group invoice_date-label-group label-group"><label for="labels_invoice_date" class="control-label col-lg-4 col-sm-4">Invoice Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_invoice_date" type="text" name="labels_invoice_date"></div></div>
															<div class="form-group invoice_due_date-label-group label-group"><label for="labels_invoice_due_date" class="control-label col-lg-4 col-sm-4">Due Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_invoice_due_date" type="text" name="labels_invoice_due_date"></div></div>
															<div class="form-group invoice_issued_to-label-group label-group"><label for="labels_invoice_issued_to" class="control-label col-lg-4 col-sm-4">Invoice issued to</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_invoice_issued_to" type="text" name="labels_invoice_issued_to"></div></div>
															<div class="form-group invoice_no-label-group label-group"><label for="labels_invoice_no" class="control-label col-lg-4 col-sm-4">Invoice No.</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_invoice_no" type="text" name="labels_invoice_no"></div></div>
															<div class="form-group invoice_number-label-group label-group"><label for="labels_invoice_number" class="control-label col-lg-4 col-sm-4">Invoice Number</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_invoice_number" type="text" name="labels_invoice_number"></div></div>
															<div class="form-group invoice_to-label-group label-group"><label for="labels_invoice_to" class="control-label col-lg-4 col-sm-4">Invoice to</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_invoice_to" type="text" name="labels_invoice_to"></div></div>
															<div class="form-group invoice_total-label-group label-group"><label for="labels_invoice_total" class="control-label col-lg-4 col-sm-4">Invoice Total</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_invoice_total" type="text" name="labels_invoice_total"></div></div>
															<div class="form-group item-label-group label-group"><label for="labels_item" class="control-label col-lg-4 col-sm-4">Item</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_item" type="text" name="labels_item"></div></div>
															<div class="form-group line_total-label-group label-group"><label for="labels_line_total" class="control-label col-lg-4 col-sm-4">Line Total</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_line_total" type="text" name="labels_line_total"></div></div>
															<div class="form-group method-label-group label-group"><label for="labels_method" class="control-label col-lg-4 col-sm-4">Method</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_method" type="text" name="labels_method"></div></div>
															<div class="form-group outstanding-label-group label-group"><label for="labels_outstanding" class="control-label col-lg-4 col-sm-4">Outstanding</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_outstanding" type="text" name="labels_outstanding"></div></div>
															<div class="form-group paid_to_date-label-group label-group"><label for="labels_paid_to_date" class="control-label col-lg-4 col-sm-4">Paid to Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_paid_to_date" type="text" name="labels_paid_to_date"></div></div>
															<div class="form-group partial_due-label-group label-group"><label for="labels_partial_due" class="control-label col-lg-4 col-sm-4">Partial Due</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_partial_due" type="text" name="labels_partial_due"></div></div>
															<div class="form-group payment_date-label-group label-group"><label for="labels_payment_date" class="control-label col-lg-4 col-sm-4">Payment Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_payment_date" type="text" name="labels_payment_date"></div></div>
															<div class="form-group phone-label-group label-group"><label for="labels_phone" class="control-label col-lg-4 col-sm-4">Phone</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_phone" type="text" name="labels_phone"></div></div>
															<div class="form-group po_number-label-group label-group"><label for="labels_po_number" class="control-label col-lg-4 col-sm-4">PO Number</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_po_number" type="text" name="labels_po_number"></div></div>
															<div class="form-group postal_city_state-label-group label-group"><label for="labels_postal_city_state" class="control-label col-lg-4 col-sm-4">Postal/City/State</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_postal_city_state" type="text" name="labels_postal_city_state"></div></div>
															<div class="form-group product_key-label-group label-group"><label for="labels_product_key" class="control-label col-lg-4 col-sm-4">Product</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_product_key" type="text" name="labels_product_key"></div></div>
															<div class="form-group quantity-label-group label-group"><label for="labels_quantity" class="control-label col-lg-4 col-sm-4">Quantity</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_quantity" type="text" name="labels_quantity"></div></div>
															<div class="form-group quote-label-group label-group"><label for="labels_quote" class="control-label col-lg-4 col-sm-4">Quote</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_quote" type="text" name="labels_quote"></div></div>
															<div class="form-group quote_date-label-group label-group"><label for="labels_quote_date" class="control-label col-lg-4 col-sm-4">Quote Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_quote_date" type="text" name="labels_quote_date"></div></div>
															<div class="form-group quote_due_date-label-group label-group"><label for="labels_quote_due_date" class="control-label col-lg-4 col-sm-4">Valid Until</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_quote_due_date" type="text" name="labels_quote_due_date"></div></div>
															<div class="form-group quote_issued_to-label-group label-group"><label for="labels_quote_issued_to" class="control-label col-lg-4 col-sm-4">Quote issued to</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_quote_issued_to" type="text" name="labels_quote_issued_to"></div></div>
															<div class="form-group quote_no-label-group label-group"><label for="labels_quote_no" class="control-label col-lg-4 col-sm-4">Quote No.</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_quote_no" type="text" name="labels_quote_no"></div></div>
															<div class="form-group quote_number-label-group label-group"><label for="labels_quote_number" class="control-label col-lg-4 col-sm-4">Quote Number</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_quote_number" type="text" name="labels_quote_number"></div></div>
															<div class="form-group quote_to-label-group label-group"><label for="labels_quote_to" class="control-label col-lg-4 col-sm-4">Quote to</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_quote_to" type="text" name="labels_quote_to"></div></div>
															<div class="form-group rate-label-group label-group"><label for="labels_rate" class="control-label col-lg-4 col-sm-4">Rate</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_rate" type="text" name="labels_rate"></div></div>
															<div class="form-group reference-label-group label-group"><label for="labels_reference" class="control-label col-lg-4 col-sm-4">Reference</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_reference" type="text" name="labels_reference"></div></div>
															<div class="form-group service-label-group label-group"><label for="labels_service" class="control-label col-lg-4 col-sm-4">Service</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_service" type="text" name="labels_service"></div></div>
															<div class="form-group statement-label-group label-group"><label for="labels_statement" class="control-label col-lg-4 col-sm-4">Statement</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_statement" type="text" name="labels_statement"></div></div>
															<div class="form-group statement_date-label-group label-group"><label for="labels_statement_date" class="control-label col-lg-4 col-sm-4">Statement Date</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_statement_date" type="text" name="labels_statement_date"></div></div>
															<div class="form-group statement_issued_to-label-group label-group"><label for="labels_statement_issued_to" class="control-label col-lg-4 col-sm-4">Statement issued to</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_statement_issued_to" type="text" name="labels_statement_issued_to"></div></div>
															<div class="form-group statement_to-label-group label-group"><label for="labels_statement_to" class="control-label col-lg-4 col-sm-4">Statement to</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_statement_to" type="text" name="labels_statement_to"></div></div>
															<div class="form-group subtotal-label-group label-group"><label for="labels_subtotal" class="control-label col-lg-4 col-sm-4">Subtotal</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_subtotal" type="text" name="labels_subtotal"></div></div>
															<div class="form-group surcharge-label-group label-group"><label for="labels_surcharge" class="control-label col-lg-4 col-sm-4">Surcharge</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_surcharge" type="text" name="labels_surcharge"></div></div>
															<div class="form-group tax-label-group label-group"><label for="labels_tax" class="control-label col-lg-4 col-sm-4">Tax</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_tax" type="text" name="labels_tax"></div></div>
															<div class="form-group tax_invoice-label-group label-group"><label for="labels_tax_invoice" class="control-label col-lg-4 col-sm-4">Tax Invoice</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_tax_invoice" type="text" name="labels_tax_invoice"></div></div>
															<div class="form-group tax_quote-label-group label-group"><label for="labels_tax_quote" class="control-label col-lg-4 col-sm-4">Tax Quote</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_tax_quote" type="text" name="labels_tax_quote"></div></div>
															<div class="form-group taxes-label-group label-group"><label for="labels_taxes" class="control-label col-lg-4 col-sm-4">Taxes</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_taxes" type="text" name="labels_taxes"></div></div>
															<div class="form-group terms-label-group label-group"><label for="labels_terms" class="control-label col-lg-4 col-sm-4">Terms</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_terms" type="text" name="labels_terms"></div></div>
															<div class="form-group to-label-group label-group"><label for="labels_to" class="control-label col-lg-4 col-sm-4">To</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_to" type="text" name="labels_to"></div></div>
															<div class="form-group total-label-group label-group"><label for="labels_total" class="control-label col-lg-4 col-sm-4">Total</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_total" type="text" name="labels_total"></div></div>
															<div class="form-group unit_cost-label-group label-group"><label for="labels_unit_cost" class="control-label col-lg-4 col-sm-4">Unit Cost</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_unit_cost" type="text" name="labels_unit_cost"></div></div>
															<div class="form-group valid_until-label-group label-group"><label for="labels_valid_until" class="control-label col-lg-4 col-sm-4">Valid Until</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_valid_until" type="text" name="labels_valid_until"></div></div>
															<div class="form-group vat_number-label-group label-group"><label for="labels_vat_number" class="control-label col-lg-4 col-sm-4">VAT Number</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_vat_number" type="text" name="labels_vat_number"></div></div>
															<div class="form-group website-label-group label-group"><label for="labels_website" class="control-label col-lg-4 col-sm-4">Website</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_website" type="text" name="labels_website"></div></div>
															<div class="form-group work_phone-label-group label-group"><label for="labels_work_phone" class="control-label col-lg-4 col-sm-4">Phone</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_work_phone" type="text" name="labels_work_phone"></div></div>
															<div class="form-group your_credit-label-group label-group"><label for="labels_your_credit" class="control-label col-lg-4 col-sm-4">Your Credit</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_your_credit" type="text" name="labels_your_credit"></div></div>
															<div class="form-group your_invoice-label-group label-group"><label for="labels_your_invoice" class="control-label col-lg-4 col-sm-4">Your Invoice</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_your_invoice" type="text" name="labels_your_invoice"></div></div>
															<div class="form-group your_quote-label-group label-group"><label for="labels_your_quote" class="control-label col-lg-4 col-sm-4">Your Quote</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_your_quote" type="text" name="labels_your_quote"></div></div>
															<div class="form-group your_statement-label-group label-group"><label for="labels_your_statement" class="control-label col-lg-4 col-sm-4">Your Statement</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="labels_your_statement" type="text" name="labels_your_statement"></div></div>
							                        </div>
                      </div>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="invoice_fields">
                    <div class="panel-body">
                      <div class="row" id="invoiceFields">
                          <div class="col-lg-3 col-md-6">

    <select class="form-control" onchange="addField('invoice_fields')" id="invoice_fields_select" name="invoice_fields_select"><option value="" disabled="disabled" selected="selected">Invoice Fields</option><option value="invoice.invoice_number">Invoice Number</option><option value="invoice.po_number">PO Number</option><option value="invoice.invoice_date">Invoice Date</option><option value="invoice.due_date">Due Date</option><option value="invoice.invoice_total">Invoice Total</option><option value="invoice.balance_due">Balance Due</option><option value="invoice.partial_due">Partial Due</option><option value="invoice.outstanding">Outstanding</option><option value="invoice.custom_text_value1">Custom Field</option><option value="invoice.custom_text_value2">Custom Field</option><option value=".blank">Blank</option></select>

    <div class="table-responsive">
        <table class="field-list">
        <tbody data-bind="sortable: { data: invoice_fields, as: 'field', afterMove: onDragged, allowDrop: true }">
            <tr style="cursor:move;background-color:#fff;margin:1px">
                <td>
                    <i class="fa fa-close" style="cursor:default" title="Remove"
                        data-bind="click: $root.removeInvoiceFields"></i>
                    <span data-bind="text: window.field_map[field]"></span>
                </td>
            </tr>
        </tbody>
        </table>
    </div>

</div>
                          <div class="col-lg-3 col-md-6">

    <select class="form-control" onchange="addField('client_fields')" id="client_fields_select" name="client_fields_select"><option value="" disabled="disabled" selected="selected">Client Fields</option><option value="client.client_name">Client Name</option><option value="client.id_number">ID Number</option><option value="client.vat_number">VAT Number</option><option value="client.website">Website</option><option value="client.work_phone">Phone</option><option value="client.address1">Street</option><option value="client.address2">Apt/Suite</option><option value="client.city_state_postal">City/State/Postal</option><option value="client.postal_city_state">Postal/City/State</option><option value="client.country">Country</option><option value="client.contact_name">Contact Name</option><option value="client.email">Contact Email</option><option value="client.phone">Contact Phone</option><option value="client.custom_value1">Custom Field</option><option value="client.custom_value2">Custom Field</option><option value="contact.custom_value1">Custom Field</option><option value="contact.custom_value2">Custom Field</option><option value=".blank">Blank</option></select>

    <div class="table-responsive">
        <table class="field-list">
        <tbody data-bind="sortable: { data: client_fields, as: 'field', afterMove: onDragged, allowDrop: true }">
            <tr style="cursor:move;background-color:#fff;margin:1px">
                <td>
                    <i class="fa fa-close" style="cursor:default" title="Remove"
                        data-bind="click: $root.removeClientFields"></i>
                    <span data-bind="text: window.field_map[field]"></span>
                </td>
            </tr>
        </tbody>
        </table>
    </div>

</div>
                          <div class="col-lg-3 col-md-6">

    <select class="form-control" onchange="addField('account_fields1')" id="account_fields1_select" name="account_fields1_select"><option value="" disabled="disabled" selected="selected">CompanyPlan Fields</option><option value="company.company_name">CompanyPlan Name</option><option value="company.id_number">ID Number</option><option value="company.vat_number">VAT Number</option><option value="company.website">Website</option><option value="company.email">Email</option><option value="company.phone">Phone</option><option value="company.address1">Street</option><option value="company.address2">Apt/Suite</option><option value="company.city_state_postal">City/State/Postal</option><option value="company.postal_city_state">Postal/City/State</option><option value="company.country">Country</option><option value="company.custom_value1">Custom Field</option><option value="company.custom_value2">Custom Field</option><option value=".blank">Blank</option></select>

    <div class="table-responsive">
        <table class="field-list">
        <tbody data-bind="sortable: { data: account_fields1, as: 'field', afterMove: onDragged, allowDrop: true }">
            <tr style="cursor:move;background-color:#fff;margin:1px">
                <td>
                    <i class="fa fa-close" style="cursor:default" title="Remove"
                        data-bind="click: $root.removeAccountFields1"></i>
                    <span data-bind="text: window.field_map[field]"></span>
                </td>
            </tr>
        </tbody>
        </table>
    </div>

</div>
                          <div class="col-lg-3 col-md-6">

    <select class="form-control" onchange="addField('account_fields2')" id="account_fields2_select" name="account_fields2_select"><option value="" disabled="disabled" selected="selected">CompanyPlan Fields</option><option value="company.company_name">CompanyPlan Name</option><option value="company.id_number">ID Number</option><option value="company.vat_number">VAT Number</option><option value="company.website">Website</option><option value="company.email">Email</option><option value="company.phone">Phone</option><option value="company.address1">Street</option><option value="company.address2">Apt/Suite</option><option value="company.city_state_postal">City/State/Postal</option><option value="company.postal_city_state">Postal/City/State</option><option value="company.country">Country</option><option value="company.custom_value1">Custom Field</option><option value="company.custom_value2">Custom Field</option><option value=".blank">Blank</option></select>

    <div class="table-responsive">
        <table class="field-list">
        <tbody data-bind="sortable: { data: account_fields2, as: 'field', afterMove: onDragged, allowDrop: true }">
            <tr style="cursor:move;background-color:#fff;margin:1px">
                <td>
                    <i class="fa fa-close" style="cursor:default" title="Remove"
                        data-bind="click: $root.removeAccountFields2"></i>
                    <span data-bind="text: window.field_map[field]"></span>
                </td>
            </tr>
        </tbody>
        </table>
    </div>

</div>
                      </div>
                      <div class="row" style="padding-top:30px">
                          <div class="pull-left help-block">
							  Drag and drop fields to change their order and location
                          </div>
						  <div class="pull-right" style="padding-right:14px">
                              <button type='button' class='btn btn-default btn-sm' onclick='sweetConfirm(function() {
                                        resetInvoiceFields();
                                    })'>Reset</button>
                          </div>
                      </div>
                    </div>
                </div>
				<div role="tabpanel" class="tab-pane" id="product_fields">
                    <div class="panel-body">
  						<div class="row" id="productFields">
                            <div class="col-lg-6 col-md-6">

    <select class="form-control" onchange="addField('product_fields')" id="product_fields_select" name="product_fields_select"><option value="" disabled="disabled" selected="selected">Product Fields</option><option value="product.item">Item</option><option value="product.description">Description</option><option value="product.custom_value1">Custom Field</option><option value="product.custom_value2">Custom Field</option><option value="product.unit_cost">Unit Cost</option><option value="product.quantity">Quantity</option><option value="product.discount">Discount</option><option value="product.tax">Tax</option><option value="product.line_total">Line Total</option></select>

    <div class="table-responsive">
        <table class="field-list">
        <tbody data-bind="sortable: { data: product_fields, as: 'field', afterMove: onDragged, allowDrop: false }">
            <tr style="cursor:move;background-color:#fff;margin:1px">
                <td>
                    <i class="fa fa-close" style="cursor:default" title="Remove"
                        data-bind="click: $root.removeProductFields"></i>
                    <span data-bind="text: window.field_map[field]"></span>
                </td>
            </tr>
        </tbody>
        </table>
    </div>

</div>
                            <div class="col-lg-6 col-md-6">

    <select class="form-control" onchange="addField('task_fields')" id="task_fields_select" name="task_fields_select"><option value="" disabled="disabled" selected="selected">Task Fields</option><option value="product.service">Service</option><option value="product.description">Description</option><option value="product.custom_value1">Custom Field</option><option value="product.custom_value2">Custom Field</option><option value="product.rate">Rate</option><option value="product.hours">Hours</option><option value="product.discount">Discount</option><option value="product.tax">Tax</option><option value="product.line_total">Line Total</option></select>

    <div class="table-responsive">
        <table class="field-list">
        <tbody data-bind="sortable: { data: task_fields, as: 'field', afterMove: onDragged, allowDrop: false }">
            <tr style="cursor:move;background-color:#fff;margin:1px">
                <td>
                    <i class="fa fa-close" style="cursor:default" title="Remove"
                        data-bind="click: $root.removeTaskFields"></i>
                    <span data-bind="text: window.field_map[field]"></span>
                </td>
            </tr>
        </tbody>
        </table>
    </div>

</div>
                        </div>
                        <div class="row" style="padding-top:30px">
                            <div class="pull-left help-block">
  							  Drag and drop fields to change their order
                            </div>
  						    <div class="pull-right" style="padding-right:14px">
                                <button type='button' class='btn btn-default btn-sm' onclick='sweetConfirm(function() {
                                          resetProductFields();
                                      })'>Reset</button>
                            </div>
                        </div>
					</div>
				</div>
                <div role="tabpanel" class="tab-pane" id="invoice_options">
                    <div class="panel-body">

													<div class="form-group"><label for="background_image_id" class="control-label col-lg-4 col-sm-4">Background Image</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="background_image_id" name="background_image_id"><option value=""></option></select><span class="help-block">Use the <a href="/proposals/create?show_assets=true" target="_blank">proposal editor</a> to manage your images, we recommend using a small file.</span></div></div>
						
						<div class="form-group"><label for="hide_paid_to_date" class="control-label col-lg-4 col-sm-4">Hide Paid to Date</label><div class="col-lg-8 col-sm-8"><div class="checkbox"><label for="hide_paid_to_date" class=""><input type="hidden" name="hide_paid_to_date" value="0"><input id="hide_paid_to_date" type="checkbox" name="hide_paid_to_date" value="1">Only display the "Paid to Date" area on your invoices once a payment has been received.</label></div></div></div>
						<div class="form-group"><label for="invoice_embed_documents" class="control-label col-lg-4 col-sm-4">Embed Documents</label><div class="col-lg-8 col-sm-8"><div class="checkbox"><label for="invoice_embed_documents" class=""><input type="hidden" name="invoice_embed_documents" value="0"><input id="invoice_embed_documents" type="checkbox" name="invoice_embed_documents" value="1">Include attached images in the invoice.</label></div></div></div>

						<br/>

						<div class="form-group"><label for="all_pages_header" class="control-label col-lg-4 col-sm-4">Show Header on</label><div class="col-lg-8 col-sm-8"><label for="all_pages_header2" class="radio-inline"><input value="0" id="all_pages_header2" type="radio" name="all_pages_header" checked="checked">First page</label><label for="all_pages_header3" class="radio-inline"><input value="1" id="all_pages_header3" type="radio" name="all_pages_header">All pages</label></div></div>

						<div class="form-group"><label for="all_pages_footer" class="control-label col-lg-4 col-sm-4">Show Footer on</label><div class="col-lg-8 col-sm-8"><label for="all_pages_footer2" class="radio-inline"><input value="0" id="all_pages_footer2" type="radio" name="all_pages_footer" checked="checked">Last page</label><label for="all_pages_footer3" class="radio-inline"><input value="1" id="all_pages_footer3" type="radio" name="all_pages_footer">All pages</label></div></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <center class="buttons">
		<a class='btn btn-primary btn-lg' href='http://invninjv1.local/settings/customize_design?design_id=11'>Customize <span class='glyphicon glyphicon-edit'></span></a>
        <button type='submit' class='btn btn-success save-button btn-lg'>Save <span class='glyphicon glyphicon-floppy-disk'></span></button>
	</center>

      <input type="hidden" name="_token" value="IH4RfjHyCuiL4BTW0mJXyy0httbYIu2gFF50M3a6"></form>

    </div>
  </div>


      <object id="pdfObject" type="application/pdf" style="display:block;background-color:#525659;border:solid 2px #9a9a9a;" frameborder="1" width="100%" height="800px"></object>
<div id="pdfCanvas" style="display:none;width:100%;background-color:#525659;border:solid 2px #9a9a9a;padding-top:40px;text-align:center">
    <canvas id="theCanvas" style="max-width:100%;border:solid 1px #CCCCCC;"></canvas>
</div>
<canvas id="signatureCanvas" style="display:none;"></canvas>



<script type="text/javascript">
  window.logoImages = {};

  logoImages.imageLogo1 = "data:image/jpeg;base64,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L2ZmAAEAbGZmAAYAAAAAAAEAL2ZmAAEAoZmaAAYAAAAAAAEAMgAAAAEAWgAAAAYAAAAAAAEANQAAAAEALQAAAAYAAAAAAAE4QklNA/gAAAAAAHAAAP////////////////////////////8D6AAAAAD/////////////////////////////A+gAAAAA/////////////////////////////wPoAAAAAP////////////////////////////8D6AAAOEJJTQQAAAAAAAACAAA4QklNBAIAAAAAAAQAAAAAOEJJTQQwAAAAAAACAQE4QklNBC0AAAAAAAYAAQAAAAQ4QklNBAgAAAAAABAAAAABAAACQAAAAkAAAAAAOEJJTQQeAAAAAAAEAAAAADhCSU0EGgAAAAADTQAAAAYAAAAAAAAAAAAAADAAAACrAAAADAByAGUAcABvAHIAdABfAGwAbwBnAG8AMQAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAAQAAAAAAAAAAAAAAqwAAADAAAAAAAAAAAAAAAAAAAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAAQAAAAAQAAAAAAAG51bGwAAAACAAAABmJvdW5kc09iamMAAAABAAAAAAAAUmN0MQAAAAQAAAAAVG9wIGxvbmcAAAAAAAAAAExlZnRsb25nAAAAAAAAAABCdG9tbG9uZwAAADAAAAAAUmdodGxvbmcAAACrAAAABnNsaWNlc1ZsTHMAAAABT2JqYwAAAAEAAAAAAAVzbGljZQAAABIAAAAHc2xpY2VJRGxvbmcAAAAAAAAAB2dyb3VwSURsb25nAAAAAAAAAAZvcmlnaW5lbnVtAAAADEVTbGljZU9yaWdpbgAAAA1hdXRvR2VuZXJhdGVkAAAAAFR5cGVlbnVtAAAACkVTbGljZVR5cGUAAAAASW1nIAAAAAZib3VuZHNPYmpjAAAAAQAAAAAAAFJjdDEAAAAEAAAAAFRvcCBsb25nAAAAAAAAAABMZWZ0bG9uZwAAAAAAAAAAQnRvbWxvbmcAAAAwAAAAAFJnaHRsb25nAAAAqwAAAAN1cmxURVhUAAAAAQAAAAAAAG51bGxURVhUAAAAAQAAAAAAAE1zZ2VURVhUAAAAAQAAAAAABmFsdFRhZ1RFWFQAAAABAAAAAAAOY2VsbFRleHRJc0hUTUxi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bj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTExIDc5LjE1ODMyNSwgMjAxNS8wOS8xMC0wMToxMDoyMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpEb2N1bWVudElEPSIwRTY5RjNCMTQxQjFGODQ3N0RFOEFEMzZFMTA4M0I4QyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoxYTg5NDhiYy00NGRiLTQzN2QtYjY5NS1lNmE4M2MzYTNiYzciIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0iMEU2OUYzQjE0MUIxRjg0NzdERThBRDM2RTEwODNCOEMiIGRjOmZvcm1hdD0iaW1hZ2UvanBlZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMyIgeG1wOkNyZWF0ZURhdGU9IjIwMTYtMDUtMjZUMDk6NDI6MjctMDU6MDAiIHhtcDpNb2RpZnlEYXRlPSIyMDE2LTA1LTI2VDA5OjQ5OjAyLTA1OjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDE2LTA1LTI2VDA5OjQ5OjAyLTA1OjAwIj4gPHhtcE1NOkluZ3JlZGllbnRzPiA8cmRmOkJhZz4gPHJkZjpsaSBzdFJlZjpsaW5rRm9ybT0iUmVmZXJlbmNlU3RyZWFtIiBzdFJlZjpmaWxlUGF0aD0iY2xvdWQtYXNzZXQ6Ly9jYy1hcGktc3RvcmFnZS5hZG9iZS5pby9hc3NldHMvYWRvYmUtbGlicmFyaWVzLzRiNjk0M2ZkLWFjMjctNDc1MS1hNjU2LTRjMjI3NDBkNjI4Yztub2RlPTYwMWIyZmRhLTdhMjMtNDUzNS1hN2Q0LWI3YTQ2YmRkZGNmMiIgc3RSZWY6RG9jdW1lbnRJRD0idXVpZDpjNDcwZGI2Ni00MDg5LTA2NGItOTE1YS0yYTRmYzY0MmE3MmQiLz4gPC9yZGY6QmFnPiA8L3htcE1NOkluZ3JlZGllbnRzPiA8eG1wTU06SGlzdG9yeT4gPHJkZjpTZXE+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJzYXZlZCIgc3RFdnQ6aW5zdGFuY2VJRD0ieG1wLmlpZDoxYTg5NDhiYy00NGRiLTQzN2QtYjY5NS1lNmE4M2MzYTNiYzciIHN0RXZ0OndoZW49IjIwMTYtMDUtMjZUMDk6NDk6MDItMDU6MDAiIHN0RXZ0OnNvZnR3YXJlQWdlbnQ9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChNYWNpbnRvc2gpIiBzdEV2dDpjaGFuZ2VkPSIvIi8+IDwvcmRmOlNlcT4gPC94bXBNTTpIaXN0b3J5PiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3hwYWNrZXQgZW5k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";
  logoImages.imageLogoWidth1 =120;
  logoImages.imageLogoHeight1 = 40

  logoImages.imageLogo2 = "data:image/jpeg;base64,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cm9wUmVjdFJpZ2h0bG9uZwAAAAAAAAALY3JvcFJlY3RUb3Bsb25nAAAAAAA4QklNA+0AAAAAABAASAAAAAEAAQBIAAAAAQABOEJJTQQmAAAAAAAOAAAAAAAAAAAAAD+AAAA4QklNBA0AAAAAAAQAAAAeOEJJTQQZAAAAAAAEAAAAHjhCSU0D8wAAAAAACQAAAAAAAAAAAQA4QklNJxAAAAAAAAoAAQAAAAAAAAABOEJJTQP1AAAAAABIAC9mZgABAGxmZgAGAAAAAAABAC9mZgABAKGZmgAGAAAAAAABADIAAAABAFoAAAAGAAAAAAABADUAAAABAC0AAAAGAAAAAAABOEJJTQP4AAAAAABwAAD/////////////////////////////A+gAAAAA/////////////////////////////wPoAAAAAP////////////////////////////8D6AAAAAD/////////////////////////////A+gAADhCSU0EAAAAAAAAAgAAOEJJTQQCAAAAAAAEAAAAADhCSU0EMAAAAAAAAgEBOEJJTQQtAAAAAAAGAAEAAAAEOEJJTQQIAAAAAAAQAAAAAQAAAkAAAAJAAAAAADhCSU0EHgAAAAAABAAAAAA4QklNBBoAAAAAA00AAAAGAAAAAAAAAAAAAABRAAABRQAAAAwAcgBlAHAAbwByAHQAXwBsAG8AZwBvADIAAAABAAAAAAAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAUUAAABRAAAAAAAAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAEAAAAAEAAAAAAABudWxsAAAAAgAAAAZib3VuZHNPYmpjAAAAAQAAAAAAAFJjdDEAAAAEAAAAAFRvcCBsb25nAAAAAAAAAABMZWZ0bG9uZwAAAAAAAAAAQnRvbWxvbmcAAABRAAAAAFJnaHRsb25nAAABRQAAAAZzbGljZXNWbExzAAAAAU9iamMAAAABAAAAAAAFc2xpY2UAAAASAAAAB3NsaWNlSURsb25nAAAAAAAAAAdncm91cElEbG9uZwAAAAAAAAAGb3JpZ2luZW51bQAAAAxFU2xpY2VPcmlnaW4AAAANYXV0b0dlbmVyYXRlZAAAAABUeXBlZW51bQAAAApFU2xpY2VUeXBlAAAAAEltZyAA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cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1wTU06RG9jdW1lbnRJRD0iRDJCNzRBQUU1NjZDODk0NUZENjVEMzYwMUNFMTYxODAiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MTk3NzhlOTctNDFiMi00NDRlLTliYWEtODdkMDJmZDViMzVkIiB4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ9IkQyQjc0QUFFNTY2Qzg5NDVGRDY1RDM2MDFDRTE2MTgwIiBkYzpmb3JtYXQ9ImltYWdlL2pwZWciIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiIHhtcDpDcmVhdGVEYXRlPSIyMDE2LTA1LTI2VDA5OjQyOjI1LTA1OjAwIiB4bXA6TW9kaWZ5RGF0ZT0iMjAxNi0wNS0yNlQwOTo0NzoxMy0wNTowMCIgeG1wOk1ldGFkYXRhRGF0ZT0iMjAxNi0wNS0yNlQwOTo0NzoxMy0wNTowMCI+IDx4bXBNTTpJbmdyZWRpZW50cz4gPHJkZjpCYWc+IDxyZGY6bGkgc3RSZWY6bGlua0Zvcm09IlJlZmVyZW5jZVN0cmVhbSIgc3RSZWY6ZmlsZVBhdGg9ImNsb3VkLWFzc2V0Oi8vY2MtYXBpLXN0b3JhZ2UuYWRvYmUuaW8vYXNzZXRzL2Fkb2JlLWxpYnJhcmllcy80YjY5NDNmZC1hYzI3LTQ3NTEtYTY1Ni00YzIyNzQwZDYyOGM7bm9kZT02MmQyMjUzNy1kNGRmLTQxN2YtODM1OS0wOGM1YjAzN2U1MDQiIHN0UmVmOkRvY3VtZW50SUQ9InV1aWQ6ODk1NzdlZGYtZjU2YS00NzQ2LWEwZTItYTYyMmNiNzcyOTVjIi8+IDwvcmRmOkJhZz4gPC94bXBNTTpJbmdyZWRpZW50cz4gPHhtcE1NOkhpc3Rvcnk+IDxyZGY6U2VxPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MTk3NzhlOTctNDFiMi00NDRlLTliYWEtODdkMDJmZDViMzVkIiBzdEV2dDp3aGVuPSIyMDE2LTA1LTI2VDA5OjQ3OjEzLTA1OjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8L3JkZjpTZXE+IDwveG1wTU06SGlzdG9yeT4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD94cGFja2V0IGVuZD0idyI/Pv/uACFBZG9iZQBkQAAAAAEDABADAgMGAAAAAAAAAAAAAAAA/9sAhAABAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAgICAgICAgICAgIDAwMDAwMDAwMDAQEBAQEBAQEBAQECAgECAgMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwP/wgARCABRAUUDAREAAhEBAxEB/8QA5AABAAEFAQEBAQEAAAAAAAAAAAoGBwgJCwUDBAECAQEBAQEBAAAAAAAAAAAAAAAAAQMEAhAAAAQEBAMFBgUCBwAAAAAAAQIICQMFBgcAEQQKEBIyIDAxEzlAUGBBIjMhIzcYGUM1FBYXOBo6ShEAAAcAAQIEAwMGCAwHAAAAAQIDBAUGBwgREwASFAkhFRYxIhcQQVEjtxgzsyR0tnd4uCAwQFAydtY4iJgZeWBhQnIlyIkSAAICAQEFBQcDBQAAAAAAAAABEQIhEDFBUXESYcEiMgMgQFDwgeGCMHCRYKGx0UL/2gAMAwEBAhEDEQAAAImM7wAAAAAL/pc5LzJZJ6o4u0nzKgP0lQJrve8nU+Z55TRjuoAAAAAAAAAAAAm13njZzXpZ3l0ntI67Scu54lTa5hlY81QuEMt97LgmVLzBinTh6oAAAAAA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";
  logoImages.imageLogoWidth2 =325/2;
  logoImages.imageLogoHeight2 = 81/2;

  logoImages.imageLogo3 = "data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAVkAAABYCAYAAABf2YTKAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4AYdEjUNNIEC1QAAIABJREFUeNrsnXl4G9W9979nRhp5OZKdUSRPNkOYIcQxINM0dDFLWAqElvayJBS6kFtqswVTA2moX5ZQbt2kgYQGAsFqgZaWlAS6BRqSlpJb4ktLCNeiOE7oHBY7y1hCgy0dbyNpzvvHWE563/dteF4KXO6jz/M4jiXN0cxZfud3fud7ziGPi2/JF1x+b5iiUVi0k2i8USRqOx3dFlmoJEjtSoVDSwGmBBgu1XiEWzRFgQgHUkaLKXWvJceEMS8A7E6uid9mNzUtExRGGA6PcoV246NGZVWUz5huOX0pzYmdzvsSv53ynJ7PNkPlQOr9JB1sgwQAok0Lg1pIqMzRVy4apk27qrnKHNj64JFTMSWUk2l0pHIU0AAAm+4z7YXXG+/7/kqUKPHRIl3QvLlAJ0+bQ9qtNKNAorbTifU2KgAAWx/k6pBDHdQDhguVVXHLSlOuRaAoUcZYwLyB+APQTcBOgjfgzGW3UAoa7lNZGVlOe/47PCS1YtO5o3RrNCaI8rlfQtfHspdr6pp4k/1+0xYtPJxvZ9M43ZEGLOi2PoimJwocSMEW2fd0fzDCVI6NronfZsNBNKEyZ0HZpRJXmVOqoiVKfLwhoik2n7PEi7SxJgTTiAJaUmt7Mm2tpWFQA6AWiPU1W+BXKgBwIIVyNuPBESSXYp7B1Z37YIssY8Qfi50bIi1mWrRpYU6tCW/3o31EU/I8cl3hMNMURpjDTJeDTHsNGNOhJ9+XAQciq8AyS2ksxBOJDHSRg0qC1IkpFk8QeoT0KaURriQcAKC2rsBBlCvoptSIXF2zNXM308dK1bREiY+xJwuY3dD1MW5Zaa1tRw/os+inBkibluacp8A1COdXswFgFVgGKqvCiN63FPMMWL1Jas+bDk4m6/NQBrsrKpajDrQLUFkVuPbRP+G4gQUAWkMm8/j6AQAYUZHRGQbfd/oOoteoKONrjhswfihyVKmto865SiKRyGi8URzpcu0bXWlq1UynvG46KAUUJAFT4txMre+JGaUqWqLEx9zIrsJQhipKPQBYG7VwHxtysu0msutZkAIRWBRw8hFQA9dwVNHeRiWIU328fGcGGkWC7TSpFhNsJ0Z5VX8GqpEENUDtSsUbQn+0jNnj3jSl4BkEcObnJAojjJWLho0fitz7HgosRw+1dQXLNlaYawHuKN1YvATHPTcwz75/h3qk662Hzwsn+vrNwv3/GeSrXzqOtCFNYYQppREoPFmqoiVKfMyN7DkqysAVAIYLi2NGLKaAk8mw9cFN95k2r+10YIx289UbKioeE59E279dV3jwkXPoDTt1LLl33FJQ6LrI0ZF5oVfXmFnSwtPQKCiM8Ef9gGHEpsfjTfKaNZfZGNH7gj/Wc1CUaByfcM04mfZ+08+2w+XxuQOw9UHKTwkDQO7E8z/lR/CFSap725GuTyS2ZmJ1dYacD/yeCuWFVzaZfg6kEkrCKcVkS5T4+EPEFXo9V9BN4Zuvtez5k7XWCK+JN9lnnnmLL6brIdz48HXu7NMulIATIVwIIoEAgABcgYKUenMvApMWafuqe8yfkeDmFd/il934vMqtBHmQYvAafLQxxXi8SW697iez4ThJDqQoEEkwlonV1Rmw1SSn1vuOGVMHXh46vF672eqxpr99MaqnbRTC9/jQVcZX/uG1xZju2uwoyig2jcG34A5CvRhySVlQosTH3pPlCrqhsio4+dQvniFBAMg33eIrf25Azne8vhazT7tDEoUTCy5exkDqAQDfwut/2VQg7mPSyLsyamqn8Gp2wGq/JAxbH7xsyU9mg3NQtS5yT8v7H46/X5rEGWH3/teewCVL6rdsMeWJYbrTkwK13nf6FIiQ5eihQIQrtLufGshVzxgR8CEn3lsa97SIHAI0UwCw8DvnqZTXTQcsAKa/VEVLlPiYG1nAm9VepTDz4LJvcVgUS3ljaBbKfyJD+rJA/h248lnyN4zFWNa4nDTTx4nylS/LzbNu2rRtUlkO/Z+ml98wFUpXFAC4o3YnGMvAcZLmWk/2hfhcPwUih77WlCjXIlBZFWBKFFaEKkp9PN4kQ2VVjLEABSLBNkjFH6oo9RRW5O+vQ8R7HREKtR4AwFm06MEylVWV4fjzJSHV2+c09S9cYKhai5mO1TSGNBoTXvzTigTbIHn3uX7iPinXIhP3CivCGAt4RtX6+/cdHu1az4IcSFGuRbLtcL3rXPjJeygB3oCutWQySIHIADasrrO5s7sHvAHBNqMwcQ/UOOx7TelQno7nHxDZct+hTgQqq4rHm2QKtZ4CkQfBAkVNL0V+fjF/S02gRIkPwchyxjJLaaVx9sJ7p3KtK114fOtZAsolZHQIZNuTC/n/Iru4gm7ShjSHlqrhJgpgZQu3GCFly1uMU6t7ex3bBwCMsUBM10OkDZ6eduWi4V2iA7zjqoHDjSCv7XRgi2w5yDSuDjnt9+w2dfH5ctj6YKyyJsSBlGhHePXMJpJdw6Zxx+nWWniatFtp2GKYOkbdho7lQ/9+/4uvQ3GiHHY3ALCLMEh5Q6S16S4VOzHqe3fwCgBQhyqlBGMZa+P4ooFEIiPaEQZvwOqZTYT2Nio483PS6x0dQ2gxy3l/Z+ZBsEA8vpIAgK7rY9TiEQ4t9UYcZZxaKQ4WJcrX9sTUy6YH2yDx2k4vhlqQJIj3Zr9W0acyGo6aAyEDAphz1r0+wHBBLWSP3iXv6tjlp6DhBN9KKKX1if7ODAeZjKZdOa4yh6kkyOOfH+Aw0wv/9RSVajxCgUh85Qre2vq8CuxO8nJWtlSpM8Ry1FFqRBLs7RdBDWTXs2CpCZQo8cFCxBV6PagCvu6E17eMPuEuesWQxEmvdaFMmSOQv2moWbmX0oYw511pDjKZQrxD858+De5bKa7Q7mAbpGw7XKisivY2KuKxHVvIUPIN6+aaJdq9f5uDiqPuEq7/s0SCJDB2xb+vLPvN/L4Z0+Eoydc7OoYM98zvSM7IVVDKI+N+lZkv4LFX5daVzzavcduUObN4T49JaxpD//rIDvshP76kWOwuTK2dIeAPEgHAPgChTl21heRvP//GLwR5Ymum8rmhUwkqlgNoLACQR0dShbKytDxkh0Vl9WtDzcedSbkW4dRKvdbxa3oy5tyUh/imAjIdAFwhvS2N8Ee/U9G/4kTcn7ts8fOzuZbYB1tkqaaFU999KRA5OPYYtGPnCoIgUCiQQXtvrmpSawE5UibKn4UzvIlff+Kif1gCKqtiOzEa28xHRUWlWI1LA9fhsevLDh64R0w5WhCACBdvk5c3v3nnyZXnzRk7M3/R9WQq6SiskVzpBLyy7VY8/u3twdXcPthsCspPCUPjgM2juUf3jvkP7v8bptR2Jm4hnz/3qyJrxsk0OjIvxPnOFEWj+GfEpEuUKPGPPFkFSe7s7kHTrtyCskslkTirCWXKHOQKGIIbBwzX4glCHa1OU+oiRguAUV83FJqkilIv1hphyvPzKfTpUKyoK2OWCEWOCXfkGuCf8TyEv05I+AOGB3c7I4E981mlArs2eaDj1YLhnrmDSLg1X1Y2CEk8hHxuk0Ch0ifjzgas/m0bbajmjtMd/LGe4+gkDwdyZysSnhJTjzk6D+kvBPmHCgQboE7NEIGl57/+am8isTVDY7EQOWCdgsF3ckDhXRku8mXlrxDkeuDkugWwl2o8AsWKouOJSZ8eNf4iCWm5MjIacEEeygvpcUIgo4Le0S7031x24/MqV3g3dc71VsI1Px2NFGpfEVOOnZ8nuYMFYD0c+VFRNbngh7S1DGUXCpJHQak48qTfTozGahpDbkUlJwL8RvHEYwH4VqG8bLdA/mEIdxMBqgsnXzD/jsGGVxZ+Z7E6oiIj2dZrIJhVOOmcCxJKwskuprPLQaaBdoHwz9lcMXsIpMtdrRZIbJ2kF1eflSPD2U6T1WLsnxGTLlGixBE82a5lqGqwFw9l8QgBjJyy3lykSO4TLvCX4eZZn/b2LAAAw43Hm+SmZbdQAPCWjBouB4tqNBbhnHdTrkXcx3e8IZB35Oy7KoKTH+KvkVa6dvanfoy9/3klxmfMVVZFb8/EUVG5EG/2bOczf3cOXXzPLKhasnv1y/k6+DZLAo05Ulg21nzcD4orx4J1bYr43Kw2VDy9LrFiJYnxRkHoZ2zxhZpT3S/etFkShHJy8HjcMnUftSsVQIPo+NsWIshch6Pe+R7ZT3fHFGhKNMF2mroucvSOvc9h2nHzgfxDuPGbt/HETzI0FjP46hsPUvfrz0DCpwVGbxm6pXw9ehGgtFIUHuK/JKRwChHyj8h1n/x5X2gXm2FXjmotPH3w+NGlBGUrAADJfdv5rfPP+EcFUHw2em+XQEUZMDwMvHPgDGtD3W7KxDvBNgPZo5+oqsSJW4nwz3Nd3C4tPW89X/10nubytuv382HkptLbjy5bc1er/eroMtx3B6G0t1FxH9/xEoF7NICTyOKpOSg8Cd6ATY/ssBfeNGc2d5zuUhMoUeID9mRjdqWSVR+thEoqwFhAeeXpSwQAAfE2AFCltg4qCUJlVa1XxGdTJ6bA1gepY9QxxgIadMGtRIoxFoDGIY1kqASfKoKT/6C9Rq6lDzfQBNv7n1fSxumABWOLKQdW/HUyysoWYiAFbPreEtp8Z3Wir9+EjWj+Fr8r/frBx0EAv51eSYHIpvtMGyqrEn0bq3D3JZv4ihXpBtycBgCB35/66pfW/1k6aL4siAtFTDmJZmpOAgAOMw3X05w5FHkA4BpPkTa7J6ZXhso7ClMxTZ9fgNiHbU/eBv4kaKzR4ImE+RCeyRak3LcgAAGlJXi1nqXqjAjv4FMkglOkrI0tJH898gPdM2x9FNDQtZZMvpf88G4XhVcAIB/VDh6xl2tD+kF4k2qAD3h79yb+b1/YTrdhkPJTwqIdYXr5Db4hSFeCAJDzSzZ9/1mbLj7xbLzTt51Apn6UfTnR2Z9pbd2gPnK9oVJbV8Yef/4YSRSOFhB/JYtn5bSbrZ6EOuTw/s7MguuNApzeZGniq0SJDyNcwBsQX7mCU1tXUIMqHNuQAiQQVx6kilMPUBhfFVnjqyILR0uCc3CwKNfYPl0XOdKG9GufFgOxxppQcQ0+EUAeybvNtYZLWnhanwcklM59HDxtLTBUYM7nIPnhVk+Kc/PxHtKmpWONNSHelzD/sGLRMP/dPQ8UBApQJ4N37CpfcL1RYADSfGsVV2h3EEuJUJ6ZzWs7Hc4SL554Gw+irHI3ERIUF1XBB+gLHDwdbDMAAiHggg7Dz20EysFmdK1nQXLhNlvOjX4Rwgc5k3oRj98a7VOHymB7Q+ibLt9f7XurbicKOUiQpoq3flDHnd4eatsXEeHCDVbFT2uePcphpjnMNCjFlDb9nRvfjAspTzYDgC95cMqRCqD3ShZcCj1UCNBhARfo+fftFFYEusiBWgDXwKmV4it9/bD2gUCKfilwYA4UZ/tA9NhbCQA/3Ct1Xbi8kCiDg2iCsUwgmbpJEBnS3pdfgGIlrbVGWAcAXR/zvGctlb2SlSRiJUp8wPg4tVJNTXEAFqh2rshVTXnbD4D0vtpIblauzq7vCVpraZjDm9nPtrHJWvXcOWtWLnzhsiYyWSzXI4k+Ygaf0+2DzZYAMgABxsBfAmMBsVw3oMaSOucpwAAHUjRHToAfkOz0v9DvucfmI3uThe9KqCgQ0loApO+/HRFE5ABJpgf+dgyAkZitgzPW97fzzEBu6UBjTqw6iVpvfApCihSm1r4jodBABODKvHCQzJWwjFSIlhoFDx+YgclTgcSvIxr0JAf6dBvI/uqzfndBvo4AIOXV9bjjVxunzKx/tVCQ5EoJriAQrgBBfgQQfozN/1IFHgfyKtV8AIRQXvMWIRjd1OH1nFs94mEtjF5EseLNJDQd+ciMA0cqgHk/FFnrbq1OlpAXkLDnorN+Mfu38SlUoQA1QNqstGi3IjTRKETN9KcJ8AVAPQaA5bsGL+IOE2TaMZ9SOgb1wFv669l20xr5EgjKMwtJwQH+88UHuDrkwMYgtfUIBzC+BSOorodKCx5KlPiAPVnqKAuDbZC4OuSk+raWkYKcFADco4/PZtvhevvJ8jQFDYt2y1smm9/T3dS0TGjQBVfMnhodVdlmqBRGGGWhLFyBLWN6BvNQxhV09yUSDoVaz8GimzsukOGOeBrTilCkEKk5UXblc2S4nxOyOKsgu2eL6qNigBgGcjbY3kga3mYz5c/lLzrpqv5RGdXPyUR8H1NmnuBOrS2XQKrJOwcMEACCyuVNGzWq1CjQKER4ah8AOJ89w4KKaDzeJAOmxFRSQSBNIgBcv1zrHn3CVBnuAiK7ZxPgbCLwOUHcc+ArT+eI+27gl6vnUX5KWBZKEAAEcd71VsohwhWrhzpGHbdoClSFG9X3A4BM3CMuR7DWGmGAokAKhAAYxs+HuIJuOFqU8660aEeYg6fH5WFDgAvF6p22oeMCm8IIw6/cSeBCOdD/3dUzm0g5iHZyy+jnEAwBRPkl/+P93cU9bScM6n/9u0SJEh+cke3Tev688VSTUFtXIjV1oVEXO4kLSAX3U3TJvREotVGjBeDqkMOhpbDh9++CN+BlnQR5fO4AAGjQBYeZHt91S4AQLPzOeWqxMc+IxRTOdpoajQmLaECgfABwkfNXLpEvnz97jbxyct9Zvplyy7EXK83LIkPXGCq58fzZvLkujMfu2L7vHPQ5j+w4TRby426opp+8tee64Z+TUOIsqWH4FvlcsvjYb42Fj7pHAAAZK8iOHoKtRsE1iAJ8QgBKfkyGjWRr010qxcl1MT5jOilIwxCAlBtdJt3UMOvVs/w10pKjT+VXIcq/Q3RpyfGnFq4iJ401S5Pf3PXtbVDtqCAjowUQV4JTSR3UkzakKT0vzBV0B9sgWU5PSiJjCgA4EPKRCoDH5w5AUSHnZQIAn3ggZwCmxBWzh4KGi9szwtYHCR+oASQ4mn7wAnJASqjM6Y5O/aELMupOnXWaJizITk1IIPB1ABiR3v1JqYqXKPERG9kZTmx00QuGIG1Idyo9qeB+owe5gZSQfRg+8dQruLO7x1rbcBq1KxXGWIBetryaUys16V2AXvfiLOrTToMDzxB73hYByWPT95+11xaXhVILXEcVVxJOU1O8UMAYAyRAdmev2XCB3dq6QZ00D+Dd7MUf4G4fVFZFWsz0lvtMGRpHbJseUkZT3xUEEBj6Fp95R/zdv5II5qGM2QhwxewJEBEiAKQckaAguWbdFXt4pLOMyMgT4gJc9Tw33oAE27nPcvpSBTnPQFxACtTzNccNxPR5Brf7Ulu2gASv1rOw1aTMG0eNFmBmSh/kzu4eKU/6ZAhJyuZP0G42e4rePQeLZtvhvnCfSKP7pfmAC1nI7pEKgC3bWLEKO03kMhRwse/alTJguME2Axw8DRgTaYhg1VQIwAfxdvDNXYWYpU+vb672EcgbJaDmsuT1F73xqPU2AfkCsimU33jGi6UqXqLER2xkuZJwxHJaJ5ajLqZgrOtK5heB6u8RAVSMKaucDnac1tL1JwA45Tk9D8WKUvjm6wDgKMnE3v4XUcuT5lrI1K5UMDYcLMAHAGiDUU2h1vPO/oyGeZGgfTOnsCLyG11/KQDwZ1JLjhPz/FxJONSqmQ5duEuVSQa1dSXbbsoLr6cqv+tnA4AFlJVHCgAKyHfTpmeqZ9gxPbY7psSUuggDmSyQO0MAwiWK4ECq9bY1Kk01jgqCEUAC7C5vGaliRXENRjWlLpLH0O8FJAgZi/4kfi5z2N2UVooFXyFU3K3UAUBfbWeZdbdRxzkjgOEKn7tDQBJOsPJ0a60RhlIbhdmFKW36O5RrkVl3EOoe/9loAYDPyR1xYkm3RfYa6GNw/RCAO0VUzqdARLQjDNXbS4IzlqHfvHU+QGZheBDD+3y7s+tZEIqVBDQMIv8QBIBC4dpjxNgCCW5FgUZ+AM5LNbxEiY/ayAav1rOotZJcQTftbVT+8Nwi9xtjuB8jg7uhlEPlVbsPHD+0gIOnszcaKhQkeTzW6cVqzbT+OxHiTr9DcfIs9A8RiDwkQCwIrJK4yhyOl16Hro9tuu+lPUJ7TOXQUljx1W5J4I8iFHHPx/yfbV7xWI5r/fsAI8fpu/s4zPSfOpbLuGVPFMs+VwFocAPK33yiAKVQddqrcS0LqnWTtTzNnd09se+/fQeBYgAgki9PwFgAthoFtSDlcwcg8kCusFC0I8wVdOvbhAtuI3DjpX0Q2Ebgm3wu5J/gdrPMwhChTkzhzu6eAu0se3jFc0n+iJmjuFhQjUek5jueJy7eVOCvx/I/XMd7esxNmyz7B+3MD85x3ApxIgEulCABOKIjiy33gTCwKEgBREiS7OZu5R27ygE1GjgIb6+G5wbc3LzFy4gARCVdjRmXFmDrgwl1yOEw028/6d8liPgrtGNPKCBwKQCMkOxPvU1mSpQo8VFC8KYueZMr3gYnpN1Ki3YrjFv2wT2m+lcS0AgAeKcP+fCMR33OwDCsN+eI6ScNC1k0AOTN4VvI54NX61mx1ggXVm9hMkiwvRlKC4ycJxcyvc27OZlMlbrTueJsGunomj4Jyg5ZKEeRd99BXg095NtvFQrTphXk7MAJ8PnnF8qDG+XmY5cQXGgX1q/6uiS5DwtIw+Sdt59B1ZTtrh9TpFH3Ires7Ggh8CNCCi1SjlyHZedvAt8BDi0lP2B+ocyHzSQ3ioKv7AcuGXxbzlVpw7eRe2CLLDpsjY4pXQjQiIB7UED8RsqLfuFDNcnyGIJVMSHItiHp0q/RpmeqN3Q02RdixWkBEdhCiBtwkf+tQOGPcnL/JBExjiWuezne/utuHBObk3exYfRq4/IjloLKqspXiD55JDvqlgffkoYz89yKivtzkF9XgHKSF1dAInMwMpDileU6e7Lcif3y4hDvfyozpmNG2KkJYMUrC6FNvQPCRYHgz689Kc+PbTvD4OgtLTgoUeKj9GSzxR2kFKWeUysl1tIwML2ed0xynhrD6TmIq13gFYSnwkewGIHqa3HUSfMh43xpLO+Hi+eppZ+zemYTATchCyIAoI0urqZcmzjjK9hmgFJdaDfvforCihw4K5jy/aB5sSBj94vqyQM++K4S02qvlYV8vRtU5ztloZ0AnuIqc7JYJaSrZ7+JZO+dBIUxhKcvhF9ZJwnlVsgCOWTny/tf7pCEDPhdbNr0rA1ooECkcC1+R0adO12/LycT99t+UbVOKgzdFrxaz2KMHEObz5zVF3jn2Bzc+4mQKiUhXy180h3E9d2AYPX8AsjfXOBXaNqVA78E+5rv9ZU1h18bI4NnI5ffTaB8UUb5vYgecweAcwqSdANXYnMLyBdkCUfc6pExFvAmCAUK5RWbepA/F317d7vAVQFB1gJiJVCYI6Tcxj9X7j8BN5RX6wsXuaBmPXR9LADd5ArtRvLAOiEwJghAHOlnf1y4Ig/0lk5WKFHio/Zks006ANMPlVQU1QDUQT1Zjp5sO9zi1n68YyCvYGi6gFOZQ1mW/vKH4C/dto9a+vQNj16w57Lmzeohg+oZ73eB0CRb76MO6hN9zIzVNIYStZ1OzNYVrjLH+KrIWmuN8L/eZ9qPjORnQxqIYN8eYHK+G7d+E4dvsq3/cZGLZRsrvnKsGP7NzEcbx868eCgnggdwlbHf2+bQTkLjgEWROIdl9G36GKU0As4BruF3j2/PnoahYysRlMl/PLwfj64AaUO6az0L6rY+yBgLxBprQrhxX/Td6tFgBQpOIHmwj9/6hRQYC2AeynAQERo+dxC26R12yLVI6pEdgUj/W4ZTW+h/Za3Ojj9AyrmNAIWefG8HSXrHga8dEVYbaDWoAW4lSPrxjSNadqHhDw5I0qtmL+7/MsAbANqFhMom8hArFw3Tpmeqf9cxkD0f5ICAVE7e+GGttaKVUA6A6iVDW6LER29kS/x3hYNFKfQkVFYFWx8MtkES7QhD4+CWlfY+QyZr3917CbRZ6wrABrnZuCGhMkcHJjSxJUqU+IjCBaUs+O8NhXiHQq33lBmIZNvYZDg0ii4NgOFSGGEK8Q5U/ToBFyKHB0ENxOzYSVi5aLiUgyVKlIxsiX9kZPkpYQ67WziPzeYw04ggkFASKTRYoA6v/3PHr0cqO8Y6hF+eQwpj20dvI68mPrs1k2CJF2nrYHUpB0uUKIULSvwDjBZT8jba1kdJG9LZdrjgLEo7+POispKSwmitkMtAckMp7q88iZ1NeEzXvX1vKQXnvLR0tkSJkidb4v+FtdYIs9eQBDUg2hFGgBmsH4MODWwko6NvoP/N3SBi2Z93VB6Dq4z9sZpGxdMnI8UTiUwpB0uUKHmyJf5RuACIcJU5W+4UfMH5xBf/44p862WbVVALRW0zaBdATwHnZgowJTDipzN0AypNljzZEiVKnmyJfwAHUtSuVBacT3zsGqD1tjUqNA4eXz9ANR7h1EqBGoDF4Z3eO6eOXQMk+phZWlZbokTJyJZ4T5a2AeU6ovo2keNOv4NBe/4UcbXLLZqiXIvwXKKqeCAi7+kxG7bdnNfnoQwOoqXMK1GiFC4oUaJEiZInW6JEiRIlSka2RIkSJUpGtkSJEiVKRrZEiRIlSpSMbIkSJUqUjGyJEiVKlIxsiRIlSpT4pxtZSmmEWjwClVV5L5l+DjYuYjel4qbdAEBhRSgQYYwFAAAqq2Iqq6KKUg8AlBoRCkQopREAoBb1/v4Af7z7NiXKtQgFvMMSVVZFHV5vtJhS8X4pEGFg0XKwGcVnKz4z5Q3j6VkRyrX6l/XxvFBZVTFN6qAeKquiXPP+BiKAKXnPbvqLaQXbih3XofRLvA8OK18vb02JWnyiTj4Ir2wBb+9dqKwKjAXWwvRP1NOPMRSIxONN8ng9kwBTKgebQR1aX2wDwTZI8XiTTIEIU1mVV9+tiPf590B8rp9yzWsnDq+fqPvjrzGVVVHeEKGKU0/7WZRybSL/N3dcIJeDzZhalZU7AAAgAElEQVRoezClYjkc/rkPMn+MFq+tUfjmF8s82AYJjAXAWICd4/2mUOsPb7vvOX/eJ2Q1Vsitt61RE539GV3Xx6iDeq6xfdiJ0XIdUV+bvt879npOlOOlHsaIP1ZXZ/B1J7xOL9t/aCu94lr68dMIEoxl9Hko+8A3jS4ayt5GhdMdacBwKdcinO5IU9Awh5aiilJPnM/vEfSvKiwKQmfaWfyqcOjkAlMKtnkbsJA2pMVyXgeqAraaBLUAaoBzc+IMtGwbm0y1KwT4DgBAsAN2djFmb3rO3LNwgaFyxjK0sSbkbaptuCVL+f4aUXEDcmrrCrgGwDt+nvY2KgCQ6O/M6LrIURhhzliG1tUZ3O5JUUWPcAUf6zPOGGOBmK6HOGeE1WIsxmdMh6MktS2mvXUBCcegi+IJIhxmmoNM1pQZEdgnJ0GfBYf2D/euoBaNkLU8/YN25l+KyhD4eeC1TzkAUDy5BFwDqIU18Sa7qSleYIwFdF3kwIif6nqo+D5nLEN1PeSd7rGVfBinclAgQtrgnUvIG8CplWIqq4rZulJst/F4k9x63brZiZ6+fTE9dhJp43/KrmfBchuhEeh9H7iRFa2xCGnhafFv0tVcen09ADCVBJ+xxfDhByEyRvwxvTIEpTba19Ozb0bdjOncUboPf1jPmzjsuJXx3fw/0Ex2sBBOw3ZOu1JFg8t2YjQWOzdk8a1E442CU8urhIxlqF4ZAqbXc+S3U1gROFqUa2wfVWqUVVZ/ZqlSZ3DH6fYMtZWiilOf6Okzix0Lmp4oUGVOHbgNKDzJ+RBBLcY8j3jG9DXrrttz5rJbaGx3TEGtHT08j0r8/zUiz4h4RwxxlTnKd8WZfkXcKZA/QYLv3QLEj7fCve38Gz8ZhGWA066UN7Kikf8JG+QwxgKx2LkhwEJfIuH8+XeCL7yeqoBRv6HjjBcuaF4j4vGVpPWKDbOh8GSCsUwsFgtp3+hKm2uP0MkzFqB1Mww4SvJLnzIHfv4XI4f4XD+9bH91cal2cROidvCBQzbBc058He6NASHdSvK5Ktfve0i68aTbwE3A0aLazWbPEb//n+BkcRsBjZ4rOH82TWGEOcw0GPHTHz6/GseffhmEOwkj2U284t6v4/I7Q5RWCkCDd8DrB+8EkWyT7mWiYQKXPA10PR6F4gC2BlAbaDgzCY0C0IBnO6I474tJ8AbwY2ieJnf4AA5YAD/xvDz90eqpGH49wlvWJ2jS9MHiQNdvP9D18/zrVxwAALr2jhhvuTNBf7puKkDBv377AfrTb0/FeV9M7lXmu8c5T0t49o9RnPfF5MGK+WJK8hcEtAH47W+jwW927Dl4FvNhHspgi2w8vpK03rZGTVj9ZBsweA30MQpEwDUkajsd3dYHKzrMhFTI+ZCxI+6kyZZkpzQQAlFd8xvpbeOqriuZX9f1sZKZ/OcZWsCU6EPiYoBsFAAIAQAXEBJcgqf/eha5RJ+HMmrrCoc5gPilQNOu3Mf++RWlnjtON4UVgVIbTfT0mLquj9HWjmtF3elNpM9sQKgqlaua9AN/c/gn5MJtdtfdjb6YXhk6kifrhbVIELY+SBWnHvfsvV8E5Aix01Exacpvhq4ymg45TCILRvy0rs7gtGdf2Qq3wQexHa6AkGSMF0cL/kX7BTQODv7hjOTG7y0eX0mamuIF2s+iiB+cI6La896xroAgABx8m/yb9qi2yEqbJ1wqf1h1g4grauq51r8vvnIFb90cvij3+W/W+nv+424x51MFAlkWcEFcCSBAgQhXhpAACQLwMlWMV3QCCEggAgBxx9//kOfVhAsQCQUA8njjm3iLePcrAJDcKIRf+UNeSM8KF1uda4zXih6rrgsX5USjI/ooh5mm/JQwr+10qBWbDmoDjpIEANFhJouZQMSh9PPA877btUthGuD+zioEdLNkJt9HJwoW7VcxFuttnA6NJ8Xql14GlBlEgIB4Vc+VBAgEyKvPL8Lqm3ZDMZOcDWXwP6CTK57pxoEUVFYVvFrPZtezIO1tVLDudwvFpNA6InIQ8EMQkZMGBk/i3/5kd3GLzCONJCdGbJRGLJ4g2vf39wt1KggBBPIvkCUnXgNbTU6MBmGmoZIgtXXF6dhzlQzfXRJcEDFuI4ANG9H6tcsu36kWPeEPvBMaD3FSPmM6HDvJoaXKO/a2SpBXu3AhCwkgLjBob8fSoxetwlBmqVJnFDurD/r+pERfv0l7G5XWpkdO1Y7+5lP+28ljuPfr0aGkXI30wUWk4D6AkXdTggAyhARXAOKQgc0TQBBpvL9wx9+QPjQDK/7+ccb/dQ/1XsQz+m4Bb+CVbbsFRr/A/Zmg9Nasc/1X0Z8q1xj9RospcUfpPl5HlMKopoW6UIKxzEQvbIss1xL7uNPbk1CZAwdRAoAQdzwfBAhcQAAyXF+isz/D6Y40AuKNkpl8v16snoz1Nk7ntZ373OVdPkCpJXAJiFc0BcmFJAiIKECceOZMrvBuACjXEf2wJjY+0Pq9nNYlGMtQrkVob6Mi2hGmtq6QdisNKePVP+IHIS4kEH+uuuI+itp6rjKH9l6sHCl9i3aSYBskznmKQk8iqKZA3PGGJYE7Trc3vzEebqMNYW4jANB6n+v7q+x9bqIZyns6fXPOutente1IUwf1H3j94NpE3J2v+8zrGzqabApE5K4XxogAJIwbWEhAaPKfOHh6KW8McdqzL9ZYE/owylCK1TSGYJjgbO+L5s9IkPeLdziQwq0G1w5OeQpfm78cy2JnHMC+Y1whLckJ+WnkhlCAZ8B8Iud5xELyerP/w/B9wK54cTAiCiiQonsuAaMjICj8CgUswat/Ol5e2vBpvv7a+uHm45/BLTUy2i8kxRiytZaGqaLUj0BYvJyV/bK/Z18sFgtRIGLRTlIOMg22PkgdrU63RZYr6BZeX+P9SMQrRM/Yug0/1nOegS5Nev1zWpKVpHalIt29+DgiChCQIMYbtjzRmftB3thbAZgSadPSMvTRYJvx8ffkFd6tz0MZaBeCj1s2V5kDruHF35o+tyo6WqyE7rhb7xfKGeKBP55EbV3R2p5MHyl9DfMi2XYTxdl3zz/xvFIyOBAGgM0drV7L0vUxcA4NugB491M5PI3UgU1EeANYF2IbenPXxepmGMtOWEk+jEnHibixE1PQ9EThguY1AlxD4qYzfgwn+wARBQhXAgas3Zz852rqGHVeldKnw6IfShlKoBa4RVPQ9TH6/dFzKGjYk16YkrWWhvM/21GOk29MTmuez/96NvmR/xrjG1zJK/LfXlhUwOgPkMdu4ubgkmKS+QkvV+CDtzFCcl13PEAnj47uxlvdm0YJTnul7NYyXH76VfwaYx2e/HISDZeAfmHJ/OJknMCv1DXx22x0bA+4D/FGOL1JqKQCI2L/RZX6dHAOaBz9DIOyEwsZLabEFdpNYYQZYwFCPGNOBEDgep2OJIDBTCTbDve/yt9K/P/H2xKMZTi0VKLzJy+CD2wnwgUByOG9eYEUhmE+uwkAvHPQNHiqmP8B7MQoh5Y62IwCtXWFUyv16deMKmnw3TIxPlKTQCY6HjKcfgz/9iO8l0mnBNtpGi0AbawJ8fjM10VZ2XAxjomQmqRA5LLLd6rbdeYcUjAgZYGRWecTHxk+7cv468vRd0jvDKn52K/iyW+CO0p3U1O8UJwM/+BHO/n5nHd5YQwYYdJupWN6ZYhff9J1ePCG44cIpvNvayfQ5lOVVQoztbYdaShIkjakPxQj683cWhGq8QhIYCPW83jypzt8RfnTNimfxCnnLXfXmw/GNiev5B0JiTZPq8Yd39k+0nz8MlxXN98d8E+RIK6AM7YJmeyEi+mFDLz4bLFBCHg20ft/HhNhhv86/v87d9id+FX4+1f7yVjuKZfg6xzZCFqOn8/bv7Qo34zOT9y+IYTFl0Tptzcux3Jrvfjqrd/AAO+GLbLUla4lbUi3jt10Ks3QPmnwwPfIhdts2tuoUE0LWwobP22AQtf1Ma7wbnOt4RYr2UQcpxiOgAS5GK4IqUmv4A1PPnZYRaOwIkX9IFWU+uJ7Rd2epzdU6+PxJpmprOqQjtfTAG/uuEAu6vzi8SYZANi4lhcAtmwx5Ql94mHXF68J4kLv+n4Wnfjucf2v1ykgMnHt+KTI4TrhQ5pUrwPx7sua0A3/nXb4ML3i/23YSB3UFzXNRY3nxL1zFi1qLjGCkK4LF/G5/piuh/Di75eAkD0T+Q8AhRzkNxJL+ZP3d0MlQcTn+r1RipYCY4Hi80FlVRwsyhgLGC2mp6MsPvd/1V/jMM3zhObT0+uO543/v+qmD88rjkN60kPP74UvvM/65hfTY8X0x++rmK/FPPy/xZa9iUC/F6Yav03ijsepQ2G40dPvmqhbh+m1i3WpmLau62PmWsPlFk1NTAQV8zWTinIgxamV+iTTB73v9Aw3hZ7UdX0su54F+f1fTh0466jUofsq5u2hPCzq0JnKqooaX8ZYYCKPKI0AplTUtBfz1NO6W5FimVIgQh1MaOC9NunbTnFyHbW9A0S71rMgHBqlQCTRu24fvanBoaDhDR1N9lLeGLLWGmGvMz40XRLEhV4Tnqh7gKcZPhRy8ia/vTUEW7aYh+rseHua0OCO64qL+T4uHuYD3KKp8Qz+Utgn9uKWP19HqRE5//L5wcKKuhWS+Yf7RcXkb1CUW7h3/3bc07G8cp15IgdScsZIksVTdvE7ypo2BSb5xlA4Gc7onQLuC8KzkAKHhvJSsQwJfCggDzFujA+N/93DDK3rvS8AIRUK8gEz5SB/m5vHJ6UltWfx6+sXjTYbjwXfmmtDcaJKh3kiveE/foFb3tiOs294zTU+cb4jDS4faiPr+Y5HvUrS/ttNwr/5OSjS8whWRArVNXb2/B9KUKwot2hKgy5gcXCVOe+3F9NazLRXQL754A140MYop1aK3Oz0aC1m2mgxJShOlPJTwr0BBBJsp9naukHVbZGFLbKMsQA07xiZ1tfWCNGmhQELN74ZFyjqAbkGyrXIwgVUZTsxmqjtdGDrg2wnRinXIgfb2WRYFMJ5bHbXlcyPmsoJbSVZjh70IgAY7iqwDGm30ocmSwzXAiOrZzYRrrF9S7WaEKzxI214AyzaSXj88wNeZbVQnBThjtNN62YYUFlVduZcGQqShxteqvEI19g+0W6F2U6MtjY9NTvBWMa7D4CqMyLZmXNlwHAxovcF24wClm2ssMAIf/LLPfw18okxDH8TXc/vxv43H4DFjrdWzH2Sg0Vp78VK8M1dBa1tR5rCimAeythOjFLMPg22PqjxRhHT9ZC5FoAuclSprUuAEe/wSTNd1OEyxgKiTQsjPtcPWx8EtcY9696o17HRampXKry208m3s2ngDeAqc7LtcBMKS9FyBCZimX3M9BqgES7qOjny2xkjflALDVfrWXAT0PUxa6MW5nRHmoOngz/W38Ps93jbAASQgwCEACBBXFX+kPmZBGMZalcqEw2fWuAqc+J/XJH/p3iRtq4AphSrqzO4ypx4vEnmIJOpXhlaBZYphiI8udSc6DYbo1zp6uEqc2K6HipOqMGioDDCsRkxI6EyR7Rp4WIoj/MhQlsHqz2jZQGOluynBjhnhAMpDhYFV5Jai+dlx2xd0Z6z9gQ7YOu2PsjX3D0ARY3OOeteX4F2lhU/R5XaumKHJpQnZsfjTTI1aoRYTusopRGuWD3gZHJxURJ4AyjVBRgLwAQAC8bPBUdloyhKVzljGYwgBFgI2jdzAJCoY9RR2lDt9SguQFzIwg2KY46/A8t/sh0Xnjf/NYYkv/vsP31jjHxKCOlmUVZ1DKbMvhZ+JOh9XUlx9J77cOf2OdQ5/aQF1xuFQHPVW3xJxXfJ5add/CakKvLGK0tcF3EX+X0gLsR4KEEAQoZvfOqoaGglABLyRS/RGUmRN7u3O6RwEXl1y5Q1056aorTM3iFd1NDLnd4edGwP+B4yzxZHvX6fuP+tZxQgIeo/vRAymZOThm6Ulsz5V6e5qhu2yBotpkS/cc86RI/bi2l184WQUCAKkJcJvWLXLChW8kGwABQlCgBUqVHebyW01tfWAaaUYHtfBLWwFJUhyrWIaOFhc63hmj8jQdi1SQCYKeoCsVgsVDybi4KGY3UzjFU8keEqc6y7G+pIu5WGo0VFmxb2pEpIcbojDdqFVRjKxHQ9FON1040WU4rFzg2t2XCBrSE2hw8lMqhFcpsOcPA0pUak2IlQWikoEFnajypxt1KH+Fy/p0sFNFSK1st2qmwnRrllpblGUxQ0zGs7nX4VY7RpVzVjxG9hiFDaEKa2rlBYEe709tDeRgXLNlYAQKKnxyx6ZGvuarVpb6PCoaU8mdFLPbEZMYMaNQK0C309ffto695qMBagDuq71rMgbJHVcNQcAKBrDbrnycqfrTnppROtO4+5k99ft49CT05p099B+Uvzs+1wrbU0DHiLVHRd5PjY3n1UUerBORLnsAzlp4QpaJj39Jkx3iiMr4osYLicWikOpGK6HgLtmpCAkTZ4Hci6z7yujw/fQQ1QW1dk6KPBx7ts79kR0SHeoanGUQoaToAR2lgTOtjOJicYy0BRotkrmZ8CkVjs3NCaeJMtltM6UMPTfFo0RXFyHYUR7mpnk99TyMxTuJCCJD2AcWWbgCRkN/ejXz/3LcDRorDFsNb2ZBqKE33QxmhrU1z9p4yFHRqFSoLkZqcH4x2mZtUI8AYshR4CDBfzUBaPryQEp++5hnmdN2yRLXZmXGWOt5DIAld4N3ZiFNSJeiMPLUWpLr703LMD1NKnc2gpTq1U15XMT7WYoECkqIW31s+p42BRDqTMBUYh2+w7lQIR2vQv1XzdZ17XdX1sBHrfmuNbCYUV0a7u7eHUSoE3gDu9PU1N8ULB6i+DQ5OJRCITbDMAqifZToxylTla25Np0oY01fXQwn89ReXqkGMtMFRe2+lQIJKdOVemuh7CiN5n0U4ilGdme45lU2WEg6epUlsn7u99zQUgi8OGDPkx5H1Km+/Gk34EiwLUAr58c33+zKtukUHOJQJwiYDkDVyGicBm8qtH1+PZJd1cHXIor5vOnd093jDDlOjiX58mPvsvMZJ6q1lEjjqGgJQVtY4gAHKjgJ3cjYH925F+exPuv9+bOaztdKhdqUCjWHNXq926NXAqzlmyBMNjF6O88rCZMMBFfquL0Wt8l5/HCf2MLejvVWf1v4eVQlUHZJwqkAPgFxNxvQN7U7hzbj1Xh8YlL6YUxFIitMfUCQ8fRb2mJ2CmHab4f8gdtvOrjDOK2k4OFtV4o5jwhK5eUS8+cUkMeciuLycACfLel43ccQ1vjqHnF7T5kjHc+nS9qD32AiHk0yUnVysCskySb6rIYzd8/k3Y8dNN/Nlbe5hKgsWVUDw+d2DL6BPugvOJT/m9OE955XdfEZ88v9PFu3IuP0kK+PIuIAR5dlMVfnn7Os5YhtY0hki7lZ6QBFEnuiZ+8Z7W29aoZy+xBv/w9tafiXnndgIAGdg3qVA1PQOSExL87t8wGJ/aXDVK+Snhw+U9VNPC3Ol3YItsWQc/SnbpIkhjZwoEZkkQFG5O4B0LkPLbRyuO+fE1Mv7w6EFDiBYehkqjXDF7Nne0kssu36lqbTvS5lrIDGRSTKmLWE5PSmvafAnotPpC3fGmBEGIPRASqprJj/mI/3rjp7ycldFU4yg0jlU8kfkaUEWhJynXIhjqJNDPFaTFTGfb4dJao9659XcnKJn+i0TllGMIT85FKJp2AS4NWkOF6uhm2e59lt9y9J+oo9UR5Wt7svFeiV6/q3HNfU0vNDUt8xYBND1yKvjkbtCuiemk4kjBXmGqan5PCybpc4TfP5NAhESBOETO78Fffi+QZneu+vX1L16jehptcDKZ0koBywBZy9NefP/w+GNRMwzQVS9di6rqdRh3UPIF6SyfjLsAfBbChSASiJ28E7d8dh1gwavfIktpQ5i08PSENOwwKjvMtwhwFABgMLWbL/3MP1QIKB2vLvKjUOuKoCsffCOU+Jr+/VhjTQjm+KQj7QKgAYoSLTg9GfnbO28pHP3Jv8mZA9Vu5dS/DF+PrWDET8+4zijcuuIMeZhc7Fb4Z0IoVVJyf3WhRtsh73o+j/KyO9P3nrqvvkW8Yd7gaXUTPT2mp4ueN93CzpSGSjFxajNjgYrncucKED2HHPEXypzXziHx2Azd4H3MpDNiBle6ehC/VKate6stniAadAGNI9HZn4ldvMzABV8/HXJgPiK1ESTfnu9ONQ4Q4pbBlTIkmxouVFU9JxeUX+Oak/s57O7ialceXz+ApqtzYCwAXR/zFiOML1088ftigPzd1H0egM+TgxZGd2P/nuuw5LzuNZtb7aameKHyQXMxIVgDCdXjhjKdJ6MX+pqP33Oo8EwJKMYzvZU7a+IX72lqihc2d1wgXzCypq6sXIR9b7wYhZVM/ftnz+j8Aua62cWYzRUc0vsBoL0XK6voUxmvhwR4R8+cCuH/tUS87xeQbLLnT/eRsm98N9tugoKGX+/47dAsceYyFMZuF77AoUk5Mq7/lVxgML3bWho9g0JPFuNnXDF7vLgq3peRpdSIcGsrYbUY022RrViPKySCh4u6WozriiEkILXvOhHVzifwfd7T+noePxHFcElRNieGfcjfSm684Gc8sTVD9XkGabN7xHLU8T5m0vhfVmPmydce/h3e/10UUHhz7f8m7+vjo6jOtq8zsztJyMkm7LLJoATFWZEk6EaRVhusIG39aP1oLQq2tVSbiKAIVR41VkW0iEpFURCTatW2WqFaLX5XJSrxC7FZNQnKjB+AMNllF5I9+ZrdmfP+cWZ2F5++leeH7/P09Znfz58Bks3uzDn3ue/rvq7rJrcf3tjYaovPSsEU1klhhmNGf5/ILFmS3rT1bKcyslbixMuUCv5vd/c3yRO9+0OBMCsxijHITUrrK3DKJZWY+J27+UEHTyXw73t7uCcicD/7rq1AxjoHV1/UBpUBTM8Hg8LXh56k+EYNb3n7Pwic83McaALA4YBEEJtOirteWpCd1bQ+mJN4Aq6yB0CQlEV+ytP6xF6ZfrzjDoyrOxdASGSDDvJvSyr42gEZGOjCxr+uwnN/Wmez54tvB+IXB1EsDiYTj7ScnqqdfodP07ThMvxQTuOvNp06eyo/79cXEY6Z4BxcIi5+in045BwZkIH+dXj44RuYPm8HtU5WWOz5vrL7tEx63KT/RJj/z0E2uIq7HQpiOdNshQ9KwJvE8YvYOziEbFHxIWO6sOPylcS/CKUBBraXIlIRM4y+L/JE/6tBdsS9+msSMAUEwGAfcNkxlV6fhzSr4gBPaQr54UOpW//a4Ft0Lx8CccRu3fVpW+z6yLnRuU/PdY48bb5E+EjRSZb+ybZyHJLc+SqebrkEL/8Nse0x3RNbCOlxaQBQoc7Xk50ryWEh1tCLe59ai7KKqW4AS7MmOQSDSNBccRFMgE4BW1G+F/PXqjTRMLT3DxuPLJewkNiZH3CfX1ADiQyyz3vJgHB/LhF1YHdmgF9kmuTNtIeMQnW1qORofYgxlpAAYEVrYyqamjyGeJxSwl1BgS8vOpCVWj42+qL9N7O5sefKEgqE+y+OPACOIzjHo4AEPLv6ZV/TxC2xoGGh9W0BVKtqiALhbEvXjfTyP20YallfefydV8oAMOu8TUHjB2Sr77rRXbHGhr+xB65oO7FpUsXWJiPEztT14patJ/KWrdfQKz5tpSlNAe3AItYQEBIzFXR2zW6HsMsAB8ja68hvZ89h8068mS8VGyfTwo4ej6kxANdxX1Fh/w2EczfrJQANJ1VMFqYRlhqHgjgQcZhh9B0wxYQ9t5fSUl4/R0sjSMqkt588VggY7HyQcCkzduVBtxPu+z4HuOzi0cT9N0hZwU8mgB98BOH+23HN71cJbK0r/uEa42AoQdDoyQF2WN0SgW1nkRNMOCJKy/CPW9jZcAJaJ/mZgk5YNG4YRhGDmtA07qjzWRIG8aN/aK4E2w3OWXdxcfHngfRaikiIlRjFFAgzZhCaaBiKzIeNZe/U4fgffoCDxk614ffAwoIb7/aqXGgKow8Hxtat5Q9tbH78Z7E+BraXbmtQROMVCWrROhY0LLSeKzO83U12GWN5jvvowZKCvnTynTxzetN624RB1r+0ICtwYrdZU0IONgCYz82+ju4Z+pyPq5sLghCId3hIbm/AFdSggA5YMqKWf+8nd+HaPy2WoQ1dDJ7xuvwsaFizmlqD0aqGAEqM6rSx3EevWzcDZ1y6gQAzBQPGa0lIXpPK4S6vmsAPjKiY4Vw49+miZdnwbez5vsidPMOXWzW0cXPFftEYuXg2kH3SYFPNWzakB3I6nJJiSJJ91/OXEf8iqyrCwPYCsAETXwURn5Bs/vEWB/oYkIjM1yXQCPhSM0S3NSisddLe9F/PtxfRaMDDkSXI4KPHbo/O23A3oqddL8kYCYmAE69TnmcnuUtY4qExUzFryVrcekc8qmmBFa3XpihTwy9oAKxIJRSl8vnLiD+kVvUytBOnuGIAcARfnsucWup46nJjkzDKRdb7XJI2Pl2x/s4FO9myjU4FH36JkKwIsA4HiFzQbHdFVtyff38ckLhcV5Sx3va1DDWgSuPqnG2icndhP4mqLLyw8cYgw9vd3g3gIIDjLTbH3SgSJ5zIErBgRKXzHq55aipa1/hxcYSTy+svxXt/n8eOmns+LFRqAHBlaAQFwqbZQwDA9/R9R+Dwb04tRvjlb16KOG3Z8hcsXbk4esuzVw8t+WxitDV2Br1q7Vw0P7ZYvSX+aOmZiPs4aSPgl8Ff3OZlIyzcXsxav7+XUTPBFL17aAddi/in55AdNTPx4V/aqKYFcI9Oilsyf/JzPA8uRRyxEIn7f3GAEBm2S30RVyre2noLUZs3Jj2s6KtQDJU1R2wowcr0uEkyUjyNUaMTPB969llMMpeKRBxy3BAiyn2msnoAACAASURBVEAHghvqyUgdiOwSIytnLPx8xrUs2G+NN6MB9GxKMPZcEk2lCezZ1QX4wN1X4rl2owOMP24qbYw1lDVDYtRMRGuqI6LrTiRzZSR06TM8i+DoqYDP3cA+cALu3atsX8XvGZCgg9oQaUaSooGD6TDHZ78NktmAshDgEIG257JfJxdjufisAJfARZDjRMaCM4/irZTWVzC6MYmefoKgUc4U1lmSQoA2vtWwJ0gORl+qkuTgpfwm5JC4eX99CNAlFaW8dvodPkDNZ4CD2lD0pv4x/IoHLsXIqjAhzj5x38vSvaw/x4EmcDMrSXJGBufye/WlLtouus0prRdQAWpVGh8gTltjZ0A9ai1oeY5LnhPm5IQ8kkQgPjtcro0EHOJPJbde3MIqzPtPCcW6t+v713gVQURyZGTlvaAWq/M9fvNycOzOt8bIGdE7X7+QqT07YBAJiDgxo7/vq4BkSV8y5D1jDO0NRObrUg+NAIyJioSaiLz/qF0Co5oxlsgfjg4AMgXRqTNEQufAcfA38vF7N2ZI9mR0tM3LIrMIA6wr9ykAwO/UYtTUe2JBw2psbLVN2k4WKTURtj2mx2j3jqimBaBPgTEWw9yfyeSkSUP9AabQTtbe0wfoUlEQCbaifK/HpZ41e/0EetUUCdu72zj3iXcoWQMYHljns3EJPn7nHHS9Pi0DRPHKn2+Ak11tA0M5hpGvBMWO9Ni7LY8N6SsFi8OjiEkrblyYYkZ/X2S+t+OzkAHYkrdBRBOKcBCv5JS4NA6HjF9bzKf/jrU8TMAY8MCidVgSGWIKOsvmaGnhkGTitWd5kpUYxc6pFz8kTlcJAMoB39k4tH4ujv3e9cUoepkfdtRaHFa/CodMnIuR4alcygS8MjpbPjJXOrJBDKNxc8br1mJJZAgLzm9LrzHKQKfAaek8G1Zfj4/LMz3FF3EfrOxtGjfYSpC46L45gGJVLmxsDZorhZOTOOUPXDHElyIU696u03lvjGcgoxA+JA6AgPhya83xqG4EOXWKBK85KE59wiW3VShBcqMAlwjsgw75FQ2eezRTOrpZlRY3gqSMgob4yKqlOYYZz1Xq4v5nszNIc/ZVL+Nf9+S2LSo7mwcno5IBid+//8KPUD5K/BjJMdUIuA3Sm+gaui7yWVmzoP/xZjUEaiL7t+4RyAxu4P4i0eGWCMDtAqj8n9xK77U5CCeOI8v4GX79zDwYxI+qyWGP5SDT6BDDp69Ws+oAj0x+l+dKbcmFU9yXYjqoVV/DoCaiNTWRHG0Ouh+nzK7kvhFPEyD4hTo0n2ATjwfl/Z2Tu3+EQyjLSPaX9O6B6xjUBAMSHpWONKe6o/PuOT572IT74OOe+i/3O7iXcZN9M3sulqD4OlgJ+unH76sXPJfUNG3YoyR9GbuAwAEkBz67AkyhnWzVtTpJ7Lg+T42UgPHHLKbBWWMidwr1kJYrmQ/ssgOjUtzTe5aUM3MlDfHlVg2YKpSSQKJjJRklW9HAs8/qMrh4Zg4ICPeNEy9iJ8jrT5wzMCdyJpb9eNVwk+9F3LHgleGmuuVsQX1dFnyOl+wBEiDh7InLhmqoRetUaJytOvIjTEaxlnJpZkpHZTRVqpCs3wc4kDnAS0p6KcwwrWoQ2XSKp+m1r1QIXJolyWJ0s7HtFvzZS0hqByxkmqSH7jmUXTpiJn5E1uHXC9rYHbPbij6NfMAe/vVidvGEeb7Wqy7nsHflOfvKqGN2lF7uSfH5YtQAgLSwsTUIlysnVpYvF5C81ecr2BBeaQtI8IGcX4pvvI+Wj6YzM0YoU8M5yhAAFuy3ZpwwJUjLq4YgWWd5mYJH0xLULKmg5JEAIrs305fLVoijnKvO15NUUepoEMNeJy+fpnSATnnwe7j9qb8RFN2D4gA4ydPCciU39v0MBIJXSHqTIc8tiwGJPGZz4IotBiQ0TRtmltJJIazfyL5iCtFodPNEb4u7UGgBtS13lous0M2QJMhlfNGfJ1HQEKBL0VSpQprVJFny4885MOD5TOS6zhzgxUotH3qoxsP2Tj01YgMmqlOlQ5SpYX7sCd/wgsw+QYIQOOXBO2AYRemluuDyUquSBQ1Ltkeu5qWlILYEAocIaEHOdb8LudAEgO1JN7mH/UuicCoLXk+v+f3xoMF4zg+WMcE7tpROEjfGkH2Cf/7ZMqgJKCwOQMhBc3h6JAPthA0c/JB8ASEBcBwhn+EFfG4Jhd/DAe7db+72KOAbcT295Hd1gC41NrbaDEjwxajhZ8z5Dx98ZeC+3B7ZB7i30uBAnDteMBdwEIEDECHisQ89MmyOeHdOYS9i/2hcHJBtbhhGESajGAtmrkPi865cPPcXhXHJmrmC723mPaFdXmohPzh3BcJfalUo96aChHjr0+EMagKW4nodCD6qCo0zhXXOOJUGvf0oudp8Dm7DeOUc9sCPHkPQKBfZX8SBouT8nP2X1G3kyN4Oks2tRQL5YoCJ/dq4OYOU1pvjzSroBFRIg3tLc3udS8RjJogeUSTETJqIBQ3LU4EhpfWyJcd24ze/rixqqnmCdS20gIjDqrQ4FL1yH74sALbp8dXcln+Zg+QAgtHVJ3qsFk/xdsCZGuGodLj0SOnDzv3Zh9ePYNRMeJSKsjlamo1tt6zrzTkSlDM4AbfdzSFOsi/qDvLln2i4uFmdlP3+5xOxCNZAGN5p1fiozYAEWl6n+O2Oxc5p568FpOMKAxT579T3HoDoThw6du5zY6eeIAN7l6DrzRscLl2GD990s9h9GjTcw8vJ0J4L2HB/OaX1oZjR35deCoe98uQbJLnjbcABIVkQ0TBzs3oJqDx0BgyjiPYYQuOvmJWxYL8l7n/R2byAg+kQF1PMcki3ztsauZNnYBApmtKUmLFJp83bjyaEn0YggcseIc/OG/LYFjDQ38Zh34r07nU2snEZOagAtjg+CIED+BXwk2ZfhFRHZdRtXB3Q+sTFqZKWzd/k0RNCEjg4cZyCICpxkoUMwsGlLrL7s3UO4Tdz4FECO83hgMAhxJHcMt/nnQtX4e7ZYQRJmaD6KXXZB94JOr7sqYAkehq5A94B0glkHJzFlDVK/1Vk/IDUr8KRfsMhcQ5wGxI4fEImzAGMPWIeg753v6slANztJka10gBSWi8UvTIbHvkDDA/k4BA+ouyi0lX6UYCKaFVDgAEJ+hXc43917WucruYORPezA8DdS5d/px2IODRVqnjZH2sd95HIhPUks6xOsnLues59w97hKqV7zyeL0X2g7y+6rUFhit7NF7MaQJfAyCimPGjFgoZFtzWM8YQyTKGdnppQ9ApQR4GwvD3yHCfOLu5Cq7ZcNAERHWxsu+VVwgdeDhNHVEEOvk8w8gPa8v5cqBQUCPPFtKZkWbZMGWYzHdggcIgEgHMJBEQsePcEz2fKhRiZl+X6IOvvLsPsJUJN0rrGjyApG9HywffpUOBjlFXMlaRsQYknlvk/61L+u13E04pDFs2L/uTq2OLDJ+KXP1jF7jh/8eDV5MEVE17xSbu2rubEceBm/7mgxAH4R9RuXcS3r1gxKxWtqYlQlYXL7tMymVDprWJB+9yKhAOOEAUheNB3oPEMq9LiFJEQU40d9XO0tHP3xjESMI4IHANZl54HDmBgdxv0FzvNlTTENJSLTa0FeNmYRmHb5DXzZMDFtcieXWBEOYotKJvef57vt+zy486Rr5t5EoaH13nPW3Y12dzDnrk9E7NXI/YVNB457gnKH392P4EkcXGESdytpAgAkuGAjQvYRZE6zP/JvDvJrdeSi+ilb6L7ILLzw3WABC5lxYpyALJn52pyRf39bPizHUjxNGVqmMndfRIC8wn3gTtexeFWUtkMsCU2zZoTefIn9y0CTWmKMZ3uxZzIneSD1+4Rz5GDwCE5gyVfaS399ZaG/TF4cZMTh0ACnmg9kYEJEYYViQ9NL9uZUUZcAYeDIAsCLpPsnkfW3aWnPHWToBni/5lblqbxzD5EfY8JARDCAfL6k09cCaKKgKTCy/5o49MVQMRBkJQZhlHEPmhrI/FtRi4ZoyORPvT96gOuNN2Mmyg/20IRCTEKUCs6pv7EdgbaEW9sbLUL1XKALgkBEeIMSAjIjW8WZaIE2c6q5AKapClNobQ+9NVkst5LSAQyHMp5ySrc9tyT7E69iimsU246Yhi/PGWqFP/8Bgxabmkkut0EkiTAiGwOYwMckXHld0mfTdIL2K3HKHhgbhyIOHTD0PgRy+z7JBQ/ZRcXhzlE8LHdAFRIwfn3j7JuBUQAzsnHaD5xnQYtLsjZQNkcLb1w1vpgrP2IX5H03qTXRPIq7QzhgK8I9bUY39jYaoNZYO09fby5PvTE8MgXuC3t4bDFNzvE7cQDcHgDWj4JGzk1jpAZSj1vXu11wbnkOD6I4OkQGygqWy2UUSw5ulnbDQDsOj3NCc7gbnDNBX7vAbz++Dw6f6wDRBwarAkDQKz9Mf2VoqGfYMjqEuvBsyFwwy2R4Uw981tRDz87kE10ExvE2JpaCOyWgEsFaw3I+jO/IBdHngYAstRMNja22itar00d13TBIWxxzUzs0G/w5OG25NyyIviH+YyxBEa7gYNuTNJJNw5Jw9kfSwCI5Ilt3Gugdx373Zw2arLw36ZHbDAVmsYdwISKEy7lHJ0SJ4XMA/Gz1ROmeRMK9qOaFGnhqGCcsimhWLDfYtRMQNOGN9+Fu7DXTID7xGcuKa89u8i+gKH9P00u+H8RbA2D+P9p9QYA6SRw9/LOJy9G7n3cAyF1BlRRlm/CUNdLC7KA7req1Mc9wRIncHyQDzjIUiD8Xuv0NFeengCmQqVRDh3xjlcaKCCUlDlVGTVBEQnpf3QrGAt14uCQU0AWnIBD8qHjQsMPjK2MsRj5SoJsIbWBQ3BVuIzjqN96ny75+6obWk7vY9RMsAWHrGb+4rEYHm6Du7FsDx5wfH3g6PMCgU9oBDh2GV2M9B45OD2whiJS8Y/5rK+o5cNL8c1pHxDgh155ReBIwj8g38F2wPf1RPg3vThxHMEm4JA3Pz/0Ce/cQSkNexJM3lwfYtRM4AWUI5tZl8fyPFzc7Vh3vXoChRmObTdcZZWJGZfSIIl/+iLx2AiSa2XjCC4YZSPPjmpaoKw5AspqxgARB+FxU/ONGck1DiaQIO3BnUvaWm9ZxiitD4mGgZ4sDuI4CaD5U03KYbAc2RfYkytWw1LiFAiz7m5dKNGigSOaKkZix+ZVwnCbe/ik5EHAkiOf4h00B3LJlUMncp/fxf9dCpq77B3g7aGmox5gQIKqLMyXIkQtVrdw4SNBFty0A4g4WFK/yga/i9jyrb5PxzcvbPz9CTCMorLUFYwiEqJsSgjnXz0RiuJSAjkct9vugABvP9NFLVYXq+2x0kvhiI1KKxhY0ry/PkR6PnkFJC+HhTC/ASfZE/4rBzUBwKZM+wjUhJbSeimlYcqyU886CVknWHIqYOf6HtLAYAutP+fE/Dy6fIAl1tCI3Ov2JQ7YcL+wwSamkiCPgAdC7QimKrUrAKrU1gBAjgMPPclaJ+2Fpg3Pmr1+AkqIqrDMLg7JtWOSpOKO14864EM4aFhHLXyrjFjf38IeOXgvmc+SUBmiKU0RSjQhtKHQ4mAqwFSUzdHSJgxCFqOb1tREgAxxmTgEBC41bls8ilL+lQRZ2z1ZiCO6xDnWjs/ncHXc3OuzK2IlLd3fBNXidG5kCDPGneOAn4cdW1bLH787Db86btp7zWRs7Duksp8sLmHSwLHY9dFqh2MWlp89FU3lO/DyOQ7Ou3lq/cTM637IK3lJKQiXJE8GDEd2bQ6knGG460r0/wMmK3mMAX70ybeNG55cxBhLeKVVrOexPmqxOg1aPEur2gVc4BMG5QXQij2xYQ+got7Vu7Oe9j4GlsS7T7cVVAXivrj4DiT/DFissuMdw88sqxMt7x+CUVVhr+FFOFz7SAnoS7y47q5lqYWNNwaZErOEOICGfNvfmsG/0CQj+UbekzCMIlCKmGH00Wg0YK6MhDKZWLnKGvgzkcn3IzPotodEk8kRST0BUO9hngdylXz++WmCfZAFJJLnJwOQMv0PetgaM6kom61IXL2gI2m4Wc5SsL1y02U3khvU5emlcAhOf82YjOI0/mozIEGWmskMl4/xqEmSI4vD3u2q2tNnn4DfvLE2ek38iRFr9CexRm911rDf0SveNnH9hrUIj5nqwAeCrKg4XW9msnfvtHtSGNo/SESs+2KMyoAKqI7FYn1vHvfp6+aasTXxppHbeVZe7jEn7BEjgLOuXvzf4lIWNMo9P4jCBowEAuwxM8yyOoWU3ELMMPpY0LBEwyzioHFzhlqoe+SB07fQQW0IpSMSxF3DBAC04w8486YpTSHzWTKL5QfRxs0V6aU6WNCw2Hx9mM7r+Ald/MxarOleW7pKjzkPv/ISHn71cW6tfV9dvmvu0EF6PSwrTgaGim0ADhF9FS879iiEBxyFZEjCTYvkOZG2GzyEiAFHEEhv0nu6VrGW1wcRqcftTb7Hce3U1WzZzDaYvs5oqlTpemlBljYtL8N0+gE76NoFg1eT59iKI/ZSdea3afYPd+CEs9cS7j/GbUa4wdQtsSSOffo0DhfUHuffv/OVa0oQwHHQB0vAZaNf0rImDFJ/n5bxQHffzs4Md7NfECmXzToA5N7dIxn0pEfLotFogLIpoduevOQ+DKYThRsyx/dWfFP/ca+5XXuBZyjMME3t+QUv5OGSAhoStVafeio4g5qgKU2hqhqKoZ/YR3zTyu8dB/lOPIC+oS6qaYEVK2alNM+LdL6eHL7775+BduC0pgllXCneLCAewQ+WOBFObRI/ZP8oTF/aV6wVH9xtWhE57wu39R9dUMx4rovfOskPasJcM7ZGS2m9rHXS3vlBMgLWc5XM6rEQNMo5eyMY7YoqVLHqDMMoSq8xyvyxDRfkmATEPexdY3uJ4KTswYfWomzkVCLhDBCcSSScwcd/I4zykVMh+WsJCMB9KJSnOBUhXBxE8X6vIUjw/W3FNOgiC6RaaWDyB6Qq1r1dV1kDJxv+fGvWkT7Oc6Xra/kadpQ3Wfq/c8Hn1HQj1W2ALs2pivXBUuPRaDRgpFAEi1UyGJVUZWFYavx0slMCsnUWJ5ZLyQEHYJc5X4HJjYn0UjgrW5ftZNCTaBkqpfP11tKJ2MWro6twUGQGiH8q/DhKAjmSQ2qwxx9Ti4B6vaJgM+7V2zA8MFV2uEu1tPFIy+mpoxpfKksi58L1Fdw37oPtkbuJA9nlLuY5iPImW/a/sh43DzL2XHIRSgNQaJyu0Ovsh1/5KW7/bPGs9JKVvIWdh8f4YbTx6QqktF7auLnikSWjX0M22+bIUnue2C3lgkWO0pFXSHmcHieHP/7bB1qRYcjb35GZauwAdCl9phGi4Lv5UoRytoiDTFC4eb6sFooTAPCDIhKCRSsBgMVifQimKse0LMgilVrnHUwikxMG69xXhKM/f/+n1IrUMKgJ+OQZgl5DQBzbVbRI4HA+Y3PGvOoB+VCUyhU3LkxFoXF5l1FPXHd8j5JnwwHhDrKUbrsNRt/CxhuDnvRWXwmg8Tu26CBP2gtO4iD5QMGJ434+IiNy7IE769MK2CTrBXGX5y2Ye2bt5C4GlkSKpxE0ynHl2hEMelKds62bwgzTxs0VSPE0FMQjP+VpakbHsLHtFlM7kiy1PRGNnhygKU3hR057t/DAhOTk+dgAfJzkGoFeNpmDtAqUazIvrDg46Hmb9uOQcfK+zWdc9gpUBsMgfgaW9DVrn2tuc0c9aGbS9/Zjv4UDuKZMAME9AOq8QGsYRhFXigf+KxSu/W0u5dgFAgkXm9YWXPc13VWRRx6enGKxWF8Uk8Nse4+usgYOPYLY2HaLNm6uID98+DXlhXu/lzvAAS5v/Hv0gPdes5qkTA0vbGwN0plrvk2HswYO0mYAGCGSEYkX0leJaOu6h6gDEKeWjwyHIXl9BYLTm9bb6nw9GYLgeQs/WUWpozDDvPCBFXKrCqYAFHq75h4wKXCo5252KVbSX9H15Dn9TThObjp8w8IPVnBKTwnh1s6pvGXLoxiBD2Qu385paC7KSucCuINWoIu3pB8uWq2fyoDErKb1QXXriMekJvpDbOuYxrN4GLbjSj3zoyU4ARe/23F/fS4QFfBc4AoaOf69clz33h129G5BwI84rAbDQMQRJawVZ0ACE47dLQOFnF/i0gxyuJo3foVqWoBZVufpTettdvC42/PPRS6kNcA+rO44shjd9JI/h0FH1drewyVCAMEJQPbseZoiEgJjwh/UsuILG1uD6nw9yUdr21yPBI8umRN9+D56veRiaMOAihWtjSkMatv3mRjR+CgIS5TktPd8X94r1PEH3ohJ762U4cvdI6+5yjmgvvd37nE5AQiCOmioYyUZxcD2wmKVQMRhQcMyV0ZCKx6ctYWymjEMZBSoFgfTvaVVJIAOT/yR08bl15m437xgKXKPhUAKKoB9IBrWVbk/a4cUNHo9XjaCpCy9FI7A53W/uSZYg/uvXMc5/uiGd04IDnduf/dExlgiMl8XU1KQV2TZhB94luLyV/dtrAm2C4kbY8SapZ2nN623oWnDDKlOaNowo2aCUTPhCQz4X88POidf9Hdv+ROAOA1ndBxwT8R1a2N36cCU2WtRRMNeAOXAJ0DmmiEL0/DM3dOyDjQLiHJkTrEJrsKeXV3ckRyRYORZUtSidXorOdj7vBIAsO5unUFNuFlifiG4QL4wUyngnfJcV1yYrKCgoWwNAB0vdrEBjMfsyLW48/I2IOJA/UaleYQ1h9/+7EaUH7KWwDdNNAoEXpYbwshtOCDf8fuyz5S26F246ol55hOX1DCoCXbTj9vIgtqlTB5UsaVzHdIJsVQ5IMhGngrIWyfcu1nc9iKARFzrrUK+5Nf3ogjW0Sbax21scuc35Ej3xAHk/vTP0odulvkRx54KSRYBkkuwSUFpt/Uf6zy/WL7cEsRti1b20AjAYeUYBTm2hLuOqr+VI24vnLU+SGHWUZWFW1sbZRbst6gVGc/LQjLx2gMFogUCB+z8pgMfQlkyIp7PCgS0YhMJhDjIHnUcBVOBEnKwZ73HoCZUlHLDIJKAaXSJpkoVBiR+9UkrZ6uO/EiFximlrlGLCSKhnxPpCyYiAqIo8MJxCHfHNHmLE3lRjqArZt2ERfwA+1bDzgN+/opVR63IeGalPmJAgqz73TIOKe71T6SMvZjOvGLq81uIH7QDxLJGcJfpLPfu+dpP9lixYlYKKsOIIus38BeHs141snfPuq1IT+y/Slld/OP6TjzxUMJ3QaREaYrsIk0NOwbJubeT3hOPJB+9fhT5NEYI97nJm4QVD87aQsurhnI8WQqEBSFZl/g+kIkoa9zSKmd6lA++Lo9V8gjnmQQcdsPOIoTY6jl1KD33U6YaO9i9ulLcsvUmLPnDa/Arqwgn422SZyQIDbmPA+ACB5Tdl/SBcNTYh028Hpfd9AFdo99E79ErmWV1ln0aTeD2s+c9U/Z+Cd56ajWcTBfh+WzFU/JAcnmhXDBmea4xI4mSlOB/wZUSKjNf9gHvAxM3s3IIgBHlKEHxJNLx1IOFAc4zqMGOD8Gu/u4bqOonALBi1bwtUJRKKCyeXgrHdrAdHq7Mc0GSEzgEO2InehlM7EftfTBpHPoULGy8MYhNGCKL0U1Y6gRe4JFh5xAssseYXn3gH39wIJGj/bhSV9kRlYxv48s/Z9RMYOU5JsWEbzOQUcIl7tpU1N0TIuseU2cYRhFfaoYi7z9qs6BhqRd0JHMKKQdxFB40BZHWRnYB3np2NXekX2aJ80vy1pOrnUH8krC+S9GbmOcgeyFs/AJ7++bhxT+sQ9e7NziwLkRfzzxcdeCKQ3XOtm4oZpxakfEAgOd+DRu4wiFueerzg5/0y+u0F7RhxvoJ2O4wPA72PnNIvp5XY2OrjR9fAsA/282xQeKfJvDrE24Y33RwKVI8DZUhZmzSmYJOBiRixiZdNMng4Pbvxe3q6B+8moIAWLhwQxDb1EqvYpNyoL/LZ/PMtElhelpgK0fcjFPysBGOLoD/HH+6pY59t+zmQNOIvUbQKKfGogZ6455Wag/v8IFcA8gjvdeQC4gNBJIQJeUerBfonVzjhZcFHBDnGi7znfSGpzY4B+sTQSm+3XSShftnLo5do3wrS/hU9O5uE8Rsj3AoubQl5GZxOcTONXU4vv5zDstakAKdAjy+7BUObudVdRKXcvJc5VT+jR/s5p6lIinwBhhzxILInTwTex29CJKyxsYrObO6umNBMfPJ948XJLG8soVrRSSnhx2Vw1TrX7giy2p7LPaIvDdm9PfRaDTAd956NqejQCSvQ+7JuR04nHRGoycfME/WUbVnvUyAeM1SQkQwP+bUWgajcj0/yGHBD/8h8GYTC3/eOoEBCcoO/TYFwi/N3/LWkS/tDQK1leZKGmq9ZRl7/jLiF3CCCgDdeVpYJp+pSIAM8h67/9RLpTkTPhkisx4y7z/rBuky+mbszJH3kT0NawauKnqMXDzhU9J7zBq29hcz2UOTVrw/vehPbFHD6i8b571fbZ2lU0KkWU0CgoOqXmF2+y6pfVdKfNaVcyCDbxq9au1cUC0uTEuEMKRAXP81rvQQznzrvGMlTvwOscXaKzv0aiHLVhMIkjIWNKxoNOoeuoKCmMuEW69NyU66OGe5CQdgG4GxZs42VSqbo6Xz3ptOzrDDK9+41zApnNVFsoA93EY2PTMPl9dPfW+69Cjb+ECCHjHpeKVl6PTossyTOOyYDfBVzIBclIPn8z/v5DA4l72DQhlnHqd0wB1J4KvE7amNnjCV+BHjt29+lF7y9zkMbK8GwNc0fTcWHXeOjYEIdn6yzoY9IA4BW2jO3aEcogPowczS134RbZxu+GAy4C/PxQH+HHckr0ohOQzbJhcRglHEYwa4GQzhNnZj22PmykhIe/kcB4MIUNDQ+u9fIgAAH4ZJREFUs8+CaCmtlzQjiY9fbeMuzubhicTzwLX2zsWYiXVlzZA4eyNItzUoaNyciVZXRcB04PizZpCCqqnQOEXqT77nYZ4HhHZ/9PrbhcwK7xT3AYC/eIZ66/bwrPM2Bem2BkXF5DBLbU/E3HExjH76KlSG6RPtP0nJ/p247iEwqInGxlY7WlMdgULjUJRKwgc25dESuSCRdYD4zusQJGUMW17FSWsllUa5Op91A8I8CJswBAQSvFkNlTVHgFvOGYhq4y4U0ykO3MAl1tPex5vVEFONHRdDG9aPfFFmltWJ156bx8EHcvt6tLYYLa9TlI1MiGfi5yCZr/3+YEBC5iMOExFPFo2ukqw4cINGOWU1Y5DiaTEynYppGowlYobRRy3ULfz5igmWv0QwbNyp1SzYb5ELaBKWGgcAKb1UB61qCIBSV3Yk5YJi1s00Hc+WDnaGAw/go7Zp7OK6aWrJaWsY60hGTzzzeH/L5stw/WMbFF78BLjvRG98rTdqxi1VOScQr5fTeLvqL/F9jjhNvGabgCNcnFZkpTkWgTwNR313VUkL76K/4T9l171sEPwwNdREP8Pik+fJt/7sGw4GFyCxTdDMJOI2IhzX28Ah/xvQgqh2xPEmbSeMmgmy/aNniCQwVw/24ZC4JKGqEG73UG2HyC+UTD8kAZigjYnxbBDDgIpTT43YFAin1xhlau1Nr3IH73rMEik3SgjgpSOBmdetWjtaJ1D0yhhtJwCgXmF289XsZAfSDAGPCzyS8AJM1xlc91XMWMPtC8A53i1wE8v3c31+gBdvwNSJdWxsuxUzNullS7Xd0aqGAF+KED3lpprsEvNBDvkshFTg4MgGtHx4JFWUOrbq+I8YkGDd3XrsiVIdDj7FF/Q9BADC1VOLlw2fMbolQmhNdSQWi1nmykgoqpUGSDOSeIYHsPC3d2PKlMpdS41RuHLtCFjSKy+8u1bKmY0f0PMXc7Q8zJk2nlURma9L7LnlbUDmWgGoASgpDyu8cq5TXDSc82Bxvv47hFrKDGnPzrAILFkAWbLX8WUognXYhCFmWWI4pzsKRwxHNcOapg3DUuNMMbuVXZ8cnLN2coQ5THopHM82VfLGhwgHb7LPRvPxLAQTKpNCcsdqqe3Jo/svivyCLZ/TBgBm+2Vnl7ZYS3HdXx8v4uV38KrqnBiAS8JBgEiOa4jsBbasKNtcmoqnRHIXqEQg5wIAQdY1ziWCrA9PFSoVeLCSCJdxd+kY53On5Zbf4F6MBlMB/e344FUjHnjzz+OKMsDpJJNpEzQaqUBe+fWHC2LGh2+oNMoBgFUH7rMxPECIU2Ce4xBwnqNQef6xIA6krtffoFUNAXU+S65oPXkLhRYHnSJmUbVO2ktTmmKujIQk4AG40mbC3cPLpSo546O1M4YS72H11tqaFjtIL7l3hjl6x5+JbP8hBzl5gdXVo5I9PQnMn5oYvUxjXwUmTT57Z71nsCwibBYOhFkOHxkMY9aNG4pu5r888qVMHf/okm9nH36t1mnpXsh/1PyqD86pyGX9vnAp5Db8+OZaMbFAl6imBbT1qCb/eOpTfJGX7RAXAlEe6kXn/fbN28q1l64fRv0U4FfvVfIRL15HlWwnrzluKn52xQb1VzFh72ip8R/d1vDVGLdYyE8mhhmGpYr5dUwN/2VYuZMMZbd4+0ABrpFYaozHFOEVo5Jf+/2xvftvKEbc81sm8CH4+tqzGVKdtKohQC3UUetkhW5rUDyXQbB6rG85XWZj2y1qRWrs0Yf35apwSVDWGIxKbyquxHra+yilYkQwz0vcARuO7dsGR5qvf+qvZsurm9nDP+qmQNjf8uFkuvqDtWi8Yy2BciVARooBiYXYrZMzyhDcR7FQOXwC1yW543Kfk7/wPw4fcvyznKmyncNyc+Rv8ecgAa4sJfwzPPTiKvs3ukZTmnLcR7WH+5sCb7FrlbPwt9XncPB7OckM/+9oegHRmppILBbro0CY/urXAfnz7W8LXDKbtwr0FhjPB0cOMoQd21Yxaiaev4z4Fza2BinMsM0eLEZK6825oFm0En+86c8A7xEdL2kfuEeCDJSHajGibK0CqZsfNW0tgmNmCBtEQcXjXgYouc5rw72LYanxdBMOeNgfAxJY+ct1BGQLiNfi9+UUgYRLQFkIPmA5gW8zvrtggw+8TYL/akAKFoo3XKpaEMd9Zy2d+euptIqMAlOBIk03Jx11LpfIwD72kFK+0Sih6HyZor0Uiwcx94E4n3DYB7xy7PUgvjCBDKcsFMZB1R/QS34/g1Ezwca2Wwxk1IF+fuFUZSJHm1MQN9eMrVGbNyZnXBoJOsUD59s5+aATAB2Vp0OAf+13iaZpw8gqXcS1V+UAnCk/rALEXEGmGjuY8rwFasKjlYGaOL1pvY2U1ov79KkycIY4U8Uha8CoVDE5nKdwadpwLBbro9VaxCM7ykC7BelH0txvnIafTvmzuob4n72BMzr/jTlOi/5XBfJbkItn7Ev2l3JZZn7UBvLNLbIPdSH/9/8CFyX7fJ8XfeV/8u+5Zg4I5zL3Fc+Qw84buFdf69zVdThQW3lPCkPs6bmPxZuk68jjt0zCwN4bsKdHACPlITF1wTD6xFx1XfCHLVZnGEYRtWhdZL77d4iEcvZtuczII0c5QF9KiAGgJynMsDdX3psnX5DmYN+vHeDjf4zK+Xq65R2CRvmK1sZUWTOkrC3nRnZ9sYsNDMDLVoTpizCUZrR7RzR6cgC2NZWxB5J8dOQm8ft8+/z6wlEfAECS2940/zKLeAtRmGSoiUFo28V3iE3LlI5uPPccyMexmzxJ7RetK4VAwZ2XlX9w3JMIO8ibi5OU2bVu5Ph7yVIzyaAnPX29Bx3w0do2vu8bd39fFp6E1zCMIqqyMNUjwl+UdXRjw0N3CUofgefEXzjTy/nC2vQUW97a9uwiXeVLG55q62Q9WpwsNZMAoDadxIm58za3ynI82mPe1wMcBRWE6H1IOVhMAgEPjATqvrlWuVf/qZh86xq47GMXmG/YAJkvXT/Cmk8VZjau0TizlE59peBgvz995AfyQP8akYVL+2w+yfN5/1dXIBznuaAsERhGkXBP0yXSjKSg64kmKYJGOffCt/ss9685ZYYBIMsLCe5O7j5QKp4zmAoGPWkEjfI81CS+L0sc7k1gzt9PXaJA+JnAe28C6IPLdZUymENXv1eHlNaLlNYrJivrSUCXjKBRbsIgOFUvokueXQUZdxesEgAOqgAwvP2Rt99Ft0zTAqZiJBzYf0HcnMamk+nKryZtZEh1socfH6Q/f3fpjL5Ps3zi5FWEo4EAhJN/Qln5H7gKG2bc3RnEowIRTCUEj+POlzYsWvjK7XT270J/ajlnDx68NoH/OGrdPz4fXYx3X76BpPvfB3Sp7D4tQ9HAy/ALCQBMpSdRpaHcVmJ9+sqIQy1Wx6AnNU0b5p4bllvqyl55WB5IiMAQcTyTYDHoTQsw6EmnrGrQeyQOL3z/Ethhk5L7WM8xoxIQHNNdS41Rvq1vhdyMMzfWydsQTtmYQfFQVdRrV2RRYlQvnLU+iBRPx2LP9/32vuOfAAByxSnvEdtOFJLOvUHBwsJXYNeD5Yf8loLv+dINoNTWxHra+9iyc1Zj6ztteb9QtxJx75Ocb2457sGYQyy8qVecY6cTHPzOqZdGbH6jdTal9SHerIZIM1zruEiYfLjp2HyTTMpPMuA+Md8sFuvTJqMYJgMiOmAyGEFSxh65cTV2f3ZDbnSNZ87tNnXlL/B8XeZMgeexj4MANnE2b/fTs9iKwb0UpjCVYWpYna8nsfqYVdhjthE4EriE/CQQJxe2CiM5/+IkBgeApEAhuJ5dp6dzh/JkFFOY4dEtEAGLqeE3J+q9TvnIof1ZP1+G2WLZBas5yGe227Pw1gXp7ftyb4O+VKXnISINDFBo2nC0uipCTTXEV9LQwoUbgp4HNN3WMEZsTwFLkV3G2P3p/jOjvw8mg7I1drC3thwuQXrtuXoGJGKx5/uYYfSxnvY+ikgomtIUbMIQ0slKDolzDvjAJSgu+qTxjDgIxEFzWlNTGT5oe5e7iYft9zmQ/Rvo3N/NpTQSBjWBYXIYRSQUVWaNUa/t/DE9cdd2qIfPdZkYNi9I9FSlJkwtdXwuk6VWfU1ZC1LUQO9Ak3wuq7qs3ZiMYhw7s44ufmYtHSxOY8LRc3n4ULgCwfzW+DcoJvLyRCF/8swy5Pw8NoKSsjBqvj2Xf+uCrZdvv+FP2SdTJbHu7frRKyMBtui7N5Pk0ZdSWh/ii1kNG9tupfFaVriy8z0qa+CD4J+LeVNmtxEkZdRCHSlopBCPukEIOCRJWJ0JukdZMyTBuQxWUkRC0ttPHstz534WZJ9TWYB6lNIwpbNDFA0ct/x9QDg3afHshGgSeeiE2MRxA7UDKZ0q8TYYZ28E6aA2JByEaCha1RC4vOnpitbWWwhjzyXRo7dlIe1z/nIQm3AQWyLg3Lf7bV/v82XNkS8lSrLubv2jZ3g2UWREsHLJJUjvzvnEeraMhEsulOQyRTxTIXi2jRzIDidI25qfSOf9PAsATFbWMdaRZGPbhXsVU3Ebe74PgYp4jsRP8vxX8WZ0UE0LUCuqgNUjFjQsNq6nWHOpUGTgO0ts8F95EU0YeEv5gIe8qpG4xXJBsCVA9pWdjjS9eg5VaOPTFV5mGPtRe5++lIzCNrWS9aun8ozvqbxU2FsnTuEvEEZoBVk+uA1IDhyC92zY03A3KVLn60lq0TpjE4bA6pE+Tw2KgaImjj8jkpV648Vfun72B1IxOzrJm0/dKrs9C6GIk7B/PFkpf9oXl/aWNUNiitnNVJpgjCVWrJiWokDY+J5RBGrGXec90WMfPXb7fsE9mjYc6+/ps44Yv8ubxSYRBzh2ciegS5qmDdOamogwmzUBC5V4+RyHj9Y+9pyxMDhUxmj3jhyl8D4tQ2GGI/N1CTCxvW7qWcTBx2L+HJEAf5jXf3sVblvfyVu2/LHk93w57u5o40v+9DbG1K5CcHSYA8DwwDqS4Y97oizi7gmmmB8h6HoXsO0xfddVBqU11ZEyLCI0eUND9GevbcW5izbgoPEzeEkgN2TOm/PF811a/m+QyeY0NF45Z0PKbZTCmpgA5Tj4iBmyM/Lj6EvDfxy+Ux9HNS2QvtDwg+lgCu2kqVIBcKc0hSIipoUGSRk1o2OoMrYmmtIUKIgL9YSUG8vi8TxtSNloQ1WAsikhDJPD0kvhAKZwegeAmuM64Z7mgM8to90b+vFbo8Tsdh1k/sYkqInRfI6DHkMMo4TNOfIjEWQuQULWhVMGvHlWuQnEOTkj3ZgEjaCxsdWmSrDGKa9Z5vMoRrmpqUS2icjoSOzF+KSmSZn9cWmiVQ2B8acR3zheU3SbtV5nlx93DnboNwB8wE3N8uNb9qHnIScMAfhjTHlvIvvz8jb2yMF7vbEjHiZJmRp+5OHJqUUoDaB/T2V+qqzr8+qedixoWCxoWEyJWQAQTU0eQwdLh9C6xm8EjfL0hYZfbtL+gSGcxB3JIJAkj2FBcpNIvfckuVQ0wCb2cJZg8bph3/Tq+bVjYKmVYPXw3pv2gjYcaea7EVTjxt2kiFx5ygUktfMGDPXnAum+8EluwK43tgWcyw4GBlfr6D3+4A/knTRVqphramvIYtbtMQTY2HaLNCPJgobFm9UQnMCXrp/9uda3nC7Hrj39Pk4yfyg8GFAa3vPlWc6wMBnlEmCly3ctNUZ5HhfUonULG1uDDEicPIFnwFTIbuOZAyC7jOr9yWS9br6id4wmALIFLA7Kpog1alnxFzQx8gqWGi/7ZLPNiV2ACe0bqsS+BMyVkRCDmqieQxW0rbkQQLe3VoXBlT8M+L4nSziTK7SGcDKCEwCZfpBU/AZc/a153M+HvCVtQ0jaKSIVHs/ZB00bptvqA1A6wH91YtAOHj5alkcuduShIcAP6aVHTsGxp7RBLoIjVwxLsaeOQf/ecPbEnz7t6+8p/Z8OsnZ5cMD35vOTnW/84B0p3VNC3nluqvzNM9uyJRUDEgGR0j0lgB+OEhx2Shz4trxRzcd9a6ddkrWKPmw7jd2sr6CXTgkwmIk/Q1dmIpKgQBgqA7N6rHtgDF2c0oahMAWpsYiNbbe0lNY7AnhPcjI+9KXCzshRppRKqCAEckWVQS6gyY4L2/2apiVEsFATdN77dcxCJ/X7EiS1KwHiB9KJsEwrEhBQK9hhx39MGQODmuAraZixGGGfapzVYJhaNOz0lzIp9T549cT3sSeuOiNHmVIWMswttc6YyA7JilRCiSTYWMNaOC81gVnopKw+HGkG9JVMjNeG3k3nRWpwb0cb6e+tQ6AqgX4z7JRXmXKWyyAWhuunzRbvmyWByL/ONMa2W1E0BMAYvqeimDIrElty+GrtLr6Kpj5fTEaP+R64FMkZoXDJzV4Jk3bvfMcOj7pabgoZlNWD9RhF4Bc5dPaHY0wllqDQ4kgBCG6rm9bUThj4bhpxHiepz8OQZTjlYVOyHdm366NajNa6DABaig9QNkVMvEUkAaig8+bXRbuicaZ1pBnQXtYTsdOHbq4ptct/QoALQDInQPJ7FENSQPP6xCHOWvneRZvx4ctt42MxH6qrsO4lc8uMX6hBo72nSNOQgGEU6X8kxTAboI0FGNMTZX3fXsLbZq/L/uias31745fwkVVh9zx2sRQbtkQgs2SCFIfvtYGWnUsoAilQ09LGARQMXd3ppREHilIJy0rQVKnClys1sW7ommb2UvMdyLzmX64fUO1fP7+gYc06b1OQaVqC/G7FLTjvokl8BLXJnl0qD4Y//LL959Cqt6QdH9ZKpRUJZ2RoqwqNw6QAZVCv6OgW2K9Raa6pDTNqdVI70wWWCBMaTKBqQvuXZ7K6mPlVYlRj3PFbYA91+XZ9VovRh3Rli0JJH3WnBjMVF1MMA0bxikdOT6XHXlUmXZ8OoKQiX3B74g6D+KmmBQRWrUsUNLyi9drUQlzRufWkWcdqGHGRJPnmgEvjRYLpeNOiCSfOLrLzw43soEMX4arK7VSxagj3f4Lt3eBjx3cCjp33aBCKQZJu1ADoUoySUVEFw623LGMLF24IglmVMWOD/vLLy7IL5z04AZYVB1MRG9tuTUwhICvVAXXOtu7cAMb/KZ4bpWEWi/XRaDQgCMNec4nWgdFOFm4vpvLJQzAZcic7UwHaAViRSqbGdtCUpqDHIKg6pBbKiMRtVrd+MbRhqih1AEOse7seramJsFVHfoTGR22q1NaApQCFxRnrJxiLYXGqVo9ZsWrelpOuvIpGu6IKxqYqWfd2nUajATAdbtByBJZp1cXo9h3aJgxhMorptgaFPXLwXjQ+alP24xBZ2pHkS33fZsaHb9BoNMDMGAHleyibUgHagRWt16YWNv7+BIZsG6BLhkH8msYzMIif1tREYt3d+pSXtGy6CcGyFqQ2Tjd8UU0LQGVgJk3cA6NoEWsIqM0bk/pKgFqRGlAFK1b9fMvCxhuDjPX/J+f8/+szYGqYUTNBmRpGMFUJywmT5uyrHUuNUVHWwHFJUyXU2lrn0GMtWxqwhtHzORDsNqZXSJrGMxQTxOesqzveHOrsuh98TzOtr4BJxXgQk4XXrTNTJ1xKQhR8N0UkxIKG5VkhrmhtTC2ct2qCGIapSwiSMmpFFWbGCKXCOBkqBTPNJKX1oe1KrHhkipsUkYpHWk5PnY6rS+i21LEoVsKQS4Bt28COnrABTeW9FJEKBj0JRkZRNcphUnd+E0C3na0w2uGuuWAlrJQ7PkVsrsh8XdJXRpwSGNXyVXuGYG6cCloUdiZ+93Ppg9cUdPy9i218uLOsGRK/n4VgUtEwteproLA4aUYyvRSOYRhFQv1mYnssZr35DGczLqVBIFLHjNgb/3L9INv2ZZmi+CKCWOz5Pu3lvzu0cU4FAKjz9eSX7W8KM6zOZ0n9yHNlD0KhMMMs2G9hEAHjA8Sj0WgA5t46YEwiNrZ9hwYg8lOe1lfmG6j/8goa5UjxAQC2GBiq5gKwWPfaMGVqGNSEaE4jDJjArR+3oWJULRwCh5Dt73+HHK5NRjFlNWNgWfEVrY2pxiuvorjlnAH68w/HM4V1GoZRFK2ORkwlllCP+PmJWLg84ZBRFdYgBotTW3bg9plxWFYl6+7WMRnFYg2agEqBDhVMZQkAaG1tlL0snvBGLewt2HXP6qkZM04JMqYnKMw6BrXTexAxwxANhZTWC+gSZVNCUFilNx30f+rKzQ+CUalC46AR3Mae7/O8OCM/5ennLxMPwntgBpBzPkeKp1tbbyHX8Vbsmm74UIVyigbOxrZbdFuDAmoCNALG9ARlapgsNZPpZmMUVX/OwTYCENLV9GxMWPeSvmXGqZEgM4w+2lAVYKaZpEptDbG+v4XjniCggjQjyZvVEKMbkyghB5cMAoOt55jr+UFO7fQ7fNGakRFyRbA7faHhh/uey/BDmeOeICy1kqnGDqS03rJmSB0XGn4NwjuWMZagFqtjas+OPUCgOlUaAKuPx3ra+6I11RG26viP6KzPRYYXJGU0NXkMQ6rTMIwi794wGJUbWhYkpzXdEaqfz3eba2prmGV1/qv7XwKj+uD5/HNz6ZQQVAbCvptK4682hRn2utowjCJadXYAqg7GmAjGtKOSBftzn8XzwRVrsVRZ0XptqrGx1UbrJP+9/P+0d36hcVxXGP+u1h6p0lnHHmXtSS2r0Lu4Hq9hA8WFokKLn+qACTStg52H0Acpqk2NRWyciBYMpcZqVETdiARvKaQPKTSJX0Rqm+IQt1EITQMeXGmDO7cUSUlG2uxEf46ENOvd24e7s1qXgvzQuqiZ38s+LczMvfc75/453wWeefp3X6JKzwLPji9CyjWlVOvkjdN3j/9jTOsLU+5rV6MPDz8liALZBQtznlKLkzdO3z3SN1aNO3z6MsKlx1Undkkd9xvznCb4UUQ5nvbM4GG3i6NowmRJj2K6+422HVNYI5Las1WUD2RXfJGfHrZccAS2/CJskcb7WKU9+aw37fmyvqQy8tsj4dDtc9q/BDDEww46dLzkwN3jEYb+sEK9g3sZfy7CFunC0EUe6C3YnlKLeSm3MSuhurGW5z1diKw556ofXj8sOg8ArRv1n41E0rOXIzmFVrLdDOxwbuQnA+GhQ89tye/q2bZuU/jvOSNV6/BnRmxiI3dY3Tu9YtGXbx2t0bGPtnuz44v4ASBfgtmc5GtlArmxvmwcwN8pf2aL3TtCHShbtEug7piW7YxF9Z5fy8p5xaKfu6H/sgU4AA3g4zvAz48eYOYJKNWq4uNbDX8KoDGmDqLtUqhXThWeBHo/qBQKvamBY2N2QOOCbKwh1CuNpcQmsb/ze333ez/M2mDHtGmol+qZrG4Bi4dBJkMQgyjrC7bLCCfi6OAptdi4RoIdgAKwraJHLkr+pO9/6yLROLpBQQm2eghDR1eMmASl+APG0VoMoqzPk8uON0NTPRbTO2WKHBcWrUe1ut1dLBAEZMz/4LKjZmiqxxIXgnJ8/QpZ+12OJu/AFu2xYJj1HpNRIZQLZEU5ccYq6vPkvmB5/ll85evAzIQZ3EhR5Oz1pmf9vLsn+0I07Z/FwSzb78/E0xuyrBxHk0Vli7QM5QIFnLngBPOnWOwg6jBG2uxkjADpiukwWTSE6x5BcUpjl4+kjvUV7JHCj8PeQ89tgdQVFJ5MofeDCiHI3DP4N6ifNx3bL8eWgXHA5sJX5+nk7b2NzNhejppt79ajpOmQxG6X0z9ZDH797U7Pu76Ig2jLh9KqQrVduao//s5h8cUU5Oo9fTHCTrb8YnowC32eXFhsDLibhNtTygQZml5v88bgDDKslhdJSlN9hbr1Hdd3oePgGr+jtd91+ieL/iUz5YyvjndO+eVZMt87fn8xiPLSyyrdaMO4EKB+Ad/6dBLGB3iXEjyrPyVQp2cvR/mwwzJZITImU39lH0eTRRPYxO5UlN8Gi+cY/vxG/ed+ZiEmYqo9VHpilen18hcgdqe4Z3UjkaWIcrD8OVAWVfba1ttIV+IZBwAUhi7ywMnRfZieLqHjCc3dbzREaKMs9l/N29OXES4dd2zuHo/S/XJJn4fLFiZgq4fS/XJJDzqdN199c/6bUSqCRaa0Hqm3hp8Rj52FOeXTmFFGnHPOBEVTOBAAcICIdjbPJF6Caj0b7NrGDplxFCHHFiaaLRzjsRULffP4ry8XJCQkJGw+YlFrvq9s6WWVpu/fegp786NVUTNe1zX8FP3ZXzQvMzUnIP9NtiTNlJCQsFmJi4b0sA2E9hzoFtA/lsMj3aNaAClzZBnivSuep9SihFwDUYb5Vpn4G533ewIjEdmEhITPLSOvDHw4dPucDtrnXThbT9Ta23obbmgtgMbqm+Laibel1BUKnAzAwH6RZsjSg3i+lqSJEhISNiuFArYOnBzdN+PiR/jy9r+iveNEC1Jb65atqKE60XJl+BwHVCoUhgQ7VBoZGwjjDfwH8YzJmmxCQsKmhUDf8uBN5h8f+S4eOz1qqqRqqImWFYHqi+I3vxzG3wYEv4sFSLmGNZVFq/57vCH+INZkk0w2ISFh8xKhJCHn8NqLN6tC3wQqN6toebblVz/72nJf6nlcex3eu1ig+i0b1NqxkB7Mmg2z/4RfcZLJJiQk/F9nsuxk0q8G4Sd9qFJAGVMM4G8lx9nOAZUo4Ayyj4K96/XjjdmaOZrKOcatP95XMUSSySYkJHxe4e7xaOm4YwN+Cxw21WHIVjigEgEZb/9sBPZhCjOytXVvj2N/UrZIP4hn/CcyOH2v5oMNSQAAAABJRU5ErkJggg==";
  logoImages.imageLogoWidth3 =325/2;
  logoImages.imageLogoHeight3 = 81/2;

  
  
  var NINJA = NINJA || {};
        NINJA.primaryColor = "";
      NINJA.secondaryColor = "";
      NINJA.fontSize = 9;
      NINJA.headerFont = "Roboto";
      NINJA.bodyFont = "Roboto";
  
  var invoiceLabels = {"invoice":"Invoice","invoice_date":"Invoice Date","due_date":"Due Date","invoice_number":"Invoice Number","po_number":"PO Number","discount":"Discount","taxes":"Taxes","tax":"Tax","item":"Item","description":"Description","unit_cost":"Unit Cost","quantity":"Quantity","line_total":"Line Total","subtotal":"Subtotal","paid_to_date":"Paid To Date","balance_due":"Balance Due","partial_due":"Partial Due","terms":"Terms","your_invoice":"Your Invoice","quote":"Quote","your_quote":"Your Quote","quote_date":"Quote Date","quote_number":"Quote Number","total":"Total","invoice_issued_to":"Invoice Issued To","quote_issued_to":"Quote Issued To","rate":"Rate","hours":"Hours","balance":"Balance","from":"From","to":"To","invoice_to":"Invoice To","quote_to":"Quote To","details":"Details","invoice_no":"Invoice No.","quote_no":"Quote No.","valid_until":"Valid Until","client_name":"Client Name","address1":"Street","address2":"Apt\/Suite","id_number":"ID Number","vat_number":"VAT Number","city_state_postal":"City\/State\/Postal","postal_city_state":"Postal\/City\/State","country":"Country","email":"Email","contact_name":"Contact Name","company_name":"CompanyPlan Name","website":"Website","phone":"Phone","blank":"Blank","surcharge":"Surcharge","tax_invoice":"Tax Invoice","tax_quote":"Tax Quote","statement":"Statement","statement_date":"Statement Date","your_statement":"Your Statement","statement_issued_to":"Statement Issued To","statement_to":"Statement To","credit_note":"Credit Note","credit_date":"Credit Date","credit_number":"Credit Number","credit_issued_to":"Credit Issued To","credit_to":"Credit To","your_credit":"Your Credit","work_phone":"Phone","invoice_total":"Invoice Total","outstanding":"Outstanding","invoice_due_date":"Due Date","quote_due_date":"Valid Until","service":"Service","product_key":"Product","custom_value1":"Custom Value","custom_value2":"Custom Value","delivery_note":"Delivery Note","date":"Date","method":"Method","payment_date":"Payment Date","reference":"Reference","amount":"Amount","amount_paid":"Amount Paid","item_orig":"Item","quantity_orig":"Quantity","unit_cost_orig":"Unit Cost","company.custom_value1":"Custom Field","company.custom_value2":"Custom Field","invoice.custom_text_value1":"Custom Field","invoice.custom_text_value2":"Custom Field","client.custom_value1":"Custom Field","client.custom_value2":"Custom Field","contact.custom_value1":"Custom Field","contact.custom_value2":"Custom Field","product.custom_value1":"Custom Field","product.custom_value2":"Custom Field"};
  var isRefreshing = false;
  var needsRefresh = false;

  function refreshPDF(force, manual) {
    
    try {
        return getPDFString(refreshPDFCB, force);
    } catch (exception) {
                    console.log(exception);
            console.warn('Failed to generate PDF: %s', exception.message);
            var href = location.href;
            if (href.indexOf('/view/') > 0 && href.indexOf('phantomjs') == -1) {
                var url = href.replace('/view/', '/download/') + '?base64=true';
                $.get(url, function(result) {
                    if (result && result.indexOf('data:application/pdf') == 0) {
                        refreshPDFCB(result);
                    }
                })
            }
            }
  }

  function refreshPDFCB(string) {
    if (!string) return;
        PDFJS.workerSrc = 'http://invninjv1.local/js/pdf_viewer.worker.js';
    var forceJS = false;
    // Use the browser's built in PDF viewer
    if ((isChrome || isFirefox) && ! forceJS && ! isMobile) {
      document.getElementById('pdfObject').data = string;
    // Use PDFJS to view the PDF
    } else {
      if (isRefreshing) {
        needsRefresh = true;
        return;
      }
      isRefreshing = true;
      var pdfAsArray = convertDataURIToBinary(string);
      PDFJS.getDocument(pdfAsArray).then(function getPdfHelloWorld(pdf) {

        pdf.getPage(1).then(function getPageHelloWorld(page) {
          var scale = 1.5;
          var viewport = page.getViewport(scale);

          var canvas = document.getElementById('theCanvas');
          var context = canvas.getContext('2d');
          canvas.height = viewport.height;
          canvas.width = viewport.width;

          page.render({canvasContext: context, viewport: viewport});
          $('#pdfObject').hide();
          $('#pdfCanvas').show();
          isRefreshing = false;
          if (needsRefresh) {
            needsRefresh = false;
            refreshPDF();
          }
        });
      });
    }
      }

  function showMoreDesigns() {
    loadImages('#designThumbs');
    trackEvent('/company', '/view_more_designs');
    $('#moreDesignsModal').modal('show');
  }

  window.signatureAsPNG = false;
  function convertSignature(invoice) {
      if (! invoice || ! invoice.invitations || ! invoice.invitations.length) {
          return invoice;
      }

      for (var i=0; i<invoice.invitations.length; i++) {
          var invitation = invoice.invitations[i];
          if (invitation.signature_base64) {
              break;
          }
      }

      if (! invitation.signature_base64) {
          return invoice;
      }

      var sourceSVG = invitation.signature_base64;
      if (! sourceSVG || sourceSVG.indexOf('data:image') == 0) {
          return invoice;
      }
      if (window.signatureAsPNG) {
          invoice.invitations[0].signature_base64 = window.signatureAsPNG;
          return invoice;
      } else {
          var signatureDiv = $('#signatureCanvas')[0];
          var ctx = signatureDiv.getContext('2d');
          var img = new Image();
          img.src = "data:image/svg+xml;base64," + sourceSVG;
          img.onload = function() {
              ctx.drawImage(img, 0, 0);
              var blankImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVQYV2NgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII=';
              var image = signatureDiv.toDataURL("image/png") || blankImage;
              window.signatureAsPNG = invoice.invitations[0].signature_base64 = image;
              refreshPDF();
          }

          return false;
      }
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
          <p>Note: the white-label license is intended for personal use, please email us at <a href="&#109;a&#105;l&#x74;&#x6f;:&#99;&#111;&#x6e;&#x74;a&#99;t&#64;&#105;&#110;&#118;&#111;i&#99;&#x65;&#110;&#x69;&#110;&#x6a;&#x61;&#x2e;&#99;&#111;&#109;">&#99;&#111;&#x6e;&#x74;a&#99;t&#64;&#105;&#110;&#118;&#111;i&#99;&#x65;&#110;&#x69;&#110;&#x6a;&#x61;&#x2e;&#99;&#111;&#109;</a> if you'd like to resell the app.</p>
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
              <input type="hidden" name="_token" value="IH4RfjHyCuiL4BTW0mJXyy0httbYIu2gFF50M3a6"></form>
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
              <input type="hidden" name="_token" value="IH4RfjHyCuiL4BTW0mJXyy0httbYIu2gFF50M3a6"></form>
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
              <div class="form-group required"><label for="" class="control-label">From</label><div required class="form-control-static" id="contact_us_from">Norman Osborn &lt;norman@rideafide.local&gt;</div></div>

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

<input type="hidden" name="_token" value="IH4RfjHyCuiL4BTW0mJXyy0httbYIu2gFF50M3a6"></form>

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
                          We're actively working to improve this feature, if there's a command you'd like us to support please email us at <a href="&#109;ai&#x6c;&#x74;&#x6f;&#x3a;"></a>.
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
        <a class="btn btn-primary" href="https://invoice-ninja.readthedocs.io/en/latest/invoice_design.html" target="_blank">User Guide</a>
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
        if ('http://invninjv1.local/settings/invoice_design' != settingsURL) {
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
