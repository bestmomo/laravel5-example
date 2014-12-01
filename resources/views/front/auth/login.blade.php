@extends('front.template')

@section('main')
  <div class="row">
    <div class="box">
      <div class="col-lg-12">
				@if(Session::has('error'))
					@include('partials/error', ['type' => 'danger', 'message' => Session::get('error')])
				@endif	
				<hr>	
				<h2 class="intro-text text-center">{{ trans('front/login.connection') }}</h2>
				<hr>
        <p>{{ trans('front/login.text') }}</p>				
				
				{!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form']) !!}	
				
				<div class="row">

					{!! Form::control('email', 6, 'email', $errors, trans('front/login.email')) !!}
					{!! Form::control('password', 6, 'password', $errors, trans('front/login.password')) !!}
					{!! Form::submit(trans('front/form.send'), ['col-lg-12']) !!}
					{!! Form::check('souvenir', trans('front/login.remind')) !!}
					{!! Form::text('user', '', ['class' => 'hpet']) !!}		  
					<div class="col-lg-12">					
						{!! link_to('password/email', trans('front/login.forget')) !!}
					</div>

				</div>
				
				{!! Form::close() !!}

				<div class="text-center">
					<hr>
						<h2 class="intro-text text-center">{{ trans('front/login.register') }}</h2>
					<hr>	
					<p>{{ trans('front/login.register-info') }}</p>
					{!! link_to('auth/register', trans('front/login.registering'), ['class' => 'btn btn-default']) !!}
				</div>

			</div>
		</div>
	</div>
@stop

