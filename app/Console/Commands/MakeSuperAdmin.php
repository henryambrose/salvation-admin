<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    protected $signature = 'user:make-superadmin {email : The email of the user to make superadmin}';

    protected $description = 'Make a user a superadmin';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found!");

            return 1;
        }

        $user->update(['is_superadmin' => true]);

        $this->info("✅ User '{$user->name}' ({$email}) is now a superadmin!");

        return 0;
    }
}
