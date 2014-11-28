<?php namespace App\Http\Controllers;

use App\Gestion\ContactGestion;
use App\Gestion\UserGestion;
use App\Gestion\BlogGestion;
use App\Gestion\CommentGestion;
use App\Services\Medias;

class AdminController extends Controller {

    /**
     * The UserGestion instance.
     *
     * @var App\Gestion\UserGestion
     */
    protected $user_gestion;

    /**
     * Create a new AdminController instance.
     *
     * @param  App\Gestion\UserGestion $user_gestion
     * @return void
     */
    public function __construct(UserGestion $user_gestion)
    {
			$this->user_gestion = $user_gestion;
			$user_gestion->getStatut();
    }

    /**
	 * Show the admin panel.
	 *
	 * @Get("admin", as="admin")
	 * @Middleware("admin")
	 *
	 * @param  App\Gestion\ContactGestion $contact_gestion
	 * @param  App\Gestion\BlogGestion $blog_gestion
	 * @param  App\Gestion\CommentGestion $comment_gestion
	 * @return Response
	 */
	public function admin(
		ContactGestion $contact_gestion, 
		BlogGestion $blog_gestion,
		CommentGestion $comment_gestion)
	{	
		$nbrMessages = $contact_gestion->getNumberVu();
		$nbrUsers = $this->user_gestion->getNumberVu();
		$nbrPosts = $blog_gestion->getNumberVu();
		$nbrComments = $comment_gestion->getNumberVu();
		return view('back.index', compact('nbrMessages', 'nbrUsers', 'nbrPosts', 'nbrComments'));
	}

	/**
	 * Show the media panel.
	 *
	 * @Get("medias", as="medias")
	 * @Middleware("redac")
	 *
	 * @param  UserGestion $user_gestion
     * @return Response
	 */
	public function filemanager()
	{
		$url = Medias::getUrl($this->user_gestion);
		return view('back.filemanager', compact('url'));
	}

}
