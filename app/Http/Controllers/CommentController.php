<?php namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\Gestion\CommentGestion;
use App\Gestion\UserGestion;
use App\Gestion\BlogGestion;

/**
 * @Resource("comment", except={"create","show"})
 * @Middleware("admin", except={"store","edit","update","destroy"})
 */
class CommentController extends Controller {

	/**
	 * The CommentGestion instance.
	 *
	 * @var App\Gestion\CommentGestion
	 */
	protected $comment_gestion;

	/**
	 * Create a new CommentController instance.
	 *
	 * @param  App\Gestion\CommentGestion $comment_gestion
	 * @return void
	 */
	public function __construct(
		CommentGestion $comment_gestion)
	{
		$this->comment_gestion = $comment_gestion;
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
	 * @Middleware("auth")
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
	 * @put("commentvu/{id}")
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
	 * @Middleware("auth")
	 *
	 * @param  App\requests\CommentRequest $commentrequest
	 * @param  Illuminate\Http\Request $request
	 * @param  App\Services\Purifier $purifier
	 * @param  App\Gestion\BlogGestion $blog_gestion
	 * @param  int  $id
	 * @return Response
	 */
	public function update(
		CommentRequest $commentrequest,
		Request $request, 
		Purifier $purifier,
		BlogGestion $blog_gestion,
		$id)
	{
		$id = $request->segment(2);
		$commentaire = $request->get('commentaire' . $id);
		$commentaire = $purifier->clean($commentaire);
		$this->comment_gestion->updateContenu($commentaire, $id);
		return response()->json(['id' => $id, 'contenu' => $commentaire]);	
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @Middleware("auth")
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
	 * @Put("/uservalid/{id}")
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @param  int  $id
     * @return Response
	 */
	public function valide(
		Request $request, 
		UserGestion $user_gestion, 
		$id)
	{
		$user_gestion->valide($request->input('valid'), $id);
		return response()->json(['statut' => 'ok']);		
	}

}
