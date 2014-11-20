<?php namespace App\Gestion;

class BaseGestion {

	/**
	 * The Model instance.
	 *
	 * @var Illuminate\Database\Eloquent\Model
	 */
	protected $model;

	/**
	 * Get number of "vu".
	 *
	 * @return void
	 */
	public function getNumberVu()
	{
		return $this->model->whereVu(0)->count();
	}

	/**
	 * Destroy a model.
	 *
	 * @param  int $id
	 * @return void
	 */
	public function destroy($id)
	{
		$this->model->findOrFail($id)->delete();
	}

}