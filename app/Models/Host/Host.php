<?php
namespace App\Models\Host;

use App\Base\BaseModel;
use App\Models\Traits\FetchDataTrait;
use App\Helpers\PaginateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\Host\HostScope;

class Host extends BaseModel
{
	use FetchDataTrait, HasFactory;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'hosts';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey = 'hostid';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'host',
		'api_host_name'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'api_host_name'
	];

	/**
	 * The attributes that can be search.
	 *
	 * @var array
	 */
	protected $searchale	= [
		'hostid'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'host'	=> [
			'type'	=> PaginateHelper::LIKE_STRING_TYPE
		]
	];

	protected static function booted()
	{
		static::addGlobalScope(new HostScope());
	}
	
	public function hostInterfaces()
	{
		return $this->hasMany(HostInterface::class, 'hostid', 'hostid');
	}
	
	public function hostGroups()
	{
		return $this->hasMany(HostGroup::class, 'hostid', 'hostid');
	}
	
	public function hostTemplates()
	{
		return $this->hasMany(HostTemplate::class, 'hostid', 'hostid');
	}
	
	public function hostMacros()
	{
		return $this->hasMany(HostMacro::class, 'hostid', 'hostid');
	}
}