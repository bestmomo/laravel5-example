<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$id = \Request::segment(2);
		return [
			'commentaire' . $id => 'required|max:65000',
		];
	}

}
