<?php

namespace App\Console\Commands;

use App\Models\PasswordResetToken;
use App\Models\VerificationCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanExpiredData extends Command
{
    protected $signature = 'clean:expired-data';
    protected $description = "delete the temporary tokens and verfication codes taht are expired ";

    public function handle()
    {
        // temporary tokens 
        $deletedResets = PasswordResetToken::where('expired_at', '<', Carbon::now())
            ->delete();
        // verfication codes 
        $deletedCodes = VerificationCode::where('expires_at', '<', Carbon::now())
            ->delete();
        $this->info("Clean up completed");
    }
}
