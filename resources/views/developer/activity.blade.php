<x-developer-layout :navigation-items="$navigation">
    <div class="space-y-4">
        <div><h1 class="text-2xl font-bold text-text">Log Aktivitas</h1><p class="text-sm text-muted">Keputusan developer, terbaru lebih dahulu.</p></div>
        <x-ui.card padding="p-0">@if($logs->isEmpty())<x-ui.empty-state class="p-5" title="Belum ada aktivitas" description="Keputusan developer akan tercatat di sini." />@else<ul class="divide-y divide-border">@foreach($logs as $log)<li class="space-y-1 p-4"><div class="flex items-center justify-between gap-2"><x-ui.badge :variant="match($log->action) { 'approve', 'reactivate' => 'success', 'reject', 'suspend' => 'danger', default => 'muted' }">{{ ucfirst($log->action) }}</x-ui.badge><time class="text-xs text-muted">{{ $log->created_at->format('d M Y, H:i') }}</time></div><p class="text-sm text-text"><strong>{{ $log->developer->name }}</strong> · {{ $log->targetUser->name }} ({{ $log->targetUser->email }})</p>@if($log->note)<p class="text-sm text-muted">{{ $log->note }}</p>@endif</li>@endforeach</ul>@endif<div class="border-t border-border p-4">{{ $logs->links() }}</div></x-ui.card>
    </div>
</x-developer-layout>
