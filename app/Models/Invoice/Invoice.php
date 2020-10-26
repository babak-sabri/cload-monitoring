<?php
namespace App\Models\Invoice;

use App\Base\BaseModel;
use App\Models\Traits\FetchDataTrait;
use App\Helpers\PaginateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends BaseModel
{
	use FetchDataTrait, HasFactory;
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table	= 'invoices';
	
	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey	= 'invoice_id';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable	= [
		'amount',
		'description',
	];
	
	/**
	 * The attributes that can be search.
	 *
	 * @var array
	 */
	protected $searchale	= [
		'invoice_id'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'description'	=> [
			'type'	=> PaginateHelper::LIKE_STRING_TYPE
		],
		'tracking_code'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'pay_type'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		]
	];
	
	public function scopeUserInvoices($query, $userId)
	{
		$query->where('user_id', $userId);
		return $query;
	}
}
