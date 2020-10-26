<?php
namespace App\Models\Graph;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GraphCache extends BaseModel
{
	use HasFactory;
	/**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'graphs_cache';
	
	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'graph_cache_id';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'user_id',
		'hostid',
		'graphid',
		'templateid',
		'graph_name',
		'template_name',
	];
	
	public $timestamps	= false;
}