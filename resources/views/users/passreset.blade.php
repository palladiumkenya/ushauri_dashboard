<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ushauri - Getting better one text at a time</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/styles/css/themes/lite-purple.min.css')}}">
</head>

<body>
    <div class="auth-layout-wrap">
        <div class="auth-content">
            <div class="card o-hidden">
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-4">
                            <div class="auth-logo text-center mb-4">
                                <img src="{{ asset('/assets/images/ushauri_logo.png') }}" style="margin-left: 50px;" width="51%" height="60%">

                                <h4>Reset Password</h4>
                            </div>

                            <form role="form" method="post" action="{{route('changepass')}}">
                                {{ csrf_field() }}
                                <div class="row">
                                    <input type="hidden" name="id" value="{{Auth::user()->id}}">

                                    <div class="form-group col-md-12">
                                        <label for="new_password" class="col-sm-2 control-label "><b>New Password </b></label>
                                        <div class="col-sm-10">
                                            <input type="password" pattern=".{6,20}" required title="password requires more than 6 characters" class="form-control new_password password" name="new_password" id="new_password" placeholder="New Password... ">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="confirm_new_password" class="col-sm-2 control-label">Confirm Password
                                        </label>
                                        <div class="col-sm-10">
                                            <input type="password" pattern=".{6,20}" required title="password requires more than 6 characters" class="form-control confirm_new_password password" name="confirm_new_password" id="confirm_new_password" placeholder="Confirm Password... ">
                                        </div>
                                    </div>
                                </div>
                                <div class="btn_div" style="display: none;">
                                    <button type="submit" class="btn btn-info pull-right">Change Password</button>

                                </div>
                            </form>

                        </div><!-- /.box-body -->
                    </div>
                    <div class="modal-footer">

                        <a type="button" class="btn btn-info pull-right" href="{{route('logout')}}">Login</a>
                    </div>
                    <div class="center">
                        <img class="pl-3" src="{{ asset('assets/images/login/moh.png') }}" alt="ushauri" height="40" style="margin-left: 20px;">
                        <img class="pl-3" src="{{ asset('assets/images/login/CDC-LOGO.jpg') }}" alt="ushauri" height="40" style="margin-left: 95px;">
                        <img class="pl-3" src="{{ asset('assets/images/login/KeHMIS.png') }}" alt="ushauri" width="31%" style="margin-left: 95px;">
                    </div>
                    <div class="register-link m-t-15 text-center">
                        <p>&copy; KenyaHMIS &nbsp;2016 - <?php echo date('Y'); ?> <b> Powered by : <a href="https://kenyahmis.org/" target="_blank"> KenyaHMIS </a></b> </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script src="{{asset('assets/js/common-bundle-script.js')}}"></script>

    <script src="{{asset('assets/js/script.js')}}"></script>

    <!-- Sweet alert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>



</body>

</html>

<script type="text/javascript">
    $(".password").keyup(function() {
        var password = $("#new_password").val();
        var password2 = $("#confirm_new_password").val();
        if (password == password2) {
            $(".btn_div").show();
        } else {
            $(".btn_div").hide();
        }
    });
</script>