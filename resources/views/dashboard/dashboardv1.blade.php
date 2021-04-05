@extends('layouts.master')
@section('main-content')
           <div class="breadcrumb">
                <ul>
                    <li><a href="">Dashboard</a></li>
                </ul>
            </div>

            <div class="separator-breadcrumb border-top"></div>

    </form>
    <div class="separator-breadcrumb border-top"></div>

            <div class="row">
                <!-- ICON BG -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Add-User"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Target Active Clients</p>
                                <p id='all_target_clients' class="text-primary text-24 line-height-1 mb-2"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Financial"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">No. of Active Clients</p>
                                <p id="all_clients_number" class="text-primary text-24 line-height-1 mb-2"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Checkout-Basket"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">% No. of Active Clients</p>
                                <p id="all_client_pec" class="text-primary text-24 line-height-1 mb-2"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Money-2"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Consented Clients</p>
                                <p id="all_consented_clients" class="text-primary text-24 line-height-1 mb-2"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Money-2"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Future Appointments</p>
                                <p id="all_future_appointments" class="text-primary text-24 line-height-1 mb-2"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Money-2"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">No. of Facilities</p>
                                <p id='number_of_facilities' class="text-primary text-24 line-height-1 mb-2"></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">

                        <div id="container" class="col" style="height: 450px;margin-top:40px;"></div> <br />

                        </div>
                    </div>
                </div>
            </div>


@endsection

@section('page-js')

    <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/themes/high-contrast-light.js"></script>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.12/js/bootstrap-select.min.js">
    </script>

     <script type="text/javascript">


        Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Monthly Number Series'
        },
        xAxis: {
            categories: ['2017-03', '2017-04']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Count'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: ( // theme
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || 'gray'
                }
            }
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    'Sum of all appointment categories: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
            }
        },
        series: [{
                name: 'Registered Clients',
                data: registered_clients
            }, {
                name: 'Consented Clients',
                data: consented_clients
            }
        ]

    });

    var colors = Highcharts.getOptions().colors;

    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'GET',
            url: "{{ route('get_client_data') }}",
            success: function(data) {
                $("#partners").selectpicker('refresh');
                $("#counties").selectpicker('refresh');
                $("#all_clients_number").html(data.all_clients_number);
                $("#all_client_pec").html(data.all_client_pec);
                $("#all_target_clients").html(data.all_target_clients);
                $("#all_consented_clients").html(data.all_consented_clients);
                $("#all_future_appointments").html(data.all_future_appointments);
                $("#number_of_facilities").html(data.number_of_facilities);
                $("#sum(actual_clients)").html(data.sum(actual_clients));

                $("#registered_clients").html(data.registered_clients);
                $("#consented_clients").html(data.consented_clients);
                $("#month_count").html(data.month_count);
            }
        });

        </script>

@endsection