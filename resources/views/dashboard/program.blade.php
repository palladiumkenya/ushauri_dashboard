@extends('layouts.master')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">

<style rel="stylesheet" type="text/css">
    .no_count {
        font-weight: 700;
        font-size: 24px;
    }

    .Clients {

        background: #FFFFFF;
        border-radius: 10px;

    }
</style>

@endsection

@section('main-content')
<!-- <div class="breadcrumb">
                <ul>
                    <li><a href="">Appointment Dashboard</a></li>
                    <li></li>
                </ul>
            </div> -->

<div class="col">

    <form role="form" method="get" action="#" id="dataFilter">
        {{ csrf_field() }}
        <div class="row">

            <div class="col-lg-3">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>

                    <!-- <select class="form-control filter_facility input-rounded input-sm select2" id="month" name="month">
                        <option value="">Month:</option>
                    </select> -->
                    <select class="form-control filter_facility input-rounded input-sm select2" id="months" name="months">
                        <option value="">Month:</option>
                    </select>


                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <span class="filter_facility_wait" style="display: none;"></span>
                    <select class="form-control filter_facility input-rounded input-sm select2" id="year" name="year">
                        <option value="">Year:</option>
                        @php
                        $currentYear = date('Y');
                        $startYear = 2019;
                        @endphp

                        @for ($year = $currentYear; $year >= $startYear; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>


                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">

                    <span class="filter_facility_wait" style="display: none;"></span>
                    <button class="btn btn-default filter btn-round  btn-small btn-primary  " type="submit" name="filter" id="filter"> <i class="fa fa-filter"></i>
                        Filter</button>
                </div>
            </div>
        </div>

    </form>

</div>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-client-tab" data-toggle="tab" href="#nav-client" role="tab" aria-controls="nav-client" aria-selected="true">Program Dashboard</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-indicators" role="tab" aria-selected="false"></a>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- main dashbaord starts -->
    <div class="tab-pane fade show active" id="nav-client" role="tabpanel" aria-labelledby="nav-client-tab">
        <div class="row">
            <div class="col-6">

                <div class="card-body row">
                    <div id="client_chart" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>
            <div class="col-6">

                <div class="card-body row">
                    <div id="site-chart" class="col" style="height:  400px;margin-top:20px;width: 900px"></div> <br />
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="table_client" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Partner</th>
                                <th>Facility</th>
                                <th>MFL Code</th>
                                <th>County</th>
                                <th>Facility Status</th>
                                <th>Month</th>
                                <th>Booked Clients</th>
                                <th>Date Last Used</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>

    </div>

</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/themes/high-contrast-light.js"></script>

<!-- Sweet alert -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get a reference to the select element
        var monthsSelect = document.getElementById("months");

        // Create an array of month names
        var monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        // Iterate through the month names and add options to the select element
        for (var i = 0; i < monthNames.length; i++) {
            var option = document.createElement("option");
            option.value = monthNames[i];
            option.text = monthNames[i];
            monthsSelect.add(option);
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var $j = jQuery.noConflict();

    function fetchData() {
        $.ajax({
            url: '/program/data',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                createChart(data);
                createChart2(data)

                var listdata = data.program;
                var maxMonths = listdata.reduce(function(max, site) {
                    return site.Months > max ? site.Months : max;
                }, '');
                var list = listdata.filter(function(site) {
                    return site.Months === maxMonths;
                });


                $.each(list, function(index, item) {
                    $('#table_client tbody').append('<tr><td>' + item.PartnerName + '</td><td>' + item.SiteName + '</td><td>' + item.SiteCode + '</td><td>' + item.County_Name + '</td><td>' + item.SiteStatus + '</td><td>' + item.MonthYear + '</td><td>' + item.num_clients + '</td><td>' + item.LastDateUsed + '</td></tr>');
                });
                $j('#table_client').DataTable({
                    columnDefs: [{
                            targets: [0],
                            orderData: [0, 1]
                        },
                        {
                            targets: [1],
                            orderData: [1, 0]
                        },
                        {
                            targets: [4],
                            orderData: [4, 0]
                        }
                    ],
                    "pageLength": 10,
                    "paging": true,
                    "responsive": true,
                    "ordering": true,
                    "info": true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5'
                    ]
                });
                // if (data.months && data.months.length > 0) {
                //     // Clear existing options
                //     $('#module').empty();

                //     // Add a default option
                //     $('#module').append('<option value="">Month : </option>');

                //     // Add each month as an option
                //     $.each(data.months, function(index, monthObj) {
                //         if (monthObj.MonthYear) {
                //             var parts = monthObj.MonthYear.split('-');
                //             var month = parts[0];
                //             var year = parts[1];
                //             $('#month').append('<option value="' + monthObj.MonthYear + '">' + month + '-' + year + '</option>');
                //         }
                //     });
                // } else {
                //     console.error('No months data available');
                // }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', status, error);
            }
        });
    }

    function fetchData2() {
        $('#dataFilter').on('submit', function(e) {
            e.preventDefault();
            let months = $('#months').val();
            let year = $('#year').val();

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
                    "months": months,
                    "year": year
                },
                url: "{{ route('program_filter') }}",
                success: function(data) {
                    createChart(data);
                    createChart2(data);
                    var listdata = data.program;

                    // finding the filtered maximum Months value
                    function parseMonthYear(dateString) {
                        var parts = dateString.split('-');
                        if (parts.length === 2) {
                            var month = parseInt(parts[0], 10);
                            var year = parseInt(parts[1], 10);
                            if (!isNaN(month) && !isNaN(year)) {
                                return new Date(year, month - 1);
                            }
                        }
                        return null;
                    }
                    var maxDate = listdata.reduce(function(max, site) {
                        var currentDate = parseMonthYear(site.Months);
                        return currentDate > max ? currentDate : max;
                    }, new Date(0));

                    var list = listdata.filter(function(site) {
                        var currentDate = parseMonthYear(site.Months);
                        return currentDate && currentDate.getTime() === maxDate.getTime();
                    });

                    var table = $j('#table_client').DataTable();

                    // Destroy the DataTable instance
                    table.destroy();

                    $('#table_client tbody').empty();
                    $.each(list, function(index, item) {
                        $('#table_client tbody').append('<tr><td>' + item.PartnerName + '</td><td>' + item.SiteName + '</td><td>' + item.SiteCode + '</td><td>' + item.County_Name + '</td><td>' + item.SiteStatus + '</td><td>' + item.MonthYear + '</td><td>' + item.num_clients + '</td><td>' + item.LastDateUsed + '</td></tr>');
                    });
                    $j('#table_client').DataTable({
                        columnDefs: [{
                                targets: [0],
                                orderData: [0, 1]
                            },
                            {
                                targets: [1],
                                orderData: [1, 0]
                            },
                            {
                                targets: [4],
                                orderData: [4, 0]
                            }
                        ],
                        "pageLength": 10,
                        "paging": true,
                        "responsive": true,
                        "ordering": true,
                        "info": true,
                        dom: 'Bfrtip',
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ]
                    });

                    Swal.close();

                }
            });
        });
    }

    function createChart(jsonData) {
        const sortedProgramData = jsonData.program.sort((a, b) => {
            const dateA = new Date(a.LastDateUsed);
            const dateB = new Date(b.LastDateUsed);
            return dateA - dateB;
        });

        const processedData = sortedProgramData.reduce((accumulator, item) => {
            if (item.MonthYear && item.num_clients) {
                const monthYear = item.MonthYear;
                accumulator[monthYear] = (accumulator[monthYear] || 0) + item.num_clients;
            }
            return accumulator;
        }, {});

        const seriesData = Object.keys(processedData).map(monthYear => ({
            name: monthYear,
            y: processedData[monthYear]
        }));


        Highcharts.chart('client_chart', {
            chart: {
                type: 'column',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            title: {
                text: 'No of Clients Booked for the last 6 Months',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: Object.keys(processedData),
                title: {
                    text: 'MonthYear'
                }
            },
            yAxis: {
                title: {
                    text: 'No of Clients',
                    style: {
                        fontFamily: 'Inter',
                        fontSize: '14px'
                    }
                }
            },
            series: [{
                name: 'No of Clients',
                color: '#01058A',
                data: seriesData
            }]
        });
    }

    function createChart2(jsonData) {
        const monthYearSum = jsonData.active_site.reduce((accumulator, item) => {
            if (item.MonthYear) {
                accumulator[item.MonthYear] = (accumulator[item.MonthYear] || 0) + item.num_sites;
            }
            return accumulator;
        }, {});

        const seriesData = Object.keys(monthYearSum).map(monthYear => ({
            name: monthYear,
            y: monthYearSum[monthYear]
        }));

        Highcharts.chart('site-chart', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Active Sites the last 6 Months',
                style: {
                    fontFamily: 'Inter',
                    fontSize: '14px'
                }
            },
            xAxis: {
                categories: Object.keys(monthYearSum),
                title: {
                    text: 'MonthYear'
                }
            },
            yAxis: {
                title: {
                    text: 'No of Sites'
                }
            },
            series: [{
                name: 'No of Sites',
                color: '#01058A',
                data: seriesData
            }]
        });
    }

    // Fetch data when the page loads
    fetchData();
    fetchData2();
</script>





<!-- end of col -->

@endsection