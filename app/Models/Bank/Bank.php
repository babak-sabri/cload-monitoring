<?php
namespace App\Models\Bank;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends BaseModel
{
	use HasFactory;
	
	protected $table		= 'bank';
	
	protected $primaryKey	= 'user_id';
	
	protected $fillable	= [
		'amount',
	];
	
}
