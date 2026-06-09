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
                    <h4 class="mb-1">My Profile</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>                
            <!-- End Page Header -->

            <div class="row">
                <div class="col-xl-4 col-lg-5">
                    <!-- Profile Card -->
                    <div class="card overflow-hidden">
                        <div class="card-body p-0">
                            <div class="bg-primary pt-5 px-3 pb-3 text-center position-relative">
                                <div class="avatar avatar-xxl avatar-rounded border border-4 border-white mx-auto mb-2 shadow-sm bg-white overflow-hidden d-flex align-items-center justify-content-center">
                                    @if($karyawan->photo)
                                        <img src="{{ $karyawan->photo_url }}" alt="user" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    @endif
                                    <span class="avatar-title bg-primary text-white fs-24 {{ $karyawan->photo ? '' : 'd-flex' }}" style="{{ $karyawan->photo ? 'display:none;' : '' }}">
                                        {{ $karyawan->initials }}
                                    </span>
                                </div>
                                <h5 class="text-white mb-1 text-capitalize">{{ $karyawan->nm_lengkap ?? $user->nik }}</h5>
                                <p class="text-white-50 mb-0 fs-13">{{ $karyawan->jabatan->nm_jabatan ?? 'Employee' }}</p>
                            </div>
                            <div class="p-3">
                                <ul class="list-group list-group-flush border-0">
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-0">
                                        <span class="text-muted fs-13">NIK</span>
                                        <span class="fw-medium text-dark">{{ $user->nik }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-0">
                                        <span class="text-muted fs-13">Department</span>
                                        <span class="fw-medium text-dark">{{ $karyawan->departemen->nm_dept ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-0">
                                        <span class="text-muted fs-13">Join Date</span>
                                        <span class="fw-medium text-dark">{{ $karyawan->tgl_masuk ? date('d M Y', strtotime($karyawan->tgl_masuk)) : '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Contact Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md bg-light-info text-info rounded flex-shrink-0">
                                    <i class="ti ti-mail fs-20"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted mb-0 fs-12">Email Address</p>
                                    <h6 class="mb-0 fs-14">{{ $karyawan->nmemail ?? '-' }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md bg-light-success text-success rounded flex-shrink-0">
                                    <i class="ti ti-phone fs-20"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted mb-0 fs-12">Phone Number</p>
                                    <h6 class="mb-0 fs-14">{{ $karyawan->notelp ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <!-- Personal Details -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="card-title mb-0">Personal Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-gap-3">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Full Name</p>
                                    <h6 class="text-dark text-capitalize">{{ $karyawan->nm_lengkap ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Gender</p>
                                    <h6 class="text-dark">{{ $karyawan->jenkel == 1 ? 'Male' : ($karyawan->jenkel == 2 ? 'Female' : '-') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Place of Birth</p>
                                    <h6 class="text-dark">{{ $karyawan->tmp_lahir ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Date of Birth</p>
                                    <h6 class="text-dark">{{ $karyawan->tgl_lahir ? date('d F Y', strtotime($karyawan->tgl_lahir)) : '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Identity Number (KTP)</p>
                                    <h6 class="text-dark">{{ $karyawan->no_ktp ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Religion</p>
                                    <h6 class="text-dark">{{ HrdConstants::AGAMA[$karyawan->agama] ?? '-' }}</h6>
                                </div>
                                <div class="col-md-12">
                                    <p class="text-muted mb-1 fs-12">Current Address</p>
                                    <h6 class="text-dark">{{ $karyawan->alamat ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Details -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Employment Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-gap-3">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Department</p>
                                    <h6 class="text-dark">{{ $karyawan->departemen->nm_dept ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Sub Department</p>
                                    <h6 class="text-dark">{{ $karyawan->subDepartemen->nm_subdept ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Division</p>
                                    <h6 class="text-dark">{{ $karyawan->divisi->nm_divisi ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Length of Service</p>
                                    <h6 class="text-dark">{{ $karyawan->lama_bekerja }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Employee Status</p>
                                    <span class="badge bg-light-success text-success">{{ HrdConstants::STATUS_KARYAWAN[$karyawan->id_status_karyawan] ?? 'Active' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Last Education</p>
                                    <h6 class="text-dark">{{ HrdConstants::JENJANG_PENDIDIKAN[$karyawan->pendidikan_akhir] ?? '-' }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1 fs-12">Marital Status</p>
                                    <h6 class="text-dark">{{ HrdConstants::STATUS_PERNIKAHAN[$karyawan->status_nikah] ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subordinates -->
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="card-title mb-0">Daftar Bawahan</h6>
                            <span class="badge bg-light-primary text-primary">{{ $bawahan->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($bawahan->isEmpty())
                                <div class="text-center text-muted py-4">
                                    <i class="ti ti-users-off fs-24 d-block mb-2"></i>
                                    <span class="fs-13">Tidak ada bawahan langsung.</span>
                                </div>
                            @else
                                @foreach($bawahanPerDept as $namaDept => $anggota)
                                    <div class="d-flex align-items-center justify-content-between mb-2 {{ !$loop->first ? 'mt-3' : '' }}">
                                        <h6 class="mb-0 fs-13 text-primary d-flex align-items-center">
                                            <i class="ti ti-building me-1"></i> {{ $namaDept }}
                                        </h6>
                                        <span class="badge bg-light-primary text-primary">{{ $anggota->count() }}</span>
                                    </div>
                                    <div class="bg-light rounded p-2">
                                        <div class="table-responsive">
                                            <table class="table table-borderless align-middle mb-0 bawahan-table" data-page-size="10">
                                                <thead>
                                                    <tr>
                                                        <th class="text-muted fs-12">#</th>
                                                        <th class="text-muted fs-12">Nama</th>
                                                        <th class="text-muted fs-12">Jabatan</th>
                                                        <th class="text-muted fs-12">Level</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($anggota as $index => $b)
                                                        <tr>
                                                            <td class="text-muted">{{ $index + 1 }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar avatar-sm avatar-rounded bg-light overflow-hidden d-flex align-items-center justify-content-center flex-shrink-0">
                                                                        @if($b->photo)
                                                                            <img src="{{ $b->photo_url }}" alt="{{ $b->nm_lengkap }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                        @endif
                                                                        <span class="avatar-title bg-primary text-white fs-12 {{ $b->photo ? '' : 'd-flex' }}" style="{{ $b->photo ? 'display:none;' : '' }}">
                                                                            {{ $b->initials }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="ms-2">
                                                                        <h6 class="mb-0 fs-14 text-capitalize">{{ $b->nm_lengkap }}</h6>
                                                                        <span class="text-muted fs-12">{{ $b->nik }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="fs-13 text-dark">{{ $b->jabatan->nm_jabatan ?? '-' }}</td>
                                                            <td>
                                                                <span class="badge bg-light-secondary text-secondary">Level {{ $b->level_bawahan }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <nav class="bawahan-pagination d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2 px-1" aria-label="Pagination {{ $namaDept }}"></nav>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.bawahan-table').forEach(function (table) {
            var pageSize = parseInt(table.dataset.pageSize, 10) || 10;
            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody > tr'));
            var nav = table.closest('.bg-light').querySelector('.bawahan-pagination');
            if (!nav) return;

            var totalPages = Math.ceil(rows.length / pageSize);

            // Tidak perlu pagination bila muat dalam satu halaman.
            if (totalPages <= 1) {
                nav.remove();
                return;
            }

            var currentPage = 1;

            function renderRows() {
                var start = (currentPage - 1) * pageSize;
                var end = start + pageSize;
                rows.forEach(function (row, i) {
                    row.style.display = (i >= start && i < end) ? '' : 'none';
                });
            }

            function makeBtn(label, page, opts) {
                opts = opts || {};
                var li = document.createElement('li');
                li.className = 'page-item' + (opts.active ? ' active' : '') + (opts.disabled ? ' disabled' : '');
                var a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                a.innerHTML = label;
                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (opts.disabled || opts.active) return;
                    currentPage = page;
                    render();
                });
                li.appendChild(a);
                return li;
            }

            function render() {
                renderRows();

                var start = (currentPage - 1) * pageSize + 1;
                var end = Math.min(currentPage * pageSize, rows.length);

                nav.innerHTML = '';

                var info = document.createElement('span');
                info.className = 'text-muted fs-12';
                info.textContent = 'Menampilkan ' + start + '-' + end + ' dari ' + rows.length;
                nav.appendChild(info);

                var ul = document.createElement('ul');
                ul.className = 'pagination pagination-sm mb-0';

                ul.appendChild(makeBtn('&laquo;', currentPage - 1, { disabled: currentPage === 1 }));
                for (var p = 1; p <= totalPages; p++) {
                    ul.appendChild(makeBtn(String(p), p, { active: p === currentPage }));
                }
                ul.appendChild(makeBtn('&raquo;', currentPage + 1, { disabled: currentPage === totalPages }));

                nav.appendChild(ul);
            }

            render();
        });
    });
</script>
@endsection
