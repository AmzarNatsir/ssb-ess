<!DOCTYPE html>
 <html lang="en">
 <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>SSB | Smart System Base | Memorandum | Surat Peringatan (SP)</title>
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
                    {{ $kop_surat['alamat_situs'] }}<br>
                    {{ $kop_surat['lokasi'] }}
                </td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
        </table>
    </div>
    </header>
<main>
<main>
    <table style="width: 100%;" class="isi">
    <tr>
        <td colspan="3" style="text-align: center; font-size:large"><b>SURAT PERINGATAN</b></td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center; font-size:13px"><b>NO. {{ $dt_sp->no_sp }}</b></td>
    </tr>
    <tr><td colspan="3" style="height: 30px;"></td></tr>
    <tr>
        <td colspan="3">Menerangkan bahwa :</td>
    </tr>
    <tr>
        <td style="width: 40%;">Nama</td>
        <td style="text-align: right; width: 1%;">:</td>
        <td style="width: 59%;"><b>{{ $dt_sp->karyawan->nm_lengkap }}</b></td>
    </tr>
    <tr>
        <td>NIK</td>
        <td style="text-align: right;">:</td>
        <td><b>{{ $dt_sp->karyawan->nik }}</b></td>
    </tr>
    <tr>
        <td>Jabatan</td>
        <td style="text-align: right;">:</td>
        <td><b>{{ $dt_sp->karyawan->jabatan->nm_jabatan }}</b></td>
    </tr>
    <tr><td colspan="3"></td></tr>
    <tr>
        <td style="vertical-align: top;">Uraian Pelanggaran</td>
        <td style="text-align: right; vertical-align: top;">:</td>
        <td><b>{{ $dt_sp->uraian_pelanggaran }}</b></td>
    </tr>
    <tr><td colspan="3"></td></tr>
    <tr>
        <td>Sesuai dengan pelanggaran diatas, maka yang bersangkutan dikenakan sanksi</td>
        <td style="text-align: right; vertical-align: top;">:</td>
        <td style="vertical-align: top;"><b>{{ $dt_sp->jenisSpDisetujui->nm_jenis_sp }}</b></td>
    </tr>
    <tr><td colspan="3" style="height: 30px;"></td></tr>
    <tr>
        <td colspan="3">Demikian surat peringatan ini dibuat untuk diketahui dan terima kasih.</td>
    </tr>
    <tr><td colspan="3" style="height: 20px;"></td></tr>
    <tr>
        <td colspan="3" style="text-align: right">Pomalaa, {{ date_format(date_create($dt_sp->tgl_sp), 'd') }} {{ \App\Helpers\HrdFunction::get_nama_bulan(date_format(date_create($dt_sp->tgl_sp), 'm')) }} {{ date_format(date_create($dt_sp->tgl_sp), 'Y') }}</td>
    </tr>
    </table>
    <br>
    <br>
    <table style="width: 100%; border-collapse:collapse; font-size: 12px">
        <tr>
            <td style="width: {{ $witdhColumn }}%"></td>
            @foreach ($approval as $head)
            <td style="width: {{ $witdhColumn }}%">Mengetahui</td>
            @endforeach
        </tr>
        <tr>
            <td>Karyawan</td>
            @foreach ($approval as $jabatan)
            <td style="width: {{ $witdhColumn }}%">{{ $jabatan->get_profil_employee->jabatan->nm_jabatan }}</td>
            @endforeach
        </tr>
        <tr>
            <td style="height: 70px"></td>
            @foreach ($approval as $index => $space)
            <td style="width: {{ $witdhColumn }}%; text-align: left; vertical-align: middle;">
                <img src="data:image/svg+xml;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(60)->errorCorrection('H')->merge(public_path($kop_surat['logo']), .3, true)->generate(route('memorandum.verify', ['type' => 'sp', 'id' => $dt_sp->id]))) !!} ">
            </td>
            @endforeach
        </tr>
        <tr>
            <td><b>{{$dt_sp->karyawan->nm_lengkap }}</b></td>
            @foreach ($approval as $pejabat)
            <td style="width: {{ $witdhColumn }}%"><b>{{ $pejabat->get_profil_employee->nm_lengkap }}</b></td>
            @endforeach
        </tr>
    </table>
</main>
</body>
 </html>
