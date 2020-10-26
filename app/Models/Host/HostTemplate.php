<?php

namespace App\Models\Host;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostTemplate extends BaseModel
{
	use HasFactory;
	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'host_templates';
	
	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'host_template_id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'template_id'
	];
	
	public $timestamps	= false;
}