<?php namespace App\Http\Requests;

class SearchRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'search' => 'required|max:100',
		];
	}

}
