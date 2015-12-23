<?php namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;

class CommentController extends Controller {

	/**
	 * The CommentRepository instance.
	 *
	 * @var App\Repositories\CommentRepository
	 */
	protected $comment_gestion;

	/**
	 * Create a new CommentController instance.
	 *
	 * @param  App\Repositories\CommentRepository $comment_gestion
	 * @return void
	 */
	public function __construct(
		CommentRepository $comment_gestion)
	{
		$this->comment_gestion = $comment_gestion;

		$this->middleware('admin', ['except' => ['store', 'edit', 'update', 'destroy']]);
		$this->middleware('auth', ['only' => ['store', 'update', 'destroy']]);
		$this->middleware('ajax', ['only' => ['updateSeen', 'update', 'valid']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$comments = $this->comment_gestion->index(4);
		$links = $comments->render();

		return view('back.comments.index', compact('comments', 'links'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\requests\CommentRequest $request
	 * @return Response
	 */
	public function store(
		CommentRequest $request)
	{
		$this->comment_gestion->store($request->all(), $request->user()->id);

		if($request->user()->valid)
		{
			return redirect()->back();
		}

		return redirect()->back()->with('warning', trans('front/blog.warning'));
	}		

	/**
	 * Update "seen" in the specified resource in storage.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function updateSeen(
		Request $request, 
		$id)
	{
		$this->comment_gestion->update($request->input('seen'), $id);

		return response()->json();		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\requests\CommentRequest $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(
		CommentRequest $request, 
		$id)
	{
		$id = $request->segment(2);
		$content = $request->input('comments' . $id);
		$this->comment_gestion->updateContent($content, $id);

		return response()->json(['id' => $id, 'content' => $content]);	
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(
		Request $request, 
		$id)
	{
		$this->comment_gestion->destroy($id);

		if($request->ajax())
		{
			return response()->json(['id' => $id]);
		}

		return redirect('comment');
	}

	/**
	 * Validate an user
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  App\Repositories\UserRepository $user_gestion
	 * @param  int  $id
     * @return Response
	 */
	public function valid(
		Request $request, 
		UserRepository $user_gestion, 
		$id)
	{
		$user_gestion->valid($request->input('valid'), $id);
		
		return response()->json();		
	}

}
