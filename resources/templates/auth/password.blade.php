@extends('front.template')

@section('main')
  <div class="row">
    <div class="box">
      <div class="col-lg-12">
      	@if(Session::has('status'))
      		@include('partials/error', ['type' => 'success', 'message' => Session::get('status')])
				@endif
				@if(Session::has('error'))
					@include('partials/error', ['type' => 'danger', 'message' => Session::get('error')])
				@endif	
				<hr>	
				<h2 class="intro-text text-center">{{ trans('front/password.title') }}</h2>
				<hr>
        <p>{{ trans('front/password.info') }}</p>		
				{!! Form::open(['url' => 'password/email', 'method' => 'post', 'role' => 'form']) !!}	

					<div class="row">

						{!! Form::control('email', 6, 'email', $errors, trans('front/password.email')) !!}
						{!! Form::submit(trans('front/form.send'), ['col-lg-12']) !!}
						{!! Form::text('user', '', ['class' => 'hpet']) !!}	
						
					</div>

				{!! Form::close() !!}

			</div>
		</div>
	</div>
@stop