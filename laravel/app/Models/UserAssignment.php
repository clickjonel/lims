<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserAssignment extends Model
{
    protected $connection = 'dohis';
    protected $table = 'dohis_user_assignment';
    protected $primaryKey = 'user_assignment_id';

    public function user():HasOne
    {
        return $this->hasOne(User::class,'user_id','user_id');
    }

    public function division():HasOne
    {
        return $this->hasOne(Division::class,'division_id','division_id');
    }

    public function section():HasOne
    {
        return $this->hasOne(Section::class,'section_id','section_id');
    }

    public function unit():HasOne
    {
        return $this->hasOne(Unit::class,'unit_id','unit_id');
    }

    public function designation():HasOne
    {
        return $this->hasOne(Designation::class,'designation_id','designation_id');
    }

    public function item():HasOne
    {
        return $this->hasOne(Item::class,'item_id','item_id');
    }

}
