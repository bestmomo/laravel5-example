<?php namespace App\Http\Requests;

class ContactRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required|max:100',
			'email' => 'required|email',
			'message' => 'required|max:1000'
		];
	}

}
