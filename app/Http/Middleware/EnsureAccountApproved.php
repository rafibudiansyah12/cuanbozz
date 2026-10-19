<?php

namespace App\Http\Middleware;

use App\Enums\AccountApprovalStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->isDeveloper()) {
            return $next($request);
        }

        if (! $user->hasVerifiedEmail() && $request->routeIs('verification.*')) {
            return $next($request);
        }

        $inactiveCashier = DB::table('store_members')
            ->where('user_id', $user->id)
            ->whereIn('role', ['admin', 'cashier'])
            ->where('is_active', false)
            ->exists();

        if ($inactiveCashier && ! $request->routeIs('account.status', 'logout')) {
            return redirect()->route('account.status');
        }

        if ($request->is('onboarding/store') || $request->routeIs('onboarding.store*', 'onboarding.create', 'account.status', 'logout')) {
            return $next($request);
        }

        $approvalUser = $user->accountApprovalUser();

        // Inactive store members must stay on account status instead of being redirected to onboarding.
        $hasStoreMembership = DB::table('store_members')->where('user_id', $user->id)->exists();

        if ($approvalUser->approval_status === AccountApprovalStatus::Approved) {
            return $next($request);
        }

        if (! $hasStoreMembership && ! $user->ownedStores()->exists()) {
            return redirect()->route('onboarding.create');
        }

        return redirect()->route('account.status');
    }
}
