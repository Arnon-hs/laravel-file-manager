<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $table = 'directories';
    protected $fillable = ['name','permission', 'user_id'];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }
}
