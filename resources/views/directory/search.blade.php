<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('assets/images/ushauri.jpeg') }}">
    <title>Ushauri - Getting better one text at a time</title>
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,400i,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/styles/css/themes/lite-purple.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <style>
        .header-block h1,
        .card-title,
        .footer-block p {
            font-family: 'Inter', sans-serif;
        }

        .header-block {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-block h1 {
            margin-right: auto;
            /* Push the h1 to the left */
        }

        .logo {
            max-height: 50px;
        }
    </style>
    <style>
     .rate {
         float: left;
         height: 20px;
         padding: 0 10px;
         }
         .rate:not(:checked) > input {
         position:absolute;
         display: none;
         }
         .rate:not(:checked) > label {
         float:right;
         width:1em;
         overflow:hidden;
         white-space:nowrap;
         cursor:pointer;
         font-size:20px;
         color:#ccc;
         }
         .rated:not(:checked) > label {
         float:right;
         width:1em;
         overflow:hidden;
         white-space:nowrap;
         cursor:pointer;
         font-size:20px;
         color:#ccc;
         }
         .rate:not(:checked) > label:before {
         content: '★ ';
         }
         .rate > input:checked ~ label {
         color: #ffc700;
         }
         .rate:not(:checked) > label:hover,
         .rate:not(:checked) > label:hover ~ label {
         color: #deb217;
         }
         .rate > input:checked + label:hover,
         .rate > input:checked + label:hover ~ label,
         .rate > input:checked ~ label:hover,
         .rate > input:checked ~ label:hover ~ label,
         .rate > label:hover ~ input:checked ~ label {
         color: #c59b08;
         }
         .star-rating-complete{
            color: #c59b08;
         }
         .rating-container .form-control:hover, .rating-container .form-control:focus{
         background: #fff;
         border: 1px solid #ced4da;
         }
         .rating-container textarea:focus, .rating-container input:focus {
         color: #000;
         }
         .rated {
         float: left;
         height: 20px;
         padding: 0 10px;
         }
         .rated:not(:checked) > input {
         position:absolute;
         display: none;
         }
         .rated:not(:checked) > label {
         float:right;
         width:1em;
         overflow:hidden;
         white-space:nowrap;
         cursor:pointer;
         font-size:20px;
         color:#ffc700;
         }
         .rated:not(:checked) > label:before {
         content: '★ ';
         }
         .rated > input:checked ~ label {
         color: #ffc700;
         }
         .rated:not(:checked) > label:hover,
         .rated:not(:checked) > label:hover ~ label {
         color: #deb217;
         }
         .rated > input:checked + label:hover,
         .rated > input:checked + label:hover ~ label,
         .rated > input:checked ~ label:hover,
         .rated > input:checked ~ label:hover ~ label,
         .rated > label:hover ~ input:checked ~ label {
         color: #c59b08;
         }
</style>
    <script type="text/javascript">
        function setMaxLength(input) {
            var isDigit = /^\d+$/.test(input.value);
            input.maxLength = isDigit ? 5 : 1000;
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <!-- Header Section -->
        <div class="header-block mb-4">
            <div class="left-end">
                <a href="{{url('/')}}"><i class="fas fa-chevron-left"></i> Back</a>
            </div>

            <div class="center">
                <h2>Facility Directory</h2>
            </div>
            <div class="right-end">
                <img src="{{ asset('assets/images/NASCOP_Logo.png') }}" alt="Logo" class="logo">
            </div>
        </div>

        <!-- Body Section -->
        <hr class="mb-4">
        <!-- Search Section -->
        <div class="row justify-content-center">
            <div class="col-md-6 mb-3">
                <label for="searchInput" class="form-label">Search Facility:</label>
                <input type="text" class="form-control" id="searchInput" placeholder="Enter Facility Name or MFL Code" oninput="setMaxLength(this)">
                <button class="btn btn-primary mt-2" id="searchButton">Search</button>
                <div id="error-message" class="text-danger mt-2" style="display: none;">Invalid MFL Code.</div>
            </div>
        </div>

        <div class="row justify-content-center" id="resultTableSection" style="display: none;">
            <table id="multicolumn_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Facility Name</th>
                        <th scope="col">MFL Code</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Facility Type</th>
                        <th scope="col">More</th>
                        <th scope="col">Click on the stars to rate this search</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Placeholder row for DataTable -->
                </tbody>
            </table>
        </div>


        <div class="modal fade"  id="modal_rating" tabIndex="-1" aria-labelledby="RatingModalLabel" aria-hidden="true">
            <div class="modal-dialog" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="RatingModalLabel">Rate this search</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            x
                        </button>
                    </div>
                    <div class="modal-body" style="padding-top:0px;">
                    <form class="py-2 px-4" action="#"  method="POST" autocomplete="off">
                            @csrf
                            <div class="form-group row">
                            <input type="hidden" name="facility_code" id="facility_code" value="">
                            <input type="hidden" name="facility_rating" id="facility_rating" value="">
                            <div class="col">
                                <div id="radioDiv" class="rate" >
                                    <input type="radio" id="star1" name="rating" value="5"/>
                                    <label for="star1" title="text" class="facility-rating" style="font-size:40px;">5 stars</label>
                                    <input type="radio" id="star2" class="rate" name="rating" value="4"/>
                                    <label for="star2" title="text" class="facility-rating" style="font-size:40px;">4 stars</label>
                                    <input type="radio" id="star3" class="rate" name="rating" value="3"/>
                                    <label for="star3" title="text" class="facility-rating" style="font-size:40px;">3 stars</label>
                                    <input type="radio" id="star4" class="rate" name="rating" value="2">
                                    <label for="star4" title="text" class="facility-rating" style="font-size:40px;">2 stars</label>
                                    <input type="radio" id="star5" class="rate" name="rating" value="1"/>
                                    <label for="star5" title="text" class="facility-rating" style="font-size:40px;">1 star</label>
                                </div>
                            </div>
                            </div>
                            <div class="form-group row mt-4">
                            <div class="col" style="margin-top:20px;">
                                <textarea class="form-control" name="comment" id="comment" rows="5" placeholder="Comment" maxlength="200"></textarea>
                            </div>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="mt-3 text-right">
                            <button id="ratingButton" class="btn btn-sm py-2 px-3 btn-info">Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <hr class="mt-4">

        <!-- Footer Section -->
        <div class="footer-block text-center mt-4">
            <p>&copy; KeHMIS &nbsp;2016 - <?php echo date('Y'); ?> </p>
            <p><b>HelpDesk Contact: Toll Free 0800722440</b></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('assets/js/common-bundle-script.js')}}"></script>
    <script src="{{asset('assets/js/script.js')}}"></script>
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            var mfl = Array.from({
                length: 10
            }, (_, index) => index).join('');
            var dataTable = $('#multicolumn_ordering_table').DataTable({
                columnDefs: [{
                    targets: [0],
                    orderData: [0, 1]
                }, {
                    targets: [1],
                    orderData: [1, 0]
                }, {
                    targets: [2],
                    orderData: [2, 0]
                }],
                "paging": true,
                "responsive": true,
                "ordering": true,
                "info": true,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'copy'
                }, {
                    extend: 'csv'
                }, {
                    extend: 'excel'
                }, {
                    extend: 'pdf'
                }, {
                    extend: 'print'
                }],
                columns: [{
                        data: 'No'
                    },
                    {
                        data: 'Facility Name'
                    },
                    {
                        data: 'MFL Code'
                    },
                    {
                        data: 'Contact'
                    },
                    {
                        data: 'Facility Type'
                    },
                    {
                        data: 'More',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Rate'
                    }
                ],
            });

            $('#ratingButton').on('click', function(event) {
                event.preventDefault();

                //get the rating information
                var vFacilityCode = $('#facility_code').val();
                var vRating = $("#radioDiv input[type='radio']:checked").val();
                var vComment = $("#comment").val();

                //post the rating information
                $.ajax({
                    type: "POST",
                    url: '/directory/rating',
                    data: {
                        "FacilityCode": vFacilityCode,
                        "Rating": vRating,
                        "Comment": vComment,
                        "_token": "{{ csrf_token()}}"
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
                        $('#modal_rating').modal('hide');
                    },
                    error: function(xhr, textStatus, errorThrown){
                        console.log(xhr.responseText);
                    },
                });

            });

            $('#searchButton').on('click', function() {
                var facility = $('#searchInput').val();

                if (!facility.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty Search',
                        text: 'Please Enter Facility Name or MFL Code.',
                    });
                    return;
                }

                Swal.fire({
                    title: "loading results......",
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                // var apiUrl = "{{ env('ART_URL') }}directory/" + mfl + '/' + facility;
                var isCode = /^\d{5}$/.test(facility);

                var apiUrl;
                if (isCode) {
                    apiUrl = "{{ env('ART_URL') }}facility/directory?code=" + facility;
                } else {
                    apiUrl = "{{ env('ART_URL') }}facility/directory?name=" + facility;
                }

                // Hide error message
                $('#error-message').hide();
                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    success: function(response) {
                        Swal.close();
                        var facilities = response.message;

                        // Clear existing table data
                        dataTable.clear();

                        facilities.forEach(function(facility, index) {

                            // Add a clickable link for "More" column
                            var moreLink = '<a href="https://kmhfl.health.go.ke/#/facility_filter/results?code=' + facility.code + '" target="_blank">More</a>';

                            var rateLink = getRating(facility.code.trim(),facility.search_rating.trim());

                            // console.log(rateLink);

                            // Add the row to the DataTable
                            dataTable.row.add({
                                'No': index + 1,
                                'Facility Name': facility.name,
                                'MFL Code': facility.code,
                                'Contact': facility.telephone,
                                'County': facility.county,
                                'Facility Type': facility.facility_type,
                                'More': moreLink,
                                'Rate': rateLink,
                            });
                        });

                        // Draw the updated DataTable
                        dataTable.draw();

                        // Clear the search input
                        $('#searchInput').val('');

                        // Show the result table section
                        $('#resultTableSection').show();
                        saveSearchLog(facility, facilities.length);
                    },
                    error: function(error) {
                        console.log(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error fetching the data.'
                        });
                    }
                });
            });

            function getRating(facilityCode,searchRating) {
                var rateLink;

                switch(searchRating) {
                    case '1':
                        rateLink = `<x-rating mflCode=${facilityCode} searchRating=1></x-rating>`;
                    break;
                    case '2':
                        rateLink = `<x-rating mflCode=${facilityCode} searchRating=2></x-rating>`;
                    break;
                    case '3':
                        rateLink = `<x-rating mflCode=${facilityCode} searchRating=3></x-rating>`;
                    break;
                    case '4':
                        rateLink = `<x-rating mflCode=${facilityCode} searchRating=4></x-rating>`;
                    break;
                    case '5':
                        rateLink = `<x-rating mflCode=${facilityCode} searchRating=5></x-rating>`;
                    break;
                    default:
                        rateLink = `<x-rating mflCode=${facilityCode} searchRating=0></x-rating>`;
                }

                return rateLink;
            }

            function saveSearchLog(searchTerm, resultCount) {
                //  search log
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('directory_log') }}",
                    method: 'POST',
                    data: {
                        search_term: searchTerm,
                        result_count: resultCount
                    },
                    success: function(response) {
                        // console.log(response);

                    },
                    error: function(error) {
                        console.log(error);

                    }
                });
            }
        });
    </script>
</body>

</html>
