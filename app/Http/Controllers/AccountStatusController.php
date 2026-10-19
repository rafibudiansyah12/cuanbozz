<?php

namespace App\Http\Controllers;

use App\Enums\AccountApprovalStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccountStatusController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isDeveloper()) {
            return redirect('/developer');
        }

        $memberInactive = DB::table('store_members')
            ->where('user_id', $user->id)
            ->whereIn('role', ['admin', 'cashier'])
            ->where('is_active', false)
            ->exists();

        if ($memberInactive) {
            return view('account.status', [
                'status' => AccountApprovalStatus::Suspended,
                'rejectionReason' => null,
                'memberInactive' => true,
            ]);
        }

        $approvalUser = $user->accountApprovalUser();

        if ($approvalUser->approval_status === AccountApprovalStatus::Approved) {
            return redirect()->route('dashboard');
        }

        return view('account.status', [
            'status' => $approvalUser->approval_status,
            'rejectionReason' => $approvalUser->rejection_reason,
            'memberInactive' => false,
        ]);
    }
}
