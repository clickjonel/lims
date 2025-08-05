<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    protected $connection = 'dohis';
    protected $table = 'dohis_section';
    protected $primaryKey = 'section_id';
    public $timestamps = false;

    public function personnel():BelongsToMany
    {
        return $this->belongsToMany(User::class,'dohis_user_assignment','section_id','user_id')->where('dohis_user.account_status','Assigned');
    }
}
