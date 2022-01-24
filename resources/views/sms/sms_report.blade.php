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

    <form role="form" method="post" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">
            <div class="col">
                <div class="form-group">

                    <select class="form-control filter_partner  input-rounded input-sm select2" id="partners" name="partner">
                        <option value="">Please select Partner</option>
                        @foreach ($all_partners as $partner => $value)
                        <option value="{{ $partner }}"> {{ $value }}</option>
                        @endforeach

                        <option></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <select class="form-control county  input-rounded input-sm select2" id="counties" name="county">
                        <option value="">Please select County:</option>
                        <option></option>
                        <option value=""></option>

                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_sub_county_wait" style="display: none;">Loading , Please Wait ...</span>
                    <select class="form-control subcounty input-rounded input-sm select2" id="subcounties" name="subcounty">
                        <option value="">Please Select Sub County : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;">Loading , Please Wait ...</span>

                    <select class="form-control filter_facility input-rounded input-sm select2" id="facilities" name="facility">
                        <option value="">Please select Facility : </option>
                        <option value=""></option>
                    </select>
                </div>
            </div>

            <div class="col">
                <div class="form-group">

                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
<div class="separator-breadcrumb border-top"></div>

<!-- ICON BG -->

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

<script type="text/javascript">
    $(function() {
        $('#daterange').daterangepicker({
            "minYear": 2017,
            "autoApply": true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            },
            "startDate": "04/10/2017",
            "endDate": moment().format('MM/DD/YYYY'),
            "opens": "left"
        }, function(start, end, label) {});
    });

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
            url: "{{ route('filter_ildashboard') }}",
            success: function(data) {


                $("#il_appointments").html(data.il_appointments);

            }
        });
    });

    var Success = <?php echo json_encode($success) ?>;
    var Failed_blacklist = <?php echo json_encode($failed_blacklist) ?>;
    var Failed_inactive = <?php echo json_encode($failed_inactive) ?>;
    var Failed_deliveryfailure = <?php echo json_encode($failed_deliveryfailure) ?>;
    var Rejected_blacklist = <?php echo json_encode($rejected_blacklist) ?>;
    var Rejected_inactive = <?php echo json_encode($rejected_inactive) ?>;
    var Rejected_deliveryfailure = <?php echo json_encode($rejected_deliveryfailure) ?>;

    var Success_cost = <?php echo json_encode($success_cost) ?>;
    var Failed_backlist_cost = <?php echo json_encode($failed_blacklist_cost) ?>;
    var Failed_inactive_cost = <?php echo json_encode($failed_inactive_cost) ?>;
    var Failed_delivery_cost = <?php echo json_encode($failed_deliveryfailure_cost) ?>;
    var Rejected_blacklist_cost = <?php echo json_encode($rejected_blacklist_cost) ?>;
    var Rejected_inactive_cost = <?php echo json_encode($rejected_inactive_cost) ?>;
    var Rejected_delivery_cost = <?php echo json_encode($rejected_deliveryfailure_cost) ?>;
    console.log(Success_cost);

    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'SMS Status & Cost Analytics'
        },
        xAxis: {
            categories: ['Delivered', 'Failed Inactive', 'Failed Blacklist', 'Failed DeliveryFailure', 'Rejected Inactive', 'Rejected Blacklist', 'Rejected DeliveryFailure']
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
                data: [Success, Failed_inactive, Failed_blacklist, Failed_deliveryfailure, Rejected_inactive, Rejected_blacklist, Rejected_deliveryfailure]
            },
            {
                type: 'spline',
                name: 'Cost(Ksh)',
                data: [Success_cost, Failed_inactive_cost, Failed_backlist_cost, Failed_delivery_cost, Rejected_inactive_cost, Rejected_blacklist_cost, Rejected_delivery_cost],
                marker: {
                    lineWidth: 2,
                    lineColor: Highcharts.getOptions().colors[3],
                    fillColor: 'white'
                }
            }

        ],


    });

    var colors = Highcharts.getOptions().colors;
</script>

@endsection





<!-- end of col -->