@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="mt-5 col-7">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h3>Employees</h3>
                            </div>
                            <div class="text-right col-6"><a class="btn btn-info" id="add_data">ADD</a></div>
                        </div>
                        <div id="success_msg" style="color: green; font-size:25px; background: gray"></div>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Image</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div id="add_form">
                    <div class="p-2 m-auto mt-5 mb-5" style="background: rgb(223, 217, 217)">
                        <h1>AJAX Image Add</h1>
                    </div>
                    <div class="m-auto">
                        <div id="err_list" style="color: red" class="mb-3"></div>
                        <form id="addEmployee" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Employee Name</label>
                                <input type="text" name="name" class="form-control" id="name">
                            </div>
                            <div class="row">
                                <div class="mb-3 col-8">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" name="image" class="form-control" id="image"
                                        onchange="loadImage(event)">
                                </div>
                                <div class="col-4">
                                    <img id="previewImage"
                                        src="https://reactnativecode.com/wp-content/uploads/2018/02/Default_Image_Thumbnail.png"
                                        alt="Image Preview" width="150">
                                </div>
                            </div>
                            <button id="add" type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>

                {{-- update data --}}
                <div id="edit_form">
                    <div class="p-2 m-auto mt-5 mb-5" style="background: rgb(223, 217, 217)">
                        <h1>AJAX Edit Data</h1>
                    </div>
                    <div class="m-auto">
                        <div id="err_list" style="color: red" class="mb-3"></div>
                        <form id="updateEmployee" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="edit_id">
                            <div class="mb-3">
                                <label for="name" class="form-label">Employee Name</label>
                                <input type="text" name="name" class="form-control" id="edit_name">
                            </div>
                            <button id="update" type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>

                {{-- delete --}}
                <div class="mt-5 card" id="delete_form">
                    <div class="card-header">
                        <h3>Delete Data</h3>
                    </div>
                    <div class="card-body">
                        <form>
                            @csrf
                            <input type="hidden" id="delete_id">
                            <p>Are You Sure?</p>
                            <button id="yes_delete" type="button" class="btn btn-warning">Yes Delete</button>
                            <button id="no_delete" type="button" class="btn btn-secondary">NO</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function loadImage(event) {
            var previewImage = document.getElementById("previewImage");
            previewImage.src = URL.createObjectURL(event.target.files[0]);
            previewImage.onload = function() {
                URL.revokeObjectURL(previewImage.src) // free memory
            }
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $("#add_form").show();
            $("#delete_form").hide();
            $("#edit_form").hide();
            $(document).on('click', '#add_data', function(event) {
                $("#add_form").show();
                $("#delete_form").hide();
                $("#edit_form").hide();
                $("#err_list").hide();
            })

            $(document).on('click', '#add', function(event) {
                event.preventDefault();
                $("#add_form").show();
                $("#err_list").show();
                let data = new FormData($('#addEmployee')[0]);

                $.ajax({
                    type: 'POST',
                    url: '/employee',
                    data: data,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 400) {
                            $("#err_list").html('');
                            $.each(response.errors, function(key, err_value) {
                                $("#err_list").append('<li>' + err_value + '</li>');
                            })
                        } else {
                            $("#err_list").html('');
                            // $("#success_msg").text(response.message);
                            $("#addEmployee").find('input').val("");
                            alertify.set('notifier', 'position', 'top-right');
                            alertify.success(response.message);
                            showdata();

                            // setTimeout(function() {
                            // $("#success_msg").hide();
                            // }, 2000);
                        }
                    }
                });
            });

            // show  data
            showdata();

            function showdata() {
                $.ajax({
                    type: "GET",
                    url: '/employees_show',
                    dataType: "json",
                    success: function(response) {
                        $('tbody').html('');
                        $.each(response.employee, function(key, value) {
                            $('tbody').append(
                                '<tr>\
                                        <td>' + value.id + '</td>\
                                        <td>' + value.name + '</td>\
                                        <td><img src="uploads/' + value.image + '" width="100" height="50"></td>\
                                        <td><button id="edit_btn" type="button" value="' + value.id + '" class="btn btn-primary">Edit</button>\
                                            <button id="delete_btn" type="button" value="' + value.id +
                                '" class="btn btn-danger">Delete</button></td>\
                                                                                                                                                        </tr>'
                            )
                        })
                    }
                });
            };

            // .........edit data...
            $(document).on('click', '#edit_btn', function(event) {
                event.preventDefault();
                $("#edit_form").show();
                $("#delete_form").hide();
                $("#add_form").hide();
                $("#err_list").hide();

                var edit_id = $(this).val();

                $.ajax({
                    type: 'GET',
                    url: '/employee_edit/' + edit_id,
                    dataType: "json",
                    success: function(response) {
                        if (response.status == 404) {
                            $("#err_list").html("");
                            $("#err_list").text(response.message);
                        } else {
                            $("#edit_id").val(edit_id);
                            $("#edit_name").val(response.employee.name);
                        }
                    }
                })
            })

            // ..........update data.......
            $(document).on('click', '#update', function(event) {
                event.preventDefault();
                var edit_id = $("#edit_id").val()
                var data = {
                    'name': $("#edit_name").val(),
                }
                // let data = new FormData($('#updateEmployee')[0]);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'PUT',
                    url: '/employee_update/' + edit_id,
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 400) {
                            $("#err_list").html('');
                            $.each(response.errors, function(key, err_value) {
                                $("#err_list").append('<li>' + err_value + '</li>');
                            })
                        } else {
                            $("#err_list").html('');
                            $("#success_msg").text(response.message);
                            $("#edit_form").hide();
                            $("#add_form").show();
                            showdata();

                            setTimeout(function() {
                                $("#success_msg").hide();
                            }, 2000);
                        }
                    }
                });
            })

            // .........delete......
            $(document).on('click', '#delete_btn', function(event) {
                event.preventDefault()
                var delete_id = $(this).val();
                $("#delete_form").show();
                $("#add_form").hide();
                $("#edit_form").hide();
                $("#delete_id").val(delete_id)

                $(document).on('click', '#yes_delete', function(event) {
                    event.preventDefault()
                    var id = $("#delete_id").val()

                    $.ajax({
                        type: 'DELETE',
                        url: '/employee_delete/' + id,
                        dataType: "json",
                        success: function(response) {
                            alertify.set('notifier', 'position', 'top-right');
                            alertify.error(response.message);

                            $("#delete_form").hide();
                            $("#add_form").show();

                        },
                    })
                    showdata();

                })

                $(document).on('click', '#no_delete', function(event) {
                    event.preventDefault()
                    $("#delete_form").hide();
                    $("#add_form").show();
                })

            });



        });
    </script>
@endsection
