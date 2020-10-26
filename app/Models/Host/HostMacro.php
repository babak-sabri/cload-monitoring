<?php

namespace App\Models\Host;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HostMacro extends BaseModel
{
	use HasFactory;
	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'host_macros';
	
	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'host_macro_id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'macro',
		'macro_value'
	];
	
	public $timestamps	= false;
}