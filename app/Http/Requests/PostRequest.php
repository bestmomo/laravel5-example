<?php namespace App\Http\Requests;

class PostRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$id = \Request::segment(2) ? ',' . \Request::segment(2) : '';
		return [
			'titre' => 'required|max:255',
			'sommaire' => 'required|max:65000',
			'contenu' => 'required|max:65000',
			'slug' => 'required|unique:posts,slug' . $id,
			'tags' => 'tags'
		];
	}

}