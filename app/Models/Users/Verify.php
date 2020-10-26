<?php
namespace App\Models\Users;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Verify extends BaseModel
{
	use HasFactory;
	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table	= 'users_verification_codes';
	
	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey	= 'verification_id';
	
	public $timestamps	= false;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
		'user_id',
		'verification_code',
		'expiration_date'
    ];
}
