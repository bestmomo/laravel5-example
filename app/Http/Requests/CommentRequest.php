<?php namespace App\Http\Requests;

class CommentRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$id = $this->comment;
		return [
			'comments' . $id => 'required|max:65000',
		];
	}

}
