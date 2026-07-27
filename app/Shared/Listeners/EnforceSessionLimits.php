<?php

namespace App\Shared\Listeners;

use App\Central\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class EnforceSessionLimits
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Ensure we're in the tenant context before doing tenant specific actions
        if (tenancy()->initialized) {
            $sessionId = session()->getId();

            // 1. Delete all other sessions for this user (Single device login)
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $sessionId)
                ->delete();

            // 2. If the user is a cashier, delete sessions for all OTHER cashiers
            if ($user->role === 'cashier') {
                $otherCashierIds = User::where('role', 'cashier')
                    ->where('id', '!=', $user->id)
                    ->pluck('id');

                if ($otherCashierIds->isNotEmpty()) {
                    DB::table('sessions')
                        ->whereIn('user_id', $otherCashierIds)
                        ->delete();
                }
            }
        }
    }
}
