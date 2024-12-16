@extends('layouts.admin')

@section('title')
Admin | Tickets
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
            <h5 class="card-title">Tickets</h5>

            <!-- Meal Type filtering options -->
            {{-- <div class="form-group row mt-3">
                
                <div class="col-sm-6">
                    <label class="col-form-label fw-bold">Company:</label>
                    <select class="form-select" id="company-select">
                        <option value="">Select a company</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="col-form-label fw-bold">Site:</label>
                    <select class="form-select" id="site-select">
                        <option value="">Select a site</option>
                    </select>
                </div>
            </div> --}}
            
            
            <table id="reports-table" class="table table-borderless table-striped my-3">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Staff No</th>
                        <th>Company</th>
                        <th>Site</th>
                        <th>Department</th>
                        <th>Meal Type</th>
                        <th>Timestamp</th>
                        @if(auth()->user()->hasRole('super-admin'))
                        <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

{{-- <script>
    $(document).ready(function() {
        // Declare DataTable globally
        var table = $('#reports-table').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            pageLength: 10,
            ajax: "{{ route('admin.tickets') }}",
            columns: [
                { data: 'full_name' },
                { data: 'employment_number' },
                { data: 'user_type' },
                { data: 'site' },
                { data: 'department' },
                { data: 'meal_type' },
                { data: 'bsl_cmn_logs_time' },
                @if(auth()->user()->hasRole('super-admin'))
                {
                    data: 'bsl_cmn_logs_id', // Use `null` to define a custom rendering column
                    orderable: false, // Disable sorting
                    searchable: false, // Disable searching
                    render: function(data, type, row) {
                        return `
                            <div class="row">
                                <div class="col mb-1">
                                    <a href="#" class="btn btn-success print-btn">Print</a>
                                </div>
                                <div class="col">
                                    <a href="/logs/${data}/delete" class="btn btn-danger btn-block">Delete</a>
                                </div>
                            </div>
                        `;
                    }
                }
                @endif
            ]

        });

        // Fetch and populate the company dropdown
        $.ajax({
            url: "{{ route('fetch.companies') }}",
            method: 'GET',
            success: function(data) {
                var companySelect = $('#company-select');
                companySelect.empty();
                companySelect.append('<option value="">Select a company</option>');
                
                $.each(data, function(index, company) {
                    companySelect.append('<option value="' + company.bsl_cmn_user_types_id + '">' + company.bsl_cmn_user_types_name + '</option>');
                });
            }
        });

        // Fetch and populate the site dropdown
        $.ajax({
            url: "{{ route('fetch.sites') }}",
            method: 'GET',
            success: function(data) {
                var siteSelect = $('#site-select');
                siteSelect.empty();
                siteSelect.append('<option value="">Select a site</option>');
                $.each(data, function(index, site) {
                    siteSelect.append('<option value="' + site.bsl_cmn_sites_id + '">' + site.bsl_cmn_sites_name + '</option>');
                });
            }
        });

        // Apply filters and reload DataTable
        $('#filter-btn').on('click', function() {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            var selectedCompany = $('#company-select').val();
            var selectedSite = $('#site-select').val();

            table.ajax.url("{{ route('admin.reports.filtered') }}" +
                "?from_date=" + fromDate +
                "&to_date=" + toDate +
                "&company=" + selectedCompany +
                "&site=" + selectedSite
            ).load();
        });

        // Reset filters and reload DataTable with default data
        $('#reset-btn').on('click', function() {
            $('#from_date').val('');
            $('#to_date').val('');
            $('.meal-type-checkbox').prop('checked', false);
            $('#company-select').val('');
            $('#site-select').val('');

            table.ajax.url("{{ route('admin.reports') }}").load();
        });

    });

    // Export to Excel function
    function downloadExcel() {
            // Get the current filter values
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();
            var selectedCompany = $('#company-select').val();
            var selectedSite = $('#site-select').val();
            
            // Construct the URL with the filter parameters
            var url = "{{ route('admin.reports.export') }}" + 
                "?from_date=" + fromDate + 
                "&to_date=" + toDate + 
                "&company=" + selectedCompany + 
                "&site=" + selectedSite;
            
            // Fetch the filtered data
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('No data found or server error!');
                }
                return response.json();
            })
            .then(data => {
                if (data.length === 0) {
                    alert('No records found for the selected filters.');
                    return;
                }

                const ws = XLSX.utils.json_to_sheet(data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "FilteredData");
                XLSX.writeFile(wb, 'MealTicketsReport.xlsx');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to download data. ' + error.message);
            });
    }

</script> --}}
<script>
    $(document).ready(function() {
    $('#reports-table').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.tickets') }}", // URL to the controller method
            type: "GET"
            
        },
        columns: [
            { data: 'full_name', name: 'full_name' },
            { data: 'employment_number', name: 'employment_number' },
            { data: 'user_type', name: 'user_type' },
            { data: 'site', name: 'site' },
            { data: 'department', name: 'department' },
            { data: 'meal_type', name: 'meal_type' },
            { data: 'bsl_cmn_logs_time', name: 'bsl_cmn_logs_time' },
            {
                data: 'bsl_cmn_logs_id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="row">
                                <div class="col mb-1">
                                    <a href="/logs/${data}/print" class="btn btn-success print-btn">Print</a>
                                </div>
                                <div class="col">
                                    <a href="/logs/${data}/delete" class="btn btn-danger btn-block">Delete</a>
                                </div>
                            </div>`;
                }
            }
        ]
    });
});

</script>
@stop
