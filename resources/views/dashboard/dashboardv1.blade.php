@extends('layouts.master')
@section('main-content')
           <div class="breadcrumb">
                <ul>
                    <li><a href="">Dashboard</a></li>
                </ul>
            </div>

    <div class="separator-breadcrumb border-top"></div>

    <div class="col-md-12">

    <form role="form" method="post" action="#" id="">
    <div class="row">
            <div class="col">
                <div class="form-group">

                <select class="form-control filter_partner  input-rounded input-sm select2" name="filter_partner"
                    id="">
                    <option value="">Please select Partner</option>

                    <option></option>
                </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                <select class="form-control filter_county  input-rounded input-sm select2" name="filter_county"
                    id="">
                    <option value="">Please select County</option>

                    <option></option>
                </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                <span class="filter_sub_county_wait" style="display: none;">Loading , Please Wait ...</span>
                <select class="form-control filter_sub_county input-rounded input-sm select2" name="filter_sub_county"
                    id="">
                    <option value="">Please Select Sub County : </option>
                </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                <span class="filter_facility_wait" style="display: none;">Loading , Please Wait ...</span>

                <select class="form-control filter_facility input-rounded input-sm select2" name="filter_facility"
                    id="">
                    <option value="">Please select Facility : </option>
                </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">

                <button class="btn btn-default filter_highcharts_dashboard btn-round  btn-small btn-primary  "
                    type="button" name="filter_highcharts_dashboard" id="filter_highcharts_dashboard"> <i
                        class="fa fa-filter"></i>
                    Filter</button>
                    </div>
            </div>
            </div>

            </form>

    </div>
    <div class="separator-breadcrumb border-top"></div>

            <div class="row">
                <!-- ICON BG -->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Target Active Clients</p>

                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $all_target_clients; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">No. of Active Clients</p>
                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $all_clients_number; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">% No. of Active Clients</p>
                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $pec_client_count; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Consented Clients</p>
                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $all_consented_clients; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-2">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Future Appointments</p>
                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $all_future_appointments; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-2">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <div class="content">
                            <a class="has-arrow" href="{{route('Reports-active-facilities')}}">
                                <p class="text-muted mt-2 mb-0">No. of Facilities</p>
                                </a>
                                <p class="text-primary text-20 line-height-1 mb-2"><?php echo $number_of_facilities; ?></p>
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

$.ajax({
            type: 'GET',
            url: "{{ route('Reports-dashboard') }}",
            success: function(data) {

                $('#partners').empty();
                $('#counties').empty();
                $.each(data.all_partners, function(number, partner) {
                    $("#partners").append($('<option>').text(partner.name).attr('value',
                        partner.id));
                });
                $.each(data.all_counties, function(number, county) {
                    $("#counties").append($('<option>').text(county.name).attr('value',
                        county.id));
                });
                $("#partners").selectpicker('refresh');
                $("#counties").selectpicker('refresh');
                $("#all_clients_number").html(data.all_clients_number);
                $("#pec_client_count").html(data.pec_client_count);
                $("#all_target_clients").html(data.all_target_clients);
                $("#all_consented_clients").html(data.all_consented_clients);
                $("#number_of_facilities").html(data.number_of_facilities);
                let userlevel = '{!!Auth::user()->access_level!!}';
                if (userlevel == 'Partner') {
                    let partnerId = '{!!Auth::user()->partner_id!!}';
                    $('#partners').attr("disabled", true);
                    $('#partners').selectpicker('val', partnerId);
                    $("#partners").selectpicker('refresh');
                }
            }
        });

var RegisteredClients =  <?php echo json_encode($registered_clients_count) ?>;
var ConsentedClients =  <?php echo json_encode($consented_clients_count) ?>;
var Months =  <?php echo json_encode($month_count) ?>;
parseConsented = JSON.parse(ConsentedClients);
parseRegistered = JSON.parse(RegisteredClients);

console.log(parseConsented);
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Monthly Number Series'
    },
    xAxis: {
        categories: ['Registered Clients', 'Consented Clients'],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Count'
        }
    },
    tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' +
                    'Total Clients: ' + this.point.stackTotal;
            }
        },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Clients Trends',
        data: [parseRegistered, parseConsented]

    }]
});



        </script>

@endsection