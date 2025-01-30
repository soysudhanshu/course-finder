<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ApiCreateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:create-token {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an api token for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error('User with this email does not exist');
            return;
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $this->info('Token created successfully');
        $this->info("Token: $token");
    }
}
