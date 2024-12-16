@extends('layouts.admin')

@section('title')
Admin | Reports
@stop

@section('report')
<style>
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 20px;
    }

    #reports-table thead {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Reports</h5>
            <!-- Filter and Reset buttons -->
            <div class="form-group row">
                <div class="col-md-6">
                    <button onclick="downloadExcel()" class="btn btn-nav fw-bold">Export to Excel</button>
                </div>
            </div>
            <!-- Filter and Reset buttons -->
            <div class="form-group row mt-3">
                <div class="col-sm-6">
                    <button id="filter-btn" class="btn btn-nav fw-bold">Filter</button>
                    <button id="reset-btn" class="btn btn-danger">Reset</button>
                </div>
            </div>

            <!-- Date filtering options -->
            <div class="form-group row mt-3">
                <label for="from_date" class="col-sm-2 col-form-label fw-bold">From:</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" id="from_date">
                </div>
                <label for="to_date" class="col-sm-2 col-form-label fw-bold">To:</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" id="to_date">
                </div>
            </div>

            <!-- Meal Type filtering options -->
            <div class="form-group row mt-3">
               <!-- <div class="col-sm-4">
                    <label class="col-form-label fw-bold">Meal Type:</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input meal-type-checkbox" type="checkbox" id="tea" value="Tea">
                            <label class="form-check-label" for="tea">Tea</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input meal-type-checkbox" type="checkbox" id="lunch" value="Food">
                            <label class="form-check-label" for="lunch">Food</label>
                        </div>
                    </div>
                </div>-->
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
            </div>

            <!-- Reports table -->
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
                        <th>Shift</th>
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

<script>
    $(document).ready(function() {
        // Declare DataTable globally
        var table = $('#reports-table').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            lengthChange: false,
            searching: false,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            pageLength: 10,
            ajax: "{{ route('admin.reports') }}",
            columns: [
                { data: 'full_name' },
                { data: 'employment_number' },
                { data: 'user_type' },
                { data: 'site' },
                { data: 'department' },
                { data: 'meal_type' },
                { data: 'bsl_cmn_logs_time' },
                { data: 'shift' }
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

</script>
@stop

