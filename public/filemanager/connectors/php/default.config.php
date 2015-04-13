<?php
/**
 *	Filemanager PHP connector
 *  This file should at least declare auth() function 
 *  and instantiate the Filemanager as '$fm'
 *  
 *  IMPORTANT : by default Read and Write access is granted to everyone
 *  Copy/paste this file to 'user.config.php' file to implement your own auth() function
 *  to grant access to wanted users only
 *
 *	filemanager.php
 *	use for ckeditor filemanager
 *
 *	@license	MIT License
 *  @author		Simon Georget <simon (at) linea21 (dot) com>
 *	@copyright	Authors
 */

require getcwd() . '/../../../../bootstrap/autoload.php';
$app = require_once getcwd() . '/../../../../bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

$response = $kernel->handle(
  $request = Illuminate\Http\Request::capture()
);

$id = $app['encrypter']->decrypt($_COOKIE[$app['config']['session.cookie']]);
$app['session']->driver()->setId($id);
$app['session']->driver()->start();

// Check if user in authentified
if(!$app['auth']->check()) 
{
  $laravelAuth = false;
} else {

  $laravelAuth = $app['auth']->user()->isNotUser();

  // Check for redactor
  if($laravelAuth && !$app['auth']->user()->isAdmin()) {

    $redactor = strtolower(strtr(utf8_decode($app['auth']->user()->username), 
      utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 
      'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
    ));

  }
}

/**
 *	Check if user is authorized
 *	
 *
 *	@return boolean true if access granted, false if no access
 */
function auth() 
{
  return $GLOBALS['laravelAuth'];
}

// we instantiate the Filemanager
// 
// 

$folderPath = $app->basePath() . '/public/filemanager/userfiles/';

if(isset($redactor)) 
{
  $folderPath .= $redactor;
}

$fm = new Filemanager();

$fm->setFileRoot($folderPath);

?>