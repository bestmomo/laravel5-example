@extends('back.template')

@section('main')

 <!-- Entête de page -->
  @include('back.partials.entete', ['titre' => trans('back/roles.dashboard'), 'icone' => 'user', 'fil' => link_to('user', trans('back/users.Users')) . ' / ' . trans('back/roles.roles')])

	<div class="col-sm-12">
		@if(Session::has('ok'))
    	@include('partials/error', ['type' => 'success', 'message' => Session::get('ok')])
		@endif
		{!! Form::open(['url' => 'user/roles', 'method' => 'post', 'class' => 'form-horizontal panel']) !!}	
			@foreach ($roles as $role) 
				{!! Form::control('text', 0, $role->slug, $errors, trans('back/roles.' . $role->slug), $role->titre) !!}
			@endforeach
			{!! Form::submit(trans('front/form.send')) !!}
		{!! Form::close() !!}
	</div>

@stop