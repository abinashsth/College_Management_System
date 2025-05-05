
@extends('layouts.app')
@section('css')
@endsection

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            <div class="page-header">
                <h1>
                    Edit Student Fee
                    <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Edit Fee Details
                    </small>
                </h1>
            </div><!-- /.page-header -->

            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="col-md-12">
                        <form class="form-horizontal" method="POST" action="{{ route('student-fee.update', $fee->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Student</label>
                                <div class="col-sm-4">
                                    <select name="student_id" class="form-control" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ $fee->student_id == $student->id ? 'selected' : '' }}>
                                                {{ $student->first_name }} {{ $student->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <label class="col-sm-2 control-label">Fee Type</label>
                                <div class="col-sm-4">
                                    <select name="fee_type" class="form-control" required>
                                        <option value="">Select Fee Type</option>
                                        <option value="tuition" {{ $fee->fee_type == 'tuition' ? 'selected' : '' }}>Tuition Fee</option>
                                        <option value="hostel" {{ $fee->fee_type == 'hostel' ? 'selected' : '' }}>Hostel Fee</option>
                                        <option value="transport" {{ $fee->fee_type == 'transport' ? 'selected' : '' }}>Transport Fee</option>
                                        <option value="other" {{ $fee->fee_type == 'other' ? 'selected' : '' }}>Other Fee</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Amount</label>
                                <div class="col-sm-4">
                                    <input type="number" name="amount" class="form-control" value="{{ $fee->amount }}" required>
                                </div>

                                <label class="col-sm-2 control-label">Due Date</label>
                                <div class="col-sm-4">
                                    <input type="date" name="due_date" class="form-control" value="{{ $fee->due_date }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Status</label>
                                <div class="col-sm-4">
                                    <select name="status" class="form-control" required>
                                        <option value="pending" {{ $fee->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $fee->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="partial" {{ $fee->status == 'partial' ? 'selected' : '' }}>Partial</option>
                                    </select>
                                </div>

                                <label class="col-sm-2 control-label">Notes</label>
                                <div class="col-sm-4">
                                    <textarea name="notes" class="form-control">{{ $fee->notes }}</textarea>
                                </div>
                            </div>

                            <div class="clearfix form-actions">
                                <div class="col-md-offset-3 col-md-9">
                                    <button class="btn btn-info" type="submit">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Update
                                    </button>
                                    &nbsp; &nbsp;
                                    <a class="btn btn-danger" href="{{ route('student-fee.index') }}">
                                        <i class="ace-icon fa fa-undo bigger-110"></i>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection
