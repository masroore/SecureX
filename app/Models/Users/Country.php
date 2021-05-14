<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users\State;

class Country extends Model
{
    use HasFactory;

    public function states() {
        return $this->hasMany(State::class);
    }
}
