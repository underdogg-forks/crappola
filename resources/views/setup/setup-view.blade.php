<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice Ninja | Setup</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="lZMf1gNYZHxhQxLLOkBymnEXtjjjEW4SjNwsntHt">
    <script src="http://invninjv1.local/built.js?no_cache=4.5.46" type="text/javascript"></script>
    <link href="http://invninjv1.local/css/built.public.css?no_cache=4.5.46" rel="stylesheet" type="text/css"/>
    <link href="http://invninjv1.local/css/built.css?no_cache=4.5.46" rel="stylesheet" type="text/css"/>
    <link href="http://invninjv1.local/favicon.png?test" rel="shortcut icon">

    <style type="text/css">
        body {
            background-color: #FEFEFE;
        }
    </style>

</head>

<body>
<div class="container">

    &nbsp;
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <div class="jumbotron">
                <h2>Invoice Ninja Setup</h2>
                If you need help you can either post to our <a href="https://www.invoiceninja.com/forums/forum/support/"
                                                               target="_blank">support forum</a> or email us at <a
                        href="mailto:contact@invoiceninja.com" target="_blank">contact@invoiceninja.com</a>.

                <p>
                <pre>-- Commands to create a MySQL database and user
CREATE SCHEMA `ninja` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'ninja'@'localhost' IDENTIFIED BY 'ninja';
GRANT ALL PRIVILEGES ON `ninja`.* TO 'ninja'@'localhost';
FLUSH PRIVILEGES;</pre>
                </p>
            </div>

            <form accept-charset="utf-8" class="form-horizontal" method="POST">

                <div style="display:block">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Application Settings</h3>
                        </div>
                        <div class="panel-body form-padding-right">
                            <div class="form-group required"><label for="app[url]"
                                                                    class="control-label col-lg-4 col-sm-4">URL</label>
                                <div class="col-lg-8 col-sm-8"><input required class="form-control" id="app[url]"
                                                                      type="text" name="app[url]"
                                                                      value="http://invninjv1.local"></div>
                            </div>
                            <div class="form-group"><label for="https"
                                                           class="control-label col-lg-4 col-sm-4">HTTPS</label>
                                <div class="col-lg-8 col-sm-8">
                                    <div class="checkbox"><label for="https" class=""><input type="hidden" name="https"
                                                                                             value="0"><input id="https"
                                                                                                              type="checkbox"
                                                                                                              name="https"
                                                                                                              value="1">Require</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group"><label for="debug"
                                                           class="control-label col-lg-4 col-sm-4">Debug</label>
                                <div class="col-lg-8 col-sm-8">
                                    <div class="checkbox"><label for="debug" class=""><input type="hidden" name="debug"
                                                                                             value="0"><input id="debug"
                                                                                                              type="checkbox"
                                                                                                              name="debug"
                                                                                                              checked="checked"
                                                                                                              value="1">Enable</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Database Connection</h3>
                        </div>
                        <div class="panel-body form-padding-right">

                            <div class="form-group"><label for="" class="control-label col-lg-4 col-sm-4">Driver</label>
                                <div class="col-lg-8 col-sm-8">
                                    <div class="form-control-static" id="driver">MySQL</div>
                                </div>
                            </div>
                            <div class="form-group required"><label for="database[type][host]"
                                                                    class="control-label col-lg-4 col-sm-4">Host</label>
                                <div class="col-lg-8 col-sm-8"><input required class="form-control"
                                                                      id="database[type][host]" type="text"
                                                                      name="database[type][host]" value="db"></div>
                            </div>
                            <div class="form-group required"><label for="database[type][database]"
                                                                    class="control-label col-lg-4 col-sm-4">Database</label>
                                <div class="col-lg-8 col-sm-8"><input required class="form-control"
                                                                      id="database[type][database]" type="text"
                                                                      name="database[type][database]"
                                                                      value="invninj_db"></div>
                            </div>
                            <div class="form-group required"><label for="database[type][username]"
                                                                    class="control-label col-lg-4 col-sm-4">Username</label>
                                <div class="col-lg-8 col-sm-8"><input required class="form-control"
                                                                      id="database[type][username]" type="text"
                                                                      name="database[type][username]" value="root">
                                </div>
                            </div>
                            <div class="form-group"><label for="database[type][password]"
                                                           class="control-label col-lg-4 col-sm-4">Password</label>
                                <div class="col-lg-8 col-sm-8"><input class="form-control" id="database[type][password]"
                                                                      type="password" name="database[type][password]"
                                                                      value="root"></div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-4 col-sm-offset-4 col-lg-8 col-sm-8">
                                    <button type='button' class='btn btn-primary btn-sm' onclick='testDatabase()'>Test
                                        connection
                                    </button> &nbsp;&nbsp;<span id="dbTestResult"/></div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Email Settings</h3>
                        </div>
                        <div class="panel-body form-padding-right">
                            <div class="form-group"><label for="mail[driver]" class="control-label col-lg-4 col-sm-4">Driver</label>
                                <div class="col-lg-8 col-sm-8"><select class="form-control"
                                                                       onchange="mailDriverChange()" id="mail[driver]"
                                                                       name="mail[driver]">
                                        <option value="smtp" selected="selected">SMTP</option>
                                        <option value="mail">Mail</option>
                                        <option value="sendmail">Sendmail</option>
                                        <option value="mailgun">Mailgun</option>
                                    </select></div>
                            </div>
                            <div class="form-group"><label for="mail[from][name]"
                                                           class="control-label col-lg-4 col-sm-4">From Name</label>
                                <div class="col-lg-8 col-sm-8"><input class="form-control" id="mail[from][name]"
                                                                      type="text" name="mail[from][name]" value="">
                                </div>
                            </div>
                            <div class="form-group"><label for="mail[from][address]"
                                                           class="control-label col-lg-4 col-sm-4">From Address</label>
                                <div class="col-lg-8 col-sm-8"><input class="form-control" id="mail[from][address]"
                                                                      type="text" name="mail[from][address]" value="">
                                </div>
                            </div>
                            <div class="form-group"><label for="mail[username]" class="control-label col-lg-4 col-sm-4">Username</label>
                                <div class="col-lg-8 col-sm-8"><input class="form-control" id="mail[username]"
                                                                      type="text" name="mail[username]" value=""></div>
                            </div>
                            <div id="standardMailSetup">
                                <div class="form-group"><label for="mail[host]" class="control-label col-lg-4 col-sm-4">Host</label>
                                    <div class="col-lg-8 col-sm-8"><input class="form-control" id="mail[host]"
                                                                          type="text" name="mail[host]" value=""></div>
                                </div>
                                <div class="form-group"><label for="mail[port]" class="control-label col-lg-4 col-sm-4">Port</label>
                                    <div class="col-lg-8 col-sm-8"><input class="form-control" id="mail[port]"
                                                                          type="text" name="mail[port]" value="587">
                                    </div>
                                </div>
                                <div class="form-group"><label for="mail[encryption]"
                                                               class="control-label col-lg-4 col-sm-4">Encryption</label>
                                    <div class="col-lg-8 col-sm-8"><select class="form-control" id="mail[encryption]"
                                                                           name="mail[encryption]">
                                            <option value="tls" selected="selected">TLS</option>
                                            <option value="ssl">SSL</option>
                                            <option value="">None</option>
                                        </select></div>
                                </div>
                                <div class="form-group"><label for="mail[password]"
                                                               class="control-label col-lg-4 col-sm-4">Password</label>
                                    <div class="col-lg-8 col-sm-8"><input class="form-control" id="mail[password]"
                                                                          type="password" name="mail[password]"
                                                                          value=""></div>
                                </div>
                            </div>
                            <div id="mailgunMailSetup">
                                <div class="form-group"><label for="mail[mailgun_domain]"
                                                               class="control-label col-lg-4 col-sm-4">Mailgun
                                        Domain</label>
                                    <div class="col-lg-8 col-sm-8"><input class="form-control" id="mail[mailgun_domain]"
                                                                          type="text" name="mail[mailgun_domain]"
                                                                          value=""></div>
                                </div>
                                <div class="form-group"><label for="mail[mailgun_secret]"
                                                               class="control-label col-lg-4 col-sm-4">Mailgun Private
                                        Key</label>
                                    <div class="col-lg-8 col-sm-8"><input class="form-control" id="mail[mailgun_secret]"
                                                                          type="text" name="mail[mailgun_secret]"
                                                                          value=""></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-4 col-sm-offset-4 col-lg-8 col-sm-8">
                                    <button type='button' class='btn btn-primary btn-sm' onclick='testMail()'>Send test
                                        email
                                    </button> &nbsp;&nbsp;<span id="mailTestResult"/></div>
                            </div>
                        </div>
                    </div>

                    <script type="text/javascript">

                        var db_valid = false
                        var mail_valid = false
                        mailDriverChange();

                        function testDatabase() {
                            var data = $("form").serialize() + "&test=db";

                            // Show Progress Text
                            $('#dbTestResult').html('Working...').css('color', 'black');

                            // Send / Test Information
                            $.post("http://invninjv1.local/setup", data, function (data) {
                                var color = 'red';
                                if (data == 'Success') {
                                    color = 'green';
                                    db_valid = true;
                                }
                                $('#dbTestResult').html(data).css('color', color);
                            });

                            return db_valid;
                        }

                        function mailDriverChange() {
                            if ($("select[name='mail[driver]']").val() == 'mailgun') {
                                $("#standardMailSetup").hide();
                                $("#standardMailSetup").children('select,input').prop('disabled', true);
                                $("#mailgunMailSetup").show();
                                $("#mailgunMailSetup").children('select,input').prop('disabled', false);

                            } else {
                                $("#standardMailSetup").show();
                                $("#standardMailSetup").children('select,input').prop('disabled', false);

                                $("#mailgunMailSetup").hide();
                                $("#mailgunMailSetup").children('select,input').prop('disabled', true);

                            }
                        }

                        function testMail() {
                            var data = $("form").serialize() + "&test=mail";

                            // Show Progress Text
                            $('#mailTestResult').html('Working...').css('color', 'black');

                            // Send / Test Information
                            $.post("http://invninjv1.local/setup", data, function (data) {
                                var color = 'red';
                                if (data == 'Sent') {
                                    color = 'green';
                                    mail_valid = true;
                                }
                                $('#mailTestResult').html(data).css('color', color);
                            });

                            return mail_valid;
                        }

                        // Prevent the Enter Button from working
                        $("form").bind("keypress", function (e) {
                            if (e.keyCode == 13) {
                                return false;
                            }
                        });

                    </script>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">User Details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group required"><label for="first_name"
                                                                class="control-label col-lg-4 col-sm-4">First
                                Name</label>
                            <div class="col-lg-8 col-sm-8"><input required class="form-control" id="first_name"
                                                                  type="text" name="first_name"></div>
                        </div>
                        <div class="form-group required"><label for="last_name" class="control-label col-lg-4 col-sm-4">Last
                                Name</label>
                            <div class="col-lg-8 col-sm-8"><input required class="form-control" id="last_name"
                                                                  type="text" name="last_name"></div>
                        </div>
                        <div class="form-group required"><label for="email" class="control-label col-lg-4 col-sm-4">Email</label>
                            <div class="col-lg-8 col-sm-8"><input required class="form-control" id="email" type="email"
                                                                  name="email"></div>
                        </div>
                        <div class="form-group required"><label for="password" class="control-label col-lg-4 col-sm-4">Password</label>
                            <div class="col-lg-8 col-sm-8"><input required class="form-control" id="password"
                                                                  type="password" name="password"></div>
                        </div>
                    </div>
                </div>


                <div class="form-group required"><label for="terms_checkbox"
                                                        class="control-label col-lg-4 col-sm-4"> </label>
                    <div class="col-lg-8 col-sm-8">
                        <div class="checkbox"><label for="terms_checkbox" class=""><input required type="hidden"
                                                                                          name="terms_checkbox"
                                                                                          value="0"><input required
                                                                                                           id="terms_checkbox"
                                                                                                           type="checkbox"
                                                                                                           name="terms_checkbox"
                                                                                                           value="1">I
                                agree to the <a href="https://www.invoiceninja.com/self-hosting-terms-service/"
                                                target="_blank">Terms of Service</a></label></div>
                    </div>
                </div>
                <div class="form-group required"><label for="privacy_checkbox"
                                                        class="control-label col-lg-4 col-sm-4"> </label>
                    <div class="col-lg-8 col-sm-8">
                        <div class="checkbox"><label for="privacy_checkbox" class=""><input required type="hidden"
                                                                                            name="privacy_checkbox"
                                                                                            value="0"><input required
                                                                                                             id="privacy_checkbox"
                                                                                                             type="checkbox"
                                                                                                             name="privacy_checkbox"
                                                                                                             value="1">I
                                agree to the <a href="https://www.invoiceninja.com/self-hosting-privacy-data-control/"
                                                target="_blank">Privacy Policy</a></label></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-offset-4 col-sm-offset-4 col-lg-8 col-sm-8">
                        <button type='submit' class='btn btn-primary btn-lg'>Submit</button>
                    </div>
                </div>
                <input type="hidden" name="_token" value="lZMf1gNYZHxhQxLLOkBymnEXtjjjEW4SjNwsntHt"></form>

        </div>

</body>
</html>
