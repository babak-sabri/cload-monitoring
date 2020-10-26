<?php

namespace App\Models\Host;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostInterface extends BaseModel
{
	use HasFactory;
	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'host_interfaces';
	
	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'host_interface_id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'type',
		'main',
		'useip',
		'ip',
		'dns',
		'port',
		'details'
	];
	
	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'details' => 'array',
	];
	
	public $timestamps	= false;
}