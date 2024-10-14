@extends('layouts.admin')

@section('title')
Admin | Create User
@stop

@section('report')
<div class="col-lg-12">
    <div class="col-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between py-2 px-3">
                <h5 class="m-0">Intern</h5>
                <a href="{{ route('hr.home')}}" class="btn btn-danger">Back</a>
            </div>

            <div class="card-body">
                <form action="{{route('hr.user.store')}}" method="POST">
                    @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @csrf
                    <div class="row">
                        <div class="mb-3 mt-3 col-lg-4">
                            <label for="name" class="fw-bold">First Name</label>
                            <input type="text" name="firstname" class="form-control">
                        </div>
                        <div class="mb-3 mt-3 col-lg-4">
                            <label for="name" class="fw-bold">Last Name</label>
                            <input type="text" name="lastname" class="form-control">
                        </div>
                        <div class="mb-3 mt-3 col-lg-4">
                            <label for="name" class="fw-bold">Email</label>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="mb-3 mt-3 col-lg-4">
                            <label for="name" class="fw-bold">Employment Number</label>
                            <input type="text" name="employment_number" class="form-control">
                        </div>
                        <div class="mb-3 mt-3 col-lg-4">
                            <label for="name" class="fw-bold">Department</label>
                            <input type="text" name="department" class="form-control">
                        </div>
                        <div class="mb-3 mt-2 col-lg-4">
                            <label for="user_type_id" class="form-label fw-bold">Company</label>
                            <select class="form-select" id="user_type_id" name="user_type_id" required>
                                <option value="">Select Company</option>
                                @foreach ($userTypes as $userTypeId => $userTypeName)
                                <option value="{{ $userTypeId }}">{{ $userTypeName }}</option>
                                @endforeach
                            </select>

                        </div>
                        <input type="hidden" name="password" value="P@ssword">

                        <div class="mb-3 mt-3 col-lg-6">
                            <label for="name" class="fw-bold">Roles</label>
                            <select name="roles[]" class="form-control" multiple>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                <option value="{{$role}}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 mt-3 col-lg-6">
                            <label for="user_type_id" class="form-label fw-bold">Shifts</label>
                            <select class="form-select" id="user_type_id" name="shift[]" required>
                                <option value="">Select Shift</option>
                                @foreach($shifts as $shiftId => $shiftName)
                                <option value="{{ $shiftId }}">{{ $shiftName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" id="status" name="status" value="1">

                        <div class="mb-3 mt-3 col-lg-12">
                            <button type="submit" class="btn btn-primary ">Save</button>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>

@stop

@section('recent-activity')

@stop