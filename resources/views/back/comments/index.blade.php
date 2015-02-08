@extends('back.template')

@section('head')

	<style type="text/css">
		.table { margin-bottom: 0; }
		.panel-heading { padding: 0 15px; }
		.border-red {
			border-style: solid;
			border-width: 5px;
			border-color: red !important;
		}
	</style>

@stop

@section('main')

  @include('back.partials.entete', ['title' => trans('back/comments.dashboard'), 'icone' => 'comment', 'fil' => trans('back/comments.comments')])

	<div class="row col-lg-12">
		<div class="pull-right">{!! $links !!}</div>
	</div>

	<div class="row col-lg-12">
		@foreach ($comments as $comment)
			<div class="panel {!! $comment->seen? 'panel-default' : 'panel-warning' !!}">
				<div class="panel-heading {!! $comment->user->valid? '' : 'border-red' !!}">
					<table class="table">
						<thead>
							<tr>
								<th class="col-lg-3">{{ trans('back/comments.author') }}</th>
								<th class="col-lg-3">{{ trans('back/comments.date') }}</th>
								<th class="col-lg-3">{{ trans('back/comments.post') }}</th>
								<th class="col-lg-1">{{ trans('back/comments.valid') }}</th>
								<th class="col-lg-1">{{ trans('back/comments.seen') }}</th>
								<th class="col-lg-1"></th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td class="text-primary"><strong>{{ $comment->user->username }}</strong></td>
							<td>{{ $comment->created_at }}</td>
							<td>{{ $comment->post->title }}</td>
							<td>{!! Form::checkbox('valide', $comment->user->id, $comment->user->valid) !!}</td>
							<td>{!! Form::checkbox('seen', $comment->id, $comment->seen) !!}</td>
							<td>
									{!! Form::open(['method' => 'DELETE', 'route' => ['comment.destroy', $comment->id]]) !!}
										{!! Form::destroy(trans('back/comments.destroy'), trans('back/comments.destroy-warning'), 'btn-xs') !!}
									{!! Form::close() !!}
							</td>
						</tr>
			  		</tbody>
					</table>	
				</div>
				<div class="panel-body">
					{!! $comment->content !!}
				</div> 
			</div>
		@endforeach
	</div>

  <div class="row col-lg-12">
    <div class="pull-right">{!! $links !!}</div>
  </div>

@stop

@section('scripts')

	<script>
		
		
		$(function() {

			// Seen gestion
			$(":checkbox[name='seen']").change(function() {     
				$(this).parents('.panel').toggleClass('panel-warning').toggleClass('panel-default');
				$(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
				var token = $('input[name="_token"]').val();
				$.ajax({
					url: 'commentseen/' + this.value,
					type: 'PUT',
					data: "seen=" + this.checked + "&_token=" + token
				})
				.done(function() {
					$('.fa-spin').remove();
					$('input[type="checkbox"]:hidden').show();
				})
				.fail(function() {
					$('.fa-spin').remove();
					var chk = $('input[type="checkbox"]:hidden');
					chk.parents('.panel').toggleClass('panel-warning').toggleClass('panel-default');
					chk.show().prop('checked', chk.is(':checked') ? null:'checked');
					alert('{{ trans('back/comments.fail') }}');
				});
			});

			// Gestion de valide
			$(":checkbox[name='valide']").change(function() { 
				var cases = $(":checkbox[name='valide'][value='" + this.value + "']");
				cases.prop('checked', this.checked);
				cases.parents('.panel-heading').toggleClass('border-red');
				cases.hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
				var token = $('input[name="_token"]').val();
				$.ajax({
					url: '{!! url('uservalid') !!}' + '/' + this.value,
					type: 'PUT',
					data: "valid=" + this.checked + "&_token=" + token
				})
				.done(function() {
					$('.fa-spin').remove();
					$('input[type="checkbox"]:hidden').show();
				})
				.fail(function() {
					$('.fa-spin').remove();
					var cases = $('input[type="checkbox"]:hidden');
					cases.parents('.panel-heading').toggleClass('border-red'); 
					cases.show().prop('checked', cases.is(':checked') ? null:'checked');
					alert('{{ trans('back/comments.fail') }}');
				});
			});

		});

	</script>

@stop