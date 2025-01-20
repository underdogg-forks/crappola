<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Source: https://github.com/invoiceninja/invoiceninja -->
    <!-- Version: 4.5.46 -->
            <meta charset="utf-8">

            <title>Invoice Ninja | Free Open-Source Online Invoicing</title>
        <meta name="description" content="Invoice Ninja is a free, open-source solution for invoicing and billing customers. With Invoice Ninja, you can easily build and send beautiful invoices from any device that has access to the web. Your clients can print your invoices, download them as pdf files, and even pay you online from within the system."/>
        <link href="http://invninjv1.local/favicon-v2.png" rel="shortcut icon" type="image/png">

        <meta property="og:site_name" content="Invoice Ninja"/>
        <meta property="og:url" content="http://invninjv1.local"/>
        <meta property="og:title" content="Invoice Ninja"/>
        <meta property="og:image" content="http://invninjv1.local/images/round_logo.png"/>
        <meta property="og:description" content="Simple, Intuitive Invoicing."/>

        <!-- http://realfavicongenerator.net -->
        <link rel="apple-touch-icon" sizes="180x180" href="http://invninjv1.local/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="http://invninjv1.local/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="http://invninjv1.local/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="http://invninjv1.local/manifest.json">
        <link rel="mask-icon" href="http://invninjv1.local/safari-pinned-tab.svg" color="#3bc65c">
        <link rel="shortcut icon" href="http://invninjv1.local/favicon.ico">
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
    <meta name="csrf-token" content="lZMf1gNYZHxhQxLLOkBymnEXtjjjEW4SjNwsntHt">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="canonical" href="https://app.invoiceninja.com/login"/>

    
    <script src="http://invninjv1.local/built.js?no_cache=4.5.46" type="text/javascript"></script>

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

        /* This causes problems with some languages. ie, fr_CA
         var appLocale = 'en';
         */

                function fbq() {
            // do nothing
        }
        ;
        
                window._fbq = window._fbq || [];

    </script>

            <link rel="stylesheet" type="text/css" href="http://invninjv1.local/css/cookieconsent.min.css"/>
        <script src="http://invninjv1.local/js/cookieconsent.min.js"></script>
        <script>
        window.addEventListener("load", function(){
            if (! window.cookieconsent) {
                return;
            }
                            window.cookieconsent.initialise({
                    "palette": {
                        "popup": {
                            "background": "#000"
                        },
                        "button": {
                            "background": "#f1d600"
                        },
                    },
                    "content": {
                        "href": "https://www.invoiceninja.com/privacy-policy/",
                        "message": "This website uses cookies to ensure you get the best experience on our website.",
                        "dismiss": "Got it!",
                        "link": "Learn more",
                    }
                });
                    });
        </script>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

            <link href="http://invninjv1.local/css/built.public.css?no_cache=4.5.46" rel="stylesheet" type="text/css"/>
    <link href="http://invninjv1.local/css/bootstrap.min.css?no_cache=4.5.46" rel="stylesheet" type="text/css"/>
    <link href="http://invninjv1.local/css/built.css?no_cache=4.5.46" rel="stylesheet" type="text/css"/>
    <link href="http://invninjv1.local/css/built.login.css?no_cache=4.5.46" rel="stylesheet" type="text/css"/>

    
</head>

<body class="body">

            <div class="container-fluid">
            <div class="row header">
                <div class="col-md-6 col-xs-12 text-center">
                    <a href="https://www.invoiceninja.com/" target="_blank">
                        <img width="231" src="http://invninjv1.local/images/invoiceninja-logox53.png"/>
                    </a>
                </div>
                <div class="col-md-6 text-right visible-lg">
                    <p>Create. Send. Get Paid.</p>
                </div>
            </div>
        </div>
    
    
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
            window.location = 'http://invninjv1.local/logout?reason=inactive';
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

    <div class="container">

        <form accept-charset="utf-8" class="form-horizontal form-signin" method="POST" action="http://invninjv1.local/login">

        <h2 class="form-signin-heading">
                            company Login
                    </h2>
        <hr class="green">

        
        
        
        
        
        <div>
            <input required class="form-control" placeholder="Email address" id="email" type="email" name="email">
            <input required class="form-control" placeholder="Password" id="password" type="password" name="password">
        </div>

        <button type='submit' class='btn btn-success green btn-lg btn-block' id='loginButton'>Login</button>

        
        <div class="row meta">
                            <div class="col-md-7 col-sm-12">
                    <a href="http://invninjv1.local/recover_password">Recover your password</a>
                </div>
                <div class="col-md-5 col-sm-12">
                                            <a href="https://www.invoiceninja.com/knowledge-base/" target="_blank">Knowledge Base</a>
                                    </div>
                    </div>
        <input type="hidden" name="_token" value="lZMf1gNYZHxhQxLLOkBymnEXtjjjEW4SjNwsntHt"></form>

                    <div class="row sign-up">
                <div class="col-md-3 col-md-offset-3 col-xs-12">
                    <h3>Not a member yet?</h3>
                    <p>Create an company!</p>
                </div>
                <div class="col-md-3 col-xs-12">
                    <a class='btn btn-primary blue btn-lg btn-block' type='submit' href='http://invninjv1.local/invoice_now?sign_up=true'>Sign Up Now</a>
                </div>
            </div>
            </div>


    <script type="text/javascript">
        $(function() {
            if ($('#email').val()) {
                $('#password').focus();
            } else {
                $('#email').focus();
            }

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
