<?php
namespace App\Models\AuditLog;

use App\Base\BaseModel;

class AuditLog extends BaseModel
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'audit_logs';

	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey = 'audit_log_id';

	public $timestamps	= false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'entity_id',
		'resource',
		'action',
		'details',
		'description',
		'action_time'
	];
}