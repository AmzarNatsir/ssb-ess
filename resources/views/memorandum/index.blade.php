@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Memorandum</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Memorandum</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Period Filter -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="{{ route('memorandum.index') }}" method="GET" id="filterForm">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label class="form-label mb-0 fw-semibold">Period Filter:</label>
                            </div>
                            <div class="col-md-3">
                                <select name="month" class="form-select" onchange="this.form.submit()">
                                    @foreach($months as $value => $name)
                                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="year" class="form-select" onchange="this.form.submit()">
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('memorandum.index') }}" class="btn btn-light" title="Reset Filter">
                                    <i class="ti ti-refresh me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-tabs-bottom mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#warning_letter" role="tab" aria-selected="true">
                        <i class="ti ti-file-text me-2"></i>Warning Letter (SP)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#reprimand_letter" role="tab" aria-selected="false">
                        <i class="ti ti-alert-circle me-2"></i>Reprimand Letter (ST)
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Warning Letter (SP) Tab -->
                <div class="tab-pane fade show active" id="warning_letter" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Sequence No.</th>
                                            <th>SP Date</th>
                                            <th>SP Number</th>
                                            <th>SP Type</th>
                                            <th>Violation Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($warningLetters as $index => $sp)
                                            <tr>
                                                <td class="ps-4 fw-medium">{{ $index + 1 }}</td>
                                                <td>{{ date('d M Y', strtotime($sp->tgl_sp)) }}</td>
                                                <td><span class="text-dark fw-semibold">{{ $sp->no_sp }}</span></td>
                                                <td>
                                                    <span class="badge badge-soft-danger badge-sm">
                                                        {{ $sp->jenisSpDisetujui->nm_jenis_sp ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="text-wrap" style="max-width: 300px;">{{ $sp->uraian_pelanggaran }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="ti ti-inbox fs-1 mb-2 d-block"></i>
                                                        <p class="mb-0">No Warning Letters found.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reprimand Letter (ST) Tab -->
                <div class="tab-pane fade" id="reprimand_letter" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Sequence No.</th>
                                            <th>Incident Date</th>
                                            <th>Incident Time</th>
                                            <th>Violation Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reprimandLetters as $index => $st)
                                            <tr>
                                                <td class="ps-4 fw-medium">{{ $index + 1 }}</td>
                                                <td>{{ date('d M Y', strtotime($st->tanggal_kejadian)) }}</td>
                                                <td><span class="text-dark">{{ date('H:i', strtotime($st->waktu_kejadian)) }}</span></td>
                                                <td>
                                                    <span class="badge badge-soft-warning badge-sm">
                                                        {{ $st->jenisPelanggaran->nm_jenis_pelanggaran ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="ti ti-inbox fs-1 mb-2 d-block"></i>
                                                        <p class="mb-0">No Reprimand Letters found.</p>
                                                    </div>
                                                </td>
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
@endsection
