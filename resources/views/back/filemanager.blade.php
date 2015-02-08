@extends('back.template')

@section('head')

	<style type="text/css">

		.iframe-responsive-wrapper {
			position: relative;
		}

		.iframe-responsive-wrapper .iframe-ratio {
			display: block;
			width: 100%;
			height: auto;
		}

		.iframe-responsive-wrapper iframe {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
		
		#page-wrapper {
			background-color: #222;
		}

		.page-header {
			color: #ddd;
		}

	</style>

@stop

@section('main')

	@include('back.partials.entete', ['title' => trans('back/medias.dashboard'), 'icone' => 'file-image-o', 'fil' => trans('back/medias.medias')])

	<div class="iframe-responsive-wrapper">
		<img class="iframe-ratio" src="data:image/gif;base64,R0lGODlhEAAJAIAAAP///wAAACH5BAEAAAAALAAAAAAQAAkAAAIKhI+py+0Po5yUFQA7"/>
		<iframe scrolling="no" src="{!! url($url) !!}" width="640" height="360" frameborder="2" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	</div>

@stop
