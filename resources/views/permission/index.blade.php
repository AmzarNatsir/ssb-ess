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
                    <h4 class="mb-1">Permission Management</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Permission</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('permission.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="ti ti-circle-plus me-1"></i>Apply Permission
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="card">
                <div class="card-header pb-0">
                    <ul class="nav nav-tabs nav-bordered mb-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#pending" role="tab" aria-selected="true">
                                <i class="ti ti-clock-share me-1"></i>Pengajuan (Request)
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#history" role="tab" aria-selected="false">
                                <i class="ti ti-history me-1"></i>History Izin
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Pending Requests Tab -->
                        <div class="tab-pane fade show active" id="pending" role="tabpanel">
                            <div class="table-responsive custom-table">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date of Request</th>
                                            <th>Type of Permission</th>
                                            <th>Duration</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingRequests as $permission)
                                            <tr>
                                                <td>{{ date('d M Y', strtotime($permission->tgl_pengajuan)) }}</td>
                                                <td>{{ $permission->jenisPermission->nm_jenis_ci ?? 'N/A' }}</td>
                                                <td>{{ $permission->jumlah_hari }} Days</td>
                                                <td>{{ date('d M Y', strtotime($permission->tgl_awal)) }} - {{ date('d M Y', strtotime($permission->tgl_akhir)) }}</td>
                                                <td>
                                                    <span class="badge bg-light-warning text-warning">
                                                        <i class="ti ti-point-filled me-1"></i>{{ HrdConstants::STATUS_IZIN[$permission->sts_pengajuan] ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-light" data-bs-toggle="dropdown">
                                                            <i class="ti ti-dots-vertical"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li><a class="dropdown-item btn-view-details" href="javascript:void(0);" data-id="{{ $permission->id }}"><i class="ti ti-eye me-2"></i>View Details</a></li>
                                                            @if($permission->sts_pengajuan == 1 && $permission->is_draft == 1)
                                                                <li><a class="dropdown-item" href="{{ route('permission.edit', $permission->id) }}"><i class="ti ti-edit me-2"></i>Edit Request</a></li>
                                                                <li><a class="dropdown-item text-danger btn-cancel-permission" href="javascript:void(0);" data-id="{{ $permission->id }}"><i class="ti ti-trash me-2"></i>Cancel Request</a></li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">No pending permission requests found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- History Tab -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <div class="table-responsive custom-table">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Date of Request</th>
                                            <th>Type of Permission</th>
                                            <th>Duration</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($history as $permission)
                                            <tr>
                                                <td>{{ date('d M Y', strtotime($permission->tgl_pengajuan)) }}</td>
                                                <td>{{ $permission->jenisPermission->nm_jenis_ci ?? 'N/A' }}</td>
                                                <td>{{ $permission->jumlah_hari }} Days</td>
                                                <td>{{ date('d M Y', strtotime($permission->tgl_awal)) }} - {{ date('d M Y', strtotime($permission->tgl_akhir)) }}</td>
                                                <td>
                                                    @if($permission->sts_pengajuan == 2)
                                                        <span class="badge bg-light-success text-success">
                                                            <i class="ti ti-check me-1"></i>{{ HrdConstants::STATUS_IZIN[$permission->sts_pengajuan] ?? 'Approved' }}
                                                        </span>
                                                    @elseif($permission->sts_pengajuan == 3)
                                                        <span class="badge bg-light-danger text-danger">
                                                            <i class="ti ti-x me-1"></i>{{ HrdConstants::STATUS_IZIN[$permission->sts_pengajuan] ?? 'Rejected' }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light-secondary text-secondary">
                                                            <i class="ti ti-ban me-1"></i>{{ HrdConstants::STATUS_IZIN[$permission->sts_pengajuan] ?? 'Cancelled' }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-truncate" style="max-width: 200px;">{{ $permission->ket_persetujuan ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">No permission history found.</td>
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

    <!-- Permission Detail Modal -->
    <div class="modal fade" id="permissionDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-0 pb-3">
                    <div>
                        <h5 class="modal-title fw-semibold mb-1">Permission Request Details</h5>
                        <p class="text-muted small mb-0">Complete information about permission request</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Permission Information Card -->
                    <div class="card border mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-primary-transparent text-primary rounded me-3 mt-1">
                                            <i class="ti ti-file-text"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Type of Permission</label>
                                            <p class="fw-semibold mb-0" id="detail-jenis-izin">-</p>
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
                                            <label class="text-muted small mb-1">Permission Period</label>
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
                                <div class="col-12">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-secondary-transparent text-secondary rounded me-3 mt-1">
                                            <i class="ti ti-message-circle"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <label class="text-muted small mb-1">Reason / Description</label>
                                            <p class="mb-0 text-dark" id="detail-ket-izin">-</p>
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
        const modal = new bootstrap.Modal(document.getElementById('permissionDetailModal'));

        // Show loading state
        $('#detail-approval-history').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: `{{ url('permission') }}/${id}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#detail-jenis-izin').text(data.jenis_izin);
                    $('#detail-tgl-awal').text(data.tgl_awal);
                    $('#detail-tgl-akhir').text(data.tgl_akhir);
                    $('#detail-jumlah-hari').text(data.jumlah_hari);
                    $('#detail-ket-izin').text(data.ket_izin);

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
                    alert('Failed to fetch permission details.');
                }
            },
            error: function() {
                alert('An error occurred while fetching details.');
            }
        });
    });

    $('.btn-cancel-permission').on('click', function() {
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
                form.action = `{{ url('permission/cancel') }}/${id}`;

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
