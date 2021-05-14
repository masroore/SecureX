<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users\Country;

class State extends Model
{
    use HasFactory;

    public function country() {
        return $this->belongsTo(Country::class);
    }
}
