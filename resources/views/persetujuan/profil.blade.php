<table class="table" style="width:100%">
    <tbody>
        <tr>
            <td style="width: 10%">
                @if(!empty($profil->get_profil->photo))
                <img src="{{ url(Storage::url('hrd/photo/'.$profil->get_profil->photo)) }}"
                    class="rounded-circle" alt="avatar"  style="width: 80px; height: auto;">
                @else
                <a href="{{ asset('assets/images/user/1.jpg') }}" data-fancybox data-caption="avatar">
                <img src="{{ asset('assets/images/user/1.jpg') }}"
                    class="rounded-circle" alt="avatar" style="width: 80px; height: auto;"></a>
                @endif
            </td>
            <td>
                <h4 class="mb-0">{{ $profil->get_profil->nik }}</h4>
                <h4 class="mb-0">{{ $profil->get_profil->nm_lengkap }}</h4>
                <h6 class="mb-0">{{ $profil->get_profil->get_jabatan->nm_jabatan }} | {{ $profil->get_profil->get_departemen->nm_dept }}</h6>
            </td>
        </tr>
    </tbody>
</table>
