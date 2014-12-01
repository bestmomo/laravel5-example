@extends('back.template')

@section('main')

 <!-- Entête de page -->
  @include('back.partials.entete', ['titre' => trans('back/users.dashboard'), 'icone' => 'user', 'fil' => link_to('user', trans('back/users.Users')) . ' / ' . trans('back/users.card')])

	<p>{{ trans('back/users.name') . ' : ' .  $user->username }}</p>
	<p>{{ trans('back/users.email') . ' : ' .  $user->email }}</p>
	<p>{{ trans('back/users.role') . ' : ' .  $user->role->titre }}</p>

@stop