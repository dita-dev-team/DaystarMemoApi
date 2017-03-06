<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    protected $fillable = [
        'body'
    ];

    public function to()
    {
        return $this->morphTo('to');
    }

    public function from()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function setSender(User $user)
    {
        $this->from()->associate($user);
    }

    public function setRecipient($to)
    {
        $this->to()->associate($to);
    }
}
