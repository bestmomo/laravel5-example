<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$id = \Request::segment(2);
		return [
			'titre' => 'required|max:255',
			'sommaire' => 'required|max:65000',
			'contenu' => 'required|max:65000',
			'slug' => 'required|unique:posts,slug,' . $id,
			'tags' => 'tags'
		];
	}

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

}
