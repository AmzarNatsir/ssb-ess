@extends('layout.mainlayout')

@section('content')
    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Edit Overtime</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('overtime.index') }}">Overtime</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('overtime.index') }}" class="btn btn-secondary d-inline-flex align-items-center">
                    <i class="ti ti-arrow-left me-1"></i>Back
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-edit me-2 text-primary"></i>Edit Overtime Request
                    </h5>
                    <p class="text-muted small mt-1 mb-0">
                        Minimum overtime: <strong>1 hour</strong> — Maximum overtime: <strong>8 hours</strong> per day.
                    </p>
                </div>
                <div class="card-body">
                    <form id="form-overtime" action="{{ route('overtime.update', $overtime->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">

                            <!-- Overtime Date -->
                            <div class="col-md-12">
                                <label class="form-label" for="tgl_pengajuan">Overtime Date <span class="text-danger">*</span></label>
                                <input type="date" id="tgl_pengajuan" name="tgl_pengajuan" class="form-control @error('tgl_pengajuan') is-invalid @enderror"
                                    value="{{ old('tgl_pengajuan', $overtime->tgl_pengajuan->format('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
                                @error('tgl_pengajuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Start Time -->
                            <div class="col-md-4">
                                <label class="form-label" for="jam_mulai">Start Time <span class="text-danger">*</span></label>
                                <input type="time" id="jam_mulai" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror"
                                    value="{{ old('jam_mulai', substr($overtime->jam_mulai, 0, 5)) }}" required>
                                @error('jam_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- End Time -->
                            <div class="col-md-4">
                                <label class="form-label" for="jam_selesai">End Time <span class="text-danger">*</span></label>
                                <input type="time" id="jam_selesai" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror"
                                    value="{{ old('jam_selesai', substr($overtime->jam_selesai, 0, 5)) }}" required>
                                @error('jam_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- Total Hours (read-only) -->
                            <div class="col-md-4">
                                <label class="form-label" for="total_jam">Total Hours</label>
                                <div class="input-group">
                                    <input type="text" id="total_jam" class="form-control bg-light"
                                        value="{{ $overtime->total_jam }}" readonly>
                                    <span class="input-group-text">hrs</span>
                                </div>
                            </div>

                            <!-- Job Description -->
                            <div class="col-md-12">
                                <label class="form-label" for="deskripsi_pekerjaan">Job Description <span class="text-danger">*</span></label>
                                <textarea id="deskripsi_pekerjaan" name="deskripsi_pekerjaan" rows="4"
                                    class="form-control @error('deskripsi_pekerjaan') is-invalid @enderror"
                                    placeholder="Describe the overtime work..." required maxlength="1000">{{ old('deskripsi_pekerjaan', $overtime->deskripsi_pekerjaan) }}</textarea>
                                @error('deskripsi_pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Max 1000 characters.</small>
                            </div>

                            <!-- Upload Overtime Order -->
                            <div class="col-md-12">
                                <label class="form-label" for="file_surat_perintah_lembur">
                                    Overtime Order (Image)
                                </label>

                                @if($overtime->file_surat_perintah_lembur)
                                    <div class="mb-2">
                                        <p class="text-muted small mb-1">Current file:</p>
                                        <a href="{{ asset('storage/' . $overtime->file_surat_perintah_lembur) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="ti ti-photo me-1"></i>View Current Image
                                        </a>
                                    </div>
                                @endif

                                <input type="file" id="file_surat_perintah_lembur" name="file_surat_perintah_lembur"
                                    class="form-control @error('file_surat_perintah_lembur') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.png">
                                <small class="text-muted">Leave empty to keep the current file. Allowed: JPG, JPEG, PNG (max 5MB).</small>
                                @error('file_surat_perintah_lembur')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                <div id="img-preview-wrap" class="mt-2" style="display:none;">
                                    <img id="img-preview" src="#" alt="Preview" class="img-thumbnail" style="max-height:200px;">
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('overtime.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x me-1"></i>Cancel
                            </a>
                            <button type="button" id="btn-submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // Auto-calculate total hours
    function calcHours() {
        const start = $('#jam_mulai').val();
        const end   = $('#jam_selesai').val();
        if (start && end && end > start) {
            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);
            const totalMins = (eh * 60 + em) - (sh * 60 + sm);
            const hrs = (totalMins / 60).toFixed(2);
            $('#total_jam').val(hrs);
        } else {
            $('#total_jam').val('-');
        }
    }

    $('#jam_mulai, #jam_selesai').on('change', calcHours);

    // Image preview
    $('#file_surat_perintah_lembur').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#img-preview').attr('src', e.target.result);
                $('#img-preview-wrap').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#img-preview-wrap').hide();
        }
    });

    // Submit with SweetAlert confirmation
    $('#btn-submit').on('click', function () {
        const tgl     = $('#tgl_pengajuan').val();
        const start   = $('#jam_mulai').val();
        const end     = $('#jam_selesai').val();
        const ket     = $('#deskripsi_pekerjaan').val().trim();
        const totalEl = $('#total_jam').val();

        if (!tgl || !start || !end || !ket) {
            Swal.fire('Incomplete Form', 'Please fill in all required fields.', 'warning');
            return;
        }

        if (end <= start) {
            Swal.fire('Invalid Time', 'End time must be after start time.', 'warning');
            return;
        }

        const totalHrs = parseFloat(totalEl);
        if (!isNaN(totalHrs)) {
            if (totalHrs < 1) {
                Swal.fire('Too Short', 'Minimum overtime duration is 1 hour.', 'warning');
                return;
            }
            if (totalHrs > 8) {
                Swal.fire('Too Long', 'Maximum overtime duration is 8 hours per day.', 'warning');
                return;
            }
        }

        Swal.fire({
            title: 'Update Overtime Request?',
            html: `<p class="mb-1">Date: <strong>${tgl}</strong></p>
                   <p class="mb-0">Time: <strong>${start} – ${end}</strong></p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Update!',
            cancelButtonText: 'Review Again'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form-overtime').submit();
            }
        });
    });

});
</script>
@endsection
