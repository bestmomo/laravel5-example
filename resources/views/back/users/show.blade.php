@extends('back.template')

@section('main')

 <!-- Entête de page -->
  {!!  HTML::backEntete(
  trans('back/users.dashboard'),
  'user',
  link_to('user', trans('back/users.Users')) . ' / ' . trans('back/users.card')
  ) !!}

	<p>{{ trans('back/users.name') . ' : ' .  $user->username }}</p>
	<p>{{ trans('back/users.email') . ' : ' .  $user->email }}</p>
	<p>{{ trans('back/users.role') . ' : ' .  $user->role->titre }}</p>

@stop