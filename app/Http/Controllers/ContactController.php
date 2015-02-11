<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Repositories\ContactRepository;

class ContactController extends Controller {

	/**
	 * Create a new ContactController instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('admin', ['except' => ['create', 'store']]);
		$this->middleware('ajax', ['only' => 'update']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param  ContactRepository $contact_gestion
	 * @return Response
	 */
	public function index(
		ContactRepository $contact_gestion)
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
	 * @param  App\Repositories\ContactRepository $contact_gestion
	 * @param  ContactRequest $request
	 * @return Response
	 */
	public function store(
		ContactRepository $contact_gestion,
		ContactRequest $request)
	{
		$contact_gestion->store($request->all());

		return redirect('/')->with('ok', trans('front/contact.ok'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Repositories\ContactRepository $contact_gestion
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(
		ContactRepository $contact_gestion,
		Request $request, 		 
		$id)
	{
		$contact_gestion->update($request->input('seen'), $id);

		return response()->json(['statut' => 'ok']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  App\Repositories\ContactRepository $contact_gestion
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(
		ContactRepository $contact_gestion, 
		$id)
	{
		$contact_gestion->destroy($id);
		
		return redirect('contact');
	}

}
