<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    protected $signature = 'user:list';
    protected $description = 'List all users';

    public function handle()
    {
        $users = User::all(['name', 'email', 'is_superadmin']);
        
        $this->info('Users:');
        $this->newLine();
        
        foreach ($users as $user) {
            $superadmin = $user->is_superadmin ? ' (SuperAdmin)' : '';
            $this->line("- {$user->name}: {$user->email}{$superadmin}");
        }
        
        return 0;
    }
} 