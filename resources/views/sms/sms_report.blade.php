@extends('layouts.master')
@section('page-css')

@endsection

@section('main-content')


<div class="breadcrumb">
    <ul>
        <li><a href="">SMS Analytics</a></li>
        <li></li>
    </ul>
</div>

<div class="col-md-12">

    <form role="form" method="get" action="{{route('filtering_sms')}}">
        {{ csrf_field() }}
        <div class="row">


            <div class='col-sm-3'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-4">
                            <label for="firstName1">From</label>
                        </div>
                        <div class="col-md-10">
                            <input type="date" id="from" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="from" max="{{date("Y-m-d")}}">
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>

            <div class='col-sm-3'>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-4">
                            <label for="firstName1">To</label>
                        </div>
                        <div class="col-md-10">

                            <input type="date" id="to" class="form-control" placeholder="YYYY-mm-dd" name="to" max="{{date("Y-m-d")}}">
                        </div>
                        <div class="input-group-append">

                        </div>
                    </div>
                </div>
            </div>



            <div class="col-sm-3">
                <div class="form-group">

                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
<div class="separator-breadcrumb border-top"></div>

<div> <b>Total SMS Cost: Ksh.{{number_format(trim($total_cost, '[]'))}}</b></div>
<div class="separator-breadcrumb border-top"></div>

<!-- ICON BG -->

<div class="row">
    <div class="col-lg-6 col-md-6">
        <div class="card mb-4">
            <div class="card-body">

                <div id="sms_analytics" class="col" style="height: 450px;margin-top:40px;"></div> <br />

            </div>
        </div>
    </div>

    <div class="col-lg-6 col-md-6">
        <div class="card mb-4">
            <div class="card-body">

                <div id="cost_analytics" class="col" style="height: 450px;margin-top:40px;"></div> <br />

            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12">
    <div class="card mb-4">
        <div class="card-body">

            <div id="county_analytic_cost" class="col" style="height: 450px;margin-top:40px;"></div> <br />

        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12">
    <div class="card mb-4">
        <div class="card-body">

            <div id="partner_analytic_delivered" class="col" style="height: 450px;margin-top:40px;"></div> <br />

        </div>
    </div>
</div>
</div>
<div class="col-lg-12 col-md-12">
    <div class="card mb-4">
        <div class="card-body">

            <div id="partner_analytic_failed" class="col" style="height: 450px;margin-top:40px;"></div> <br />

        </div>
    </div>
</div>
</div>

<div class="col-lg-12 col-md-12">
    <div class="card mb-4">
        <div class="card-body">

            <div id="partner_analytic_cost" class="col" style="height: 450px;margin-top:40px;"></div> <br />

        </div>
    </div>
</div>
</div>


@endsection

@section('page-js')


<div id="highchart"></div>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js">
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.12/js/bootstrap-select.min.js">
</script>
<script src="https://code.highcharts.com/maps/highmaps.js"></script>
<script src="https://code.highcharts.com/maps/modules/data.js"></script>
<script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
<script src="https://code.highcharts.com/maps/modules/offline-exporting.js"></script>
<script src="https://code.highcharts.com/modules/bullet.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('select[name="partner"]').on('change', function() {
            var partnerID = $(this).val();
            if (partnerID) {
                $.ajax({
                    url: '/get_dashboard_counties/' + partnerID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="county"]').empty();
                        $('select[name="county"]').append('<option value="">Please Select County</option>');
                        $.each(data, function(key, value) {
                            $('select[name="county"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="county"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="county"]').on('change', function() {
            var countyID = $(this).val();
            if (countyID) {
                $.ajax({
                    url: '/get_dashboard_sub_counties/' + countyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="subcounty"]').empty();
                        $('select[name="subcounty"]').append('<option value="">Please Select SubCounty</option>');
                        $.each(data, function(key, value) {
                            $('select[name="subcounty"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="subcounty"]').empty();
            }
        });
    });

    $(document).ready(function() {
        $('select[name="subcounty"]').on('change', function() {
            var subcountyID = $(this).val();
            if (subcountyID) {
                $.ajax({
                    url: '/get_dashboard_facilities/' + subcountyID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {


                        $('select[name="facility"]').empty();
                        $('select[name="facility"]').append('<option value="">Please Select Facility</option>');
                        $.each(data, function(key, value) {
                            $('select[name="facility"]').append('<option value="' + key + '">' + value + '</option>');
                        });


                    }
                });
            } else {
                $('select[name="facility"]').empty();
            }
        });
    });

    $('#dataFilter').on('submit', function(e) {
        e.preventDefault();
        let partners = $('#partners').val();
        let counties = $('#counties').val();
        let subcounties = $('#subcounties').val();
        let facilities = $('#facilities').val();
        Swal.fire({
                title: "Please wait, Loading Charts!",
                showConfirmButton: false,
                allowOutsideClick: false
            });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'GET',
            data: {
                "partners": partners,
                "counties": counties,
                "subcounties": subcounties,
                "facilities": facilities
            },
            url: "{{ route('filter_sms') }}",
            success: function(data) {


                Success = parseInt(data.success)
                Failed_blacklist = parseInt(data.failed_blacklist)
                Failed_absent = parseInt(data.failed_absent)
                Failed_inactive = parseInt(data.failed_inactive)
                Failed_deliveryfailure = parseInt(data.failed_deliveryfailure)

                Success_cost = parseInt(data.success_cost)
                Failed_blacklist_cost = parseInt(data.failed_blacklist_cost)
                Failed_absent_cost = parseInt(data.failed_absent_cost)
                Failed_inactive_cost = parseInt(data.failed_inactive_cost)
                Failed_deliveryfailure_cost = parseInt(data.failed_deliveryfailure_cost)

                smsAnalytics.series[0].setData([Success, Failed_blacklist, Failed_absent, Failed_deliveryfailure, Failed_inactive]);
                costAnalytics.series[0].setData([Success_cost, Failed_backlist_cost, Failed_absent_cost, Failed_delivery_cost, Failed_inactive_cost]);
                Swal.close();

            }
        });
    });

    var Total_cost = <?php echo json_encode($total_cost) ?>;
    var Success = <?php echo json_encode($success) ?>;
    var Failed_blacklist = <?php echo json_encode($failed_blacklist) ?>;
    var Failed_absent = <?php echo json_encode($failed_absent) ?>;
    var Failed_inactive = <?php echo json_encode($failed_inactive) ?>;
    var Failed_deliveryfailure = <?php echo json_encode($failed_deliveryfailure) ?>;


    var Success_cost = <?php echo json_encode($success_cost) ?>;
    var Failed_backlist_cost = <?php echo json_encode($failed_blacklist_cost) ?>;
    var Failed_absent_cost = <?php echo json_encode($failed_absent_cost) ?>;
    var Failed_inactive_cost = <?php echo json_encode($failed_inactive_cost) ?>;
    var Failed_delivery_cost = <?php echo json_encode($failed_deliveryfailure_cost) ?>;


    var partner_delivery = <?php echo json_encode($delivered_partners) ?>;
    console.log(partner_delivery);

    var smsAnalytics = Highcharts.chart('sms_analytics', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'SMS Delivery Status Analytics'
        },
        xAxis: {
            categories: ['Delivered', 'Service Blacklisted', 'Phone off >48 Hours', 'Failure at Telco', 'Phone No. Inactive']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Number'
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
                    this.series.name + ': ' + this.y;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
            },
            spline: {
                stacking: 'normal',
            }
        },
        series: [{
                name: 'SMS Count',
                data: [Success, Failed_blacklist, Failed_absent, Failed_deliveryfailure, Failed_inactive]
            }
            // {
            //     type: 'spline',
            //     name: 'Cost(Ksh)',
            //     data: [Success_cost, Failed_backlist_cost, Failed_absent_cost, Failed_delivery_cost, Failed_inactive_cost, Rejected_inactive_cost, Rejected_blacklist_cost, Rejected_delivery_cost],
            //     marker: {
            //         lineWidth: 2,
            //         lineColor: Highcharts.getOptions().colors[3],
            //         fillColor: 'white'
            //     }
            // }

        ],



       // }],
    });

    var costAnalytics = Highcharts.chart('cost_analytics', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'SMS Cost Analytics'
        },
        xAxis: {
            categories: ['Delivered', 'Service Blacklisted', 'Phone off >48 Hours', 'Failure at Telco', 'Phone No. Inactive']
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Cost'
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
                    this.series.name + ': ' + this.y;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal',
            },
            spline: {
                stacking: 'normal',
            }
        },
        series: [{
                name: 'Cost(Ksh)',
                data: [Success_cost, Failed_backlist_cost, Failed_absent_cost, Failed_delivery_cost, Failed_inactive_cost]
            }

        ],


    });
    $(function() {
        var partner_delivered_array = [];
        var partner_failed_array = [];
        var partner_cost_array = [];
        var county_cost_array = [];

        var partner_delivery = <?php echo json_encode($delivered_partners) ?>;
        var partner_failed = <?php echo json_encode($failed_partners) ?>;
        var partner_cost = <?php echo json_encode($cost_partners) ?>;
        var county_cost = <?php echo json_encode($cost_counties) ?>;

        $.each(partner_delivery, function(key, value) {

            var total_value = value.total;
            delete value.total; //remove the attribute total
            value.y = total_value; //add a new attribute "y" for plotting values on y-axis
            partner_delivered_array.push(value);
        });

        $.each(partner_failed, function(key, value) {

            var total_value = value.total;
            delete value.total; //remove the attribute total
            value.y = total_value; //add a new attribute "y" for plotting values on y-axis
            partner_failed_array.push(value);
        });

        $.each(partner_cost, function(key, value) {

            var total_value = value.total_cost;
            delete value.total_cost; //remove the attribute total
            value.y = total_value; //add a new attribute "y" for plotting values on y-axis
            partner_cost_array.push(value);
        });

        $.each(county_cost, function(key, value) {

            var total_value = value.total_cost;
            delete value.total_cost; //remove the attribute total
            value.y = total_value; //add a new attribute "y" for plotting values on y-axis
            county_cost_array.push(value);
        });

        $('#partner_analytic_delivered').highcharts({
            chart: {
                type: 'column',

            },
            title: {
                text: 'Numbers of Delivered SMS By Partners'
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Numbers'
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

            legend: {
                enabled: false
            },

            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                    }
                }
            },

            series: [{
                name: 'Delivered Sms Count',
                colorByPoint: false,
                data: partner_delivered_array,
                dataSorting: {
                    enabled: true,
                    sortKey: 'y'
                }

            }],

            drilldown: {
                series: []
            }
        });

        $('#partner_analytic_failed').highcharts({
            chart: {
                type: 'column',

            },
            title: {
                text: 'Number of Failed SMS By Partner '
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Number'
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

            legend: {
                enabled: false
            },

            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                    }
                }
            },

            series: [{
                name: 'Failed Sms Count',
                colorByPoint: false,
                data: partner_failed_array,
                dataSorting: {
                    enabled: true,
                    sortKey: 'y'
                }

            }],

            drilldown: {
                series: []
            }
        });

        $('#partner_analytic_cost').highcharts({
            chart: {
                type: 'column',

            },
            title: {
                text: 'SMS Cost Distribution By Partners'
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Cost'
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

            legend: {
                enabled: false
            },

            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                    }
                }
            },

            series: [{
                name: 'SMS Cost(Ksh)',
                colorByPoint: false,
                data: partner_cost_array,
                dataSorting: {
                    enabled: true,
                    sortKey: 'y'
                }

            }],

            drilldown: {
                series: []
            }
        });

        $('#county_analytic_cost').highcharts({
            chart: {
                type: 'column',

            },
            title: {

                text: 'SMS Cost Distribution By Counties'
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Cost'
                },
                stackLabels: {
                    enabled: false,
                    style: {
                        fontWeight: 'bold',
                        color: ( // theme
                            Highcharts.defaultOptions.title.style &&
                            Highcharts.defaultOptions.title.style.color
                        ) || 'gray'
                    }
                }
            },

            legend: {
                enabled: false
            },

            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                    }
                }
            },

            series: [{
                name: 'SMS Cost(Ksh)',
                colorByPoint: false,
                data: county_cost_array,
                dataSorting: {
                    enabled: true,
                    sortKey: 'y'
                }

            }],

            drilldown: {
                series: []
            }
        })
    });
</script>

@endsection





<!-- end of col -->
