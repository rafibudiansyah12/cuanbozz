<x-developer-layout :navigation-items="$navigation">
    <div class="space-y-4">
        <div><h1 class="text-2xl font-bold text-text">Pendaftaran</h1><p class="text-sm text-muted">Tinjau dan kelola status akun pemilik.</p></div>
        <x-ui.card>
            <form method="GET" class="grid gap-3 sm:grid-cols-[1fr_12rem_auto]">
                <x-ui.input label="Cari pendaftar atau toko" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama, email, nama toko" />
                <x-ui.select label="Status" name="status"><option value="">Semua status</option>@foreach(\App\Enums\AccountApprovalStatus::cases() as $status)<option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ ucfirst($status->value) }}</option>@endforeach</x-ui.select>
                <div class="flex items-end"><x-ui.button type="submit" class="w-full sm:w-auto">Terapkan</x-ui.button></div>
            </form>
        </x-ui.card>
        <x-ui.card padding="p-0">
            @if($applicants->isEmpty())<x-ui.empty-state class="p-5" title="Pendaftar tidak ditemukan" description="Ubah filter atau kata pencarian." />@else
                <ul class="divide-y divide-border">@foreach($applicants as $applicant)<li class="flex items-center justify-between gap-3 p-4"><div class="min-w-0"><p class="truncate font-semibold text-text">{{ $applicant->name }}</p><p class="truncate text-sm text-muted">{{ $applicant->email }}</p><p class="truncate text-xs text-muted">{{ $applicant->ownedStores->first()?->name ?? 'Belum ada toko' }} · {{ $applicant->created_at->format('d M Y') }}</p></div><div class="flex shrink-0 flex-col items-end gap-2"><x-ui.badge :variant="match($applicant->approval_status) { \App\Enums\AccountApprovalStatus::Pending => 'warning', \App\Enums\AccountApprovalStatus::Approved => 'success', \App\Enums\AccountApprovalStatus::Rejected, \App\Enums\AccountApprovalStatus::Suspended => 'danger' }">{{ ucfirst($applicant->approval_status->value) }}</x-ui.badge><a href="{{ route('developer.registrations.show', $applicant) }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-primary-700">Detail</a></div></li>@endforeach</ul>
            @endif
            <div class="border-t border-border p-4">{{ $applicants->links() }}</div>
        </x-ui.card>
    </div>
</x-developer-layout>
