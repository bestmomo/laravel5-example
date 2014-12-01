@extends('back.template')

@section('main')

 <!-- Entête de page -->
  @include('back.partials.entete', ['titre' => trans('back/users.dashboard'), 'icone' => 'user', 'fil' => link_to('user', trans('back/users.Users')) . ' / ' . trans('back/users.edition')])

	<div class="col-sm-12">
		{!! Form::open(['url' => 'user/' . $user->id, 'method' => 'put', 'class' => 'form-horizontal panel']) !!}	
			{!! Form::control('text', 0, 'username', $errors, trans('back/users.name'), $user->username) !!}
		  {!! Form::control('email', 0, 'email', $errors, trans('back/users.email'), $user->email) !!}
		  {!! Form::selection('role', $select, $user->role_id, trans('back/users.role')) !!}
			{!! Form::submit(trans('front/form.send')) !!}
		{!! Form::close() !!}
	</div>

@stop