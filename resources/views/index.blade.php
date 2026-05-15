<?php $page = 'index'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content pb-0">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-0">Welcome</h4>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="form-control w-auto d-flex align-items-center">
                        <i class="ti ti-calendar text-dark me-2"></i>
                        <span class="text-dark">{{ date('d F Y') }}</span>
                    </div>
                    <a href="{{ route('home') }}" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i class="ti ti-refresh"></i></a>
                    <a href="javascript:void(0);" class="btn btn-icon btn-outline-light shadow" data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i class="ti ti-transition-top"></i></a>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- ===================== Row 1: Memo Internal + Birthday Carousel ===================== -->
            <div class="row g-3 mb-3">

                <!-- Card: Memo Internal -->
                <div class="col-xl-8 col-lg-8">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom bg-primary-subtle">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-sm bg-primary text-white rounded">
                                    <i class="ti ti-news fs-5"></i>
                                </span>
                                <h5 class="mb-0 fw-semibold">Memo Internal</h5>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $memos->count() }}</span>
                        </div>
                        <div class="card-body p-0" style="max-height: 340px; overflow-y: auto;">
                            @forelse($memos as $memo)
                                <div class="p-3 border-bottom d-flex gap-3 align-items-start memo-item"
                                     role="button"
                                     data-bs-toggle="modal"
                                     data-bs-target="#memoModal"
                                     data-judul="{{ $memo->judul }}"
                                     data-tgl="{{ $memo->tgl_post ? $memo->tgl_post->format('d F Y') : '-' }}"
                                     data-deskripsi="{{ $memo->deskripsi }}"
                                     data-file="{{ $memo->file_url ?? '' }}"
                                     style="cursor:pointer;">
                                    <div class="avatar avatar-sm bg-primary-subtle text-primary rounded flex-shrink-0">
                                        <i class="ti ti-file-text"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="mb-0 fw-semibold text-truncate">{{ $memo->judul }}</p>
                                        <small class="text-muted">
                                            <i class="ti ti-calendar-event me-1"></i>
                                            {{ $memo->tgl_post ? $memo->tgl_post->format('d F Y') : '-' }}
                                        </small>
                                    </div>
                                    <i class="ti ti-chevron-right text-muted flex-shrink-0 mt-1"></i>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="ti ti-inbox fs-1 d-block mb-2"></i>
                                    <span>Tidak ada memo internal</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom bg-info-subtle">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-sm bg-info text-white rounded">
                                    <i class="ti ti-calendar-event fs-5"></i>
                                </span>
                                <h5 class="mb-0 fw-semibold">Kalender &amp; Hari Libur</h5>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <button class="btn btn-sm btn-outline-secondary py-1 px-2" id="cal-prev"><i class="ti ti-chevron-left"></i></button>
                                <span id="cal-title" class="fw-semibold small px-1"></span>
                                <button class="btn btn-sm btn-outline-secondary py-1 px-2" id="cal-next"><i class="ti ti-chevron-right"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <!-- Calendar Grid -->
                            <div id="mini-calendar" class="mb-3"></div>
                            <!-- Holiday Legend -->
                            <div id="holiday-list" class="mt-2" style="max-height:110px; overflow-y:auto;"></div>
                        </div>
                    </div>
                </div>

                <!-- Card: Karyawan Ulang Tahun Bulan Ini -->
                {{-- <div class="col-xl-4 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom bg-warning-subtle">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-sm bg-warning text-white rounded">
                                    <i class="ti ti-cake fs-5"></i>
                                </span>
                                <h5 class="mb-0 fw-semibold">Ulang Tahun Bulan {{ date('F') }}</h5>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill">{{ $birthdayKaryawan->count() }}</span>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center p-3">
                            @if($birthdayKaryawan->isEmpty())
                                <div class="text-center text-muted py-4">
                                    <i class="ti ti-confetti-off fs-1 d-block mb-2"></i>
                                    <span>Tidak ada karyawan berulang tahun bulan ini</span>
                                </div>
                            @else
                                <div id="birthdayCarousel" class="carousel slide w-100" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($birthdayKaryawan as $index => $karyawan)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <div class="text-center px-4 py-2">
                                                    <div class="avatar avatar-xxl avatar-rounded border border-3 border-warning shadow mb-3 mx-auto overflow-hidden d-flex align-items-center justify-content-center" style="width:90px; height:90px;">
                                                        @if($karyawan->photo)
                                                            <img src="{{ $karyawan->photo_url }}" alt="{{ $karyawan->nm_lengkap }}" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        @endif
                                                        <span class="avatar-title bg-warning text-dark fs-20 {{ $karyawan->photo ? '' : 'd-flex' }}" style="{{ $karyawan->photo ? 'display:none;' : '' }}">
                                                            {{ $karyawan->initials }}
                                                        </span>
                                                    </div>
                                                    <h6 class="mb-1 fw-bold">{{ $karyawan->nm_lengkap }}</h6>
                                                    <p class="text-muted small mb-1">
                                                        <i class="ti ti-id-badge me-1"></i>{{ $karyawan->nik ?? '-' }}
                                                    </p>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="ti ti-gift me-1"></i>
                                                        {{ $karyawan->tgl_lahir ? \Carbon\Carbon::parse($karyawan->tgl_lahir)->format('d F') : '-' }}
                                                    </span>
                                                    @if($karyawan->jabatan)
                                                        <p class="text-muted small mt-2 mb-0">{{ $karyawan->jabatan->nm_jabatan ?? '' }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($birthdayKaryawan->count() > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#birthdayCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#birthdayCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                        <div class="carousel-indicators position-relative mt-2">
                                            @foreach($birthdayKaryawan as $index => $karyawan)
                                                <button type="button"
                                                        data-bs-target="#birthdayCarousel"
                                                        data-bs-slide-to="{{ $index }}"
                                                        class="{{ $index === 0 ? 'active' : '' }}"
                                                        aria-current="{{ $index === 0 ? 'true' : 'false' }}">
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div> --}}

            </div>
            <!-- End Row 1 -->

            <!-- ===================== Row 2: Pelatihan + Kalender ===================== -->
            <div class="row g-3 mb-3">

                <!-- Card: Pelatihan -->
                <div class="col-xl-8 col-lg-8">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom bg-success-subtle">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar avatar-sm bg-success text-white rounded">
                                    <i class="ti ti-school fs-5"></i>
                                </span>
                                <h5 class="mb-0 fw-semibold">Pelatihan {{ $currentYear }}</h5>
                            </div>
                            @php $totalTraining = collect($trainingsByMonth)->sum(fn($m) => count($m['items'])); @endphp
                            <span class="badge bg-success rounded-pill">{{ $totalTraining }}</span>
                        </div>
                        <div class="card-body p-3" style="max-height: 340px; overflow-y: auto;">
                            @if(empty($trainingsByMonth))
                                <div class="text-center py-5 text-muted">
                                    <i class="ti ti-calendar-off fs-1 d-block mb-2"></i>
                                    <span>Tidak ada pelatihan tahun ini</span>
                                </div>
                            @else
                                <div class="accordion accordion-flush" id="trainingAccordion">
                                    @foreach($trainingsByMonth as $monthNum => $monthData)
                                        <div class="accordion-item border rounded mb-2">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed py-2 fw-semibold"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#month-{{ $monthNum }}"
                                                        aria-expanded="false">
                                                    <i class="ti ti-calendar-month me-2 text-success"></i>
                                                    {{ $monthData['name'] }} {{ $currentYear }}
                                                    <span class="badge bg-success-subtle text-success ms-2">{{ count($monthData['items']) }}</span>
                                                </button>
                                            </h2>
                                            <div id="month-{{ $monthNum }}" class="accordion-collapse collapse">
                                                <div class="accordion-body p-0">
                                                    @foreach($monthData['items'] as $training)
                                                        <div class="p-3 border-top d-flex gap-3 align-items-start">
                                                            <div class="avatar avatar-sm bg-success-subtle text-success rounded flex-shrink-0">
                                                                <i class="ti ti-certificate"></i>
                                                            </div>
                                                            <div class="flex-grow-1 overflow-hidden">
                                                                <p class="mb-1 fw-semibold text-truncate small">
                                                                    {{ $training->nama_pelatihan ?: ($training->get_nama_pelatihan->nama_pelatihan ?? '-') }}
                                                                </p>
                                                                <small class="text-muted">
                                                                    <i class="ti ti-calendar me-1"></i>
                                                                    {{ $training->tanggal_awal ? date('d M', strtotime($training->tanggal_awal)) : '-' }}
                                                                    @if($training->tanggal_sampai && $training->tanggal_sampai !== $training->tanggal_awal)
                                                                        &ndash; {{ date('d M Y', strtotime($training->tanggal_sampai)) }}
                                                                    @else
                                                                        {{ $training->tanggal_awal ? date(' Y', strtotime($training->tanggal_awal)) : '' }}
                                                                    @endif
                                                                </small>
                                                                @if($training->tempat_pelaksanaan)
                                                                    <br><small class="text-muted"><i class="ti ti-map-pin me-1"></i>{{ $training->tempat_pelaksanaan }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card: Kalender + Hari Libur -->


            </div>
            <!-- End Row 2 -->

        </div>
        <!-- End Content -->

        @component('components.footer')
        @endcomponent
    </div>

    <!-- ========================
        End Page Content
    ========================= -->

    <!-- Modal: Detail Memo Internal -->
    <div class="modal fade" id="memoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="ti ti-news me-2"></i><span id="memoModalJudul"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3"><i class="ti ti-calendar-event me-1"></i><span id="memoModalTgl"></span></p>
                    <div id="memoModalDeskripsi" class="mb-3" style="white-space: pre-line;"></div>
                    <div id="memoModalFile"></div>
                </div>
            </div>
        </div>
    </div>

@section('scripts')
<style>
    /* Calendar */
    #mini-calendar table { width: 100%; border-collapse: collapse; }
    #mini-calendar th { text-align: center; font-size: 0.75rem; color: #6c757d; padding: 4px 2px; font-weight: 600; }
    #mini-calendar td { text-align: center; padding: 4px 2px; font-size: 0.82rem; border-radius: 6px; cursor: default; }
    #mini-calendar td.today { background: #0d6efd; color: #fff; font-weight: bold; border-radius: 50%; }
    #mini-calendar td.holiday { background: #dc3545; color: #fff; border-radius: 50%; cursor: pointer; }
    #mini-calendar td.holiday.today { background: linear-gradient(135deg,#dc3545,#0d6efd); }
    #mini-calendar td.sunday { color: #dc3545; }
    #mini-calendar td.other-month { color: #ccc; }
    .holiday-badge { display: inline-flex; align-items: center; gap: 5px; background: #fff3f3; border: 1px solid #f5c6cb; border-radius: 20px; padding: 2px 10px; margin: 2px; font-size: 0.78rem; color: #dc3545; }
    /* Birthday carousel controls */
    #birthdayCarousel .carousel-control-prev,
    #birthdayCarousel .carousel-control-next { width: 30px; height: 30px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,.15); border-radius: 50%; }
    #birthdayCarousel .carousel-indicators button { background-color: #ffc107; width: 8px; height: 8px; border-radius: 50%; border: none; }
</style>
<script>
(function () {
    // ==================== Memo Modal ====================
    document.querySelectorAll('.memo-item').forEach(function (el) {
        el.addEventListener('click', function () {
            document.getElementById('memoModalJudul').textContent = this.dataset.judul;
            document.getElementById('memoModalTgl').textContent    = this.dataset.tgl;
            document.getElementById('memoModalDeskripsi').textContent = this.dataset.deskripsi;
            var fileEl = document.getElementById('memoModalFile');
            if (this.dataset.file) {
                fileEl.innerHTML = '<a href="' + this.dataset.file + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-paperclip me-1"></i>Lihat File Memo</a>';
            } else {
                fileEl.innerHTML = '';
            }
        });
    });

    // ==================== Mini Calendar ====================
    var holidays = @json($holidays->mapWithKeys(fn($h, $k) => [$k => $h->keterangan]));
    var today    = new Date();
    var viewYear, viewMonth;

    function renderCalendar(year, month) {
        viewYear  = year;
        viewMonth = month;
        var monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('cal-title').textContent = monthNames[month] + ' ' + year;

        var firstDay = new Date(year, month, 1).getDay(); // 0=Sun
        var daysInMonth = new Date(year, month + 1, 0).getDate();

        var html = '<table><thead><tr>';
        var dayNames = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
        dayNames.forEach(function(d){ html += '<th>' + d + '</th>'; });
        html += '</tr></thead><tbody><tr>';

        // Pad start
        for (var i = 0; i < firstDay; i++) {
            var prevDate = new Date(year, month, -firstDay + i + 1);
            html += '<td class="other-month">' + prevDate.getDate() + '</td>';
        }

        var holidayDates = {};
        for (var day = 1; day <= daysInMonth; day++) {
            var d = new Date(year, month, day);
            var dow = d.getDay();
            var dateStr = year + '-' + String(month + 1).padStart(2,'0') + '-' + String(day).padStart(2,'0');
            var isToday   = (year === today.getFullYear() && month === today.getMonth() && day === today.getDate());
            var isHoliday = holidays.hasOwnProperty(dateStr);
            var isSunday  = (dow === 0);

            var cls = [];
            if (isToday)   cls.push('today');
            if (isHoliday) { cls.push('holiday'); holidayDates[dateStr] = holidays[dateStr]; }
            else if (isSunday) cls.push('sunday');

            var title = isHoliday ? ' title="' + holidays[dateStr] + '"' : '';
            if ((firstDay + day - 1) % 7 === 0 && day !== 1) html += '</tr><tr>';
            html += '<td class="' + cls.join(' ') + '"' + title + '>' + day + '</td>';
        }

        // Pad end
        var remaining = 7 - ((firstDay + daysInMonth) % 7);
        if (remaining < 7) {
            for (var j = 1; j <= remaining; j++) {
                html += '<td class="other-month">' + j + '</td>';
            }
        }
        html += '</tr></tbody></table>';
        document.getElementById('mini-calendar').innerHTML = html;

        // Holiday list below calendar
        var listHtml = '';
        var keys = Object.keys(holidayDates);
        if (keys.length === 0) {
            listHtml = '<p class="text-muted small mb-0 text-center">Tidak ada hari libur bulan ini</p>';
        } else {
            keys.sort();
            keys.forEach(function(k) {
                var parts = k.split('-');
                var label = parseInt(parts[2]) + ' ' + monthNames[parseInt(parts[1]) - 1] + ' ' + parts[0];
                listHtml += '<span class="holiday-badge"><i class="ti ti-circle-filled" style="font-size:8px;"></i>' + label + ' &mdash; ' + holidayDates[k] + '</span>';
            });
        }
        document.getElementById('holiday-list').innerHTML = listHtml;
    }

    document.getElementById('cal-prev').addEventListener('click', function () {
        if (viewMonth === 0) { renderCalendar(viewYear - 1, 11); }
        else { renderCalendar(viewYear, viewMonth - 1); }
    });
    document.getElementById('cal-next').addEventListener('click', function () {
        if (viewMonth === 11) { renderCalendar(viewYear + 1, 0); }
        else { renderCalendar(viewYear, viewMonth + 1); }
    });

    renderCalendar(today.getFullYear(), today.getMonth());
})();
</script>
@endsection

@endsection
