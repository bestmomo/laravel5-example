<?php namespace App\Http\Controllers;

use App\Gestion\ContactGestion;
use App\Gestion\UserGestion;
use App\Gestion\BlogGestion;
use App\Gestion\CommentGestion;
use App\Services\Medias;

class AdminController extends Controller {

	/**
	 * Show the admin panel.
	 *
	 * @Get("admin", as="admin")
	 * @Middleware("admin")
	 *
	 * @param  App\Gestion\ContactGestion $contact_gestion
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @param  App\Gestion\BlogGestion $blog_gestion
	 * @param  App\Gestion\CommentGestion $comment_gestion
	 * @return Response
	 */
	public function admin(
		ContactGestion $contact_gestion, 
		UserGestion $user_gestion,
		BlogGestion $blog_gestion,
		CommentGestion $comment_gestion)
	{	
		$nbrMessages = $contact_gestion->getNumberVu();
		$nbrUsers = $user_gestion->getNumberVu();
		$nbrPosts = $blog_gestion->getNumberVu();
		$nbrComments = $comment_gestion->getNumberVu();
		$statut = $user_gestion->getStatut();
		return view('back.index', compact('nbrMessages', 'nbrUsers', 'nbrPosts', 'nbrComments', 'statut'));
	}

	/**
	 * Show the media panel.
	 *
	 * @Get("medias", as="medias")
	 * @Middleware("redac")
	 *
	 * @param  UserGestion $user_gestion
	 */
	public function filemanager(
		UserGestion $user_gestion)
	{
		$statut = $user_gestion->getStatut();
		$url = Medias::getUrl($statut, $user_gestion);
		return view('back.filemanager', compact('statut', 'url'));
	}

}
