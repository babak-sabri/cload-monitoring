<?php
namespace App\Base;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	protected $searchFields	= [];
	
	public function getDateFormat()
	{
		return 'U';
	}
}