<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    protected $fillable = ['user_id', 'content', 'to', 'file_url'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
