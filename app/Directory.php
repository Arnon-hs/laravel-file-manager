<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $table = 'directories';
    protected $fillable = ['name', 'user_id', 'path'];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }
}
