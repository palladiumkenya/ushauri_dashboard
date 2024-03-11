<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('assets/images/ushauri.jpeg') }}">
    <title>Ushauri - Getting better one text at a time</title>
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,400i,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/styles/css/themes/lite-purple.min.css')}}">
</head>

<body>
    <style>
        .header-block h1,
        .card-title,
        .footer-block p {
            font-family: 'Inter', sans-serif;
        }
    </style>

    <div class="container mt-5">
        <div class="header-block text-center mb-4">
            <h1>mHealth Applications</h1>
        </div>

        <hr class="mb-4">

        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <a href="{{ url('/login') }}" class="card">
                    <img src="{{ asset('/assets/images/Ushauri_big.png') }}" class="card-img-top" alt="Ushauri Logo">
                    <div class="card-body text-center">
                        <h5 class="card-title">Ushauri</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="{{ url('directory') }}" class="card" style="align-items:center">
                    <img src="{{ asset('assets/images/login/moh.png') }}" class="pl-3" alt="ART Directory Logo" height="90" width="100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Facility Directory</h5>
                    </div>
                </a>
            </div>
            <!-- Add more cards for other applications -->
        </div>

        <hr class="mt-4">

        <div class="footer-block text-center mt-4">
            <p>&copy; KeHMIS &nbsp;2016 - <?php echo date('Y'); ?> </p>
            <p><b>HelpDesk Contact: Toll Free 0800722440</p>
        </div>
    </div>

    <script src="{{asset('assets/js/common-bundle-script.js')}}"></script>
    <script src="{{asset('assets/js/script.js')}}"></script>
</body>

</html>