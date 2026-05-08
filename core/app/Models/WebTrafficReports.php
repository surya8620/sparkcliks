<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebTrafficReports extends Model
{
    protected $table = "webtraffic_reports";

    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}