<?php

namespace Plusinfolab\DodoCashier\Tests\Models;

use Plusinfolab\DodoCashier\Billable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;
    use Billable;

    protected $guarded = [];
}
