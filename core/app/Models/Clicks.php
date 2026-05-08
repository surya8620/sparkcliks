<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clicks extends Model
{
    protected $table = "clicks";

    protected  $guarded = ['click_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
