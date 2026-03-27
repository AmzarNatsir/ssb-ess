@extends('layout.mainlayout')

@section('content')
    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Overtime Management</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Overtime</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('overtime.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="ti ti-circle-plus me-1"></i>Apply Overtime
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
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
                <div class="card-header pb-0">
                    <ul class="nav nav-tabs nav-bordered mb-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#submission" role="tab" aria-selected="true">
                                <i class="ti ti-clock-share me-1"></i>Pengajuan
                                @if($pendingRequests->count() > 0)
                                    <span class="badge bg-warning text-dark ms-1">{{ $pendingRequests->count() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#history" role="tab" aria-selected="false">
                                <i class="ti ti-history me-1"></i>History Lembur
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">

                        <!-- Submission Tab -->
                        <div class="tab-pane fade show active" id="submission" role="tabpanel">
                            <div class="table-responsive custom-table">
                                <table class="table table-hover mb-0" id="table-submission">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Submission Date</th>
                                            <th>Overtime Date</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Total Hours</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingRequests as $overtime)
                                            <tr>
                                                <td>{{ $overtime->created_at->format('d M Y') }}</td>
                                                <td>{{ date('d M Y', strtotime($overtime->tgl_pengajuan)) }}</td>
                                                <td>{{ $overtime->jam_mulai }}</td>
                                                <td>{{ $overtime->jam_selesai }}</td>
                                                <td>{{ $overtime->total_jam }} hrs</td>
                                                <td>
                                                    <span class="badge bg-light-warning text-warning mb-1">
                                                        <i class="ti ti-point-filled me-1"></i>Pending
                                                    </span>
                                                    @if($overtime->currentApproval)
                                                        <div class="small text-muted">
                                                            Waiting: <span class="text-primary">{{ $overtime->currentApproval->nm_lengkap }}</span>
                                                            <div class="x-small">({{ $overtime->currentApproval->jabatan->nm_jabatan ?? '-' }})</div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light" data-bs-toggle="dropdown">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item btn-view-details" href="javascript:void(0);" data-id="{{ $overtime->id }}">
                                                                    <i class="ti ti-eye me-2"></i>View Details
                                                                </a>
                                                            </li>
                                                            @if (!$overtime->isLockedByApprover())
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('overtime.edit', $overtime->id) }}">
                                                                        <i class="ti ti-edit me-2"></i>Edit Request
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger btn-cancel-overtime" href="javascript:void(0);" data-id="{{ $overtime->id }}">
                                                                        <i class="ti ti-trash me-2"></i>Cancel Request
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No pending overtime requests found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <div class="table-responsive custom-table">
                                <table class="table table-hover mb-0" id="table-history">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Submission Date</th>
                                            <th>Overtime Date</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Total Hours</th>
                                            <th>Job Description</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($history as $overtime)
                                            <tr>
                                                <td>{{ $overtime->created_at->format('d M Y') }}</td>
                                                <td>{{ date('d M Y', strtotime($overtime->tgl_pengajuan)) }}</td>
                                                <td>{{ $overtime->jam_mulai }}</td>
                                                <td>{{ $overtime->jam_selesai }}</td>
                                                <td>{{ $overtime->total_jam }} hrs</td>
                                                <td class="text-truncate" style="max-width: 220px;">{{ $overtime->deskripsi_pekerjaan }}</td>
                                                <td>
                                                    @if($overtime->status_pengajuan == 1)
                                                        <span class="badge bg-light-warning text-warning mb-1">
                                                            <i class="ti ti-clock me-1"></i>Waiting
                                                        </span>
                                                    @elseif($overtime->status_pengajuan == 2)
                                                        <span class="badge bg-light-success text-success mb-1">
                                                            <i class="ti ti-check me-1"></i>Approved
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light-danger text-danger mb-1">
                                                            <i class="ti ti-x me-1"></i>Rejected
                                                        </span>
                                                    @endif

                                                    @if($overtime->currentApproval)
                                                        <div class="small text-muted">
                                                            @if($overtime->status_pengajuan == 2)
                                                                Approved by:
                                                            @elseif($overtime->status_pengajuan == 3)
                                                                Rejected by:
                                                            @else
                                                                Waiting:
                                                            @endif
                                                            <span class="text-primary">{{ $overtime->currentApproval->nm_lengkap }}</span>
                                                            <div class="x-small">({{ $overtime->currentApproval->jabatan->nm_jabatan ?? '-' }})</div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light btn-view-details" data-id="{{ $overtime->id }}" data-bs-toggle="tooltip" title="View Details">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No overtime history found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Overtime Detail Modal -->
    <div class="modal fade" id="overtimeDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-0 pb-3">
                    <div>
                        <h5 class="modal-title fw-semibold mb-1">Overtime Request Details</h5>
                        <p class="text-muted small mb-0">Complete information about overtime request</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="card border mb-3">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-primary-transparent text-primary rounded me-3 mt-1">
                                            <i class="ti ti-calendar-event"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Overtime Date</label>
                                            <p class="fw-semibold mb-0" id="detail-tgl-pengajuan">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-success-transparent text-success rounded me-3 mt-1">
                                            <i class="ti ti-clock-play"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Start Time</label>
                                            <p class="fw-semibold mb-0" id="detail-jam-mulai">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-danger-transparent text-danger rounded me-3 mt-1">
                                            <i class="ti ti-clock-stop"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">End Time</label>
                                            <p class="fw-semibold mb-0" id="detail-jam-selesai">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-warning-transparent text-warning rounded me-3 mt-1">
                                            <i class="ti ti-hourglass"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Total Hours</label>
                                            <p class="fw-semibold mb-0"><span id="detail-total-jam">-</span> hrs</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-secondary-transparent text-secondary rounded me-3 mt-1">
                                            <i class="ti ti-info-circle"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Status</label>
                                            <div><span class="badge badge-sm" id="detail-status">-</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-primary-transparent text-primary rounded me-3 mt-1">
                                            <i class="ti ti-paperclip"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Overtime Order</label>
                                            <div id="detail-file-wrap">-</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-secondary-transparent text-secondary rounded me-3 mt-1">
                                            <i class="ti ti-message-circle"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Job Description</label>
                                            <p class="mb-0 text-dark" id="detail-keterangan">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval History -->
                    <div class="mb-0">
                        <h6 class="fw-semibold mb-3">
                            <i class="ti ti-timeline me-2 text-primary"></i>Approval History
                        </h6>
                        <div class="card border mb-0">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-0 py-3">Level</th>
                                                <th class="border-0 py-3">Approver</th>
                                                <th class="border-0 py-3">Status</th>
                                                <th class="border-0 py-3">Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail-approval-history">
                                            <!-- Populated via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden cancel form -->
    <form id="form-cancel-overtime" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // View Detail Modal
    $('.btn-view-details').on('click', function () {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('overtimeDetailModal'));

        $.ajax({
            url: `{{ url('overtime') }}/${id}`,
            method: 'GET',
            success: function (res) {
                if (res.success) {
                    const d = res.data;
                    $('#detail-tgl-pengajuan').text(d.tgl_pengajuan);
                    $('#detail-jam-mulai').text(d.jam_mulai);
                    $('#detail-jam-selesai').text(d.jam_selesai);
                    $('#detail-total-jam').text(d.total_jam);
                    $('#detail-keterangan').text(d.deskripsi_pekerjaan);

                    // File link
                    if (d.file_surat_perintah_lembur) {
                        $('#detail-file-wrap').html(
                            `<a href="${d.file_surat_perintah_lembur}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-photo me-1"></i>View Image
                            </a>`
                        );
                    } else {
                        $('#detail-file-wrap').text('-');
                    }

                    // Status badge
                    const badge = $('#detail-status');
                    badge.text(d.status_text).removeClass();
                    if (d.status_pengajuan === 1) {
                        badge.addClass('badge badge-soft-warning badge-sm');
                    } else if (d.status_pengajuan == 2) {
                        badge.addClass('badge badge-soft-success badge-sm');
                    } else {
                        badge.addClass('badge badge-soft-danger badge-sm');
                    }

                    // Approval History
                    let approvalHtml = '';
                    if (d.approvals && d.approvals.length > 0) {
                        d.approvals.forEach(function(appr) {
                            let statusClass = 'badge-soft-secondary';
                            let statusIcon = 'ti-clock';
                            if (appr.status === 'Approved') {
                                statusClass = 'badge-soft-success';
                                statusIcon = 'ti-check';
                            } else if (appr.status === 'Pending') {
                                statusClass = 'badge-soft-warning';
                                statusIcon = 'ti-clock';
                            } else if (appr.status === 'Rejected') {
                                statusClass = 'badge-soft-danger';
                                statusIcon = 'ti-x';
                            }

                            approvalHtml += `
                                <tr>
                                    <td class="py-3">
                                        <span class="badge badge-soft-info badge-sm">Level ${appr.level}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs bg-light-primary text-primary rounded-circle me-2">
                                                <i class="ti ti-user fs-6"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium">${appr.name}</div>
                                                <small class="text-muted text-truncate d-block" style="max-width:150px;">${appr.position}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge ${statusClass} badge-sm">
                                            <i class="ti ${statusIcon} me-1"></i>${appr.status}
                                        </span>
                                    </td>
                                    <td class="py-3 text-muted small">${appr.remark}</td>
                                </tr>
                            `;
                        });
                    } else {
                        approvalHtml = '<tr><td colspan="4" class="text-center py-4 text-muted small">No approval process found.</td></tr>';
                    }
                    $('#detail-approval-history').html(approvalHtml);

                    modal.show();
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to fetch overtime details.', 'error');
            }
        });
    });

    // Cancel / Delete
    $('.btn-cancel-overtime').on('click', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Cancel Overtime Request?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('#form-cancel-overtime');
                form.attr('action', `{{ url('overtime') }}/${id}`);
                form.submit();
            }
        });
    });

});
</script>
@endsection
