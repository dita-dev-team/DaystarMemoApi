<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','img_url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function addConnection(User $user)
    {
        $this->connections()->attach($user->id);
    }

    public function connections()
    {
        return $this->belongsToMany('App\User', 'connections', 'user_id', 'connection_id');
    }

    public function removeConnection(User $user)
    {
        $this->connections()->detach($user->id);
    }

    public function groups()
    {
        return $this->belongsToMany('App\Group', 'members', 'user_id', 'group_id');
    }
    public function memo()
    {
        return $this->hasMany('App\Memo');
    }
}
