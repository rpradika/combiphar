<?php

namespace App\Models;

use App\Concerns\HasLocalizedContent;
use Illuminate\Database\Eloquent\Model;

class AboutHistory extends Model
{
    use HasLocalizedContent;

    protected $table = 'about_histories';

    protected $guarded = [];
}
