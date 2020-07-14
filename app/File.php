<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * @var string
     */
    protected $table = 'files';
    /**
     * @var array
     */
    protected $fillable = ['name', 'folder_id', 'path', 'user_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function directory()
    {
        return $this->hasOne('App\Directory');
    }
}
