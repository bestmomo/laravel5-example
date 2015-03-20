<?php namespace App\Http\Requests;

use App\Models\Post;

class PostRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		if(!parent::authorize()) return false;

		if($this->blog)
		{
			if($this->user()->isAdmin()) return true;

			return Post::where('id', $this->blog)
					   ->where('user_id', $this->user()->id)->exists();
		}

		return true;
	}

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