<?php

namespace App\Services;

use App\Enums\AccountApprovalStatus;
use App\Models\DeveloperAuditLog;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeveloperDecisionService
{
    public function approve(User $developer, User $target): User
    {
        return DB::transaction(function () use ($developer, $target) {
            $target = $this->lockTarget($developer, $target);
            $this->requireStatus($target, AccountApprovalStatus::Pending, 'Pendaftar ini bukan lagi menunggu persetujuan.');

            $target->forceFill([
                'approval_status' => AccountApprovalStatus::Approved,
                'approved_at' => now(),
                'approved_by' => $developer->id,
                'rejection_reason' => null,
            ])->save();

            $this->log($developer, $target, 'approve');

            return $target;
        });
    }

    public function reject(User $developer, User $target, string $reason): User
    {
        return DB::transaction(function () use ($developer, $target, $reason) {
            $target = $this->lockTarget($developer, $target);
            $this->requireStatus($target, AccountApprovalStatus::Pending, 'Pendaftar ini bukan lagi menunggu persetujuan.');

            $target->forceFill([
                'approval_status' => AccountApprovalStatus::Rejected,
                'approved_at' => null,
                'approved_by' => null,
                'rejection_reason' => $reason,
            ])->save();

            $this->log($developer, $target, 'reject', $reason);

            return $target;
        });
    }

    public function suspendAccount(User $developer, User $target, ?string $note = null): User
    {
        return DB::transaction(function () use ($developer, $target, $note) {
            $target = $this->lockTarget($developer, $target);
            $this->requireStatus($target, AccountApprovalStatus::Approved, 'Hanya akun aktif yang dapat ditangguhkan.');

            $target->forceFill(['approval_status' => AccountApprovalStatus::Suspended])->save();
            $this->log($developer, $target, 'suspend', $note);

            return $target;
        });
    }

    public function reactivateAccount(User $developer, User $target, ?string $note = null): User
    {
        return DB::transaction(function () use ($developer, $target, $note) {
            $target = $this->lockTarget($developer, $target);
            $this->requireStatus($target, AccountApprovalStatus::Suspended, 'Hanya akun yang ditangguhkan yang dapat diaktifkan kembali.');

            $target->forceFill([
                'approval_status' => AccountApprovalStatus::Approved,
                'approved_at' => now(),
                'approved_by' => $developer->id,
                'rejection_reason' => null,
            ])->save();
            $this->log($developer, $target, 'reactivate', $note);

            return $target;
        });
    }

    public function suspendStore(User $developer, Store $store, ?string $note = null): Store
    {
        return DB::transaction(function () use ($developer, $store, $note) {
            $store = Store::query()->lockForUpdate()->with('owner')->findOrFail($store->id);
            $owner = $this->lockTarget($developer, $store->owner);

            if (! $store->is_active || ! in_array($owner->approval_status, [AccountApprovalStatus::Approved, AccountApprovalStatus::Suspended], true)) {
                throw ValidationException::withMessages(['status' => 'Toko ini tidak dapat ditangguhkan dari status saat ini.']);
            }

            $store->forceFill(['is_active' => false])->save();
            $owner->forceFill(['approval_status' => AccountApprovalStatus::Suspended])->save();
            $this->log($developer, $owner, 'suspend', $this->storeNote($store, $note));

            return $store;
        });
    }

    public function reactivateStore(User $developer, Store $store, ?string $note = null): Store
    {
        return DB::transaction(function () use ($developer, $store, $note) {
            $store = Store::query()->lockForUpdate()->with('owner')->findOrFail($store->id);
            $owner = $this->lockTarget($developer, $store->owner);

            if ($store->is_active || $owner->approval_status !== AccountApprovalStatus::Suspended) {
                throw ValidationException::withMessages(['status' => 'Toko ini tidak dapat diaktifkan kembali dari status saat ini.']);
            }

            $store->forceFill(['is_active' => true])->save();
            $owner->forceFill([
                'approval_status' => AccountApprovalStatus::Approved,
                'approved_at' => now(),
                'approved_by' => $developer->id,
                'rejection_reason' => null,
            ])->save();
            $this->log($developer, $owner, 'reactivate', $this->storeNote($store, $note));

            return $store;
        });
    }

    private function lockTarget(User $developer, User $target): User
    {
        abort_unless($developer->isDeveloper(), 404);

        if ($developer->is($target)) {
            throw ValidationException::withMessages(['target' => 'Developer tidak dapat mengubah status akunnya sendiri.']);
        }

        // Developer decisions intentionally target owner accounts across store boundaries.
        return User::query()->whereHas('ownedStores')->lockForUpdate()->findOrFail($target->id);
    }

    private function requireStatus(User $target, AccountApprovalStatus $status, string $message): void
    {
        if ($target->is_developer || $target->approval_status !== $status) {
            throw ValidationException::withMessages(['status' => $message]);
        }
    }

    private function log(User $developer, User $target, string $action, ?string $note = null): void
    {
        DeveloperAuditLog::query()->create([
            'developer_id' => $developer->id,
            'action' => $action,
            'target_user_id' => $target->id,
            'note' => $note,
        ]);
    }

    private function storeNote(Store $store, ?string $note): string
    {
        $storeReference = 'Toko: '.$store->name.' (ID '.$store->id.')';

        return $note ? $storeReference.' — '.$note : $storeReference;
    }
}
