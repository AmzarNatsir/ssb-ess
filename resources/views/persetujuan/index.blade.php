@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-4 flex-wrap">
            <div>
                <h4 class="mb-1">Approval</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ url('index') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Approval</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-lg-12">
                @if(\Session::has('konfirm'))
                    <div class="alert alert-success" role="alert" id="success-alert">
                        {{ \Session::get('konfirm') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">Daftar Pengajuan</h5>
                    </div>
                    <div class="card-body" style="width:100%; height:auto">
                        <div class="row justify-content-center table-responsive">
                            <table class="table datatable table-hover" style="font-size: 13px;">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th class="text-center" style="width: 5%;">Level</th>
                                        <th style="width: 20%;">Kategori</th>
                                        <th style="width: 10%;">Pengajuan</th>
                                        <th style="width: 15%;">Departemen</th>
                                        <th>Keterangan</th>
                                        <th style="width: 15%;">Diajukan Oleh</th>
                                        <th style="width: 5%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($no=1)
                                    @foreach ($list_persetujuan as $list)
                                    @php($detail = $list['detail'] ?? [])
                                    <tr>
                                        <td>{{ $no }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-success text-white px-3 py-2">{{ $list['approval_level'] }}</span>
                                        </td>
                                        <td>{{ $list['get_ref_approval']['ref_group'] }}</td>
                                        <td>{{ empty($detail['tgl_pengajuan']) ? '-' : date('d-m-Y', strtotime($detail['tgl_pengajuan'])) }}</td>
                                        <td>{{ $detail['departemen'] ?? '-' }}</td>
                                        <td>{{ $detail['catatan_pengajuan'] ?? '-' }}</td>
                                        <td>{{ $detail['diajukan_oleh'] ?? '-' }}</td>
                                        <td>
                                            @if(empty($detail['status_app']))
                                            <button type="button" class="btn btn-primary btn-sm" value="{{ $list['id'] }}" data-bs-toggle="modal" data-bs-target="#modalFormPersetujuan" onclick="window.goForm(this)" title="Review Approval">
                                                <i class="ti ti-pencil"></i>
                                            </button>
                                            @elseif($detail['status_app']=="pending")
                                            <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                            @else
                                            <span class="badge rounded-pill bg-secondary">Closed</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php($no++)
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @component('components.footer')
    @endcomponent
</div>
<div id="modalFormPersetujuan" class="modal fade bg-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenteredScrollableTitle" aria-hidden="true" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" id="v_inputan" style="overflow-y: auto;"></div>
    </div>
 </div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        function hasSwalFire() {
            return typeof window.Swal !== 'undefined' && typeof window.Swal.fire === 'function';
        }

        function toTitleCase(value) {
            if (!value) {
                return '';
            }

            return value
                .replace(/[_-]+/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()
                .replace(/\b\w/g, function (char) { return char.toUpperCase(); });
        }

        function getFieldLabel(form, field) {
            if (!field) {
                return 'Data Wajib';
            }

            // Prefer explicit label[for=id]
            if (field.id) {
                var forLabel = form.querySelector('label[for="' + field.id + '"]');
                if (forLabel) {
                    return forLabel.textContent.replace('*', '').trim();
                }
            }

            // Fallback to nearest row label for Bootstrap-like forms
            var row = field.closest('.row');
            if (row) {
                var rowLabel = row.querySelector('label');
                if (rowLabel) {
                    return rowLabel.textContent.replace('*', '').trim();
                }
            }

            // Fallback to previous label inside the same group/card
            var group = field.closest('.form-group, .card-body, .col-sm-8, .col-md-8, .col-lg-8');
            if (group) {
                var prev = field.previousElementSibling;
                while (prev) {
                    if (prev.tagName && prev.tagName.toLowerCase() === 'label') {
                        return prev.textContent.replace('*', '').trim();
                    }
                    prev = prev.previousElementSibling;
                }
            }

            if (field.name) {
                return toTitleCase(field.name);
            }

            return 'Data Wajib';
        }

        function focusField(field) {
            if (!field) {
                return;
            }

            if (window.jQuery && window.jQuery(field).hasClass('select2-hidden-accessible')) {
                window.jQuery(field).select2('open');
                return;
            }

            field.focus();
        }

        function showWarning(text, onClose) {
            if (hasSwalFire()) {
                window.Swal.fire({
                    icon: 'warning',
                    title: 'Form Belum Lengkap',
                    text: text,
                    confirmButtonText: 'OK'
                }).then(function () {
                    if (typeof onClose === 'function') {
                        onClose();
                    }
                });
                return;
            }

            alert(text);
            if (typeof onClose === 'function') {
                onClose();
            }
        }

        function validateApprovalForm(form) {
            var pilPengganti = form.querySelector('#pil_pengganti');
            if (pilPengganti && !pilPengganti.value) {
                showWarning('Silakan lengkapi: ' + getFieldLabel(form, pilPengganti), function () {
                    focusField(pilPengganti);
                });
                return false;
            }

            var ket = form.querySelector('#inp_keterangan');
            if (ket && !ket.value.trim()) {
                showWarning('Silakan lengkapi: ' + getFieldLabel(form, ket), function () {
                    focusField(ket);
                });
                return false;
            }

            if (!form.checkValidity()) {
                var invalidField = null;
                var elements = form.elements || [];
                for (var i = 0; i < elements.length; i++) {
                    if (typeof elements[i].checkValidity === 'function' && !elements[i].checkValidity()) {
                        invalidField = elements[i];
                        break;
                    }
                }

                var fieldName = getFieldLabel(form, invalidField);

                showWarning('Silakan lengkapi: ' + fieldName, function () {
                    if (!invalidField) {
                        return;
                    }
                    focusField(invalidField);
                });

                return false;
            }

            return true;
        }

        function confirmAndSubmit(form) {
            if (!hasSwalFire()) {
                var ok = window.confirm('Yakin data akan disimpan?');
                if (ok) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
                return;
            }

            window.Swal.fire({
                title: 'Yakin data akan disimpan?',
                text: 'Submit pengajuan persetujuan ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        }

        var alertEl = document.getElementById('success-alert');
        if (alertEl) {
            window.setTimeout(function () {
                if (window.bootstrap && window.bootstrap.Alert) {
                    window.bootstrap.Alert.getOrCreateInstance(alertEl).close();
                } else {
                    alertEl.remove();
                }
            }, 2000);
        }

        // Centralized handler for approval forms loaded dynamically into modal.
        // This guarantees submit works even when inline scripts in loaded partials are not executed.
        if (!window.__approvalSubmitBinding) {
            window.__approvalSubmitBinding = true;

            // Strong fallback: handle submit button click directly.
            // Some browsers/flows can skip submit event when dynamic modal content has mixed handlers.
            document.addEventListener('click', function (event) {
                var btn = event.target.closest('button[type="submit"]');
                if (!btn) {
                    return;
                }

                var form = btn.form;
                if (!form || form.id !== 'myForm') {
                    return;
                }

                if (form.dataset.confirmed === '1') {
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                if (!validateApprovalForm(form)) {
                    return;
                }

                confirmAndSubmit(form);
            }, true);

            document.addEventListener('submit', function (event) {
                var form = event.target;
                if (!form || form.id !== 'myForm') {
                    return;
                }

                if (form.dataset.confirmed === '1') {
                    return;
                }

                event.preventDefault();
                // Ensure only this centralized handler runs for dynamically loaded forms.
                // Some partials still attach their own submit listeners and can block submission.
                event.stopImmediatePropagation();

                if (!validateApprovalForm(form)) {
                    return;
                }

                confirmAndSubmit(form);
            }, true);
        }
    });

    window.goForm = function(el)
    {
        var id_data = el.value;
        var formUrl = "{{ route('approval.form', ['id' => '__ID__']) }}".replace('__ID__', id_data);

        if (window.jQuery) {
            window.jQuery("#v_inputan").load(formUrl, function () {
                window.jQuery('ul.iq-email-sender-list li').on('click', function() {
                    window.jQuery(this).next().addClass('show');
                });
                window.jQuery('.email-app-details li h4').on('click', function() {
                    window.jQuery('.email-app-details').removeClass('show');
                });
            });
            return;
        }

        var container = document.getElementById('v_inputan');
        if (!container) return;
        container.innerHTML = '<div class="p-3 text-center">Loading...</div>';
        fetch(formUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (resp) {
                if (!resp.ok) throw new Error('Failed to load approval form');
                return resp.text();
            })
            .then(function (html) {
                container.innerHTML = html;
            })
            .catch(function () {
                container.innerHTML = '<div class="p-3 text-danger">Gagal memuat form approval.</div>';
            });
    }
</script>
@endsection
