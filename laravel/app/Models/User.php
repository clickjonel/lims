<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    protected $connection = 'dohis';
    protected $table = 'dohis_user';
    protected $primaryKey = 'user_id';

    protected $hidden = [
        'password',
        'biometrics_id',
        'user_code',
        'user_id_number',
        'username',
        'prefix',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'suffix_title',
        'account_status',
        'contact_number',
        'created_at',
        'deleted_at',
        'id_status',
        'id_date_print',
        'online_status',
        'refresh_token',
        'token',
        'uid',
        'updated_at',
        'email_address'
    ];

    protected $appends = [
        'full_name',
    ];

    public function getFullNameAttribute():string
    {
         $fullName = trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);

        if (!empty($this->suffix)) {
            $fullName .= ', ' . $this->suffix;
        }

        if (!empty($this->prefix)) {
            $fullName = $this->prefix . ' ' . $fullName;
        }

        return $fullName;
    }

    public function getDivisionAttribute():Division
    {
        return $this->assignment->division;
    }

    public function getSectionAttribute():UserAssignment
    {
        return $this->assignment->section;
    }

    public function getUnitAttribute():Unit
    {
        return $this->assignment->unit;
    }

    public function assignment()
    {
        return $this->hasOne(UserAssignment::class,'user_id','user_id');
    }

    public function position():UserAssignment
    {
        return $this->assignment->item;
    }

    public function properties()
    {
        return $this->hasMany(PropertyCurrentUser::class,'user_id');
    }
}
