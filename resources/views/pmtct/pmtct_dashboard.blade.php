@extends('layouts.master')
@section('page-css')

@endsection

@section('main-content')
<div class="breadcrumb">
    <ul>
        <li><a href="">PMTCT Summary Dashboard</a></li>
        <li></li>
    </ul>
</div>


<div class="col-md-12 mb-4">
    <div class="row">

        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-title mb-3"></div>

                    <h4></h4>
                    <form role="form" method="get" action="{{route('filter-pmtct-summary')}}">
                        {{ csrf_field() }}
                        <div class="row">

                            <div class='col-sm-6'>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="col-md-4">
                                            <label for="firstName1">From</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="date" id="from" class="form-control" data-width="100%" placeholder="YYYY-mm-dd" name="from">
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" type="button">
                                                <i class="icon-regular i-Calendar-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-6'>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="col-md-4">
                                            <label for="firstName1">To</label>
                                        </div>
                                        <div class="col-md-10">

                                            <input type="date" id="to" class="form-control" placeholder="YYYY-mm-dd" name="to">
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" type="button">
                                                <i class="icon-regular i-Calendar-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-block btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>



        <div id="highchart"></div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/series-label.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="https://code.highcharts.com/themes/high-contrast-light.js"></script>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body row">

                        <div class="row">
                            <div class="col-12">

                                <div class="card-body row">
                                    <div id="container" class="col" style="height:  600px;margin-top:20px;width: 900px"></div> <br />
                                </div>

                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <script type="text/javascript">
                var ToNine_scheduled = <?php echo json_encode($tonine_scheduled) ?>;
                var ToFourteen_scheduled = <?php echo json_encode($tofourteen_scheduled) ?>;
                var ToNineteen_scheduled = <?php echo json_encode($tonineteen_scheduled) ?>;
                var ToTwentyFour_scheduled = <?php echo json_encode($totwentyfour_scheduled) ?>;
                var ToTwentyNine_scheduled = <?php echo json_encode($totwentynine_scheduled) ?>;
                var ToThirtyFour_scheduled = <?php echo json_encode($tothirtyfour_scheduled) ?>;
                var ToThirtyNine_scheduled = <?php echo json_encode($tothirtynine_scheduled) ?>;
                var ToFortyFour_scheduled = <?php echo json_encode($tofortyfour_scheduled) ?>;
                var ToFortyNine_scheduled = <?php echo json_encode($tofortynine_scheduled) ?>;
                var ToFirtyPlus_scheduled = <?php echo json_encode($tofiftyplus_scheduled) ?>;
                var Total_Scheduled = <?php echo json_encode($tototal_scheduled) ?>;

                var ToNine_unscheduled = <?php echo json_encode($tonine_unscheduled) ?>;
                var ToFourteen_unscheduled = <?php echo json_encode($tofourteen_unscheduled) ?>;
                var ToNineteen_unscheduled = <?php echo json_encode($tonineteen_unscheduled) ?>;
                var ToTwentyFour_unscheduled = <?php echo json_encode($totwentyfour_unscheduled) ?>;
                var ToTwentyNine_unscheduled = <?php echo json_encode($totwentynine_unscheduled) ?>;
                var ToThirtyFour_unscheduled = <?php echo json_encode($tothirtyfour_unscheduled) ?>;
                var ToThirtyNine_unscheduled = <?php echo json_encode($tothirtynine_unscheduled) ?>;
                var ToFortyFour_unscheduled = <?php echo json_encode($tofortyfour_unscheduled) ?>;
                var ToFortyNine_unscheduled = <?php echo json_encode($tofortynine_unscheduled) ?>;
                var ToFirtyPlus_unscheduled = <?php echo json_encode($tofifty_unscheduled) ?>;
                var Total_unscheduled = <?php echo json_encode($tototal_unscheduled) ?>;

                var ToNine_booked = <?php echo json_encode($tonine_booked) ?>;
                var ToFourteen_booked = <?php echo json_encode($tofourteen_booked) ?>;
                var ToNineteen_booked = <?php echo json_encode($tonineteen_booked) ?>;
                var ToTwentyFour_booked = <?php echo json_encode($totwentyfour_booked) ?>;
                var ToTwentyNine_booked = <?php echo json_encode($totwentynine_booked) ?>;
                var ToThirtyFour_booked = <?php echo json_encode($tothirtyfour_booked) ?>;
                var ToThirtyNine_booked = <?php echo json_encode($tothirtynine_booked) ?>;
                var ToFortyFour_booked = <?php echo json_encode($tofortyfour_booked) ?>;
                var ToFortyNine_booked = <?php echo json_encode($tofortynine_booked) ?>;
                var ToFirtyPlus_booked = <?php echo json_encode($tofifty_booked) ?>;
                var Total_booked = <?php echo json_encode($tototal_booked) ?>;

                var ToNine_defaulted = <?php echo json_encode($tonine_defaulted) ?>;
                var ToFourteen_defaulted = <?php echo json_encode($tofourteen_defaulted) ?>;
                var ToNineteen_defaulted = <?php echo json_encode($tonineteen_defaulted) ?>;
                var ToTwentyFour_defaulted = <?php echo json_encode($totwentyfour_defaulted) ?>;
                var ToTwentyNine_defaulted = <?php echo json_encode($totwentynine_defaulted) ?>;
                var ToThirtyFour_defaulted = <?php echo json_encode($tothirtyfour_defaulted) ?>;
                var ToThirtyNine_defaulted = <?php echo json_encode($tothirtynine_defaulted) ?>;
                var ToFortyFour_defaulted = <?php echo json_encode($tofortyfour_defaulted) ?>;
                var ToFortyNine_defaulted = <?php echo json_encode($tofortynine_defaulted) ?>;
                var ToFirtyPlus_defaulted = <?php echo json_encode($tofifty_defaulted) ?>;
                var Total_defaulted = <?php echo json_encode($tototal_defaulted) ?>;

                var ToNine_missed = <?php echo json_encode($tonine_missed) ?>;
                var ToFourteen_missed = <?php echo json_encode($tofourteen_missed) ?>;
                var ToNineteen_missed = <?php echo json_encode($tonineteen_missed) ?>;
                var ToTwentyFour_missed = <?php echo json_encode($totwentyfour_missed) ?>;
                var ToTwentyNine_missed = <?php echo json_encode($totwentynine_missed) ?>;
                var ToThirtyFour_missed = <?php echo json_encode($tothirtyfour_missed) ?>;
                var ToThirtyNine_missed = <?php echo json_encode($tothirtynine_missed) ?>;
                var ToFortyFour_missed = <?php echo json_encode($tofortyfour_missed) ?>;
                var ToFortyNine_missed = <?php echo json_encode($tofortynine_missed) ?>;
                var ToFirtyPlus_missed = <?php echo json_encode($tofifty_missed) ?>;
                var Total_missed = <?php echo json_encode($tototal_missed) ?>;

                var ToNine_ltfu = <?php echo json_encode($tonine_ltfu) ?>;
                var ToFourteen_ltfu = <?php echo json_encode($tofourteen_ltfu) ?>;
                var ToNineteen_ltfu = <?php echo json_encode($tonineteen_ltfu) ?>;
                var ToTwentyFour_ltfu = <?php echo json_encode($totwentyfour_ltfu) ?>;
                var ToTwentyNine_ltfu = <?php echo json_encode($totwentynine_ltfu) ?>;
                var ToThirtyFour_ltfu = <?php echo json_encode($tothirtyfour_ltfu) ?>;
                var ToThirtyNine_ltfu = <?php echo json_encode($tothirtynine_ltfu) ?>;
                var ToFortyFour_ltfu = <?php echo json_encode($tofortyfour_ltfu) ?>;
                var ToFortyNine_ltfu = <?php echo json_encode($tofortynine_ltfu) ?>;
                var ToFirtyPlus_ltfu = <?php echo json_encode($tofifty_ltfu) ?>;
                var Total_ltfu = <?php echo json_encode($tototal_ltfu) ?>;

                var ToNine_honoured = <?php echo json_encode($tonine_honoured) ?>;
                var ToFourteen_honoured = <?php echo json_encode($tofourteen_honoured) ?>;
                var ToNineteen_honoured = <?php echo json_encode($tonineteen_honoured) ?>;
                var ToTwentyFour_honoured = <?php echo json_encode($totwentyfour_honoured) ?>;
                var ToTwentyNine_honoured = <?php echo json_encode($totwentynine_honoured) ?>;
                var ToThirtyFour_honoured = <?php echo json_encode($tothirtyfour_honoured) ?>;
                var ToThirtyNine_honoured = <?php echo json_encode($tothirtynine_honoured) ?>;
                var ToFortyFour_honoured = <?php echo json_encode($tofortyfour_honoured) ?>;
                var ToFortyNine_honoured = <?php echo json_encode($tofortynine_honoured) ?>;
                var ToFirtyPlus_honoured = <?php echo json_encode($tofifty_honoured) ?>;
                var Total_honoured = <?php echo json_encode($tototal_honoured) ?>;



                Highcharts.drawTable = function() {


                    // user options
                    var tableTop = 55,
                        colWidth = 95,
                        tableLeft = 60,
                        rowHeight = 40,
                        cellPadding = 6,
                        valueDecimals = 0;

                    var chart = this,
                        series = chart.series,
                        renderer = chart.renderer,
                        cellLeft = tableLeft;

                    // draw category labels
                    $.each(chart.xAxis[0].categories, function(i, name) {
                        renderer.text(
                                name,
                                cellLeft + cellPadding,
                                tableTop + (i + 2) * rowHeight - cellPadding
                            )
                            .css({
                                fontWeight: 'bold'
                            })
                            .add();
                    });

                    $.each(series, function(i, serie) {
                        cellLeft += colWidth;

                        // Apply the cell text
                        renderer.text(
                                serie.name,
                                cellLeft - cellPadding + colWidth,
                                tableTop + rowHeight - cellPadding
                            )
                            .attr({
                                align: 'right'
                            })
                            .css({
                                fontWeight: 'bold'
                            })
                            .add();

                        $.each(serie.data, function(row, point) {

                            // Apply the cell text
                            renderer.text(
                                    Highcharts.numberFormat(point.y, valueDecimals),
                                    cellLeft + colWidth - cellPadding,
                                    tableTop + (row + 2) * rowHeight - cellPadding
                                )
                                .attr({
                                    align: 'right'
                                })
                                .add();

                            // horizontal lines
                            if (row == 0) {
                                Highcharts.tableLine( // top
                                    renderer,
                                    tableLeft,
                                    tableTop + cellPadding,
                                    cellLeft + colWidth,
                                    tableTop + cellPadding
                                );
                                Highcharts.tableLine( // bottom
                                    renderer,
                                    tableLeft,
                                    tableTop + (serie.data.length + 1) * rowHeight + cellPadding,
                                    cellLeft + colWidth,
                                    tableTop + (serie.data.length + 1) * rowHeight + cellPadding
                                );
                            }
                            // horizontal line
                            Highcharts.tableLine(
                                renderer,
                                tableLeft,
                                tableTop + row * rowHeight + rowHeight + cellPadding,
                                cellLeft + colWidth,
                                tableTop + row * rowHeight + rowHeight + cellPadding
                            );

                        });

                        // vertical lines
                        if (i == 0) { // left table border
                            Highcharts.tableLine(
                                renderer,
                                tableLeft,
                                tableTop + cellPadding,
                                tableLeft,
                                tableTop + (serie.data.length + 1) * rowHeight + cellPadding
                            );
                        }

                        Highcharts.tableLine(
                            renderer,
                            cellLeft,
                            tableTop + cellPadding,
                            cellLeft,
                            tableTop + (serie.data.length + 1) * rowHeight + cellPadding
                        );

                        if (i == series.length - 1) { // right table border

                            Highcharts.tableLine(
                                renderer,
                                cellLeft + colWidth,
                                tableTop + cellPadding,
                                cellLeft + colWidth,
                                tableTop + (serie.data.length + 1) * rowHeight + cellPadding
                            );
                        }

                    });


                };
                Highcharts.tableLine = function(renderer, x1, y1, x2, y2) {
                    renderer.path(['M', x1, y1, 'L', x2, y2])
                        .attr({
                            'stroke': 'silver',
                            'stroke-width': 1
                        })
                        .add();
                }
                window.chart = new Highcharts.Chart({

                    chart: {
                        renderTo: 'container',
                        events: {
                            load: Highcharts.drawTable
                        },
                        borderWidth: 2
                    },

                    title: {
                        text: 'PMTCT Evaluation of Attendance Summary'
                    },

                    xAxis: {
                        visible: false,
                        categories: ['10-14', '15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49', '50+', 'Total']
                    },

                    yAxis: {
                        visible: false
                    },

                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            visible: false
                        }
                    },

                    series: [{
                        name: 'Scheduled',
                        data: [ToFourteen_scheduled, ToNineteen_scheduled, ToTwentyFour_scheduled, ToTwentyNine_scheduled, ToThirtyFour_scheduled, ToThirtyNine_scheduled, ToFortyFour_scheduled, ToFortyNine_scheduled, ToFirtyPlus_scheduled, Total_Scheduled]
                    }, {
                        name: 'App Honoured',
                        data: [ToFourteen_honoured, ToNineteen_honoured, ToTwentyFour_honoured, ToTwentyNine_honoured, ToThirtyFour_honoured, ToThirtyNine_honoured, ToFortyFour_honoured, ToFortyNine_honoured, ToFirtyPlus_honoured, Total_honoured]
                    }, {
                        name: 'Un-Scheduled',
                        data: [ToFourteen_unscheduled, ToNineteen_unscheduled, ToTwentyFour_unscheduled, ToTwentyNine_unscheduled, ToThirtyFour_unscheduled, ToThirtyNine_unscheduled, ToFortyFour_unscheduled, ToFortyNine_unscheduled, ToFirtyPlus_unscheduled, Total_unscheduled]

                    }, {
                        name: 'Defaulter',
                        data: [ToFourteen_defaulted, ToNineteen_defaulted, ToTwentyFour_defaulted, ToTwentyNine_defaulted, ToThirtyFour_defaulted, ToThirtyNine_defaulted, ToFortyFour_defaulted, ToFortyNine_defaulted, ToFirtyPlus_defaulted, Total_defaulted]

                    }, {
                        name: 'Missed',
                        data: [ToFourteen_missed, ToNineteen_missed, ToTwentyFour_missed, ToTwentyNine_missed, ToThirtyFour_missed, ToThirtyNine_missed, ToFortyFour_missed, ToFortyNine_missed, ToFirtyPlus_missed, Total_missed]

                    }, {
                        name: 'LTFU',
                        data: [ToFourteen_ltfu, ToNineteen_ltfu, ToTwentyFour_ltfu, ToTwentyNine_ltfu, ToThirtyFour_ltfu, ToThirtyNine_ltfu, ToFortyFour_ltfu, ToFortyNine_ltfu, ToFirtyPlus_ltfu, Total_ltfu]

                    }]
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
            </script>




        </div>
    </div>
    <!-- end of col -->

    @endsection