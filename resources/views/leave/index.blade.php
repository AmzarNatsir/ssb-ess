@php
    use App\Helpers\HrdConstants;
@endphp

@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Leave Management</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Leave</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($count_aktif_cuti == 0)
                        <a href="{{ route('leave.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                            <i class="ti ti-circle-plus me-1"></i>Apply Leave
                        </a>
                    @else
                        <button type="button" class="btn btn-secondary d-inline-flex align-items-center" disabled data-bs-toggle="tooltip" title="You cannot apply for leave while you are currently on leave">
                            <i class="ti ti-lock me-1"></i>Apply Leave
                        </button>
                        <div class="alert alert-warning d-inline-flex align-items-center mb-0 py-2 px-3" role="alert">
                            <i class="ti ti-alert-circle me-2"></i>
                            <small>You are currently on leave and cannot submit a new request</small>
                        </div>
                    @endif
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-tabs-bottom mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#pending" role="tab" aria-selected="true">
                        <i class="ti ti-clock-share me-2"></i>Pengajuan (Request)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#history" role="tab" aria-selected="false">
                        <i class="ti ti-history me-2"></i>History Cuti
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Pending Requests Tab -->
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    @forelse($pendingRequests as $leave)
                        <div class="card mb-3 shadow-sm border">
                            <div class="card-body p-3">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar avatar-md bg-danger-transparent text-danger rounded">
                                            <i class="ti ti-calendar fs-5"></i>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <p class="text-muted mb-1 small">Request Date</p>
                                        <h6 class="mb-0 fw-semibold">{{ date('d M Y', strtotime($leave->tgl_pengajuan)) }}</h6>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <p class="text-muted mb-1 small">Type of Leave</p>
                                        <h6 class="mb-1 fw-semibold">{{ $leave->jenisCuti->nm_jenis_ci ?? 'N/A' }}</h6>
                                        <span class="badge badge-soft-secondary badge-sm">
                                            <i class="ti ti-clock-hour-4 me-1"></i>{{ $leave->jumlah_hari }} Days
                                        </span>
                                    </div>

                                    <div class="col-lg-3 col-md-4">
                                        <p class="text-muted mb-1 small">Leave Period</p>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <span class="fw-medium">{{ date('d M', strtotime($leave->tgl_awal)) }}</span>
                                            <i class="ti ti-arrow-narrow-right mx-2 text-muted"></i>
                                            <span class="fw-medium">{{ date('d M Y', strtotime($leave->tgl_akhir)) }}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <p class="text-muted mb-1 small">Status</p>
                                        <span class="badge badge-soft-warning badge-sm mb-1">
                                            <i class="ti ti-point-filled"></i>{{ HrdConstants::STATUS_CUTI[$leave->sts_pengajuan] ?? 'Unknown' }}
                                        </span>
                                        @if($leave->get_current_approve)
                                            <div class="small text-muted">
                                                Waiting: <span class="text-primary">{{ $leave->get_current_approve->nm_lengkap }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-auto col-md-12 ms-lg-auto">
                                        <div class="d-flex gap-2 justify-content-md-end">
                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light btn-view-details" data-id="{{ $leave->id }}" data-bs-toggle="tooltip" title="View Details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if($leave->sts_pengajuan == 1)
                                                <a href="{{ route('leave.edit', $leave->id) }}" class="btn btn-icon btn-sm btn-light" data-bs-toggle="tooltip" title="Edit Request">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                @if($leave->is_draft == 1)
                                                    <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light text-danger btn-cancel-leave" data-id="{{ $leave->id }}" data-bs-toggle="tooltip" title="Cancel Request">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <div class="avatar avatar-xl bg-light-secondary text-secondary rounded-circle mx-auto mb-3">
                                    <i class="ti ti-inbox fs-1"></i>
                                </div>
                                <h5 class="text-muted mb-1">No Pending Requests</h5>
                                <p class="text-muted small mb-0">You don't have any pending leave requests at the moment.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" id="history" role="tabpanel">
                    @forelse($history as $leave)
                        <div class="card mb-3 shadow-sm border">
                            <div class="card-body p-3">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar avatar-md bg-secondary-transparent text-secondary rounded">
                                            <i class="ti ti-calendar-check fs-5"></i>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <p class="text-muted mb-1 small">Request Date</p>
                                        <h6 class="mb-0 fw-semibold">{{ date('d M Y', strtotime($leave->tgl_pengajuan)) }}</h6>
                                    </div>

                                    <div class="col-lg-2 col-md-3">
                                        <p class="text-muted mb-1 small">Type of Leave</p>
                                        <h6 class="mb-1 fw-semibold">{{ $leave->jenisCuti->nm_jenis_ci ?? 'N/A' }}</h6>
                                        <span class="badge badge-soft-secondary badge-sm">
                                            <i class="ti ti-clock-hour-4 me-1"></i>{{ $leave->jumlah_hari }} Days
                                        </span>
                                    </div>

                                    <div class="col-lg-3 col-md-4">
                                        <p class="text-muted mb-1 small">Leave Period</p>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <span class="fw-medium">{{ date('d M', strtotime($leave->tgl_awal)) }}</span>
                                            <i class="ti ti-arrow-narrow-right mx-2 text-muted"></i>
                                            <span class="fw-medium">{{ date('d M Y', strtotime($leave->tgl_akhir)) }}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-2 col-md-4">
                                        <p class="text-muted mb-1 small">Status</p>
                                        @if($leave->sts_pengajuan == 2)
                                            <span class="badge badge-soft-success badge-sm mb-1">
                                                <i class="ti ti-check"></i>{{ HrdConstants::STATUS_CUTI[$leave->sts_pengajuan] ?? 'Approved' }}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-danger badge-sm mb-1">
                                                <i class="ti ti-x"></i>{{ HrdConstants::STATUS_CUTI[$leave->sts_pengajuan] ?? 'Rejected' }}
                                            </span>
                                        @endif
                                        @if($leave->get_current_approve)
                                            <div class="small text-muted">
                                                Current: {{ $leave->get_current_approve->nm_lengkap }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-2 col-md-6">
                                        <p class="text-muted mb-1 small">Remark</p>
                                        <p class="mb-0 text-truncate" style="max-width: 200px;" title="{{ $leave->ket_persetujuan ?? '-' }}">
                                            {{ $leave->ket_persetujuan ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="col-lg-auto col-md-12 ms-lg-auto">
                                        <div class="d-flex gap-2 justify-content-md-end">
                                            <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light btn-view-details" data-id="{{ $leave->id }}" data-bs-toggle="tooltip" title="View Details">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <div class="avatar avatar-xl bg-light-secondary text-secondary rounded-circle mx-auto mb-3">
                                    <i class="ti ti-history-off fs-1"></i>
                                </div>
                                <h5 class="text-muted mb-1">No History Found</h5>
                                <p class="text-muted small mb-0">You don't have any leave history yet.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Leave Detail Modal -->
    <div class="modal fade" id="leaveDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-0 pb-3">
                    <div>
                        <h5 class="modal-title fw-semibold mb-1">Leave Request Details</h5>
                        <p class="text-muted small mb-0">Complete information about leave request</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Leave Information Card -->
                    <div class="card border mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-primary-transparent text-primary rounded me-3 mt-1">
                                            <i class="ti ti-file-text"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Type of Leave</label>
                                            <p class="fw-semibold mb-0" id="detail-jenis-cuti">-</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-success-transparent text-success rounded me-3 mt-1">
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
                                        <div class="avatar avatar-sm bg-info-transparent text-info rounded me-3 mt-1">
                                            <i class="ti ti-calendar-event"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Leave Period</label>
                                            <p class="mb-0">
                                                <span id="detail-tgl-awal" class="fw-medium">-</span>
                                                <i class="ti ti-arrow-narrow-right mx-1"></i>
                                                <span id="detail-tgl-akhir" class="fw-medium">-</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-warning-transparent text-warning rounded me-3 mt-1">
                                            <i class="ti ti-clock-hour-4"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Duration</label>
                                            <p class="mb-0">
                                                <span id="detail-jumlah-hari" class="fw-semibold">-</span> Days
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" id="detail-pengganti-wrapper" style="display: none;">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-purple-transparent text-purple rounded me-3 mt-1">
                                            <i class="ti ti-user-check"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Replacement Employee</label>
                                            <div>
                                                <p class="fw-semibold mb-0" id="detail-pengganti-name">-</p>
                                                <small class="text-muted" id="detail-pengganti-position">-</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-secondary-transparent text-secondary rounded me-3 mt-1">
                                            <i class="ti ti-message-circle"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Reason / Description</label>
                                            <p class="mb-0 text-dark" id="detail-ket-cuti">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval History -->
                    <div class="mb-3">
                        <h6 class="fw-semibold mb-3">
                            <i class="ti ti-timeline me-2 text-primary"></i>Approval History
                        </h6>
                        <div class="card border">
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
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.btn-view-details').on('click', function() {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('leaveDetailModal'));

        // Show loading state or clear previous
        $('#detail-approval-history').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: `{{ url('leave') }}/${id}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#detail-jenis-cuti').text(data.jenis_cuti);
                    $('#detail-tgl-awal').text(data.tgl_awal);
                    $('#detail-tgl-akhir').text(data.tgl_akhir);
                    $('#detail-jumlah-hari').text(data.jumlah_hari);
                    $('#detail-ket-cuti').text(data.ket_cuti);

                    // Show/hide replacement employee info
                    if (data.pengganti) {
                        $('#detail-pengganti-name').text(data.pengganti.name);
                        $('#detail-pengganti-position').text(data.pengganti.position);
                        $('#detail-pengganti-wrapper').show();
                    } else {
                        $('#detail-pengganti-wrapper').hide();
                    }

                    const statusBadge = $('#detail-status');
                    statusBadge.text(data.status);
                    statusBadge.removeClass().addClass('badge badge-sm');
                    if (data.status === 'Disetujui') {
                        statusBadge.addClass('badge-soft-success');
                    } else if (data.status === 'Pengajuan') {
                        statusBadge.addClass('badge-soft-warning');
                    } else {
                        statusBadge.addClass('badge-soft-danger');
                    }

                    let approvalHtml = '';
                    if (data.approvals.length > 0) {
                        data.approvals.forEach(function(appr) {
                            let statusClass = 'badge-soft-secondary';
                            let statusIcon = 'ti-clock';
                            if (appr.status === 'Processed') {
                                statusClass = 'badge-soft-success';
                                statusIcon = 'ti-check';
                            } else if (appr.status === 'Pending') {
                                statusClass = 'badge-soft-warning';
                                statusIcon = 'ti-clock';
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
                                                <small class="text-muted">${appr.position}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge ${statusClass} badge-sm">
                                            <i class="ti ${statusIcon} me-1"></i>${appr.status}
                                        </span>
                                    </td>
                                    <td class="py-3 text-muted">${appr.remark}</td>
                                </tr>
                            `;
                        });
                    } else {
                        approvalHtml = '<tr><td colspan="4" class="text-center py-4 text-muted">No approval process found.</td></tr>';
                    }
                    $('#detail-approval-history').html(approvalHtml);

                    modal.show();
                } else {
                    alert('Failed to fetch lead details.');
                }
            },
            error: function() {
                alert('An error occurred while fetching details.');
            }
        });
    });

    $('.btn-cancel-leave').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('leave/cancel') }}/${id}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>
@endsection
