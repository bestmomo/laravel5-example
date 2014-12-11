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
		->orderBy('vu', 'asc')
		->orderBy('created_at', 'desc')
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
		$comment->contenu = $inputs['commentaire'];
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
 	public function updateContenu($commentaire, $id)
	{
		$comment = $this->model->find($id);		
		$comment->contenu = $commentaire;
		$comment->save();
	}

	/**
	 * Update a comment.
	 *
	 * @param  bool  $vu
	 * @param  int   $id
	 * @return void
	 */
	public function update($vu, $id)
	{
		$comment = $this->model->findOrFail($id);
		$comment->vu = $vu == 'true';
		$comment->save();
	}

	/**
	 * Get number of comments.
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->model->count();
	}

	/**
	 * Get a comment.
	 *
	 * @param  int   $id
	 * @return Illuminate\Support\Collection
	 */
	public function getComment($id)
	{
		return $this->model->findOrFail($id);
	}

}