<?php namespace App\Gestion;

use App\Models\Post, App\Models\Tag, App\Models\Comment;

class BlogGestion extends BaseGestion{

	/**
	 * The Tag instance.
	 *
	 * @var App\Models\Tag
	 */
	protected $tag;

	/**
	 * The Comment instance.
	 *
	 * @var App\Models\Comment
	 */
	protected $comment;

	/**
	 * Create a new BlogGestion instance.
	 *
	 * @param  App\Models\Post $post
	 * @param  App\Models\Tag $tag
	 * @param  App\Models\Comment $comment
	 * @return void
	 */
	public function __construct(
		Post $post, 
		Tag $tag, 
		Comment $comment)
	{
		$this->model = $post;
		$this->tag = $tag;
		$this->comment = $comment;
	}

	/**
	 * Create or update a post.
	 *
	 * @param  App\Models\Post $post
	 * @param  array  $inputs
	 * @param  bool   $user_id
	 * @return App\Models\Post
	 */
  private function savePost($post, $inputs, $user_id = null)
	{	
		$post->titre = $inputs['titre'];
		$post->sommaire = $inputs['sommaire'];	
		$post->contenu = $inputs['contenu'];	
		$post->slug = $inputs['slug'];
		$post->actif = isset($inputs['actif']);	
		if($user_id) $post->user_id = $user_id;
		$post->save();
		return $post;
	}

	/**
	 * Get count.
	 *
	 * @param  int $user_id
	 * @return int
	 */
	public function count($user_id = false)
	{
		if($user_id)
		{
			return $this->model->where('user_id', $user_id)->count();
		} 
		return $this->model->count();		
	}

	/**
	 * Get post collection.
	 *
	 * @param  int  $n
	 * @return Illuminate\Support\Collection
	 */
	public function indexFront($n)
	{
		return $this->model
		->select('id', 'created_at', 'updated_at', 'titre', 'slug', 'user_id', 'sommaire')
		->whereActif(true)
		->with('user')
		->orderBy('created_at', 'desc')
		->paginate($n);
	}

	/**
	 * Get model collection.
	 *
	 * @param  int  $n
	 * @param  int  $id
	 * @return Illuminate\Support\Collection
	 */
	public function indexTag($n, $id)
	{
		return $this->model
		->select('id', 'created_at', 'updated_at', 'titre', 'slug', 'user_id', 'sommaire')
		->whereActif(true)
		->with('user')
		->orderBy('created_at', 'desc')
		->whereHas('tags', function($q) use($id) { $q->where('tags.id', $id);	})
		->paginate($n);
	}

	/**
	 * Get search collection.
	 *
	 * @param  int  $n
	 * @param  string  $search
	 * @return Illuminate\Support\Collection
	 */
	public function search($n, $search)
	{
		return $this->model
		->select('id', 'created_at', 'updated_at', 'titre', 'slug', 'user_id', 'sommaire')
		->where('sommaire', 'like', "%$search%")
		->orWhere('contenu', 'like', "%$search%")
		->with('user')
		->orderBy('created_at', 'desc')
		->paginate($n);
	}

	/**
	 * Get post collection.
	 *
	 * @param  int     $n
	 * @param  int     $user_id
	 * @param  string  $orderby
	 * @param  string  $direction
	 * @return Illuminate\Support\Collection
	 */
	public function index($n, $user_id = null, $orderby = 'created_at', $direction = 'desc')
	{
		$query = $this->model
		->select('posts.id', 'posts.created_at', 'titre', 'posts.vu', 'actif', 'user_id', 'slug', 'username')
		->join('users', 'users.id', '=', 'posts.user_id')
		->orderBy($orderby, $direction);
		if($user_id) 
		{
			$query->where('user_id', $user_id);
		} 
		return $query->paginate($n);
	}

	/**
	 * Get post collection.
	 *
	 * @param  string  $slug
	 * @return Illuminate\Support\Collection
	 */
	public function show($slug)
	{
		$post = $this->model->with('user', 'tags')->whereSlug($slug)->firstOrFail();
		$comments = $this->comment
		->wherePost_id($post->id)
		->with('user')
		->whereHas('user', function($q) {	$q->whereValid(true);	})
		->get();
		return compact('post', 'comments');
	}

	/**
	 * Get post collection.
	 *
	 * @param  int  $id
	 * @return Illuminate\Support\Collection
	 */
	public function edit($id)
	{
		$post = $this->model->with('tags')->findOrFail($id);
		$tags = [];
		foreach($post->tags as $tag) {
			array_push($tags, $tag->tag);
		}
		return compact('post', 'tags');
	}

	/**
	 * Update a post.
	 *
	 * @param  array  $inputs
	 * @param  int    $id
	 * @return void
	 */
	public function update($inputs, $id)
	{
		$post = $this->model->findOrFail($id);
		$post = $this->savePost($post, $inputs);

		// Gestion éventuelle des tags
		$tags_id = [];
		if(array_key_exists('tags',  $inputs) && $inputs['tags'] != '') {
			$tags = explode(',', $inputs['tags']);
			foreach ($tags as $tag) {
				$tag_ref = $this->tag->whereTag($tag)->first();
				if(is_null($tag_ref)) {
					$tag_ref = new $this->tag();	
					$tag_ref->tag = $tag;
					$tag_ref->save();
				} 
				array_push($tags_id, $tag_ref->id);
			}
		}
		$post->tags()->sync($tags_id);
	}

	/**
	 * Update "vu" in post.
	 *
	 * @param  array  $inputs
	 * @param  int    $id
	 * @return void
	 */
	public function updateVu($inputs, $id)
	{
		$post = $this->model->findOrFail($id);
		$post->vu = $inputs['vu'] == 'true';	
		$post->save();			
	}

	/**
	 * Update "actif" in post.
	 *
	 * @param  array  $inputs
	 * @param  int    $id
	 * @return void
	 */
	public function updateActif($inputs, $id)
	{
		$post = $this->model->findOrFail($id);
		$post->actif = $inputs['actif'] == 'true';	
		$post->save();			
	}

	/**
	 * Create a post.
	 *
	 * @param  array  $inputs
	 * @param  int    $user_id
	 * @return void
	 */
	public function store($inputs, $user_id)
	{
		$post = new $this->model;	
		$post = $this->savePost($post, $inputs, $user_id);

		// Gestion éventuelle des tags
		if(array_key_exists('tags',  $inputs) && $inputs['tags'] != '') {
			$tags = explode(',', $inputs['tags']);
			foreach ($tags as $tag) {
				$tag_ref = $this->tag->whereTag($tag)->first();
				if(is_null($tag_ref)) {
					$tag_ref = new $this->tag();	
					$tag_ref->tag = $tag;
					$post->tags()->save($tag_ref);
				} else {
					$post->tags()->attach($tag_ref->id);
				}
			}
		}

		// Envisager purge des tags orphelins
	}

	/**
	 * Destroy a post.
	 *
	 * @param  int $id
	 * @return void
	 */
	public function destroy($id)
	{
		$model = $this->model->findOrFail($id);
		$model->tags()->detach();
		$model->delete();
	}	

	/**
	 * Get post slug.
	 *
	 * @param  int  $comment_id
	 * @return int
	 */
	public function getSlug($comment_id)
	{
		return $this->comment->findOrFail($comment_id)->post->slug;
	}

	/**
	 * Get tag name by id.
	 *
	 * @param  int  $tag_id
	 * @return string
	 */
	public function getTagById($tag_id)
	{
		return $this->tag->findOrFail($tag_id)->tag;
	}

}