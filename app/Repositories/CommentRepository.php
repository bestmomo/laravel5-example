<?php namespace App\Repositories;

use App\Models\Comment;

class CommentRepository extends BaseRepository {

	/**
	 * Create a new CommentRepository instance.
	 *
	 * @param  App\Models\Comment $comment
	 * @return void
	 */
	public function __construct(Comment $comment)
	{
		$this->model = $comment;
	}

	/**
	 * Get comments collection.
	 *
	 * @param  int  $n
	 * @return Illuminate\Support\Collection
	 */
	public function index($n)
	{
		return $this->model
		->with('post', 'user')
		->oldest('seen')
		->latest()
		->paginate($n);
	}

	/**
	 * Store a comment.
	 *
	 * @param  array $inputs
	 * @param  int   $user_id
	 * @return void
	 */
 	public function store($inputs, $user_id)
	{
		$comment = new $this->model;	

		$comment->content = $inputs['comments'];
		$comment->post_id = $inputs['post_id'];
		$comment->user_id = $user_id;

		$comment->save();
	}

	/**
	 * Update a comment.
	 *
	 * @param  string $commentaire
	 * @param  int    $id
	 * @return void
	 */
 	public function updateContent($content, $id)
	{
		$comment = $this->getById($id);	

		$comment->content = $content;

		$comment->save();
	}

	/**
	 * Update a comment.
	 *
	 * @param  bool  $vu
	 * @param  int   $id
	 * @return void
	 */
	public function update($seen, $id)
	{
		$comment = $this->getById($id);

		$comment->seen = $seen == 'true';

		$comment->save();
	}

}