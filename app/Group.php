<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name', 'type', 'privacy', 'interaction'
    ];

    public function addOwner(User $user)
    {
        $this->owners()->attach($user->id);
    }

    public function owners()
    {
        return $this->belongsToMany('App\User', 'group_owners', 'group_id', 'user_id');
    }

    public function removeOwner(User $user)
    {
        $this->owners()->detach($user->id);
    }

    public function addMember(User $user)
    {
        $this->members()->attach($user->id);
    }

    public function members()
    {
        return $this->belongsToMany('App\User', 'members', 'group_id', 'user_id');
    }

    public function removeMember(User $user)
    {
        $this->members()->detach($user->id);
    }

    public function isOwner(User $user)
    {
        $result = $this->owners()->where('id', $user->id)->get()->first();
        return $result != null;
    }

    public function isMember(User $user)
    {
        $result = $this->members()->where('id', $user->id)->get()->first();
        return $result != null;
    }

    public function memo(){
        return $this->hasMany('App\Memo');
    }
}
