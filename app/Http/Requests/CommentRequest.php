<?php namespace App\Http\Requests;

class CommentRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$id = $this->segment(2);
		return [
			'commentaire' . $id => 'required|max:65000',
		];
	}

}
