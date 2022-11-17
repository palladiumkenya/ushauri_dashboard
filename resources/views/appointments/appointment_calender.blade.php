@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.5.0/fullcalendar.min.css" />

@endsection

@section('main-content')
<div class="breadcrumb">
                <ul>
                    <li><a href="">Appointment Calender</a></li>
                    <li></li>
                </ul>
            </div>

<div class="col-md-12 mb-4">
    <div class="row">

    <!-- <button class="printBtn hidden-print">Print</button> -->

        <div id='calendar'></div>



    </div>
</div>
<!-- end of col -->

@endsection

@section('page-js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.5.0/fullcalendar.min.js"></script>




<script type="text/javascript">



var cal = jQuery.noConflict();


cal(document).ready(function () {





    function draw_calendar() {

        var RefillURL = "{{ url('/report/refill_apps') }}";

        cal('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: true,
            windowResize: true,
            eventSources: [
                {
                    url: '{{ route('app_count_calendar') }}',
                    title: 'Total Apps:',
                    color: '#A3FF33',
                    textColor: 'black'
                },
                {
                    url: '{{ route('refill_calendar') }}',
                    color: '#33FFE5',
                    textColor: 'black'
                },
                {
                    url: '{{ route('clinical_calendar') }}',
                    color: '#33FFE5',
                    textColor: 'black'
                },
                {
                    url: '{{ route('adherence_calendar') }}',
                    color: '#FF33FB',
                    textColor: 'black'
                },{
                    url: '{{ route('lab_calendar') }}',
                    color: '#FF33FB',
                    textColor: 'black'
                }, {
                    url: '{{ route('viral_load') }}',
                    color: '#AB99FB',
                    textColor: 'black'
                },
                {
                    url: '{{ route('other_calendar') }}',
                    color: '#AB99FB',
                    textColor: 'black'
                },
                {
                    url: '{{ route('vl_cd_calendar') }}',
                    color: '#FFFF00',
                    textColor: 'black'
                },
                {
                    url: '{{ route('honored_calendar') }}',
                    color: '#3374FF',
                    textColor: 'black'
                },
                {
                    url: '{{ route('pcr_calendar') }}',
                    color: '#33FFE5',
                    textColor: 'black'
                },
                {
                    url: '{{ route('unscheduled_calendar') }}',
                    color: '#FFFF00',
                    textColor: 'black'
                }

            ]
        });




    }

    draw_calendar();

});
</script>


@endsection