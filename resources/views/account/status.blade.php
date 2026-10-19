<x-guest-layout>
    <div class="space-y-4 text-center">
        @switch($status)
            @case(\App\Enums\AccountApprovalStatus::Pending)
                <x-ui.badge variant="warning">Menunggu persetujuan</x-ui.badge>
                <h1 class="text-xl font-bold text-text">Akun sedang ditinjau</h1>
                <p class="text-sm text-muted">{{ ($memberInactive ?? false) ? 'Akses kasir untuk toko ini dinonaktifkan oleh pemilik toko.' : 'Toko Anda sudah terdaftar. Anda dapat menggunakan aplikasi setelah akun disetujui oleh developer.' }}</p>
                @break

            @case(\App\Enums\AccountApprovalStatus::Rejected)
                <x-ui.badge variant="danger">Pendaftaran ditolak</x-ui.badge>
                <h1 class="text-xl font-bold text-text">Pendaftaran belum dapat disetujui</h1>
                <p class="text-sm text-muted">Alasan penolakan:</p>
                <x-ui.card class="text-left text-sm text-text">{{ $rejectionReason ?: 'Tidak ada alasan yang dicantumkan.' }}</x-ui.card>
                @break

            @case(\App\Enums\AccountApprovalStatus::Suspended)
                <x-ui.badge variant="danger">Akun ditangguhkan</x-ui.badge>
                <h1 class="text-xl font-bold text-text">Akses toko sedang ditangguhkan</h1>
                <p class="text-sm text-muted">{{ ($memberInactive ?? false) ? 'Akses kasir untuk toko ini dinonaktifkan oleh pemilik toko. Hubungi pemilik toko jika Anda memerlukan akses kembali.' : 'Pemilik dan anggota toko belum dapat menggunakan aplikasi. Hubungi pengelola platform untuk informasi lebih lanjut.' }}</p>
                @break

            @default
                <x-ui.badge variant="success">Akun disetujui</x-ui.badge>
                <h1 class="text-xl font-bold text-text">Akun Anda sudah aktif</h1>
                <p class="text-sm text-muted">Anda dapat melanjutkan menggunakan CuanBozz.</p>
        @endswitch

        @if (! auth()->user()->hasVerifiedEmail())
            <p class="text-sm text-muted">Verifikasi email Anda melalui tautan yang telah dikirim sebelum melanjutkan.</p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-ui.button type="submit" variant="secondary" class="w-full">Kirim ulang tautan verifikasi</x-ui.button>
            </form>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="ghost" class="w-full">Keluar</x-ui.button>
        </form>
    </div>
</x-guest-layout>
