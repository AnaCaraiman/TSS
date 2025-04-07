<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    protected $model;

    public function __construct(User $model){
        $this->model = $model;
    }

    public function getEmail(string $email): string|null {
        return DB::table('users')
            ->where('email', $email)
            ->value('email');
    }




}
