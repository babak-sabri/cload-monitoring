<?php

namespace App\Models\Currency;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends BaseModel
{
	use HasFactory;
	
	protected $table		= 'currencies';
	
	protected $primaryKey	= 'currency_id';
	
	protected $fillable	= [
		
		'currency_title',
		'currency_price',
	];
	
}
