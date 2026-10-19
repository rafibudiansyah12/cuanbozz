<?php

namespace App\Http\Controllers\Developer;

use App\Enums\AccountApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\RejectApplicantRequest;
use App\Http\Requests\Developer\UpdatePasswordRequest;
use App\Models\DeveloperAuditLog;
use App\Models\Store;
use App\Models\User;
use App\Services\DeveloperDecisionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeveloperPanelController extends Controller
{
    public function __construct(private readonly DeveloperDecisionService $decisions) {}

    public function home(): View
    {
        // This dashboard aggregates account and store counts across all tenants for the platform operator.
        $counts = [
            'pending' => $this->applicantQuery()->where('approval_status', AccountApprovalStatus::Pending)->count(),
            'approved' => $this->accountQuery()->where('approval_status', AccountApprovalStatus::Approved)->count(),
            'rejected' => $this->accountQuery()->where('approval_status', AccountApprovalStatus::Rejected)->count(),
            'suspended' => $this->accountQuery()->where('approval_status', AccountApprovalStatus::Suspended)->count(),
            'stores' => Store::query()->count(),
        ];

        $latestApplicants = $this->applicantQuery()
            ->with('ownedStores')
            ->latest()
            ->limit(5)
            ->get();

        return $this->page('developer.home', compact('counts', 'latestApplicants'));
    }

    public function registrations(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_column(AccountApprovalStatus::cases(), 'value'))],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        // Registration review spans all owners and stores; it is confined to developer routes.
        // Registration review only includes owner applications that have completed store onboarding.
        $query = $this->accountQuery()->whereHas('ownedStores')->with('ownedStores');

        if (! empty($filters['status'])) {
            $query->where('approval_status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhereHas('ownedStores', fn (Builder $stores) => $stores
                        ->where('name', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('phone', 'like', $term));
            });
        }

        $applicants = $query->latest()->paginate(15)->withQueryString();

        return $this->page('developer.registrations', compact('applicants', 'filters'));
    }

    public function registration(User $user): View
    {
        abort_if($user->is_developer || ! $user->ownedStores()->exists(), 404);
        $user->load('ownedStores');

        return $this->page('developer.registration', ['applicant' => $user]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $this->decisions->approve($request->user(), $user);

        return back()->with('status', 'Pendaftaran disetujui.');
    }

    public function reject(RejectApplicantRequest $request, User $user): RedirectResponse
    {
        $this->decisions->reject($request->user(), $user, $request->validated('reason'));

        return redirect()->route('developer.registrations.show', $user)->with('status', 'Pendaftaran ditolak.');
    }

    public function suspendAccount(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);
        $this->decisions->suspendAccount($request->user(), $user, $data['note'] ?? null);

        return back()->with('status', 'Akun ditangguhkan.');
    }

    public function reactivateAccount(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);
        $this->decisions->reactivateAccount($request->user(), $user, $data['note'] ?? null);

        return back()->with('status', 'Akun diaktifkan kembali.');
    }

    public function stores(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100']]);

        // Stores are intentionally listed across tenants in the protected platform panel.
        $query = Store::query()->with('owner');
        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('name', 'like', $term)
                    ->orWhereHas('owner', fn (Builder $owner) => $owner
                        ->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term));
            });
        }

        $stores = $query->latest()->paginate(15)->withQueryString();

        return $this->page('developer.stores', compact('stores', 'filters'));
    }

    public function suspendStore(Request $request, Store $store): RedirectResponse
    {
        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);
        $this->decisions->suspendStore($request->user(), $store, $data['note'] ?? null);

        return back()->with('status', 'Toko ditangguhkan.');
    }

    public function reactivateStore(Request $request, Store $store): RedirectResponse
    {
        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);
        $this->decisions->reactivateStore($request->user(), $store, $data['note'] ?? null);

        return back()->with('status', 'Toko diaktifkan kembali.');
    }

    public function users(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100']]);

        // The developer user directory intentionally spans tenant accounts, excluding all developers.
        $query = $this->accountQuery()->with('ownedStores');
        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhereHas('ownedStores', fn (Builder $stores) => $stores->where('name', 'like', $term));
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return $this->page('developer.users', compact('users', 'filters'));
    }

    public function activity(): View
    {
        $logs = DeveloperAuditLog::query()
            ->with(['developer', 'targetUser'])
            ->latest('created_at')
            ->paginate(20);

        return $this->page('developer.activity', compact('logs'));
    }

    public function more(): View
    {
        return $this->page('developer.more');
    }

    public function editAccount(): View
    {
        return $this->page('developer.account');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->forceFill(['password' => Hash::make($request->validated('password'))])->save();

        return back()->with('status', 'Kata sandi berhasil diubah.');
    }

    private function accountQuery(): Builder
    {
        // This shared query is only used by EnsureDeveloper-protected platform screens.
        return User::query()->where('is_developer', false);
    }

    private function applicantQuery(): Builder
    {
        return $this->accountQuery()
            ->where('approval_status', AccountApprovalStatus::Pending)
            ->whereHas('ownedStores');
    }

    private function page(string $view, array $data = []): View
    {
        $pendingCount = $this->applicantQuery()->count();
        $navigation = [
            ['label' => 'Beranda', 'href' => '/developer', 'icon' => '⌂'],
            ['label' => 'Pendaftaran', 'href' => '/developer/registrations', 'icon' => '▤', 'badge' => $pendingCount],
            ['label' => 'Toko', 'href' => '/developer/stores', 'icon' => '▣'],
            ['label' => 'Pengguna', 'href' => '/developer/users', 'icon' => '♙'],
            ['label' => 'Lainnya', 'href' => '/developer/more', 'icon' => '•••'],
        ];

        return view($view, $data + ['navigation' => $navigation, 'pendingCount' => $pendingCount]);
    }
}
