<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Source: https://github.com/invoiceninja/invoiceninja -->
    <!-- Version: 4.5.19 -->
            <meta charset="utf-8">

            <title>Invoice Ninja | Free Open-Source Online Invoicing</title>
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
    <meta name="csrf-token" content="QbCf8ff9iAKpM8R3zxcWMY4J9UHJiJW0DRu0yRgT">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="canonical" href="https://app.invoiceninja.com/recover_password"/>

    
    <script src="/built.js?no_cache=4.5.19" type="text/javascript"></script>

    <script type="text/javascript">
        function trackEvent(category, action) {
        }

        var NINJA = NINJA || {};
        NINJA.fontSize = 9;
        NINJA.isRegistered = false;
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
        <link href="/css/built.public.css?no_cache=4.5.19" rel="stylesheet" type="text/css"/>
    <link href="/css/bootstrap.min.css?no_cache=4.5.19" rel="stylesheet" type="text/css"/>
    <link href="/css/built.css?no_cache=4.5.19" rel="stylesheet" type="text/css"/>
    <link href="/css/built.login.css?no_cache=4.5.19" rel="stylesheet" type="text/css"/>

    
</head>

<body class="body">

            <div class="container-fluid">
            <div class="row header">
                <div class="col-md-6 col-xs-12 text-center">
                    <a href="https://www.invoiceninja.com/" target="_blank">
                        <img width="231" src="/images/invoiceninja-logox53.png"/>
                    </a>
                </div>
                <div class="col-md-6 text-right visible-lg">
                    <p>Create. Send. Get Paid.</p>
                </div>
            </div>
        </div>
    
    <div class="container">

<form accept-charset="utf-8" class="form-horizontal form-signin" method="POST" action="/recover_password">

    <h2 class="form-signin-heading">Password Recovery</h2>
    <hr class="green">

    
    
    <!-- if there are login errors, show them here -->
    
    
    
    <div>
        <input required class="form-control" placeholder="Email address" id="email" type="email" name="email">
    </div>
    <button type='submit' class='btn btn-success green btn-lg btn-block'>Send Email</button>

    <input type="hidden" name="_token" value="QbCf8ff9iAKpM8R3zxcWMY4J9UHJiJW0DRu0yRgT"></form>

</div>

<script type="text/javascript">
    $(function() {
        $('#email').focus();

        $('.form-signin').submit(function() {
            $('button.btn-success').prop('disabled', true);
        });

    })
</script>


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
