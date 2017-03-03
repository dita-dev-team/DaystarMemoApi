<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    protected $fillable = ['content','file_url'];

    public function user()
    {
        return $this->belongsTo('App\Group');
    }

    public function group()
    {
        return $this->belongsTo('App\Group');
    }
}
