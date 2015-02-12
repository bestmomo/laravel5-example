<?php namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Requests\SearchRequest;
use App\Repositories\BlogRepository;
use App\Repositories\UserRepository;
use App\Services\Medias;

class BlogController extends Controller {

	/**
	 * The BlogRepository instance.
	 *
	 * @var App\Repositories\BlogRepository
	 */
	protected $blog_gestion;

	/**
	 * The UserRepository instance.
	 *
	 * @var App\Repositories\UserRepository
	 */
	protected $user_gestion;

	/**
	 * The pagination number.
	 *
	 * @var int
	 */
	protected $nbrPages;

	/**
	 * Create a new BlogController instance.
	 *
	 * @param  App\Repositories\BlogRepository $blog_gestion
	 * @param  App\Repositories\UserRepository $user_gestion
	 * @return void
	*/
	public function __construct(
		BlogRepository $blog_gestion,
    	UserRepository $user_gestion)
	{
    	$this->user_gestion = $user_gestion;
		$this->blog_gestion = $blog_gestion;
		$this->nbrPages = 2;

		$this->middleware('redac', ['except' => ['indexFront', 'show', 'tag', 'search']]);
		$this->middleware('ajax', ['only' => ['indexOrder', 'updateSeen', 'updateActive']]);
	}	

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexFront()
	{
    	$posts = $this->blog_gestion->indexFront($this->nbrPages);
    	$links = str_replace('/?', '?', $posts->render());

		return view('front.blog.index', compact('posts', 'links'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param  Illuminate\Contracts\Auth\Guard $auth
	 * @return Response
	 */
	public function index(Guard $auth)
	{
		$statut = $this->user_gestion->getStatut();
		$posts = $this->blog_gestion->index(10, $statut == 'admin' ? null : $auth->user()->id);

		$links = str_replace('/?', '?', $posts->render());

		return view('back.blog.index', compact('posts', 'links'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @return Response
	 */
	public function indexOrder(Request $request)
	{
		$statut = $this->user_gestion->getStatut();
		$posts = $this->blog_gestion->index(10, $statut == 'admin' ? null : $request->user()->id, $request->input('name'), $request->input('sens'));
		
		$links = str_replace('/?', '?', $posts->render());

		return response()->json([
			'view' => view('back.blog.table', compact('statut', 'posts'))->render(), 
			'links' => $links
		]);		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$url = Medias::getUrl($this->user_gestion);
		
		return view('back.blog.create')->with(compact('url'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\PostCreateRequest $request
	 * @return Response
	 */
	public function store(PostRequest $request)
	{
		$this->blog_gestion->store($request->all(), $request->user()->id);

		return redirect('blog')->with('ok', trans('back/blog.stored'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Illuminate\Contracts\Auth\Guard $auth	 
	 * @param  string  $slug
	 * @return Response
	 */
	public function show(
		Guard $auth, 
		$slug)
	{
		$user = $auth->user();

		return view('front.blog.show',  array_merge($this->blog_gestion->show($slug), compact('user')));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  App\Repositories\UserRepository $user_gestion
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(
		UserRepository $user_gestion, 
		$id)
	{
		$url = Medias::getUrl($user_gestion);

		return view('back.blog.edit',  array_merge($this->blog_gestion->edit($id), compact('url')));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\PostUpdateRequest $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(
		PostRequest $request,
		$id)
	{
		$this->blog_gestion->update($request->all(), $id);

		return redirect('blog')->with('ok', trans('back/blog.updated'));		
	}

	/**
	 * Update "vu" for the specified resource in storage.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function updateSeen(
		Request $request, 
		$id)
	{
		$this->blog_gestion->updateSeen($request->all(), $id);

		return response()->json(['statut' => 'ok']);
	}

	/**
	 * Update "active" for the specified resource in storage.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function updateActive(
		Request $request, 
		$id)
	{
		$this->blog_gestion->updateActive($request->all(), $id);

		return response()->json(['statut' => 'ok']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->blog_gestion->destroy($id);

		return redirect('blog')->with('ok', trans('back/blog.destroyed'));		
	}

	/**
	 * Get tagged posts
	 * 
	 * @param  Illuminate\Http\Request $request
	 * @return Response
	 */
	public function tag(Request $request)
	{
		$tag = $request->input('tag');
		$posts = $this->blog_gestion->indexTag($this->nbrPages, $tag);
		$links = str_replace('/?', '?', $posts->appends(compact('tag'))->render());
		$info = trans('front/blog.info-tag') . '<strong>' . $this->blog_gestion->getTagById($tag) . '</strong>';
		
		return view('front.blog.index', compact('posts', 'links', 'info'));
	}

	/**
	 * Find search in blog
	 *
	 * @param  App\Http\Requests\SearchRequest $request
	 * @return Response
	 */
	public function search(SearchRequest $request)
	{
		$search = $request->input('search');
		$posts = $this->blog_gestion->search($this->nbrPages, $search);
		$links = str_replace('/?', '?', $posts->appends(compact('search'))->render());
		$info = trans('front/blog.info-search') . '<strong>' . $search . '</strong>';
		
		return view('front.blog.index', compact('posts', 'links', 'info'));
	}

}
