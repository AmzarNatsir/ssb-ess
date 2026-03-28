<div class="card border-0 shadow-sm mt-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light text-nowrap">
                    <tr>
                        <th class="ps-4" style="width: 80px;">No.Urut</th>
                        <th>Tanggal Pelatihan</th>
                        <th>Nama Pelatihan</th>
                        <th>Tempat</th>
                        <th>Durasi</th>
                        <th>Kategori</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $index => $item)
                        <tr>
                            <td class="ps-4 fw-medium text-muted">{{ $index + 1 }}</td>
                            <td class="text-nowrap">
                                {{ date('d M Y', strtotime($item->tanggal_awal)) }} - {{ date('d M Y', strtotime($item->tanggal_sampai)) }}
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ $item->get_nama_pelatihan->nama_pelatihan ?? $item->nama_pelatihan }}
                                <br>
                                <span class="badge bg-soft-primary text-primary">{{ $item->get_pelaksana->nama_lembaga ?? "-" }}</span>
                            </td>
                            <td>{{ $item->tempat_pelaksanaan }}</td>
                            <td class="text-nowrap"><span class="badge bg-soft-primary text-primary">{{ $item->durasi }} Jam</span></td>
                            <td>
                                <span class="badge bg-soft-info text-info">
                                    {{ $item->kategori ?: 'General' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-info btn-detail-training" data-id="{{ $item->id }}" data-bs-toggle="tooltip" title="View Detail">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success btn-report-training" data-id="{{ $item->id }}" data-bs-toggle="tooltip" title="Laporan Setelah Pelatihan">
                                        <i class="ti ti-file-report"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ti ti-inbox fs-1 mb-2 d-block text-light"></i>
                                    <p class="mb-0">Tidak ada data pelatihan ditemukan untuk kategori ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
