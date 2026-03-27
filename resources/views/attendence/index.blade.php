@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
                <div>
                    <h4 class="mb-1">Attendance Calendar</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{url('index')}}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Attendance</li>
                        </ol>
                    </nav>
                </div>
            </div>                
            <!-- End Page Header -->

            <div class="row">
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Attendance Style</h5>
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 100%; height: 32px; background-color: #6ce4ad; border-radius: 6px; display: flex; align-items: center; padding: 0 10px;">
                                    <div style="width: 6px; height: 6px; background-color: white; border-radius: 50%; margin-right: 8px;"></div>
                                    <span style="color: #064e3b; font-weight: 600; font-size: 12px;">07.00 - 10.00 IN</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 100%; height: 32px; background-color: #e4e26cff; border-radius: 6px; display: flex; align-items: center; padding: 0 10px;">
                                    <div style="width: 6px; height: 6px; background-color: white; border-radius: 50%; margin-right: 8px;"></div>
                                    <span style="color: #064e3b; font-weight: 600; font-size: 12px;">11.00 - 14.00 Break</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 100%; height: 32px; background-color: #6ce4ad; border-radius: 6px; display: flex; align-items: center; padding: 0 10px;">
                                    <div style="width: 6px; height: 6px; background-color: white; border-radius: 50%; margin-right: 8px;"></div>
                                    <span style="color: #064e3b; font-weight: 600; font-size: 12px;">13.00 - 14.00 IN</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 100%; height: 32px; background-color: #dc2007ff; border-radius: 6px; display: flex; align-items: center; padding: 0 10px;">
                                    <div style="width: 6px; height: 6px; background-color: white; border-radius: 50%; margin-right: 8px;"></div>
                                    <span style="color: #f6f9f8ff; font-weight: 600; font-size: 12px;">>= 17.00 OUT</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 100%; height: 32px; background-color: #3b82f6; border-radius: 6px; display: flex; align-items: center; padding: 0 10px;">
                                    <div style="width: 6px; height: 6px; background-color: white; border-radius: 50%; margin-right: 8px;"></div>
                                    <span style="color: #ffffff; font-weight: 600; font-size: 12px;">Cuti / Leave</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 100%; height: 32px; background-color: #8b5cf6; border-radius: 6px; display: flex; align-items: center; padding: 0 10px;">
                                    <div style="width: 6px; height: 6px; background-color: white; border-radius: 50%; margin-right: 8px;"></div>
                                    <span style="color: #ffffff; font-weight: 600; font-size: 12px;">Izin / Permission</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 100%; height: 32px; background-color: #ef4444; border-radius: 6px; display: flex; align-items: center; padding: 0 10px;">
                                    <div style="width: 6px; height: 6px; background-color: white; border-radius: 50%; margin-right: 8px;"></div>
                                    <span style="color: #ffffff; font-weight: 600; font-size: 12px;">Libur / Holiday</span>
                                </div>
                            </div>
                            <p class="text-muted small mb-0">Events are displayed with high visibility as per company standards.</p>
                            <hr>
                            <p class="text-muted small">Your attendance is tracked automatically based on your employee ID (NIK).</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @component('components.footer')
        @endcomponent
    </div>

    <!-- Attendance Details Modal -->
    <div class="modal fade" id="attendance_details" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header border-0 bg-light-gray py-3">
                    <h5 class="modal-title fw-semibold text-dark"><i class="ti ti-calendar-check me-2 text-primary"></i>Daily Attendance: <span id="modal_date_title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 text-muted small fw-bold">STATUS</th>
                                    <th class="border-0 text-muted small fw-bold">TIME</th>
                                    <th class="pe-4 border-0 text-muted small fw-bold text-end">LOKASI</th>
                                </tr>
                            </thead>
                            <tbody id="attendance_list">
                                <!-- List will be populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light-gray d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<style>
    .fc-v-event, .fc-h-event {
        border: none !important;
        border-radius: 6px !important;
    }
    .fc-event-main {
        padding: 1px 4px !important;
    }
    .fc-daygrid-event {
        margin-top: 1px !important;
        margin-bottom: 1px !important;
    }
    .fc-daygrid-day-events {
        max-height: 90px;
        overflow-y: auto !important;
        scrollbar-width: thin;
    }
    .fc-daygrid-day-events::-webkit-scrollbar {
        width: 4px;
    }
    .fc-daygrid-day-events::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 4px;
    }
    .fc-day-today {
        background: rgba(22, 101, 52, 0.4) !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Attendance Calendar script starting...');
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            console.log('Calendar element found');
            if (typeof FullCalendar !== 'undefined') {
                console.log('FullCalendar library is loaded');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth'
                    },
                    dayMaxEvents: false,
                    events: '{{ route("attendence.data") }}',
                    dateClick: function(info) {
                        showAttendanceDetails(info.dateStr, calendar);
                    },
                    eventClick: function(info) {
                        const dateStr = info.event.startStr.split('T')[0];
                        showAttendanceDetails(dateStr, calendar);
                    },
                    eventContent: function(arg) {
                        let container = document.createElement('div');
                        container.style.display = 'flex';
                        container.style.alignItems = 'center';
                        container.style.width = '100%';
                        container.style.padding = '1px';

                        let dot = document.createElement('div');
                        dot.style.width = '6px';
                        dot.style.height = '6px';
                        dot.style.backgroundColor = 'white';
                        dot.style.borderRadius = '50%';
                        dot.style.marginRight = '6px';
                        dot.style.flexShrink = '0';

                        let title = document.createElement('div');
                        title.innerText = arg.event.title;
                        title.style.fontSize = '11px';
                        title.style.fontWeight = '700';
                        title.style.color = arg.event.textColor || '#064e3b';
                        title.style.overflow = 'hidden';
                        title.style.textOverflow = 'ellipsis';
                        title.style.whiteSpace = 'nowrap';

                        container.appendChild(dot);
                        container.appendChild(title);
                        
                        container.style.backgroundColor = arg.event.backgroundColor;
                        container.style.borderRadius = '4px';

                        return { domNodes: [container] };
                    },
                    loading: function(isLoading) {
                        if (isLoading) {
                            console.log('Calendar events are loading...');
                        } else {
                            console.log('Calendar events loading finished');
                        }
                    }
                });
                calendar.render();
                console.log('Calendar render called');

                function showAttendanceDetails(dateStr, calendarInstance) {
                    const allEvents = calendarInstance.getEvents();
                    const dayEvents = allEvents.filter(e => e.startStr.startsWith(dateStr));
                    
                    dayEvents.sort((a, b) => a.startStr.localeCompare(b.startStr));

                    const dateObj = new Date(dateStr);
                    const formattedDate = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    
                    $('#modal_date_title').text(formattedDate);
                    
                    const listBody = $('#attendance_list');
                    listBody.empty();

                    if (dayEvents.length === 0) {
                        listBody.append('<tr><td colspan="3" class="text-center py-4 text-muted fs-14">No records found for this date.</td></tr>');
                    } else {
                        dayEvents.forEach(e => {
                        const props = e.extendedProps;
                        const label = props.status_label;
                        
                        let badgeClass = 'bg-success'; // IN, IN-After Break
                        if (label.includes('Break')) badgeClass = 'bg-warning';
                        if (label.includes('OUT')) badgeClass = 'bg-danger';
                        if (label.includes('Cuti')) badgeClass = 'bg-primary';
                        if (label.includes('Izin')) badgeClass = 'bg-info';
                        if (label.includes('Libur')) badgeClass = 'bg-danger';

                        listBody.append(`
                            <tr>
                                <td class="ps-4">
                                    <span class="badge ${badgeClass} fs-12 px-2 py-1">${label}</span>
                                </td>
                                <td class="fw-bold fs-15 text-dark">${e.title.includes(' - ') ? e.title.split(' - ')[1] : props.time}</td>
                                <td class="pe-4 text-end">
                                    <span class="badge bg-light text-dark border fs-11">${props.location}</span>
                                </td>
                            </tr>
                        `);
                    });
                    }
                    
                    $('#attendance_details').modal('show');
                }
            } else {
                console.error('FullCalendar library NOT found!');
            }
        } else {
            console.error('Calendar element NOT found!');
        }
    });
</script>
@endsection
