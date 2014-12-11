<?php namespace App\Repositories;

use App\Models\Contact;

class ContactRepository extends BaseRepository {

	/**
	 * Create a new ContactRepository instance.
	 *
	 * @param  App\Models\Contact $contact
	 * @return void
	 */
	public function __construct(Contact $contact)
	{
		$this->model = $contact;
	}

	/**
	 * Get contacts collection.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function index()
	{
		return $this->model
		->orderBy('vu', 'asc')
		->orderBy('created_at', 'desc')
		->get();
	}

	/**
	 * Store a contact.
	 *
	 * @param  array $inputs
	 * @return void
	 */
	public function store($inputs)
	{
		$contact = new $this->model;		
		$contact->nom = $inputs['nom'];
		$contact->email = $inputs['email'];
		$contact->texte = $inputs['message'];
		$contact->save();
	}

	/**
	 * Update a contact.
	 *
	 * @param  bool  $vu
	 * @param  int   $id
	 * @return void
	 */
	public function update($vu, $id)
	{
		$contact = $this->model->findOrFail($id);
		$contact->vu = $vu == 'true';
		$contact->save();
	}

}