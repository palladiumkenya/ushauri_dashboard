@extends('layouts.master')
@section('page-css')

<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
@endsection

@section('main-content')
@include('sweetalert::alert')

<div class="col-md-12 mb-4">
                    <div class="card text-left">

                        <div class="card-body">
                        <! <h4 class="card-title mb-3">User Password Reset</h4>
                            <div class="col-md-12" style="margin-top:10px; ">
                            </div>
                            <div id="ResetModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ResetTitle">Reset User</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <input type="hidden" name="id" id="id">
                <p>Are you sure you want to reset this user's password?.</p>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                <button id="reset" type="button" class="btn btn-success" data-person_id="">Reset</button>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


                        </div>
                    </div>
                </div>
                <!-- end of col -->

@endsection

@section('page-js')

 <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
 <script type="text/javascript">
 function resetUser(id) {
        $('#ResetModal').modal('show');
        console.log(id);
        $(document).off("click", "#reset").on("click", "#reset", function(event) {
            $.ajax({
                type: "POST",
                url: '/reset/user',
                data: {
                    "id": id,
                    "_token": "{{ csrf_token()}}"
                },
                dataType: "json",
                success: function(data) {
                    toastr.success(data.details);
                    $('#ResetModal').modal('hide');
                }

            })
        });
    };
   </script>


@endsection
