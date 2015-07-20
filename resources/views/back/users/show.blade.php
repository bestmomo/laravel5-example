@extends('back.template')

@section('main')

	@include('back.partials.entete', ['title' => trans('back/users.dashboard'), 'icone' => 'user', 'fil' => link_to('user', trans('back/users.Users')) . ' / ' . trans('back/users.card')])

	<p>{{ trans('back/users.name') . ' : ' .  $user->username }}</p>
	<p>{{ trans('back/users.email') . ' : ' .  $user->email }}</p>
	<p>{{ trans('back/users.role') . ' : ' .  $user->role->title }}</p>
	<p>{{ $user->confirmed ? trans('back/users.confirmed') : trans('back/users.not-confirmed') }}</p>

@stop