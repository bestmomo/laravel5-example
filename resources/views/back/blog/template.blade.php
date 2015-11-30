@extends('back.template')

@section('head')

	{!! HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/default.css') !!}

@stop

@section('main')

	<!-- Entête de page -->
	@include('back.partials.entete', ['title' => trans('back/blog.dashboard'), 'icone' => 'pencil', 'fil' => link_to('blog', trans('back/blog.posts')) . ' / ' . trans('back/blog.creation')])

	<div class="col-sm-12">
		@yield('form')

		<div class="form-group checkbox pull-right">
			<label>
				{!! Form::checkbox('active') !!}
				{{ trans('back/blog.published') }}
			</label>
		</div>

		{!! Form::control('text', 0, 'title', $errors, trans('back/blog.title')) !!}

		<div class="form-group {!! $errors->has('slug') ? 'has-error' : '' !!}">
			{!! Form::label('slug', trans('back/blog.permalink'), ['class' => 'control-label']) !!}
			{!! url('/') . '/blog/' . Form::text('slug', null, ['id' => 'permalien']) !!}
			<small class="text-danger">{!! $errors->first('slug') !!}</small>
		</div>

		{!! Form::control('textarea', 0, 'summary', $errors, trans('back/blog.summary')) !!}
		{!! Form::control('textarea', 0, 'content', $errors, trans('back/blog.content')) !!}
		{!! Form::control('text', 0, 'tags', $errors, trans('back/blog.tags'), isset($tags)? implode(',', $tags) : '') !!}

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

	CKEDITOR.replace( 'summary', config);

	config['height'] = 400;		

	CKEDITOR.replace( 'content', config);

	$("#title").keyup(function(){
			var str = sansAccent($(this).val());
			str = str.replace(/[^a-zA-Z0-9\s]/g,"");
			str = str.toLowerCase();
			str = str.replace(/\s/g,'-');
			$("#permalien").val(str);        
		});

  </script>

@stop