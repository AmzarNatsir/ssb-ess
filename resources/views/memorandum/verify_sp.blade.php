<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Verification - PT. SSB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .verification-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .card-header-status {
            background: #10b981;
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .status-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 15px;
        }
        .detail-item {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .value {
            font-weight: 600;
            font-size: 15px;
        }
        .approver-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .approver-avatar {
            width: 40px;
            height: 40px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            margin-right: 12px;
        }
        .footer-note {
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            padding: 20px;
            line-height: 1.5;
        }
        .badge-sp {
            background: #fee2e2;
            color: #ef4444;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <div class="card-header-status">
            <div class="status-icon">
                <i class="ti ti-shield-check"></i>
            </div>
            <h4 class="mb-1 fw-bold">Document Authenticity Verified</h4>
            <p class="mb-0 opacity-75 small">PT. SUMBER SETIA BUDI</p>
        </div>
        
        <div class="p-4">
            <div class="mb-4">
                <div class="detail-item">
                    <div class="label">Document Type</div>
                    <div class="value text-primary">{{ $type == 'sp' ? 'Warning Letter (Surat Peringatan)' : 'Reprimand Letter (Surat Teguran)' }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Document Number</div>
                    <div class="value">{{ $type == 'sp' ? $dt->no_sp : $dt->no_st }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Employee Name</div>
                    <div class="value">{{ $dt->karyawan->nm_lengkap }}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Violation Type</div>
                    <div class="value">
                        <span class="badge-sp">{{ $type == 'sp' ? ($dt->jenisSpDisetujui->nm_jenis_sp ?? 'N/A') : ($dt->jenisPelanggaran->jenis_pelanggaran ?? 'N/A') }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="label">Active Period</div>
                    <div class="value">
                        {{ date('d M Y', strtotime($dt->sp_mulai_active)) }} — {{ date('d M Y', strtotime($dt->sp_akhir_active)) }}
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <h6 class="fw-bold mb-3 d-flex align-items-center">
                    <i class="ti ti-users me-2 text-primary"></i> Digital Signatories
                </h6>
                @foreach($approval as $appr)
                <div class="approver-item">
                    <div class="approver-avatar">
                        <i class="ti ti-user-check"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">{{ $appr->get_profil_employee->nm_lengkap }}</div>
                        <div style="font-size: 11px; color: #64748b;">
                            {{ $appr->get_profil_employee->nik }} | {{ $appr->get_profil_employee->jabatan->nm_jabatan ?? 'Approver' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="footer-note">
            <i class="ti ti-info-circle me-1"></i>
            This is an electronically generated document by SSB Smart System. 
            Validation was successful against the original system records.
        </div>
    </div>
</body>
</html>
