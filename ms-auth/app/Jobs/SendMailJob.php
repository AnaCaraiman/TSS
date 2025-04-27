<?php

namespace App\Jobs;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        Log::info("💌 SendMailJob triggered for: {$this->user->email}");

        if ($this->user->email) {
            Mail::to($this->user->email)->send(new WelcomeMail($this->user));
            Log::info("✅ Email sent to {$this->user->email}");
        } else {
            Log::warning("⚠️ No email found for user.");
        }
    }

}
