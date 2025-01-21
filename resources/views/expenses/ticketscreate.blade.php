<style>
    .td-left {width:1%; white-space:nowrap; text-align: right; height:40px;}
    .td-right {width:1%; white-space:nowrap; text-align: left; height:40px;}
    #accordion .ui-accordion-header {background: #033e5e; color: #fff;}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Source: https://github.com/invoiceninja/invoiceninja -->
    <!-- Version: 4.5.19 -->
            <meta charset="utf-8">

            <title>New Ticket | Invoice Ninja</title>
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
    <link rel="canonical" href="https://app.invoiceninja.com/tickets/create"/>

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
        weekStart: 0
    });

    if (isStorageSupported()) {
          }

    $('ul.navbar-settings, ul.navbar-search').hover(function () {
        if ($('.user-companies').css('display') == 'block') {
            $('.user-companies').dropdown('toggle');
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



    <script src="/js/jquery.datetimepicker.js" type="text/javascript"></script>
    <link href="/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css"/>
    <link href="/css/quill.snow.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="/js/quill.min.js" type="text/javascript"></script>


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
                                                                <li class="nav-tickets active">

            <a type="button" class="btn btn-primary btn-sm pull-right"
            href="/tickets/create">
            <i class="fa fa-plus-circle" style="width:20px" title="Create New"></i>
        </a>
    
    <a href="/tickets"
        style="padding-top:6px; padding-bottom:6px"
        class="nav-link active">
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

            <a type="button" class="btn btn-default btn-sm pull-right" title="User Guide: Latest"
            href="https://invoice-ninja.readthedocs.io/en/latest" target="_blank">
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
          
          
    <form accept-charset="utf-8" class="form-horizontal col-lg-10 col-lg-offset-1 warn-on-exit main-form" autocomplete="off" method="POST" action="/tickets">

    <div style="display:none">
        <div class="form-group"><label for="data" class="control-label col-lg-4 col-sm-4">Data</label><div class="col-lg-8 col-sm-8"><input class="form-control" data-bind="value: ko.mapping.toJSON(model)" id="data" type="text" name="data"></div></div>
        <input type="hidden" name="category_id" value="1">
                <input type="hidden" name="status_id" value="1">
    </div>

    <div style="display:none">
        <div class="form-group"><label for="data" class="control-label col-lg-4 col-sm-4">Data</label><div class="col-lg-8 col-sm-8"><input class="form-control" data-bind="value: ko.mapping.toJSON(model)" id="data-2" type="text" name="data"></div></div>
    </div>

    <div class="panel panel-default">

        <div class="panel-heading">
            <h3 class="panel-title">New Ticket</h3>
        </div>

        <div class="panel-body">

            Subject
            <div class="form-group required"><div class="col-lg-8 col-sm-8"><input required class="form-control" id="subject" style="width:100%;" type="text" name="subject"></div></div>


            Description

            <textarea required class="form-control" style="display:none" id="description" name="description"></textarea>

            <div id="descriptionEditor" class="form-control" style="min-height:160px; max-height:160px;" onclick="focusEditor()"></div>

            <div class="pull-left">
                <div id="toolbar-toolbar" class="toolbar ql-toolbar ql-snow" style="padding-left:0px;padding-right:0px">
    <div id="descriptionToolbar" class="toolbar" style="padding-left: 0px">
        <span class="ql-format-group">
            <select title="Font" class="ql-font">
                <option value="sans-serif" selected="">Sans Serif</option>
                <option value="serif">Serif</option>
                <option value="monospace">Monospace</option>
            </select>
            <select title="Size" class="ql-size">
                <option value="10px">Small</option>
                <option value="13px" selected="">Normal</option>
                <option value="18px">Large</option>
                <option value="32px">Huge</option>
            </select>
        </span>
        <span class="ql-format-group">
            <span title="Bold" class="ql-format-button ql-bold"></span>
            <span class="ql-format-separator"></span>
            <span title="Italic" class="ql-format-button ql-italic"></span>
            <span class="ql-format-separator"></span>
            <span title="Underline" class="ql-format-button ql-underline"></span>
            <span class="ql-format-separator"></span>
            <span title="Strikethrough" class="ql-format-button ql-strike"></span>
        </span>
        <span class="ql-format-group">
            <select title="Text Color" class="ql-color">
                <option value="rgb(0, 0, 0)" label="rgb(0, 0, 0)" selected=""></option>
                <option value="rgb(230, 0, 0)" label="rgb(230, 0, 0)"></option>
                <option value="rgb(255, 153, 0)" label="rgb(255, 153, 0)"></option>
                <option value="rgb(255, 255, 0)" label="rgb(255, 255, 0)"></option>
                <option value="rgb(0, 138, 0)" label="rgb(0, 138, 0)"></option>
                <option value="rgb(0, 102, 204)" label="rgb(0, 102, 204)"></option>
                <option value="rgb(153, 51, 255)" label="rgb(153, 51, 255)"></option>
                <option value="rgb(255, 255, 255)" label="rgb(255, 255, 255)"></option>
                <option value="rgb(250, 204, 204)" label="rgb(250, 204, 204)"></option>
                <option value="rgb(255, 235, 204)" label="rgb(255, 235, 204)"></option>
                <option value="rgb(255, 255, 204)" label="rgb(255, 255, 204)"></option>
                <option value="rgb(204, 232, 204)" label="rgb(204, 232, 204)"></option>
                <option value="rgb(204, 224, 245)" label="rgb(204, 224, 245)"></option>
                <option value="rgb(235, 214, 255)" label="rgb(235, 214, 255)"></option>
                <option value="rgb(187, 187, 187)" label="rgb(187, 187, 187)"></option>
                <option value="rgb(240, 102, 102)" label="rgb(240, 102, 102)"></option>
                <option value="rgb(255, 194, 102)" label="rgb(255, 194, 102)"></option>
                <option value="rgb(255, 255, 102)" label="rgb(255, 255, 102)"></option>
                <option value="rgb(102, 185, 102)" label="rgb(102, 185, 102)"></option>
                <option value="rgb(102, 163, 224)" label="rgb(102, 163, 224)"></option>
                <option value="rgb(194, 133, 255)" label="rgb(194, 133, 255)"></option>
                <option value="rgb(136, 136, 136)" label="rgb(136, 136, 136)"></option>
                <option value="rgb(161, 0, 0)" label="rgb(161, 0, 0)"></option>
                <option value="rgb(178, 107, 0)" label="rgb(178, 107, 0)"></option>
                <option value="rgb(178, 178, 0)" label="rgb(178, 178, 0)"></option>
                <option value="rgb(0, 97, 0)" label="rgb(0, 97, 0)"></option>
                <option value="rgb(0, 71, 178)" label="rgb(0, 71, 178)"></option>
                <option value="rgb(107, 36, 178)" label="rgb(107, 36, 178)"></option>
                <option value="rgb(68, 68, 68)" label="rgb(68, 68, 68)"></option>
                <option value="rgb(92, 0, 0)" label="rgb(92, 0, 0)"></option>
                <option value="rgb(102, 61, 0)" label="rgb(102, 61, 0)"></option>
                <option value="rgb(102, 102, 0)" label="rgb(102, 102, 0)"></option>
                <option value="rgb(0, 55, 0)" label="rgb(0, 55, 0)"></option>
                <option value="rgb(0, 41, 102)" label="rgb(0, 41, 102)"></option>
                <option value="rgb(61, 20, 102)" label="rgb(61, 20, 102)"></option>
            </select>
            <span class="ql-format-separator"></span>
            <select title="Background Color" class="ql-background">
                <option value="rgb(0, 0, 0)" label="rgb(0, 0, 0)"></option>
                <option value="rgb(230, 0, 0)" label="rgb(230, 0, 0)"></option>
                <option value="rgb(255, 153, 0)" label="rgb(255, 153, 0)"></option>
                <option value="rgb(255, 255, 0)" label="rgb(255, 255, 0)"></option>
                <option value="rgb(0, 138, 0)" label="rgb(0, 138, 0)"></option>
                <option value="rgb(0, 102, 204)" label="rgb(0, 102, 204)"></option>
                <option value="rgb(153, 51, 255)" label="rgb(153, 51, 255)"></option>
                <option value="rgb(255, 255, 255)" label="rgb(255, 255, 255)" selected=""></option>
                <option value="rgb(250, 204, 204)" label="rgb(250, 204, 204)"></option>
                <option value="rgb(255, 235, 204)" label="rgb(255, 235, 204)"></option>
                <option value="rgb(255, 255, 204)" label="rgb(255, 255, 204)"></option>
                <option value="rgb(204, 232, 204)" label="rgb(204, 232, 204)"></option>
                <option value="rgb(204, 224, 245)" label="rgb(204, 224, 245)"></option>
                <option value="rgb(235, 214, 255)" label="rgb(235, 214, 255)"></option>
                <option value="rgb(187, 187, 187)" label="rgb(187, 187, 187)"></option>
                <option value="rgb(240, 102, 102)" label="rgb(240, 102, 102)"></option>
                <option value="rgb(255, 194, 102)" label="rgb(255, 194, 102)"></option>
                <option value="rgb(255, 255, 102)" label="rgb(255, 255, 102)"></option>
                <option value="rgb(102, 185, 102)" label="rgb(102, 185, 102)"></option>
                <option value="rgb(102, 163, 224)" label="rgb(102, 163, 224)"></option>
                <option value="rgb(194, 133, 255)" label="rgb(194, 133, 255)"></option>
                <option value="rgb(136, 136, 136)" label="rgb(136, 136, 136)"></option>
                <option value="rgb(161, 0, 0)" label="rgb(161, 0, 0)"></option>
                <option value="rgb(178, 107, 0)" label="rgb(178, 107, 0)"></option>
                <option value="rgb(178, 178, 0)" label="rgb(178, 178, 0)"></option>
                <option value="rgb(0, 97, 0)" label="rgb(0, 97, 0)"></option>
                <option value="rgb(0, 71, 178)" label="rgb(0, 71, 178)"></option>
                <option value="rgb(107, 36, 178)" label="rgb(107, 36, 178)"></option>
                <option value="rgb(68, 68, 68)" label="rgb(68, 68, 68)"></option>
                <option value="rgb(92, 0, 0)" label="rgb(92, 0, 0)"></option>
                <option value="rgb(102, 61, 0)" label="rgb(102, 61, 0)"></option>
                <option value="rgb(102, 102, 0)" label="rgb(102, 102, 0)"></option>
                <option value="rgb(0, 55, 0)" label="rgb(0, 55, 0)"></option>
                <option value="rgb(0, 41, 102)" label="rgb(0, 41, 102)"></option>
                <option value="rgb(61, 20, 102)" label="rgb(61, 20, 102)"></option>
            </select>
        </span>
        <span class="ql-format-group">
            <span title="List" class="ql-format-button ql-list"></span>
            <span class="ql-format-separator"></span>
            <span title="Bullet" class="ql-format-button ql-bullet"></span>
            <span class="ql-format-separator"></span>
            <select title="Text Alignment" class="ql-align">
                <option value="left" label="Left" selected=""></option>
                <option value="center" label="Center"></option>
                <option value="right" label="Right"></option>
                <option value="justify" label="Justify"></option>
            </select>
        </span>
        <span class="ql-format-group">
            <span title="Link" class="ql-format-button ql-link"></span>
        </span>
    </div>
</div>
            </div>

        </div>

        <div class="panel-body">

            <div class="row">

                <div class="col-md-3">
                    Client
                </div>

                <div class="col-md-9">

                    <div class="form-group client-select"><div class="col-lg-8 col-sm-8"><select class="pull-right form-control" data-bind="dropdown: client_public_id, dropdownOptions: {highlighter: comboboxHighlighter}" id="client_public_id" name="client_public_id"><option value=""></option></select></div></div>

                </div>

            </div>

            <div class="row">

                <div class="col-md-3">
                    Agent
                </div>

                <div class="col-md-9">
                    <div class="form-group"><div class="col-lg-8 col-sm-8"><select class="pull-right form-control" data-bind="dropdown: agent_id, dropdownOptions: {highlighter: comboboxHighlighter}" id="agent_id" name="agent_id"><option value=""></option></select></div></div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-3">
                    Priority
                </div>
                <div class="col-md-9">
                    <div class="form-group"><div class="col-lg-8 col-sm-8"><select class="form-control" id="priority_id" name="priority_id"><option value="10">Low</option><option value="20">Medium</option><option value="30">High</option></select></div></div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-3">
                    Due Date
                </div>

                <div class="col-md-9">
                    <input id="due_date" type="text" data-bind="value: due_date.pretty" name="due_date"
                           class="form-control time-input time-input-end" placeholder="Due Date"/>
                </div>

            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-3">
                    Internal ticket
                </div>

                <div class="col-md-9">
                    <div class="form-group"><div class="col-lg-8 col-sm-8"><div class="checkbox"><input type="hidden" name="is_internal" value="0"><input data-bind="checked: is_internal.pretty" id="is_internal" type="checkbox" name="is_internal" value="1"></div></div></div>
                </div>
            </div>


            <div class="row" data-bind="visible: isParentTicketVisible()">

                <div class="col-md-3">
                    Parent Ticket
                </div>

                <div class="col-md-9">

                    <div class="form-group"><div class="col-lg-8 col-sm-8"><select class="pull-right form-control" data-bind="dropdown: parent_ticket_id, dropdownOptions: {highlighter: comboboxHighlighter}" id="parent_ticket_id" name="parent_ticket_id"><option value=""></option></select></div></div>

                </div>

            </div>

        </div>

    </div>

    <div role="tabpanel" class="panel-default" style="margin-top:30px;">

        <ul class="nav nav-tabs" role="tablist" style="border: none">
            <li role="presentation" class="active"><a href="#private_notes" aria-controls="private_notes" role="tab" data-toggle="tab">Private Notes</a></li>
                            <li role="presentation"><a href="#attached-documents" aria-controls="attached-documents" role="tab" data-toggle="tab">
                        Documents

                    </a></li>
                    </ul>

        
        

        <div class="tab-content" style="padding-right:12px;">

            <div role="tabpanel" class="tab-pane active" id="private_notes" style="padding-bottom:44px">
                <div class="form-group"><div class="col-lg-12 col-sm-12"><textarea class="form-control" data-bind="value: private_notes, valueUpdate: 'afterkeydown'" style="width: 100%" rows="4" id="private_notes" name="private_notes"></textarea></div></div>
            </div>

            <div role="tabpanel" class="tab-pane" id="attached-documents" style="position:relative; z-index:9;">
                <div id="document-upload">
                    <div class="dropzone">
                        <div data-bind="foreach: documents">
                            <input type="hidden" name="document_ids[]" data-bind="value: public_id"/>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        
        

    </div>

    <div class="row">
        <center class="buttons">
            <button type='button' class='btn btn-primary btn-lg' onclick='saveAction()'>Create ticket</button>
        </center>
    </div>

    <input type="hidden" name="_token" value="IH4RfjHyCuiL4BTW0mJXyy0httbYIu2gFF50M3a6"></form>


    <script type="text/javascript">

        $( function() {
            $("#subject").focus();

            window.model = new ViewModel({"parent_ticket_id":null,"subject":"","description":"","due_date":"","client_public_id":null,"agent_id":null,"is_internal":false,"private_notes":"","priority_id":1});

            <!----- Client Selector ----->
            var clients = [];
            var clientMap = {};
            var $clientSelect = $('select#client_public_id');

            // create client dictionary
            for (var i=0; i<clients.length; i++) {
                var client = clients[i];
                clientMap[client.public_id] = client;

                if (!getClientDisplayName(client)) {
                    continue;
                }

                var clientName = client.name || '';
                for (var j=0; j<client.contacts.length; j++) {
                    var contact = client.contacts[j];
                    var contactName = getContactDisplayNameWithEmail(contact);
                    if (clientName && contactName) {
                        clientName += ' • ';
                    }
                    if (contactName) {
                        clientName += contactName;
                    }
                }
                $clientSelect.append(new Option(clientName, client.public_id));
            }

            //harvest and set the client_id and contact_id here
            var $input = $('select#client_public_id');
            $input.combobox().on('change', function(e) {
                var clientId = parseInt($('input[name=client_public_id]').val(), 10) || 0;

                if (clientId > 0) {

                    model.client_public_id(clientId);

                }
            });

            <!----- User Selector ----->
            var users = [{"id":1,"company_id":1,"created_at":"2020-08-08 18:20:19","updated_at":"2020-08-08 18:20:20","deleted_at":null,"first_name":"Norman","last_name":"Osborn","phone":null,"username":"norman@rideafide.local","email":"norman@rideafide.local","registered":1,"confirmed":1,"notify_sent":1,"notify_viewed":0,"notify_paid":1,"public_id":null,"force_pdfjs":0,"news_feed_id":null,"notify_approved":1,"failed_logins":null,"dark_mode":1,"referral_code":null,"is_admin":1,"bot_user_id":null,"accepted_terms_version":"1.0.1","accepted_terms_timestamp":"2020-08-08 18:20:20","accepted_terms_ip":"172.26.0.1","only_notify_owned":0,"permissions":"","avatar":"","avatar_width":0,"avatar_height":0,"avatar_size":0,"signature":""}];
            var userMap = {};
            var $userSelect = $('select#agent_id');

            // create user dictionary
            for (var i=0; i<users.length; i++) {
                var user = users[i];
                userMap[user.id] = user;

                var userName = user.first_name + ' ' + user.last_name || '';
                $userSelect.append(new Option(userName, user.id));
            }

            //harvest and set the agent_id here
            var $input = $('select#agent_id');
            $input.combobox().on('change', function(e) {
                var agentId = parseInt($('input[name=agent_id]').val(), 10) || 0;

                if (agentId > 0) {

                    model.agent_id(agentId);

                }
            });

            <!----- Parent Ticket Selector ----->
            var parent_tickets = [];
            var ticketMap = {};
            var $ticketSelect = $('select#parent_ticket_id');

            // create client dictionary
            for (var i=0; i<parent_tickets.length; i++) {
                var ticket = parent_tickets[i];
                ticketMap[ticket.public_id] = ticket;

                var ticketName = 'Ticket #' + '' + ticket.ticket_number;
                $ticketSelect.append(new Option(ticketName, ticket.public_id));
            }

            //harvest and set the parent_ticket_id here
            var $input = $('select#parent_ticket_id');
            $input.combobox().on('change', function(e) {
                var parentTicketId = parseInt($('input[name=parent_ticket_id]').val(), 10) || 0;

                if (parentTicketId > 0) {

                    model.parent_ticket_id(parentTicketId);

                }
            });


            ko.applyBindings(model);

            Dropzone.autoDiscover = false;
window.countUploadingDocuments = 0;

window.dropzone = new Dropzone('#document-upload .dropzone', {
    url: "http:\/\/invninjv1.local\/documents",
    params: {
        '_token': 'IH4RfjHyCuiL4BTW0mJXyy0httbYIu2gFF50M3a6',
        'is_default': 0,
    },
    acceptedFiles: "image\/png,image\/jpeg,image\/tiff,application\/pdf,image\/gif,image\/vnd.adobe.photoshop,text\/plain,application\/msword,application\/excel,application\/vnd.ms-excel,application\/x-excel,application\/x-msexcel,application\/vnd.openxmlformats-officedocument.wordprocessingml.document,application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application\/postscript,image\/svg+xml,application\/vnd.openxmlformats-officedocument.presentationml.presentation,application\/vnd.ms-powerpoint",
    addRemoveLinks: true,
    dictRemoveFileConfirmation: "Are you sure?",
            "dictDefaultMessage" : "Drop files or click to upload",
            "dictFallbackMessage" : "Your browser does not support drag'n'drop file uploads.",
            "dictFallbackText" : "Please use the fallback form below to upload your files like in the olden days.",
            "dictFileTooBig" : "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
            "dictInvalidFileType" : "You can't upload files of this type.",
            "dictResponseError" : "Server responded with {{statusCode}} code.",
            "dictCancelUpload" : "Cancel upload",
            "dictCancelUploadConfirmation" : "Are you sure you want to cancel this upload?",
            "dictRemoveFile" : "Remove file",
        maxFilesize: 10,
    parallelUploads: 1,
});

if (dropzone instanceof Dropzone) {
    dropzone.on('addedfile', handleDocumentAdded);
    dropzone.on('removedfile', handleDocumentRemoved);
    dropzone.on('success', handleDocumentUploaded);
    dropzone.on('canceled', handleDocumentCanceled);
    dropzone.on('error', handleDocumentError);
    for (var i=0; i < model.documents().length; i++) {
        var document = model.documents()[i];
        var mockFile = {
            name: ko.utils.unwrapObservable(document.name),
            size: ko.utils.unwrapObservable(document.size),
            type: ko.utils.unwrapObservable(document.type),
            public_id: ko.utils.unwrapObservable(document.public_id),
            status: Dropzone.SUCCESS,
            accepted: true,
            url: ko.utils.unwrapObservable(document.url),
            mock: true,
            index: i,
        };

        dropzone.emit('addedfile', mockFile);
        dropzone.emit('complete', mockFile);

        var documentType = ko.utils.unwrapObservable(document.type);
        var previewUrl = ko.utils.unwrapObservable(document.preview_url);
        var documentUrl = ko.utils.unwrapObservable(document.url);

        if (previewUrl) {
            dropzone.emit('thumbnail', mockFile, previewUrl);
        } else if (documentType == 'jpeg' || documentType == 'png' || documentType == 'svg') {
            dropzone.emit('thumbnail', mockFile, documentUrl);
        }

        dropzone.files.push(mockFile);
    }
}

function handleDocumentAdded(file){
    // open document when clicked
    if (file.url) {
        file.previewElement.addEventListener("click", function() {
            window.open(file.url, '_blank');
        });
    }
    if(file.mock)return;
    if (window.addDocument) {
        addDocument(file);
    }
    window.countUploadingDocuments++;
}

function handleDocumentRemoved(file){
    if (window.deleteDocument) {
        deleteDocument(file);
    }
    $.ajax({
        url: '/documents/' + file.public_id,
        type: 'DELETE',
        success: function(result) {
            // Do something with the result
        }
    });
}

function handleDocumentUploaded(file, response){
    window.countUploadingDocuments--;
    file.public_id = response.document.public_id
    if (window.addedDocument) {
        addedDocument(file, response);
    }
    if(response.document.preview_url){
        dropzone.emit('thumbnail', file, response.document.preview_url);
    }
}

function handleDocumentCanceled() {
    window.countUploadingDocuments--;
}

function handleDocumentError(file) {
    dropzone.removeFile(file);
    window.countUploadingDocuments--;
    swal("An error occurred, please refresh the page and try again.");
}

        });

        <!-- Initialize drop zone file uploader -->
        $('.main-form').submit(function(){
            if($('#document-upload .dropzone .fallback input').val())$(this).attr('enctype', 'multipart/form-data')
            else $(this).removeAttr('enctype')
        })

        <!-- Initialize date time picker for due date -->
        jQuery('#due_date').datetimepicker({
            lazyInit: true,
            validateOnBlur: false,
            step: '15',
            minDate: 'moment()',
            validateOnBlur: false
        });

        var ViewModel = function (data) {
            var self = this;

            console.log(data);

            var dateTimeFormat = 'MMM D, YYYY h:mm:ss a';
            var timezone = 'US/Eastern';

            self.documents = ko.observableArray();
            self.due_date = ko.observable(data.due_date);
            self.priority_id = ko.observable(data.priority_id);
            self.agent_id = ko.observable(data.agent_id);
            self.is_internal = ko.observable(data.is_internal);
            self.subject = ko.observable(data.subject);
            self.description = ko.observable(data.description);
            self.client_public_id = ko.observable(data.client_public_id);
            self.parent_ticket_id = ko.observable(data.parent_ticket_id);
            self.private_notes = ko.observable(data.private_notes);

            self.mapping = {
                'documents': {
                    create: function (options) {
                        return new DocumentModel(options.data);
                    }
                }
            }

            self.isParentTicketVisible = function() {
                return self.is_internal.pretty;
            }

            self.is_internal.pretty = ko.computed({
                read: function() {
                    if(self.is_internal() == 0)
                        return false;
                    else
                        return true;
                },
                write: function(newValue) {
                    model.is_internal(newValue ? true : false);
                }

            });

            self.due_date.pretty = ko.computed({
                read: function() {
                    return self.due_date() ? moment(self.due_date()).format(dateTimeFormat) : '';
                },
                write: function(data) {
                    self.due_date(moment(data).format("YYYY-MM-DD HH:mm:ss"));
                }
            });

            if (data) {
                ko.mapping.fromJS(data, self.mapping, this);
            }

            self.addDocument = function() {
                var documentModel = new DocumentModel();
                self.documents.push(documentModel);
                return documentModel;
            }

            self.removeDocument = function(doc) {
                var public_id = doc.public_id?doc.public_id():doc;
                self.documents.remove(function(document) {
                    return document.public_id() == public_id;
                });
            }


        };


        function DocumentModel(data) {
            var self = this;
            self.public_id = ko.observable(0);
            self.size = ko.observable(0);
            self.name = ko.observable('');
            self.type = ko.observable('');
            self.url = ko.observable('');

            self.update = function(data){
                ko.mapping.fromJS(data, {}, this);
            }

            if (data) {
                self.update(data);
            }
        }

        function addDocument(file) {
            file.index = model.documents().length;
            model.addDocument({name:file.name, size:file.size, type:file.type});
        }

        function addedDocument(file, response) {
            model.documents()[file.index].update(response.document);
        }

        function deleteDocument(file) {
            model.removeDocument(file.public_id);
        }

        var editor = false;
        $(function() {
            editor = new Quill('#descriptionEditor', {
                modules: {
                    'toolbar': { container: '#descriptionToolbar' },
                    'link-tooltip': true
                },
                theme: 'snow'
            });
            editor.setHTML($('#description').val());
            editor.on('text-change', function(delta, source) {
                if (source == 'api') {
                    return;
                }
                var html = editor.getHTML();
                $('#description').val(html);
                NINJA.formIsChanged = true;
            });
        });

        function focusEditor() {
            editor.focus();
        }



        function saveAction() {

            var dateTimeFormat = 'MMM D, YYYY h:mm:ss a';

            if($('#due_date').val().length > 1)
                $('#due_date').val(moment($('#due_date').val(), dateTimeFormat).format("YYYY-MM-DD HH:mm:ss"));

            $('.main-form').submit();
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
          <p>Note: the white-label license is intended for personal use, please email us at <a href="m&#97;&#x69;&#108;&#116;o:&#x63;&#111;&#x6e;&#116;&#97;&#x63;&#116;&#64;i&#110;&#x76;o&#105;&#99;e&#110;&#x69;&#110;&#x6a;&#x61;&#46;&#x63;&#111;&#x6d;">&#x63;&#111;&#x6e;&#116;&#97;&#x63;&#116;&#64;i&#110;&#x76;o&#105;&#99;e&#110;&#x69;&#110;&#x6a;&#x61;&#46;&#x63;&#111;&#x6d;</a> if you'd like to resell the app.</p>
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
                          We're actively working to improve this feature, if there's a command you'd like us to support please email us at <a href="&#109;&#97;&#x69;&#x6c;to:"></a>.
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
        <a class="btn btn-primary" href="https://invoice-ninja.readthedocs.io/en/latest" target="_blank">User Guide</a>
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
        if ('http://invninjv1.local/tickets/create' != settingsURL) {
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
