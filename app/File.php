<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';
    protected $fillable = ['name','permission', 'folder_id', 'path', 'user_id'];

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function directory()
    {
        return $this->hasOne('App\Directory');
    }
}
