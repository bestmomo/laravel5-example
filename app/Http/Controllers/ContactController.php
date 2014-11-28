<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Gestion\ContactGestion;
use App\Gestion\UserGestion;

/**
 * @Resource("contact", except={"show", "edit"})
 * @Middleware("admin", except={"create","store"})
 */
class ContactController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @param  ContactGestion $contact_gestion
	 * @return Response
	 */
	public function index(
		ContactGestion $contact_gestion)
	{
		$messages = $contact_gestion->index();
		return view('back.messages.index', compact('messages'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('front.contact');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Gestion\ContactGestion $contact_gestion
	 * @param  ContactRequest $contactrequest
	 * @param  Illuminate\Http\Request $request
	 * @return Response
	 */
	public function store(
		ContactGestion $contact_gestion,
		ContactRequest $contactrequest,
		Request $request)
	{
		// Vérification pot de miel
		if($request->get('user') != '') return redirect('/');	
		// Traitement	
		$contact_gestion->store($request->all());
		return redirect('/')->with('ok', trans('front/contact.ok'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Gestion\ContactGestion $contact_gestion
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(
		ContactGestion $contact_gestion,
		Request $request, 		 
		$id)
	{
		$contact_gestion->update($request->input('vu'), $id);
		return response()->json(['statut' => 'ok']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  App\Gestion\ContactGestion $contact_gestion
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(
		ContactGestion $contact_gestion, 
		$id)
	{
		$contact_gestion->destroy($id);
		return redirect('contact');
	}

}
