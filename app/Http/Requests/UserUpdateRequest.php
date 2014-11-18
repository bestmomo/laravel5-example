<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$id = \Request::segment(2);
		return $rules = [
			'username' => 'required|max:30|alpha|unique:users,username,' . $id, 
			'email' => 'required|email|unique:users,email,' . $id
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
