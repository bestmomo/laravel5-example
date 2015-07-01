<?php

return [
	'email-title' => 'Vérification d\'Email',
	'email-intro'=> 'Pour valider votre email ',
	'email-link' => 'Cliquez sur ce lien ',
	'message' => 'Merci de vous être enregistré ! Regardez dans vos emails en réception.',
	'success' => 'Vous avez maintenant confirmé votre compte ! Vous pouvez vous connecter.',
	'again' => 'Vous devez valider votre email avant de pouvoir accéder à ce site. ' .
                '<br>Si vous n\'avez pas reçu l\'email de confirmation veuillez consulter votre dossier de spams.' .
                '<br>Pour recevoir à nouveau un email de confirmation <a href="' . url('auth/resend') . '" class="alert-link">cliquez ici</a>.', 
    'resend' => 'Un email de confirmation vous a été envoyé. Regardez dans vos emails en réception.'
];