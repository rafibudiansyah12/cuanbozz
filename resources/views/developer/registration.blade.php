<x-developer-layout :navigation-items="$navigation">
    @php($store = $applicant->ownedStores->first())
    <div class="mx-auto max-w-2xl space-y-4">
        <a href="{{ route('developer.registrations') }}" class="inline-flex min-h-11 items-center text-sm font-medium text-primary-700">← Kembali ke pendaftaran</a>
        @if(session('status'))<x-ui.toast>{{ session('status') }}</x-ui.toast>@endif
        @if($errors->any())<x-ui.card class="border-danger text-sm text-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-ui.card>@endif
        <x-ui.card>
            <div class="flex items-start justify-between gap-3"><div><h1 class="text-xl font-bold text-text">{{ $applicant->name }}</h1><p class="text-sm text-muted">{{ $applicant->email }}</p></div><x-ui.badge :variant="match($applicant->approval_status) { \App\Enums\AccountApprovalStatus::Pending => 'warning', \App\Enums\AccountApprovalStatus::Approved => 'success', default => 'danger' }">{{ ucfirst($applicant->approval_status->value) }}</x-ui.badge></div>
            <dl class="mt-4 grid grid-cols-1 gap-3 border-y border-border py-4 text-sm sm:grid-cols-2">
                <div><dt class="text-muted">Metode login</dt><dd class="font-medium text-text">{{ $applicant->getAttribute('google_id') ? 'Google' : 'Email' }}</dd></div>
                <div><dt class="text-muted">Tanggal daftar</dt><dd class="font-medium text-text">{{ $applicant->created_at->format('d M Y, H:i') }}</dd></div>
                <div><dt class="text-muted">Nama toko</dt><dd class="font-medium text-text">{{ $store?->name ?? 'Belum diisi' }}</dd></div>
                <div><dt class="text-muted">Telepon</dt><dd class="font-medium text-text">{{ $store?->phone ?? '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-muted">Alamat</dt><dd class="font-medium text-text">{{ $store?->address ?? '—' }}</dd></div>
            </dl>

            @if($applicant->approval_status === \App\Enums\AccountApprovalStatus::Pending)
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <form method="POST" action="{{ route('developer.registrations.approve', $applicant) }}" onsubmit="return confirm('Setujui pendaftaran {{ addslashes($applicant->name) }}?')">@csrf<x-ui.button type="submit" class="w-full">Setujui</x-ui.button></form>
                    <form method="POST" action="{{ route('developer.registrations.reject', $applicant) }}" class="space-y-2" onsubmit="return confirm('Tolak pendaftaran ini?')">@csrf<x-ui.textarea label="Alasan penolakan (wajib)" name="reason" required>{{ old('reason') }}</x-ui.textarea><x-ui.button type="submit" variant="danger" class="w-full">Tolak pendaftaran</x-ui.button></form>
                </div>
            @elseif($applicant->approval_status === \App\Enums\AccountApprovalStatus::Approved)
                <form method="POST" action="{{ route('developer.registrations.suspend', $applicant) }}" class="mt-4 space-y-2" onsubmit="return confirm('Tangguhkan akun ini dan blokir akses tokonya?')">@csrf<x-ui.input label="Catatan (opsional)" name="note" /><x-ui.button type="submit" variant="danger" class="w-full">Tangguhkan</x-ui.button></form>
            @elseif($applicant->approval_status === \App\Enums\AccountApprovalStatus::Suspended)
                <form method="POST" action="{{ route('developer.registrations.reactivate', $applicant) }}" class="mt-4 space-y-2" onsubmit="return confirm('Aktifkan kembali akun ini?')">@csrf<x-ui.input label="Catatan (opsional)" name="note" /><x-ui.button type="submit" class="w-full">Aktifkan kembali</x-ui.button></form>
            @elseif($applicant->rejection_reason)
                <p class="mt-4 rounded-control border border-danger p-3 text-sm text-text"><strong>Alasan penolakan:</strong> {{ $applicant->rejection_reason }}</p>
            @endif
        </x-ui.card>
    </div>
</x-developer-layout>
