@extends('front.template')

@section('head')

  {!! HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/monokai.css') !!}

@stop

@section('main')
	<div class="row">
		<div class="box">
			<div class="col-lg-12">
				<hr>
				<h2 class="text-center">{{ $post->title }}
				<br>
				<small>{{ $post->user->username }} {{ trans('front/blog.on') }} {!! $post->created_at . ($post->created_at != $post->updated_at ? trans('front/blog.updated') . $post->updated_at : '') !!}</small>
				</h2>
				<hr>
				{!! $post->summary !!}<br>
				{!! $post->content !!}
				<hr>
				@if($post->tags->count())
					<div class="text-center">
						@if($post->tags->count() > 0)
							<small>{{ trans('front/blog.tags') }}</small> 
							@foreach($post->tags as $tag)
								{!! link_to('blog/tag?tag=' . $tag->id, $tag->tag, ['class' => 'btn btn-default btn-xs']) !!}
							@endforeach
						@endif
					</div>
				@endif
			</div>
		</div>
	</div>

	<div class="row">
		<div class="box">
			<div class="col-lg-12">
				<div class="col-lg-12">
					<hr>
					<h3 class="text-center">{{ trans('front/blog.comments') }}</h3>
					<hr>

					@if($comments->count())
						@foreach($comments as $comment)
							<div class="commentitem">
								<h3>
									<small>{{ $comment->user->username . ' ' . trans('front/blog.on') . ' ' . $comment->created_at }}</small>
									@if($user && $user->username == $comment->user->username) 
										<a id="deletecomment{!! $comment->id !!}" href="#" class="deletecomment"><span class="fa fa-fw fa-trash pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('front/blog.delete') }}"></span></a>
										<a id="comment{!! $comment->id !!}" href="#" class="editcomment"><span class="fa fa-fw fa-pencil pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('front/blog.edit') }}"></span></a>
									@endif
								</h3>
								<div id="contenu{!! $comment->id !!}">{!! $comment->content !!}</div>
								<hr>
							</div>
						@endforeach
					@endif	

					<div class="row" id="formcreate"> 
						@if(session()->has('warning'))
							@include('partials/error', ['type' => 'warning', 'message' => session('warning')])
						@endif	
						@if(session('statut') != 'visitor')
							{!! Form::open(['url' => 'comment']) !!}	
								{!! Form::hidden('post_id', $post->id) !!}
								{!! Form::control('textarea', 12, 'comments', $errors, trans('front/blog.comment')) !!}
								{!! Form::submit(trans('front/form.send'), ['col-lg-12']) !!}
							{!! Form::close() !!}
						@else
							<div class="text-center"><i class="text-center">{{ trans('front/blog.info-comment') }}</i></div>
						@endif
					</div>

				</div>
			</div>
		</div>
	</div>

</div>

@stop

@section('scripts')

	{!! HTML::script('ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js') !!}

	@if(session('statut') != 'visitor')
		{!! HTML::script('ckeditor/ckeditor.js') !!}
	@endif

	<script>	  

		@if(session('statut') != 'visitor')

			CKEDITOR.replace('comments', {
				language: '{{ config('app.locale') }}',
				height: 200,
				toolbarGroups: [
					{ name: 'basicstyles', groups: [ 'basicstyles'] }, 
					{ name: 'links' },
					{ name: 'insert' }
				],
				removeButtons: 'Table,SpecialChar,HorizontalRule,Anchor'
			});

			function buttons(i) {
				return "<input id='val" + i +"' class='btn btn-default' type='submit' value='{{ trans('front/blog.valid') }}'><input id='btn" + i + "' class='btn btn-default btnannuler' type='button' value='{{ trans('front/blog.undo') }}'></div>";
			}

			$(function() {

				$('a.editcomment span').tooltip();
				$('a.deletecomment span').tooltip();

				// Set comment edition
				$('a.editcomment').click(function(e) {   
					e.preventDefault();
					$(this).hide();
					var i = $(this).attr('id').substring(7);
					var existing = $('#contenu' + i).html();
					var url = $('#formcreate').find('form').attr('action');
					jQuery.data(document.body, 'comment' + i, existing);
					var html = "<div class='row'><form id='form" + i + "' method='POST' action='" + url + '/' + i + "' accept-charset='UTF-8' class='formajax'><input name='_token' type='hidden' value='" + $('input[name="_token"]').val() + "'><div class='form-group col-lg-12 '><label for='comments' class='control-label'>{{ trans('front/blog.change') }}</label><textarea id='cont" + i +"' class='form-control' name='comments" + i + "' cols='50' rows='10' id='comments" + i + "'>" + existing + "</textarea><small class='help-block'></small></div><div class='form-group col-lg-12'>" + buttons(i) + "</div>";
					$('#contenu' + i).html(html);
					CKEDITOR.replace('comments' + i, {
						language: '{{ config('app.locale') }}',
						height: 200,
						toolbarGroups: [
							{ name: 'basicstyles', groups: [ 'basicstyles'] }, 
							{ name: 'links' },
							{ name: 'insert' }
						],
						removeButtons: 'Table,SpecialChar,HorizontalRule,Anchor'
					});
				});

				// Escape edition
				$(document).on('click', '.btnannuler', function() {    
					var i = $(this).attr('id').substring(3);
					$('#comment' + i).show();
					$('#contenu' + i).html(jQuery.data(document.body, 'comment' + i));
				});

				// Validation 
				$(document).on('submit', '.formajax', function(e) {  
					e.preventDefault();
					var i = $(this).attr('id').substring(4);
					$('#val' + i).parent().html('<i class="fa fa-refresh fa-spin fa-2x"></i>').addClass('text-center');
					$.ajax({
						method: 'put',
						url: $(this).attr('action'),
						data: $(this).serialize()
					})
					.done(function(data) {
						$('#comment' + data.id).show();
						$('#contenu' + data.id).html(data.content);	
					})
					.fail(function(data) {
						var errors = data.responseJSON;
						$.each(errors, function(index, value) {
							$('textarea[name="' + index + '"]' + ' ~ small').text(value);
							$('textarea[name="' + index + '"]').parent().addClass('has-error');
							$('.fa-spin').parent().html(buttons(index.slice(-1))).removeClass('text-center');
						});
					});
				});

				// Delete comment
				$('a.deletecomment').click(function(e) {   
					e.preventDefault();		
					if (!confirm('{{ trans('front/blog.confirm') }}')) return;	
					var i = $(this).attr('id').substring(13);
					var token = $('input[name="_token"]').val();
					$(this).replaceWith('<i class="fa fa-refresh fa-spin pull-right"></i>');
					$.ajax({
						method: 'delete',
						url: '{!! url('comment') !!}' + '/' + i,
						data: '_token=' + token
					})
					.done(function(data) {
						$('#comment' + data.id).parents('.commentitem').remove();
					})
					.fail(function() {
						alert('{{ trans('front/blog.fail-delete') }}');
					});					
				});

			});

		@endif

		hljs.initHighlightingOnLoad();

	</script>

@stop
