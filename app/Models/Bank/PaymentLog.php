<?php
namespace App\Models\Bank;

use App\Base\BaseModel;
use App\Models\Traits\FetchDataTrait;
use App\Helpers\PaginateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentLog extends BaseModel
{
	use FetchDataTrait, HasFactory;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table	= 'payments_logs';
	
	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey	= 'payment_log_id';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable	= [
		'user_id',
		'price',
		'entity_id',
		'pay_for'
	];
	
	/**
	 * The attributes that can be search.
	 *
	 * @var array
	 */
	protected $searchale	= [
		'payment_log_id'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'user_id'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'entity_id'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'pay_for'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		]
	];
	
	public function scopeUserPayments($query, $userId)
	{
		$query->where('user_id', $userId);
		return $query;
	}
}