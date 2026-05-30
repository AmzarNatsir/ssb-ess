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
