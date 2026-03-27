@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Edit Leave Request</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{route('leave.index')}}">Leave</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Request</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit Leave Application</h5>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('leave.update', $leave->id) }}" method="POST" id="leaveForm">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                                        <select name="id_jenis_cuti" id="id_jenis_cuti" class="form-control @error('id_jenis_cuti') is-invalid @enderror" required>
                                            <option value="">-- Select Leave Type --</option>
                                            @foreach($leaveTypes as $type)
                                                <option value="{{ $type->id }}" {{ (old('id_jenis_cuti', $leave->id_jenis_cuti) == $type->id) ? 'selected' : '' }}>
                                                    {{ $type->nm_jenis_ci }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_jenis_cuti')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="tgl_awal" id="tgl_awal" class="form-control @error('tgl_awal') is-invalid @enderror" value="{{ old('tgl_awal', $leave->tgl_awal ? $leave->tgl_awal->format('Y-m-d') : '') }}" required>
                                        @error('tgl_awal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control @error('tgl_akhir') is-invalid @enderror" value="{{ old('tgl_akhir', $leave->tgl_akhir ? $leave->tgl_akhir->format('Y-m-d') : '') }}" required>
                                        @error('tgl_akhir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Number of Days</label>
                                        <input type="text" id="jumlah_hari" class="form-control bg-light" readonly value="{{ $leave->jumlah_hari }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Remaining Leave Entitlement</label>
                                        <input type="text" id="remaining_entitlement" class="form-control bg-light" readonly placeholder="-">
                                        <small class="text-muted">(Excluding this request)</small>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Description/Reason <span class="text-danger">*</span></label>
                                        <textarea name="ket_cuti" class="form-control @error('ket_cuti') is-invalid @enderror" rows="3" required>{{ old('ket_cuti', $leave->ket_cuti) }}</textarea>
                                        @error('ket_cuti')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('leave.index') }}" class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Application</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const leaveTypeSelect = document.getElementById('id_jenis_cuti');
            const startDateInput = document.getElementById('tgl_awal');
            const endDateInput = document.getElementById('tgl_akhir');
            const jumlahHariInput = document.getElementById('jumlah_hari');
            const remainingInput = document.getElementById('remaining_entitlement');
            const leaveId = "{{ $leave->id }}";

            function calculateDays() {
                const start = new Date(startDateInput.value);
                const end = new Date(endDateInput.value);

                if (startDateInput.value && endDateInput.value) {
                    if (end >= start) {
                        const diffTime = Math.abs(end - start);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        jumlahHariInput.value = diffDays;
                    } else {
                        jumlahHariInput.value = 'Invalid Period';
                    }
                } else {
                    jumlahHariInput.value = '0';
                }
            }

            function fetchEntitlement() {
                const id = leaveTypeSelect.value;
                if (id) {
                    fetch(`/leave/remaining-entitlement/${id}?exclude_id=${leaveId}`)
                        .then(response => response.json())
                        .then(data => {
                            remainingInput.value = data.remaining + ' Days';
                        })
                        .catch(error => {
                            console.error('Error fetching entitlement:', error);
                            remainingInput.value = 'Error';
                        });
                } else {
                    remainingInput.value = '-';
                }
            }

            leaveTypeSelect.addEventListener('change', fetchEntitlement);
            startDateInput.addEventListener('change', calculateDays);
            endDateInput.addEventListener('change', calculateDays);

            // Initial calculation
            fetchEntitlement();
            if (startDateInput.value && endDateInput.value) calculateDays();
        });
    </script>

@endsection
