@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Official Travel (Perdis)</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Official Travel</li>
                        </ol>
                    </nav>
                </div>
            </div>                
            <!-- End Page Header -->

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('perdis.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Month</label>
                            <select name="month" class="form-select">
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Year</label>
                            <select name="year" class="form-select">
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-filter me-2"></i>Filter Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-briefcase me-2 text-primary"></i>Travel History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Number</th>
                                    <th>Date</th>
                                    <th>Destination</th>
                                    <th>Reason</th>
                                    <th>Departure</th>
                                    <th>Return</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perdisList as $index => $perdis)
                                    @php
                                        $isApproved = $perdis->sts_pengajuan == 2;
                                        $isReturned = $perdis->tgl_kembali <= now();
                                        $showAccountability = $isApproved && $isReturned;
                                    @endphp
                                    <tr>
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $perdis->no_perdis }}</td>
                                        <td>{{ $perdis->tgl_perdis ? $perdis->tgl_perdis->format('d M Y') : '-' }}</td>
                                        <td>
                                            <span class="d-block fw-semibold">{{ $perdis->tujuan }}</span>
                                        </td>
                                        <td class="text-truncate" style="max-width: 200px;" title="{{ $perdis->maksud_tujuan }}">
                                            {{ $perdis->maksud_tujuan }}
                                        </td>
                                        <td>{{ $perdis->tgl_berangkat ? $perdis->tgl_berangkat->format('d M Y') : '-' }}</td>
                                        <td>{{ $perdis->tgl_kembali ? $perdis->tgl_kembali->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if($perdis->sts_pengajuan == 2)
                                                <span class="badge bg-success-transparent text-success px-3 py-2">Approved</span>
                                            @elseif($perdis->sts_pengajuan == 3)
                                                <span class="badge bg-danger-transparent text-danger px-3 py-2">Rejected</span>
                                            @else
                                                <span class="badge bg-warning-transparent text-warning px-3 py-2">Pending</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 text-center">
                                            @if($showAccountability)
                                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 btn-accountability" data-id="{{ $perdis->id }}">
                                                    Accountability
                                                </button>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="ti ti-info-circle fs-2 d-block mb-2"></i>
                                            No travel records found for this period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- Accountability Modal -->
        <div class="modal fade" id="accountabilityModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0 p-3">
                        <h5 class="modal-title text-white fw-bold"><i class="ti ti-report-money me-2"></i>Official Travel Accountability</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light-gray" style="max-height: 80vh; overflow-y: auto;">
                        <form id="accountabilityForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="perdis_id" id="perdis_id">
                            
                            <!-- Trip Details Section -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-0 py-2">
                                    <h6 class="mb-0 fw-bold">Business Trip Details</h6>
                                </div>
                                <div class="card-body py-3">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="small text-muted mb-1 d-block">Number</label>
                                            <span id="detail_no" class="fw-bold text-dark"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small text-muted mb-1 d-block">Destination</label>
                                            <span id="detail_tujuan" class="fw-bold text-dark"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small text-muted mb-1 d-block">Departure</label>
                                            <span id="detail_berangkat" class="fw-bold text-dark"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small text-muted mb-1 d-block">Return</label>
                                            <span id="detail_kembali" class="fw-bold text-dark"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approval History Section -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-0 py-2">
                                    <h6 class="mb-0 fw-bold">Approval History</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-nowrap mb-0">
                                            <thead class="bg-light fs-11">
                                                <tr>
                                                    <th class="ps-3">Level</th>
                                                    <th>Approver</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th class="pe-3">Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody id="approval_history_body" class="fs-13">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Facilities & Realization Section -->
                            <div class="card border-0 shadow-sm mb-0">
                                <div class="card-header bg-white border-0 py-2">
                                    <h6 class="mb-0 fw-bold">Business Trip Facilities & Realization</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light fs-11">
                                                <tr>
                                                    <th class="ps-3">Facility Item</th>
                                                    <th class="text-center">Days</th>
                                                    <th class="text-end">Cost (Unit)</th>
                                                    <th class="text-end">Subtotal</th>
                                                    <th style="width: 200px;">Realization (Unit)</th>
                                                    <th class="pe-3">Documents (File 1 & 2)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="facility_body" class="fs-13">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 p-3 bg-white shadow-sm">
                        <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary px-4" id="btn_save_realization">
                            <i class="ti ti-device-floppy me-2"></i>Update Realization
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @component('components.footer')
        @endcomponent
    </div>

@endsection

@section('scripts')
<style>
    .bg-success-transparent { background-color: rgba(30, 163, 76, 0.15); }
    .bg-danger-transparent { background-color: rgba(220, 38, 38, 0.15); }
    .bg-warning-transparent { background-color: rgba(234, 179, 8, 0.15); }
    .bg-light-gray { background-color: #f9fafb; }
    .fs-11 { font-size: 11px; }
    .fs-13 { font-size: 13px; }
</style>

<script>
    $(document).ready(function() {
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Rupiah and Number only formatting
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        $(document).on('input', '.input-realisasi', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            if (val === '') {
                $(this).val('');
            } else {
                $(this).val(formatRupiah(parseInt(val)));
            }
        });

        // Open Accountability Modal
        $('.btn-accountability').on('click', function() {
            const id = $(this).data('id');
            $('#perdis_id').val(id);
            
            // Show loading state or clear previous
            $('#approval_history_body').html('<tr><td colspan="5" class="text-center py-3">Loading...</td></tr>');
            $('#facility_body').html('<tr><td colspan="6" class="text-center py-3">Loading...</td></tr>');

            $.get(`/perdis/${id}/details`, function(data) {
                const p = data.perdis;
                $('#detail_no').text(p.no_perdis);
                $('#detail_tujuan').text(p.tujuan);
                $('#detail_berangkat').text(new Date(p.tgl_berangkat).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}));
                $('#detail_kembali').text(new Date(p.tgl_kembali).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}));

                // Approval History
                let appHtml = '';
                data.approvals.forEach(a => {
                    appHtml += `
                        <tr>
                            <td class="ps-3">${a.level}</td>
                            <td>${a.approver}</td>
                            <td><span class="badge ${a.status === 'Approved' ? 'bg-success' : (a.status === 'Rejected' ? 'bg-danger' : 'bg-warning')}">${a.status}</span></td>
                            <td>${a.date}</td>
                            <td class="pe-3">${a.remark}</td>
                        </tr>
                    `;
                });
                $('#approval_history_body').html(appHtml || '<tr><td colspan="5" class="text-center py-3">No approval history</td></tr>');

                // Facilities
                let facHtml = '';
                data.facilities.forEach(f => {
                    facHtml += `
                        <tr>
                            <td class="ps-3 fw-semibold">${f.item}</td>
                            <td class="text-center">${f.hari}</td>
                            <td class="text-end">${formatRupiah(f.biaya)}</td>
                            <td class="text-end fw-bold text-primary">${formatRupiah(f.sub_total)}</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="realisasi[${f.id}]" class="form-control input-realisasi" value="${formatRupiah(f.realisasi || 0 )}">
                                </div>
                            </td>
                            <td class="pe-3">
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="small text-muted mb-0" style="width: 40px;">File 1:</label>
                                        <input type="file" name="file_1[${f.id}]" class="form-control form-control-sm">
                                        ${f.file_1 ? `<a href="{{ asset('storage') }}/${f.file_1}" target="_blank" class="text-primary"><i class="ti ti-file-download"></i></a>` : ''}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="small text-muted mb-0" style="width: 40px;">File 2:</label>
                                        <input type="file" name="file_2[${f.id}]" class="form-control form-control-sm">
                                        ${f.file_2 ? `<a href="{{ asset('storage') }}/${f.file_2}" target="_blank" class="text-primary"><i class="ti ti-file-download"></i></a>` : ''}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                $('#facility_body').html(facHtml || '<tr><td colspan="6" class="text-center py-3">No facilities found</td></tr>');

                $('#accountabilityModal').modal('show');
            });
        });

        // Save Realization
        $(document).on('click', '#btn_save_realization', function() {
            console.log('Update Realization clicked');
            const form = $('#accountabilityForm')[0];
            const formData = new FormData(form);
            const id = $('#perdis_id').val();
            
            if (!id) {
                Swal.fire('Error!', 'System error: Perdis ID missing.', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to update the realization for this trip.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('/perdis') }}/${id}/accountability`,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#btn_save_realization').prop('disabled', true).html('<i class="ti ti-loader-2 ti-spin me-2"></i>Updating...');
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(err) {
                            console.error('AJAX Error:', err);
                            Swal.fire('Error!', 'Something went wrong while saving.', 'error');
                        },
                        complete: function() {
                            $('#btn_save_realization').prop('disabled', false).html('<i class="ti ti-device-floppy me-2"></i>Update Realization');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
