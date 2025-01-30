<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UserCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->input->getArgument('name');
        $email = $this->input->getArgument('email');
        $password = $this->input->getArgument('password');


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address');
            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('User with this email already exists');
            return;
        }

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long');
            return;
        }

        $user = new User();

        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);

        $user->save();
    }
}
