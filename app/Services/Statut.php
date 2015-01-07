<?php namespace App\Services;

use Illuminate\Session\SessionManager;

class Statut  {

	/**
	 * The SessionManager instance.
	 *
	 * @var Illuminate\Session\SessionManager
	 */
	protected $session;

	/**
	 * Create a new CommentController instance.
	 *
	 * @param  Illuminate\Session\SessionManager $session
	 * @return void
	 */
	public function __construct(
		SessionManager $session)
	{
		$this->session = $session;
	}

	/**
	 * Set the login user statut
	 * 
	 * @param  App\Models\User $user
	 * @return void
	 */
	public function setLoginStatut($user)
	{
		$this->session->put('statut', $user->role->slug);
	}

	/**
	 * Set the visitor user statut
	 * 
	 * @return void
	 */
	public function setVisitorStatut()
	{
		$this->session->put('statut', 'visitor');
	}

}