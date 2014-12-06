@extends('back.template')

@section('main')

 <!-- Entête de page -->
  @include('back.partials.entete', ['titre' => trans('back/blog.dashboard'), 'icone' => 'pencil', 'fil' => link_to('blog', 'Articles') . ' / ' . trans('back/blog.edition')])

	<div class="col-sm-12">
		{!! Form::model($post, ['route' => ['blog.update', $post->id], 'method' => 'put', 'class' => 'form-horizontal panel']) !!}
		  
		  <div class="form-group checkbox pull-right">
			  <label>
			    {!! Form::checkbox('actif', $post->actif, $post->actif) !!}
			    {{ trans('back/blog.published') }}
			  </label>
			</div>
			
			{!! Form::control('text', 0, 'titre', $errors, trans('back/blog.title')) !!}

		  <div class="form-group {!! $errors->has('slug') ? 'has-error' : '' !!}">
		  	{!! Form::label('slug', trans('back/blog.permalink'), ['class' => 'control-label']) !!}
		  	{!! url('/') . '/ ' . Form::text('slug', $post->slug) !!}
		  	<small class="help-block">{!! $errors->first('slug') !!}</small>
		  </div>

		  {!! Form::control('textarea', 0, 'sommaire', $errors, trans('back/blog.summary')) !!}
			{!! Form::control('textarea', 0, 'contenu', $errors, trans('back/blog.content')) !!}
			{!! Form::control('text', 0, 'tags', $errors, trans('back/blog.tags'), implode(',', $tags)) !!}

		  {!! Form::submit(trans('front/form.send')) !!}

		{!! Form::close() !!}
	</div>

@stop

@section('scripts')

	{!! HTML::script('ckeditor/ckeditor.js') !!}
	
	<script>

	  var config = {
			codeSnippet_theme: 'Monokai',
			language: '{{ config('app.locale') }}',
			height: 100,
			filebrowserBrowseUrl: '{!! url($url) !!}',
			toolbarGroups: [
				{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
				{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
				{ name: 'links' },
				{ name: 'insert' },
				{ name: 'forms' },
				{ name: 'tools' },
				{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
				{ name: 'others' },
				//'/',
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
				{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
				{ name: 'styles' },
				{ name: 'colors' }
			]
		};

    CKEDITOR.replace( 'sommaire', config);

		config['height'] = 400;		

		CKEDITOR.replace( 'contenu', config);

	</script>
@stop