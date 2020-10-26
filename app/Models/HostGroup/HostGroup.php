<?php
namespace App\Models\HostGroup;

use App\Base\BaseModel;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostGroup extends BaseModel
{
	use NodeTrait, HasFactory;
	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'hosts_groups';
	
	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'group_id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'group_name',
		'description',
		'user_id',
		'api_group_name',
		'decription'
	];
	
	public function scopeUserGroups($query, $userId)
	{
		$query->where('user_id', $userId);
		return $query;
	}
}