<?php
namespace App\Base;

abstract class BaseRepository
{
	protected $model;
	
	public function __construct($model=null)
    {
		$this->model = $model;
    }
	
	public function __call($method, $args)
    {
        return call_user_func_array([$this->model, $method], $args);
    }

	public function insert(array $data)
	{
		$this->model->insert($data);
		return $this->model;
	}
	
	/**
     * Get`s all entities.
     *
     * @return mixed
     */
	public function all()
	{
		return $this->model->all();
	}

	/**
	 * Deletes an entity.
	 *
	 * @param $id
	 * @return int|mixed
	 */
	public function delete($id)
	{
		return $this->model->destroy($id);
	}

	/**
	 * Get`s an entity by it's ID
	 * @param $id
	 * @return mixed
	 */
	public function get($id)
	{
		return $this->model->find($id);
	}

	/**
	 * Updates an entity.
	 *
	 * @param $id
	 * @param $data
	 * @return mixed
	 */
	public function update($id, $data)
	{
		return $this->model->find($id)->update($data);
	}

	/**
	 * find an entity or fail.
	 *
	 * @param $id
	 * @return mixed
	 */
	public function findOrFail($id)
	{
		return $this->model->findORFail($id);
	}
}