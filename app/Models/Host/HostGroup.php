<?php

namespace App\Models\Host;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostGroup extends BaseModel
{
	use HasFactory;
	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'host_groups';
	
	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'host_group_id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'group_id'
	];
	
	public $timestamps	= false;
}