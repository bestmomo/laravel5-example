@extends('front.template')

@section('main')
	<div class="row">
		<div class="box">
			<div class="col-lg-12">
				<hr>	
				<h2 class="intro-text text-center">{{ trans('front/register.title') }}</h2>
				<hr>
				<p>{{ trans('front/register.infos') }}</p>		

				{!! Form::open(['url' => 'auth/register', 'method' => 'post', 'role' => 'form']) !!}	

					<div class="row">
						{!! Form::control('text', 6, 'username', $errors, trans('front/register.pseudo'), null, [trans('front/register.warning'), trans('front/register.warning-name')]) !!}
						{!! Form::control('email', 6, 'email', $errors, trans('front/register.email')) !!}
					</div>
					<div class="row">	
						{!! Form::control('password', 6, 'password', $errors, trans('front/register.password'), null, [trans('front/register.warning'), trans('front/register.warning-password')]) !!}
						{!! Form::control('password', 6, 'password_confirmation', $errors, trans('front/register.confirm-password')) !!}
					</div>
					{!! Form::text('address', '', ['class' => 'hpet']) !!}	

					<div class="row">	
						{!! Form::submit(trans('front/form.send'), ['col-lg-12']) !!}
					</div>
					
				{!! Form::close() !!}

			</div>
		</div>
	</div>
@stop

@section('scripts')

	<script>
		$(function() { $('.badge').popover();	});
	</script>

@stop