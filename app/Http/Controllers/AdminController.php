<?php namespace App\Http\Controllers;

use App\Repositories\ContactRepository;
use App\Repositories\UserRepository;
use App\Repositories\BlogRepository;
use App\Repositories\CommentRepository;

class AdminController extends Controller {

    /**
     * The UserRepository instance.
     *
     * @var App\Repositories\UserRepository
     */
    protected $user_gestion;

    /**
     * Create a new AdminController instance.
     *
     * @param  App\Repositories\UserRepository $user_gestion
     * @return void
     */
    public function __construct(UserRepository $user_gestion)
    {
		$this->user_gestion = $user_gestion;
    }

	/**
	* Show the admin panel.
	*
	* @param  App\Repositories\ContactRepository $contact_gestion
	* @param  App\Repositories\BlogRepository $blog_gestion
	* @param  App\Repositories\CommentRepository $comment_gestion
	* @return Response
	*/
	public function admin(
		ContactRepository $contact_gestion, 
		BlogRepository $blog_gestion,
		CommentRepository $comment_gestion)
	{	
		$nbrMessages = $contact_gestion->getNumber();
		$nbrUsers = $this->user_gestion->getNumber();
		$nbrPosts = $blog_gestion->getNumber();
		$nbrComments = $comment_gestion->getNumber();

		return view('back.index', compact('nbrMessages', 'nbrUsers', 'nbrPosts', 'nbrComments'));
	}

	/**
	 * Show the media panel.
	 *
     * @return Response
	 */
	public function filemanager()
	{
		$url = config('medias.url') . '?langCode=' . config('app.locale');
		
		return view('back.filemanager', compact('url'));

	}

}
