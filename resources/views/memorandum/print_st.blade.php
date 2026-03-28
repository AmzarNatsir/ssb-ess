<!DOCTYPE html>
 <html lang="en">
 <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>SSB | Smart System Base | Surat Teguran (ST)</title>
<style>
    @page {
        margin: 0px;
    }
    body {
        margin : 120px 100px;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
    }
    a {
        color: #fff;
        text-decoration: none;
    }
    table {
        font-size: 11px;
        font-family: 'Poppins', sans-serif;
    }
    tfoot tr td {
        font-weight: bold;
        font-size: x-small;
    }
    .page-break {
        page-break-after: always;
    }
    .information {
            background-color: #ffffff;
            color: #020202;
        }
        .information .logo {
            margin: 5px;
        }
        .information table {
            padding: 10px;
        }
    header { position: fixed; top: 10px; left: 20px; right: 20px; height: 30px; }
    /*
    footer { position: fixed; bottom: -60px; left: 0px; right: 0px; background-color: #03a9f4; height: 25px; }
    */
    .page-break:last-child { page-break-after: never; }
    </style>
    </head>
    <header>
    <div class="information">
        <table width="100%">
            <tr>
                <td align="left" style="width: 50%;">
                <img src="{{ public_path($kop_surat['logo']) }}" alt="Logo" width="100px" width="auto" class="logo"/>
                </td>
                <td align="right" style="width: 50%;">
                    <h2>PT. SUMBER SETIA BUDI</h2>
                    {{-- <pre> --}}
                        {{ $kop_surat['alamat_situs'] }}<br>
                        {{ $kop_surat['lokasi'] }}
                    {{-- </pre> --}}
                </td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
        </table>
    </div>
    </header>
<body>
<main>
    <table style="width: 100%" cellpadding='5' class="isi">
        <tr>
            <td style="text-align: center; font-size:large"><b>SURAT TEGURAN</b></td>
        </tr>
    </table>
    <table style="border: 2px solid black; border-collapse: collapse; width: 100%;" cellpadding='5' class="isi">
    <tr>
        <td colspan="3" style="border-top: 2px solid black; border-collapse: collapse;">Kepada Yth,</td>
    </tr>
    <tr>
        <td style="width: 20%;">Nama</td>
        <td style="text-align: right; width: 1%;">:</td>
        <td style="width: 79%;"><b>{{ $dt_st->karyawan->nm_lengkap }}</b></td>
    </tr>
    <tr>
        <td>NIK</td>
        <td style="text-align: right;">:</td>
        <td><b>{{ $dt_st->karyawan->nik }}</b></td>
    </tr>
    <tr>
        <td>Jabatan</td>
        <td style="text-align: right;">:</td>
        <td><b>{{ $dt_st->karyawan->jabatan->nm_jabatan }}</b></td>
    </tr>
    <tr>
        <td>Departemen</td>
        <td style="text-align: right;">:</td>
        <td><b>{{ $dt_st->karyawan->departemen->nm_dept }}</b></td>
    </tr>
    <tr>
        <td colspan="3" style="border-top: 2px solid black; border-collapse: collapse;">Sehubungan dengan pelanggaran yang Saudara lakukan, yaitu :</td>
    </tr>
    <tr>
        <td colspan="3"><b>{{ $dt_st->jenisPelanggaran->jenis_pelanggaran }}</b><br><br></td>
    </tr>
    <tr>
        <td colspan="3" style="border-top: 2px solid black; border-collapse: collapse;">Maka sesuai dengan peraturan yang berlaku, kepada Saudara diberikan sanksi berupa <b>SURAT TEGURAN</b><br><br>
            Setelah menerima SURAT TEGURAN ini, diharapkan agar merubah sikap dan
            memperbaiki kinerjanya menjadi lebih baik.<br><br>
            Surat Teguran ini berlaku selama 3 (tiga) bulan, apabila Saudara kembali melakukan
            kesalahan atau pelanggaran lainnya, maka akan diberikan sanksi yang lebih keras sesuai
            dengan peraturan yang berlaku.<br><br>
            Demikian surat teguran ini dibuat untuk dapat dimengerti dan terima kasih.<br><br>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="border-top: 2px solid black; border-collapse: collapse;">
            <table style="width: 100%" cellpadding="0">
                <tr>
                    <td style="width: 33%">Pomalaa, {{ date_format(date_create($dt_st->created_at), 'd') }} {{ \App\Helpers\HrdFunction::get_nama_bulan(date_format(date_create($dt_st->created_at), 'm')) }} {{ date_format(date_create($dt_st->created_at), 'Y') }}</td>
                    <td style="width: 33%">Karyawan yang</td>
                    <td style="width: 34%"></td>
                </tr>
                <tr>
                    <td>Dibuat oleh, </td>
                    <td>bersangkutan, </td>
                    <td>Diketahui oleh,</td>
                </tr>
                <tr>
                    <td style="height: 70px; vertical-align: middle;">
                        <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->errorCorrection('H')->merge(public_path($kop_surat['logo']), .3, true)->generate(route('memorandum.verify', ['type' => 'st', 'id' => $dt_st->id]))) !!} ">
                    </td>
                    <td style="height: 70px; vertical-align: middle;">
                    </td>
                    @foreach ($approval as $index => $space)
                    <td style="width: {{ $witdhColumn }}%; text-align: left; vertical-align: middle;">
                        <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->errorCorrection('H')->merge(public_path($kop_surat['logo']), .3, true)->generate(route('memorandum.verify', ['type' => 'st', 'id' => $dt_st->id]))) !!} ">
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <td><b>{{ $dt_st->diajukanOleh->nm_lengkap ?? '-' }}</b></td>
                    <td><b>{{ $dt_st->karyawan->nm_lengkap }}</b></td>
                    @foreach ($approval as $pejabat)
                    <td><b>{{ $pejabat->get_profil_employee->nm_lengkap }}</b></td>
                    @endforeach
                </tr>
                <tr>
                    <td>{{ $dt_st->diajukanOleh->jabatan->nm_jabatan ?? '-' }}</td>
                    <td>{{ $dt_st->karyawan->jabatan->nm_jabatan }}</td>
                    @foreach ($approval as $pejabat)
                    <td>{{ $pejabat->get_profil_employee->jabatan->nm_jabatan ?? '-' }}</td>
                    @endforeach
                </tr>
            </table>
        </td>
    </tr>
    </table>
</main>
</body>
 </html>
