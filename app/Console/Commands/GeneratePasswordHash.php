<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GeneratePasswordHash extends Command
{
    protected $signature = 'password:hash {password}';
    protected $description = 'Generate a password hash for given password';

    public function handle()
    {
        $password = $this->argument('password');
        $hash = Hash::make($password);
        $this->info("Password hash for '$password': $hash");
        return 0;
    }
} 