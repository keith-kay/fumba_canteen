@extends('layouts.admin')

@section('title')
Canteen Management | HR 
@stop
@section('report')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <div class="container my-3">
            <style>
                .ajs-success {
                    background-color: #4CAF50;
                    color: #ffffff;
                }

                .colored-toast.swal2-icon-success {
                    background-color: #28a745 !important;
                }

                .colored-toast.swal2-icon-error {
                    background-color: #f27474 !important;
                }

                .colored-toast.swal2-icon-warning {
                    background-color: #f8bb86 !important;
                }

                .colored-toast.swal2-icon-info {
                    background-color: #3fc3ee !important;
                }

                .colored-toast.swal2-icon-question {
                    background-color: #87adbd !important;
                }

                .colored-toast .swal2-title {
                    color: white;
                }

                .colored-toast .swal2-close {
                    color: white;
                }

                .colored-toast .swal2-html-container {
                    color: white;
                }
                /**/
                .blurred {
                filter: blur(6px); /* Blur the cell content */
                transition: filter 0.3s ease; /* Smooth transition */
                }

                .blurred:hover {
                filter: blur(0); /* Remove blur on hover */
                }
            </style>
            @if(session('success'))
            <script>
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-right',
                    iconColor: 'white',
                    customClass: {
                        popup: 'colored-toast',
                    },
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                (async () => {
                    await Toast.fire({
                        icon: 'success',
                        title: "{{session('success')}}",
                    })
                })()
            </script>
            @elseif(session('error'))
            <script>
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-right',
                    iconColor: 'white',
                    customClass: {
                        popup: 'colored-toast',
                    },
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                (async () => {
                    await Toast.fire({
                        icon: 'error',
                        title: "{{ session('error') }}",
                    })
                })();
            </script>
            @endif
        </div>
        <div class="col-lg-12">
            <div class="card">
                
                <div class="card-body">
                    <h5 class="card-title">Human Resources</h5>
                    <!-- Your existing filter options and other form elements remain unchanged -->
                    <style>
                        .hide-id {
                            display: none; /* Hides the column */
                        }
                    </style>
                    <div>
                        <a href="{{ route('hr.user.add') }}">
                            <button class="btn btn-primary">
                                <i class="bi bi-plus"></i>
                                Add Intern
                            </button>
                        </a>
                        <!--  -->
                        <a href="{{ route('guests.create') }}">
                            <button class="btn btn-primary">
                                <i class="bi bi-plus"></i>
                                Add Guest
                            </button>
                        </a>
                    </div>
                    <!-- -->
                    <div class="container mt-5">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="interns-tab" data-bs-toggle="tab" data-bs-target="#interns" type="button" role="tab" aria-controls="guests" aria-selected="true">Guests</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" id="guests-tab" data-bs-toggle="tab" data-bs-target="#guests" type="button" role="tab" aria-controls="interns" aria-selected="false">Interns</button>
                          </li>
                        </ul>
                        
                        <div class="tab-content" id="myTabContent">
                          <!-- Interns Tab -->
                          <div class="tab-pane fade show active" id="interns" role="tabpanel" aria-labelledby="interns-tab">


                            <table id="users-table" class="table table-striped table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Token</th>
                                        <th>Number of Days</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($guests as $guest)
                                    <tr>
                                        <td style="white-space: nowrap;">{{$guest -> bsl_cmn_users_employment_number}}</td>
                                        <td style="white-space: nowrap;">{{ $guest->bsl_cmn_users_firstname }}
                                            {{$guest->bsl_cmn_users_lastname}}
                                        </td>
                                        <td class="blurred" style="white-space: nowrap;">{{$guest -> bsl_cmn_users_pin}}</td>
                                        <td style="white-space: nowrap;">{{$guest -> bsl_cmn_users_days}}</td>
                                        <td style="white-space: nowrap;">
                                            <div class="row">
                                                <div class="col">
                                                    <a href="{{ url('guests/'.$guest->bsl_cmn_users_id.'/delete') }}"
                                                        class="btn btn-danger btn-block">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- -->
                          </div>
                          
                          <!-- Guests Tab -->
                          <div class="tab-pane fade" id="guests" role="tabpanel" aria-labelledby="guests-tab">
                            
                            <table id="users-table" class="table table-striped table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Token</th>
                                        <th>Number of Days</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($interns as $intern)
                                    <tr>
                                        <td style="white-space: nowrap;">{{$intern->bsl_cmn_users_employment_number}}</td>
                                        <td style="white-space: nowrap;">{{ $intern->bsl_cmn_users_firstname }}
                                            {{$intern->bsl_cmn_users_lastname}}
                                        </td>
                                        <td class="blurred" style="white-space: nowrap;">{{$intern->bsl_cmn_users_pin}}</td>
                                        <td style="white-space: nowrap;">{{$intern->bsl_cmn_users_days}}</td>
                                        <td style="white-space: nowrap;">
                                            <div class="row">
                                                <div class="col">
                                                    <a href="{{ url('guests/'.$intern->bsl_cmn_users_id.'/delete') }}"
                                                        class="btn btn-danger btn-block">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                
                                    @endforeach
                                </tbody>
                            </table>

                          </div>
                        </div>
                      </div>

                    <!-- -->

                    
                </div>
            </div>
            
        </div>
        @if(auth()->user()->hasRole('security') || auth()->user()->hasRole('super-admin'))
        <div class="form-group row mt-3 mb-2">
            <div class="col-sm-12 text-end"> <!-- Align the button to the right -->
                <a class="btn btn-success print-btn print-btn">Print</a>
            </div>
        </div>
        @endif
        </div>
    </div>
</div>
@stop

@section('scripts')
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
    function displayTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        document.getElementById("currentTime").innerHTML = timeString;
    }

    // Display time when the page loads
    displayTime();
    
    // Update the time every second
    setInterval(displayTime, 1000);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

<script>
    
    console.log("Request URL: ", "{{ route('get.sites') }}");
    
    $(document).ready(function() {
        var table = $('#reports-table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "pageLength": 10 // Display 10 rows per page
        });

        $('#custom-search').on('keyup', function() {
            table.search(this.value).draw();  // Perform a search and redraw the table
        });

        // Select all checkboxes when "Select All" is clicked
        $('#select-all').on('click', function() {
            var checked = $(this).prop('checked');
            $('.user-checkbox').prop('checked', checked);
        });

        // If any checkbox is unchecked, uncheck the "Select All" checkbox
        $(document).on('change', '.user-checkbox', function() {
            if (!$(this).prop('checked')) {
                $('#select-all').prop('checked', false);
            }
        });

        
        // Fetch and populate the site dropdown without triggering a filter
        $.ajax({
            url: "{{ route('get.sites') }}",
            method: 'GET',
            success: function(data) {
                var siteSelect = $('#site-select');
                
                siteSelect.empty();
                siteSelect.append('<option value="">Select a site</option>');
                
                $.each(data, function(index, site) {
                    // Use site.id as the value and site.bsl_cmn_sites_name as the text
                    siteSelect.append('<option value="' + site.bsl_cmn_sites_id + '">' + site.bsl_cmn_sites_name + '</option>');
                });
            }
        });

    

               
        $('.print-btn').on('click', function() {
            var selectedSiteId = $('#site-select').val(); // Get selected site ID
            var ticketsToPrint = [];

            // Collect data from checked rows in the DataTable
            table.rows({ search: 'applied' }).every(function() {
                var data = this.data();
                var checkbox = $(this.node()).find('.user-checkbox'); // Use 'user-checkbox' class

                if (checkbox.is(':checked')) {
                    ticketsToPrint.push({
                        name: data[0], // User's name
                        staffNo: data[1],
                        company: data[2],
                        site: selectedSiteId,
                        department: data[3], // Ensure the correct index
                        mealType: data[4], // Ensure the correct index
                        timestamp: data[5], // Ensure the correct index
                        userId: data[6]
                    });
                }
            });

            console.log("Tickets to Print: ", ticketsToPrint);
            console.log("Selected Site ID: ", selectedSiteId);

            // Send data to the controller for processing
            $.ajax({
                url: "{{ route('log.ticket') }}",
                method: 'POST',
                data: {
                    tickets: ticketsToPrint, // ticketsToPrint.length > 0 ? ticketsToPrint : [],
                    site_id: selectedSiteId, // selectedSiteId ? parseInt(selectedSiteId) : null,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
		    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Print Successful!',
                            text: response.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Print Failed!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred',
                        text: xhr.responseJSON?.message || 'Please try again later.'
                    });
                }
            });
        });


        // Hide DataTable search input
        $('.dataTables_filter').hide();
    });
</script>


@stop