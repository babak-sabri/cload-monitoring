<?php

namespace App\Models\Users;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory;
	
	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table	= 'users';
	
	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey	= 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'first_name',
		'last_name',
		'user_name',
		'email',
		'cellphone',
		'job_title',
		'organization',
		'gender',
		'timezone',
		'profile_image',
		'how_to_find',
		'email_verified_at',
		'cellphone_verified_at',
		'birth_date',
		'language',
		'calendar_type',
		'expiration_date'
    ];
			
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	public function getDateFormat()
	{
		return 'U';
	}
	
	/**
     * Validate the password of the user for the Passport password grant.
     *
     * @param  string  $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }
}
