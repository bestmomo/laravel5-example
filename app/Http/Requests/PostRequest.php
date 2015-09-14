<?php namespace App\Http\Requests;

use App\Models\Post;

class PostRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$id = $this->blog ? ',' . $this->blog : '';
		return [
			'title' => 'required|max:255',
			'summary' => 'required|max:65000',
			'content' => 'required|max:65000',
			'slug' => 'required|unique:posts,slug' . $id,
			'tags' => 'tags'
		];
	}

}