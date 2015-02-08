<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>{{ $title }}</h2>

		<div>
			{!! $intro . link_to('password/reset/' . $token, $link) !!}.<br>
			{{ $expire . config('auth.reminder.expire', 60) . $minutes}}.
		</div>
	</body>
</html>
