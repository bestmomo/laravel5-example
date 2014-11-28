@extends('back.template')

@section('head')

	{!! HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/default.css') !!}

@stop

@section('main')

 <!-- Entête de page -->
  {!!  HTML::backEntete(
  trans('back/blog.dashboard'),
  'pencil',
  link_to('blog', 'Articles') . ' / ' . trans('back/blog.creation')
  ) !!}

	<div class="col-sm-12">
		{!! Form::open(['url' => 'blog', 'method' => 'post', 'class' => 'form-horizontal panel']) !!}	
		  
		  <div class="form-group checkbox pull-right">
			  <label>
			    {!! Form::checkbox('actif') !!}
			    {{ trans('back/blog.published') }}
			  </label>
			</div>

			{!! Form::control('text', 0, 'titre', $errors, trans('back/blog.title')) !!}

		  <div class="form-group {!! $errors->has('slug') ? 'has-error' : '' !!}">
		  	{!! Form::label('slug', trans('back/blog.permalink'), ['class' => 'control-label']) !!}
		  	{!! url('/') . '/ ' . Form::text('slug', null, ['id' => 'permalien']) !!}
		  	<small class="text-danger">{!! $errors->first('slug') !!}</small>
		  </div>

		  {!! Form::control('textarea', 0, 'sommaire', $errors, trans('back/blog.summary')) !!}
		  {!! Form::control('textarea', 0, 'contenu', $errors, trans('back/blog.content')) !!}
			{!! Form::control('text', 0, 'tags', $errors, trans('back/blog.tags')) !!}

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

		function sansAccent(str){
		    var accent = [
		        /[\300-\306]/g, /[\340-\346]/g, // A, a
		        /[\310-\313]/g, /[\350-\353]/g, // E, e
		        /[\314-\317]/g, /[\354-\357]/g, // I, i
		        /[\322-\330]/g, /[\362-\370]/g, // O, o
		        /[\331-\334]/g, /[\371-\374]/g, // U, u
		        /[\321]/g, /[\361]/g, // N, n
		        /[\307]/g, /[\347]/g // C, c
		    ];
		    var noaccent = ['A','a','E','e','I','i','O','o','U','u','N','n','C','c'];
		    for(var i = 0; i < accent.length; i++){
		        str = str.replace(accent[i], noaccent[i]);
		    }
		    return str;
		}

    $("#titre").keyup(function(){
			var str = sansAccent($(this).val());
			str = str.replace(/[^a-zA-Z0-9\s]/g,"");
			str = str.toLowerCase();
			str = str.replace(/\s/g,'-');
			$("#permalien").val(str);        
		});

  </script>

@stop