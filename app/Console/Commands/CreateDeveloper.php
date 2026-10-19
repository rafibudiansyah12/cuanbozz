<?php

namespace App\Console\Commands;

use App\Enums\AccountApprovalStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateDeveloper extends Command
{
    protected $signature = 'developer:create';

    protected $description = 'Create a verified CuanBozz developer account.';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Nama developer'));
        $email = mb_strtolower(trim((string) $this->ask('Email developer')));

        $validator = Validator::make(compact('name', 'email'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = (string) $this->secret('Kata sandi developer');
        $passwordConfirmation = (string) $this->secret('Konfirmasi kata sandi');

        if ($password === '' || ! hash_equals($password, $passwordConfirmation)) {
            $this->error('Kata sandi wajib diisi dan konfirmasi harus sama.');

            return self::FAILURE;
        }

        $user = new User;
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
            'approval_status' => AccountApprovalStatus::Approved,
            'approved_at' => now(),
            'approved_by' => null,
            'rejection_reason' => null,
            'is_developer' => true,
        ])->save();

        $this->info('Akun developer berhasil dibuat.');

        return self::SUCCESS;
    }
}
