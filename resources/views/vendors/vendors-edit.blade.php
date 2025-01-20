<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Source: https://github.com/invoiceninja/invoiceninja -->
    <!-- Version: 4.5.19 -->
            <meta charset="utf-8">

            <title>Edit Vendor | Invoice Ninja</title>
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
    <link rel="canonical" href="https://app.invoiceninja.com/vendors/3/edit"/>

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
                                                      <li class="active"><a href="/vendors">Vendors</a></li>
                                                      <li><a href="/reports">Reports</a></li>
                                                      <li><a href="/settings">Settings</a></li>
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
                                                                <li class="nav-vendors active">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/vendors/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/vendors"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link active">
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
                        <li class="nav-settings ">

            <a type="button" class="btn btn-default btn-sm pull-right" title="User Guide: Vendors &gt; Create Vendor"
            href="https://invoice-ninja.readthedocs.io/en/latest/vendors.html#create-vendor" target="_blank">
            <i class="fa fa-info-circle" style="width:20px"></i>
        </a>
    
    <a href="/settings"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link ">
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
          
          

<div class="row">

	<form accept-charset="utf-8" class="form-horizontal col-md-12 warn-on-exit" autocomplete="off" method="POST" action="/vendors/3"><input type="hidden" name="_method" value="PUT">

    <!-- http://stackoverflow.com/a/30873633/497368 -->
<div style="display: none;">
    <input type="text" id="PreventChromeAutocomplete" name="PreventChromeAutocomplete" autocomplete="address-level4" />
</div>

			
        <input type="hidden" name="public_id" value="3">
	
	<div class="row">
		<div class="col-md-6">


        <div class="panel panel-default" style="min-height: 380px">
          <div class="panel-heading">
            <h3 class="panel-title">Organization</h3>
          </div>
            <div class="panel-body">

			<div class="form-group"><label for="name" class="control-label col-lg-4 col-sm-4">Name</label><div class="col-lg-8 col-sm-8"><input class="form-control" data-bind="attr { placeholder: placeholderName }" id="name" type="text" name="name" value="Miss Burdette Toy II"></div></div>
			<div class="form-group"><label for="id_number" class="control-label col-lg-4 col-sm-4">ID Number</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="id_number" type="text" name="id_number"></div></div>
                        <div class="form-group"><label for="vat_number" class="control-label col-lg-4 col-sm-4">VAT Number</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="vat_number" type="text" name="vat_number"></div></div>
                        <div class="form-group"><label for="website" class="control-label col-lg-4 col-sm-4">Website</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="website" type="text" name="website" value=""></div></div>
			<div class="form-group"><label for="work_phone" class="control-label col-lg-4 col-sm-4">Phone</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="work_phone" type="text" name="work_phone" value=""></div></div>

			            </div>
            </div>

        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Address</h3>
          </div>
            <div class="panel-body" id="billing_address">

			<div class="form-group"><label for="address1" class="control-label col-lg-4 col-sm-4">Street</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="address1" type="text" name="address1" value="563 Marvin Pines"></div></div>
			<div class="form-group"><label for="address2" class="control-label col-lg-4 col-sm-4">Apt/Suite</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="address2" type="text" name="address2" value="Apt. 805"></div></div>
			<div class="form-group"><label for="city" class="control-label col-lg-4 col-sm-4">City</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="city" type="text" name="city" value="Joanland"></div></div>
			<div class="form-group"><label for="state" class="control-label col-lg-4 col-sm-4">State/Province</label><div class="col-lg-8 col-sm-8"><input class="form-control" id="state" type="text" name="state" value="Wyoming"></div></div>

			<div class="form-group"><label for="postal_code" class="control-label col-lg-4 col-sm-4">Postal Code</label><div class="col-lg-8 col-sm-8"><input class="form-control" oninput="" id="postal_code" type="text" name="postal_code" value="46938"></div></div>
			<div class="form-group"><label for="country_id" class="control-label col-lg-4 col-sm-4">Country</label><div class="col-lg-8 col-sm-8"><select class="form-control" autocomplete="off" id="country_id" name="country_id"><option value=""></option><option value="4">Afghanistan</option><option value="8">Albania</option><option value="12">Algeria</option><option value="16">American Samoa</option><option value="20">Andorra</option><option value="24">Angola</option><option value="660">Anguilla</option><option value="10">Antarctica</option><option value="28">Antigua and Barbuda</option><option value="32">Argentina</option><option value="51">Armenia</option><option value="533">Aruba</option><option value="36">Australia</option><option value="40">Austria</option><option value="31">Azerbaijan</option><option value="44">Bahamas</option><option value="48">Bahrain</option><option value="50">Bangladesh</option><option value="52">Barbados</option><option value="112">Belarus</option><option value="56">Belgium</option><option value="84">Belize</option><option value="204">Benin</option><option value="60">Bermuda</option><option value="64">Bhutan</option><option value="68">Bolivia, Plurinational State of</option><option value="535">Bonaire, Sint Eustatius and Saba</option><option value="70">Bosnia and Herzegovina</option><option value="72">Botswana</option><option value="74">Bouvet Island</option><option value="76">Brazil</option><option value="86">British Indian Ocean Territory</option><option value="96">Brunei Darussalam</option><option value="100">Bulgaria</option><option value="854">Burkina Faso</option><option value="108">Burundi</option><option value="116">Cambodia</option><option value="120">Cameroon</option><option value="124">Canada</option><option value="132">Cape Verde</option><option value="136">Cayman Islands</option><option value="140">Central African Republic</option><option value="148">Chad</option><option value="152">Chile</option><option value="156">China</option><option value="162">Christmas Island</option><option value="166">Cocos (Keeling) Islands</option><option value="170">Colombia</option><option value="174">Comoros</option><option value="178">Congo</option><option value="180">Congo, the Democratic Republic of the</option><option value="184">Cook Islands</option><option value="188">Costa Rica</option><option value="191">Croatia</option><option value="192">Cuba</option><option value="531">Curaçao</option><option value="196">Cyprus</option><option value="203">Czech Republic</option><option value="384">Côte d'Ivoire</option><option value="208">Denmark</option><option value="262">Djibouti</option><option value="212">Dominica</option><option value="214">Dominican Republic</option><option value="218">Ecuador</option><option value="818">Egypt</option><option value="222">El Salvador</option><option value="226">Equatorial Guinea</option><option value="232">Eritrea</option><option value="233">Estonia</option><option value="231">Ethiopia</option><option value="238">Falkland Islands (Malvinas)</option><option value="234">Faroe Islands</option><option value="242">Fiji</option><option value="246">Finland</option><option value="250">France</option><option value="254">French Guiana</option><option value="258">French Polynesia</option><option value="260">French Southern Territories</option><option value="266">Gabon</option><option value="270">Gambia</option><option value="268">Georgia</option><option value="276">Germany</option><option value="288">Ghana</option><option value="292">Gibraltar</option><option value="300">Greece</option><option value="304">Greenland</option><option value="308">Grenada</option><option value="312">Guadeloupe</option><option value="316">Guam</option><option value="320">Guatemala</option><option value="831">Guernsey</option><option value="324">Guinea</option><option value="624">Guinea-Bissau</option><option value="328">Guyana</option><option value="332">Haiti</option><option value="334">Heard Island and McDonald Islands</option><option value="336">Holy See (Vatican City State)</option><option value="340">Honduras</option><option value="344">Hong Kong</option><option value="348">Hungary</option><option value="352">Iceland</option><option value="356">India</option><option value="360">Indonesia</option><option value="364">Iran, Islamic Republic of</option><option value="368">Iraq</option><option value="372">Ireland</option><option value="833">Isle of Man</option><option value="376">Israel</option><option value="380">Italy</option><option value="388">Jamaica</option><option value="392">Japan</option><option value="832">Jersey</option><option value="400">Jordan</option><option value="398">Kazakhstan</option><option value="404">Kenya</option><option value="296">Kiribati</option><option value="408">Korea, Democratic People's Republic of</option><option value="410">Korea, Republic of</option><option value="414">Kuwait</option><option value="417">Kyrgyzstan</option><option value="418">Lao People's Democratic Republic</option><option value="428">Latvia</option><option value="422">Lebanon</option><option value="426">Lesotho</option><option value="430">Liberia</option><option value="434">Libya</option><option value="438">Liechtenstein</option><option value="440">Lithuania</option><option value="442">Luxembourg</option><option value="446">Macao</option><option value="807">Macedonia, the former Yugoslav Republic of</option><option value="450">Madagascar</option><option value="454">Malawi</option><option value="458">Malaysia</option><option value="462">Maldives</option><option value="466">Mali</option><option value="470">Malta</option><option value="584">Marshall Islands</option><option value="474">Martinique</option><option value="478">Mauritania</option><option value="480">Mauritius</option><option value="175">Mayotte</option><option value="484">Mexico</option><option value="583">Micronesia, Federated States of</option><option value="498">Moldova, Republic of</option><option value="492">Monaco</option><option value="496">Mongolia</option><option value="499">Montenegro</option><option value="500">Montserrat</option><option value="504">Morocco</option><option value="508">Mozambique</option><option value="104">Myanmar</option><option value="516">Namibia</option><option value="520">Nauru</option><option value="524">Nepal</option><option value="528">Netherlands</option><option value="540">New Caledonia</option><option value="554">New Zealand</option><option value="558">Nicaragua</option><option value="562">Niger</option><option value="566">Nigeria</option><option value="570">Niue</option><option value="574">Norfolk Island</option><option value="580">Northern Mariana Islands</option><option value="578">Norway</option><option value="512">Oman</option><option value="586">Pakistan</option><option value="585">Palau</option><option value="275">Palestinian Territory, Occupied</option><option value="591">Panama</option><option value="598">Papua New Guinea</option><option value="600">Paraguay</option><option value="604">Peru</option><option value="608">Philippines</option><option value="612">Pitcairn</option><option value="616">Poland</option><option value="620">Portugal</option><option value="630">Puerto Rico</option><option value="634">Qatar</option><option value="642">Romania</option><option value="643">Russian Federation</option><option value="646">Rwanda</option><option value="638">Réunion</option><option value="652">Saint Barthélemy</option><option value="654">Saint Helena, Ascension and Tristan da Cunha</option><option value="659">Saint Kitts and Nevis</option><option value="662">Saint Lucia</option><option value="663">Saint Martin (French part)</option><option value="666">Saint Pierre and Miquelon</option><option value="670">Saint Vincent and the Grenadines</option><option value="882">Samoa</option><option value="674">San Marino</option><option value="678">Sao Tome and Principe</option><option value="682">Saudi Arabia</option><option value="686">Senegal</option><option value="688">Serbia</option><option value="690">Seychelles</option><option value="694">Sierra Leone</option><option value="702">Singapore</option><option value="534">Sint Maarten (Dutch part)</option><option value="703">Slovakia</option><option value="705">Slovenia</option><option value="90">Solomon Islands</option><option value="706">Somalia</option><option value="710">South Africa</option><option value="239">South Georgia and the South Sandwich Islands</option><option value="728">South Sudan</option><option value="724">Spain</option><option value="144">Sri Lanka</option><option value="729">Sudan</option><option value="740">Suriname</option><option value="744">Svalbard and Jan Mayen</option><option value="748">Swaziland</option><option value="752">Sweden</option><option value="756">Switzerland</option><option value="760">Syrian Arab Republic</option><option value="158">Taiwan, Province of China</option><option value="762">Tajikistan</option><option value="834">Tanzania, United Republic of</option><option value="764">Thailand</option><option value="626">Timor-Leste</option><option value="768">Togo</option><option value="772">Tokelau</option><option value="776">Tonga</option><option value="780">Trinidad and Tobago</option><option value="788">Tunisia</option><option value="792">Turkey</option><option value="795">Turkmenistan</option><option value="796">Turks and Caicos Islands</option><option value="798">Tuvalu</option><option value="800">Uganda</option><option value="804">Ukraine</option><option value="784">United Arab Emirates</option><option value="826">United Kingdom</option><option value="840">United States</option><option value="581">United States Minor Outlying Islands</option><option value="858">Uruguay</option><option value="860">Uzbekistan</option><option value="548">Vanuatu</option><option value="862">Venezuela, Bolivarian Republic of</option><option value="704">Viet Nam</option><option value="92">Virgin Islands, British</option><option value="850">Virgin Islands, U.S.</option><option value="876">Wallis and Futuna</option><option value="732">Western Sahara</option><option value="887">Yemen</option><option value="894">Zambia</option><option value="716">Zimbabwe</option><option value="248">Åland Islands</option></select></div></div>

        </div>
        </div>
		</div>
		<div class="col-md-6">


        <div class="panel panel-default" style="min-height: 380px">
          <div class="panel-heading">
            <h3 class="panel-title">Contacts</h3>
          </div>
            <div class="panel-body">

			<div data-bind='template: { foreach: vendor_contacts,
		                            beforeRemove: hideContact,
		                            afterAdd: showContact }'>
				<input data-bind="value: public_id, valueUpdate: 'afterkeydown',
                        attr: {name: 'vendor_contacts[' + $index() + '][public_id]'}" type="hidden" name="public_id" value="3">
				<div class="form-group"><label for="first_name" class="control-label col-lg-4 col-sm-4">First Name</label><div class="col-lg-8 col-sm-8"><input class="form-control" data-bind="value: first_name, valueUpdate: 'afterkeydown',
                        attr: {name: 'vendor_contacts[' + $index() + '][first_name]'}" id="first_name" type="text" name="first_name"></div></div>
				<div class="form-group"><label for="last_name" class="control-label col-lg-4 col-sm-4">Last Name</label><div class="col-lg-8 col-sm-8"><input class="form-control" data-bind="value: last_name, valueUpdate: 'afterkeydown',
                        attr: {name: 'vendor_contacts[' + $index() + '][last_name]'}" id="last_name" type="text" name="last_name"></div></div>
				<div class="form-group"><label for="email" class="control-label col-lg-4 col-sm-4">Email</label><div class="col-lg-8 col-sm-8"><input class="form-control" data-bind="value: email, valueUpdate: 'afterkeydown',
                        attr: {name: 'vendor_contacts[' + $index() + '][email]', id:'email'+$index()}" id="email" type="email" name="email"></div></div>
				<div class="form-group"><label for="phone" class="control-label col-lg-4 col-sm-4">Phone</label><div class="col-lg-8 col-sm-8"><input class="form-control" data-bind="value: phone, valueUpdate: 'afterkeydown',
                        attr: {name: 'vendor_contacts[' + $index() + '][phone]'}" id="phone" type="text" name="phone"></div></div>

				<div class="form-group">
					<div class="col-lg-8 col-lg-offset-4 bold">
						<span class="redlink bold" data-bind="visible: $parent.vendor_contacts().length > 1">
							<a href="#" data-bind="click: $parent.removeContact">Remove contact -</a>
						</span>
						<span data-bind="visible: $index() === ($parent.vendor_contacts().length - 1)" class="pull-right greenlink bold">
							<a href="#" onclick="return addContact()">Add contact +</a>
						</span>
					</div>
				</div>
			</div>
            </div>
            </div>


        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Additional Info</h3>
          </div>
            <div class="panel-body">

            <div class="form-group"><label for="currency_id" class="control-label col-lg-4 col-sm-4">Currency</label><div class="col-lg-8 col-sm-8"><select class="form-control" id="currency_id" name="currency_id"><option value="" disabled="disabled" selected="selected">US Dollar</option><option value=""></option><option value="94">Albanian Lek</option><option value="90">Algerian Dinar</option><option value="82">Angolan Kwanza</option><option value="23">Argentine Peso</option><option value="97">Armenian Dram</option><option value="40">Aruban Florin</option><option value="12">Australian Dollar</option><option value="98">Azerbaijan Manat</option><option value="77">Bahraini Dinar</option><option value="24">Bangladeshi Taka</option><option value="71">Barbadian Dollar</option><option value="100">Belarusian Ruble</option><option value="93">Bolivian Boliviano</option><option value="99">Bosnia and Herzegovina Convertible Mark</option><option value="68">Botswana Pula</option><option value="20">Brazilian Real</option><option value="2">British Pound</option><option value="72">Brunei Dollar</option><option value="39">Bulgarian Lev</option><option value="86">CFP Franc</option><option value="9">Canadian Dollar</option><option value="88">Cape Verdean Escudo</option><option value="62">Chilean Peso</option><option value="32">Chinese Renminbi</option><option value="30">Colombian Peso</option><option value="47">Costa Rican Colón</option><option value="43">Croatian Kuna</option><option value="51">Czech Koruna</option><option value="5">Danish Krone</option><option value="61">Dominican Peso</option><option value="37">East Caribbean Dollar</option><option value="29">Egyptian Pound</option><option value="3">Euro</option><option value="92">Fijian Dollar</option><option value="73">Georgian Lari</option><option value="38">Ghanaian Cedi</option><option value="101">Gibraltar Pound</option><option value="104">Gibraltar Pound</option><option value="18">Guatemalan Quetzal</option><option value="83">Haitian Gourde</option><option value="75">Honduran Lempira</option><option value="26">Hong Kong Dollar</option><option value="69">Hungarian Forint</option><option value="63">Icelandic Króna</option><option value="11">Indian Rupee</option><option value="27">Indonesian Rupiah</option><option value="6">Israeli Shekel</option><option value="81">Jamaican Dollar</option><option value="45">Japanese Yen</option><option value="65">Jordanian Dinar</option><option value="103">Kazakhstani Tenge</option><option value="8">Kenyan Shilling</option><option value="89">Kuwaiti Dinar</option><option value="96">Lebanese Pound</option><option value="59">Macanese Pataca</option><option value="91">Macedonian Denar</option><option value="19">Malaysian Ringgit</option><option value="46">Maldivian Rufiyaa</option><option value="87">Mauritian Rupee</option><option value="28">Mexican Peso</option><option value="102">Moldovan Leu</option><option value="80">Moroccan Dirham</option><option value="56">Mozambican Metical</option><option value="66">Myanmar Kyat</option><option value="53">Namibian Dollar</option><option value="85">Nepalese Rupee</option><option value="35">Netherlands Antillean Guilder</option><option value="15">New Zealand Dollar</option><option value="22">Nigerian Naira</option><option value="14">Norske Kroner</option><option value="57">Omani Rial</option><option value="48">Pakistani Rupee</option><option value="64">Papua New Guinean Kina</option><option value="67">Peruvian Sol</option><option value="10">Philippine Peso</option><option value="49">Polish Zloty</option><option value="74">Qatari Riyal</option><option value="42">Romanian New Leu</option><option value="55">Russian Ruble</option><option value="33">Rwandan Franc</option><option value="44">Saudi Riyal</option><option value="95">Serbian Dinar</option><option value="13">Singapore Dollar</option><option value="4">South African Rand</option><option value="79">South Korean Won</option><option value="50">Sri Lankan Rupee</option><option value="76">Surinamese Dollar</option><option value="7">Swedish Krona</option><option value="17">Swiss Franc</option><option value="60">Taiwan New Dollar</option><option value="34">Tanzanian Shilling</option><option value="21">Thai Baht</option><option value="36">Trinidad and Tobago Dollar</option><option value="54">Tunisian Dinar</option><option value="41">Turkish Lira</option><option value="1">US Dollar</option><option value="70">Ugandan Shilling</option><option value="58">Ukrainian Hryvnia</option><option value="25">United Arab Emirates Dirham</option><option value="52">Uruguayan Peso</option><option value="78">Venezuelan Bolivars</option><option value="16">Vietnamese Dong</option><option value="31">West African Franc</option><option value="84">Zambian Kwacha</option></select></div></div>
			<div class="form-group"><label for="private_notes" class="control-label col-lg-4 col-sm-4">Private Notes</label><div class="col-lg-8 col-sm-8"><textarea class="form-control" rows="6" id="private_notes" name="private_notes"></textarea></div></div>

            </div>
            </div>

		</div>
	</div>


	<input data-bind="value: ko.toJSON(model)" type="hidden" name="data" value="">

	<script type="text/javascript">

	$(function() {
		$('#country_id').combobox();
	});

	function VendorContactModel(data) {
		var self = this;
		self.public_id = ko.observable('');
		self.first_name = ko.observable('');
		self.last_name = ko.observable('');
		self.email = ko.observable('');
		self.phone = ko.observable('');

		if (data) {
			ko.mapping.fromJS(data, {}, this);
		}
	}

	function VendorModel(data) {
		var self = this;

        self.vendor_contacts = ko.observableArray();

		self.mapping = {
		    'vendor_contacts': {
		    	create: function(options) {
		    		return new VendorContactModel(options.data);
		    	}
		    }
		}

		if (data) {
			ko.mapping.fromJS(data, self.mapping, this);
		} else {
			self.vendor_contacts.push(new VendorContactModel());
		}

		self.placeholderName = ko.computed(function() {
			if (self.vendor_contacts().length == 0) return '';
			var contact = self.vendor_contacts()[0];
			if (contact.first_name() || contact.last_name()) {
				return (contact.first_name() || '') + ' ' + (contact.last_name() || '');
			} else {
				return contact.email();
			}
		});
	}

    	    window.model = new VendorModel({"created_at":"2020-08-08 19:16:16","updated_at":"2020-08-08 19:16:16","deleted_at":null,"user_id":2,"company_id":2,"currency_id":null,"name":"Miss Burdette Toy II","address1":"563 Marvin Pines","address2":"Apt. 805","city":"Joanland","state":"Wyoming","postal_code":"46938","country_id":null,"work_phone":"","private_notes":"","website":"","is_deleted":0,"public_id":3,"vat_number":null,"id_number":null,"transaction_name":null,"custom_value1":null,"custom_value2":null,"vendor_contacts":[{"company_id":2,"user_id":2,"vendor_id":28,"created_at":"2020-08-08 19:16:16","updated_at":"2020-08-08 19:16:16","deleted_at":null,"is_primary":1,"first_name":"Evie","last_name":"Lindgren","email":"mazie.hettinger@example.com","phone":"1-535-762-9456","public_id":3}]});
    
	model.showContact = function(elem) { if (elem.nodeType === 1) $(elem).hide().slideDown() }
	model.hideContact = function(elem) { if (elem.nodeType === 1) $(elem).slideUp(function() { $(elem).remove(); }) }


	ko.applyBindings(model);

	function addContact() {
		model.vendor_contacts.push(new VendorContactModel());
		return false;
	}

	model.removeContact = function() {
		model.vendor_contacts.remove(this);
	}


	</script>
		<center class="buttons">
    	<a class='btn btn-default btn-lg' href='http://invninjv1.local/vendors/3'>Cancel <span class='glyphicon glyphicon-remove-circle'></span></a>
        <button type='submit' class='btn btn-success btn-lg'>Save <span class='glyphicon glyphicon-floppy-disk'></span></button>
	</center>
		<input type="hidden" name="_token" value="frPRPZtD0NIePrpcFHDC2ELJ89NvFzlG0dzIkrWt"></form>
</div>
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
          <p>Note: the white-label license is intended for personal use, please email us at <a href="&#109;ai&#x6c;t&#111;:&#x63;&#111;&#x6e;&#x74;a&#x63;&#x74;&#x40;i&#x6e;v&#x6f;&#105;c&#x65;&#x6e;&#x69;&#110;&#106;&#97;.&#99;&#111;&#109;">&#x63;&#111;&#x6e;&#x74;a&#x63;&#x74;&#x40;i&#x6e;v&#x6f;&#105;c&#x65;&#x6e;&#x69;&#110;&#106;&#97;.&#99;&#111;&#109;</a> if you'd like to resell the app.</p>
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
                          We're actively working to improve this feature, if there's a command you'd like us to support please email us at <a href="&#109;&#97;&#105;l&#x74;&#111;:"></a>.
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
        <a class="btn btn-primary" href="https://invoice-ninja.readthedocs.io/en/latest/vendors.html#create-vendor" target="_blank">User Guide</a>
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
        if ('http://invninjv1.local/vendors/3/edit' != settingsURL) {
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
