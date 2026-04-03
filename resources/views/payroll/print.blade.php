@php
    use App\Helpers\HrdConstants;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLIP GAJI - {{ $karyawan->nm_lengkap }} - {{ HrdConstants::MONTHS[(int) $payroll->bulan] }}
        {{ $payroll->tahun }}
    </title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 10px;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .logo {
            width: 180px;
        }

        .header-info {
            text-align: right;
        }

        .header-info h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 16pt;
        }

        .header-info p {
            margin: 2px 0;
            color: #7f8c8d;
            font-size: 9pt;
        }

        .employee-info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 4px;
        }

        .info-col {
            width: 50%;
        }

        .info-item {
            display: flex;
            margin-bottom: 4px;
        }

        .info-label {
            width: 100px;
            font-weight: bold;
            font-size: 8pt;
            color: #555;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px solid #eee;
            font-size: 9pt;
        }

        .main-content {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .section {
            flex: 1;
        }

        .section h3 {
            font-size: 10pt;
            background: #ecf0f1;
            padding: 6px 10px;
            margin: 0 0 10px;
            border-left: 4px solid #3498db;
            text-transform: uppercase;
        }

        .section-deduction h3 {
            border-left-color: #e74c3c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 4px 0;
            font-size: 9pt;
        }

        .label {
            text-align: left;
        }

        .amount {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
        }

        .subtotal-row {
            border-top: 1px solid #ddd;
            font-weight: bold;
        }

        .subtotal-row td {
            padding-top: 8px;
        }

        .net-pay-section {
            background: #2c3e50;
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .net-pay-section h4 {
            margin: 0;
            font-size: 10pt;
            opacity: 0.8;
            text-transform: uppercase;
        }

        .net-pay-section p {
            margin: 5px 0 0;
            font-size: 18pt;
            font-weight: bold;
        }

        .terbilang {
            font-style: italic;
            font-size: 8pt;
            margin-bottom: 20px;
            padding: 5px;
            background: #fdfdfd;
            border: 1px dashed #ddd;
            text-align: center;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature {
            width: 200px;
            text-align: center;
        }

        .signature p {
            margin: 0;
            font-size: 8pt;
        }

        .signature .signature-space {
            height: 60px;
        }

        .signature .signature-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9pt;
        }

        @media print {
            body {
                padding: 0;
                background: none;
            }

            .container {
                border: none;
                box-shadow: none;
                width: 100%;
                max-width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <div class="header">
            <img src="{{ URL::asset('assets/logo_perusahaan/logo_ssb.png') }}" alt="Logo" class="logo">
            <div class="header-info">
                <h2>SLIP GAJI KARYAWAN</h2>
                <p>Periode: {{ HrdConstants::MONTHS[(int) $payroll->bulan] }} {{ $payroll->tahun }}</p>
            </div>
        </div>

        <div class="employee-info">
            <div class="info-col">
                <div class="info-item">
                    <div class="info-label">NAMA</div>
                    <div class="info-value">{{ strtoupper($karyawan->nm_lengkap) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIK</div>
                    <div class="info-value">{{ $karyawan->nik }}</div>
                </div>
            </div>
            <div class="info-col">
                <div class="info-item">
                    <div class="info-label">JABATAN</div>
                    <div class="info-value">{{ strtoupper($karyawan->jabatan->nm_jabatan ?? '-') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">DEPARTEMEN</div>
                    <div class="info-value">{{ strtoupper($karyawan->departemen->nm_dept ?? '-') }}</div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <!-- Earnings -->
            <div class="section">
                <h3>I. PENERIMAAN (EARNINGS)</h3>
                <table>
                    <tr>
                        <td class="label">1. Gaji Pokok</td>
                        <td class="amount">Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">2. Tunjangan Perusahaan</td>
                        <td class="amount">Rp {{ number_format($payroll->tunj_perusahaan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">3. Hours Meter</td>
                        <td class="amount">Rp {{ number_format($payroll->hours_meter, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">4. Lembur</td>
                        <td class="amount">Rp {{ number_format($payroll->lembur, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">5. Bonus</td>
                        <td class="amount">Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td class="label">TOTAL PENERIMAAN KOTOR</td>
                        <td class="amount">Rp {{ number_format($payroll->gaji_bruto, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Deductions -->
            <div class="section section-deduction">
                <h3>II. POTONGAN (DEDUCTIONS)</h3>
                @php
                    $pot_lain = $payroll->pot_sedekah + $payroll->pot_pkk + $payroll->pot_air + $payroll->pot_rumah + $payroll->pot_toko_alif;
                    $totalBpjsKaryawan = $payroll->bpjsks_karyawan + $payroll->bpjstk_jht_karyawan + $payroll->bpjstk_jp_karyawan;
                    $totalPotongan = $totalBpjsKaryawan + $pot_lain;
                @endphp
                <table>
                    <tr>
                        <td class="label">1. BPJS</td>
                        <td class="amount">Rp {{ number_format($totalBpjsKaryawan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">2. Sedekah Bulanan</td>
                        <td class="amount">Rp {{ number_format($payroll->pot_sedekah, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">3. PKK</td>
                        <td class="amount">Rp {{ number_format($payroll->pot_pkk, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">4. Air</td>
                        <td class="amount">Rp {{ number_format($payroll->pot_air, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">5. Rumah</td>
                        <td class="amount">Rp {{ number_format($payroll->pot_rumah, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">6. Toko Alif</td>
                        <td class="amount">Rp {{ number_format($payroll->pot_toko_alif, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td class="label">TOTAL POTONGAN</td>
                        <td class="amount">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="net-pay-section">
            <h4>TAKE HOME PAY (THP)</h4>
            <p>Rp {{ number_format($payroll->thp, 0, ',', '.') }}</p>
        </div>

        <div class="terbilang">
            " TERBILANG: {{ strtoupper(\App\Helpers\HrdFunction::terbilang($payroll->thp)) }} RUPIAH "
        </div>

        <div class="footer">
            <div class="signature">
                <p>Diterima Oleh,</p>
                <div class="signature-space">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=https://sumbersetiabudi.co.id"
                        alt="QR Code" style="margin-top: 5px">
                </div>
                <br>
                <p class="signature-name">{{ strtoupper($karyawan->nm_lengkap) }}</p>
                <p>Karyawan</p>
            </div>
            <div class="signature">
                <p>Makassar, {{ date('d') }} {{ HrdConstants::MONTHS[(int) date('n')] }} {{ date('Y') }}</p>
                <div class="signature-space">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=https://sumbersetiabudi.co.id"
                        alt="QR Code" style="margin-top: 5px">
                </div>
                <br>
                <p class="signature-name">HR DEPARTMENT</p>
                <p>PT. SUMBER SEJAHTERA BERSAMA</p>
            </div>
        </div>

        <p style="font-size: 7pt; text-align: center; margin-top: 30px; color: #999;">
            Dokumen ini dicetak otomatis dari Sistem HR Employee Self Service. Segala perbedaan data harus segera
            dilaporkan ke bagian HR.
        </p>
    </div>

    <div style="text-align: center; margin-top: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Tutup</button>
    </div>
</body>

</html>