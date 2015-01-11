<?php namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;
use App\Repositories\BlogRepository;

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
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$comments = $this->comment_gestion->index(4);
		$links = str_replace('/?', '?', $comments->render());

		return view('back.commentaires.index', compact('comments', 'links'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\requests\CommentRequest $commentrequest
	 * @param  Illuminate\Http\Request $request
	 * @param  Illuminate\Contracts\Auth\Guard $auth
	 * @return Response
	 */
	public function store(
		CommentRequest $commentrequest,
		Request $request,
		Guard $auth)
	{
		$inputs = $request->all();
		$this->comment_gestion->store($inputs, $auth->user()->id);

		if($auth->user()->valid)
		{
			return redirect()->back();
		}

		return redirect()->back()->with('warning', trans('front/blog.warning'));
	}		

	/**
	 * Update "vu" in the specified resource in storage.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function updateVu(
		Request $request, 
		$id)
	{
		$this->comment_gestion->update($request->input('vu'), $id);

		return response()->json(['statut' => 'ok']);		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\requests\CommentRequest $commentrequest
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(
		CommentRequest $commentrequest,
		Request $request, 
		$id)
	{
		$id = $request->segment(2);
		$commentaire = $request->get('commentaire' . $id);
		$this->comment_gestion->updateContenu($commentaire, $id);

		return response()->json(['id' => $id, 'contenu' => $commentaire]);	
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request, $id)
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
	public function valide(
		Request $request, 
		UserRepository $user_gestion, 
		$id)
	{
		$user_gestion->valide($request->input('valid'), $id);
		
		return response()->json(['statut' => 'ok']);		
	}

}
