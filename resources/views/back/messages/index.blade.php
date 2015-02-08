@extends('back.template')

@section('head')

	<style type="text/css">
		.table { margin-bottom: 0; }
		.panel-heading { padding: 0 15px; }
	</style>

@stop

@section('main')

 <!-- Entête de page -->
  @include('back.partials.entete', ['title' => trans('back/messages.dashboard'), 'icone' => 'envelope', 'fil' => trans('back/messages.messages')])

  @foreach ($messages as $message)
		<div class="panel {!! $message->seen? 'panel-default' : 'panel-warning' !!}">
		  <div class="panel-heading">
				<table class="table">
					<thead>
						<tr>
							<th class="col-lg-1">{{ trans('back/messages.name') }}</th>
							<th class="col-lg-1">{{ trans('back/messages.email') }}</th>
							<th class="col-lg-1">{{ trans('back/messages.date') }}</th>
							<th class="col-lg-1">{{ trans('back/messages.seen') }}</th>
							<th class="col-lg-1"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="text-primary"><strong>{{ $message->name }}</strong></td>
							<td>{!! HTML::mailto($message->email, $message->email) !!}</a></td>
							<td>{{ $message->created_at }}</td>
							<td>{!! Form::checkbox('vu', $message->id, $message->seen) !!}</td>
							<td>
							{!! Form::open(['method' => 'DELETE', 'route' => ['contact.destroy', $message->id]]) !!}
								{!! Form::destroy(trans('back/messages.destroy'), trans('back/messages.destroy-warning'), 'btn-xs') !!}
							{!! Form::close() !!}
							</td>
						</tr>
					</tbody>
				</table>	
			</div>
			<div class="panel-body">
				{{ $message->text }}
			</div>
		</div>
	@endforeach

@stop

@section('scripts')

	<script>
		
    $(function() {
			$(':checkbox').change(function() {     
				$(this).parents('.panel').toggleClass('panel-warning').toggleClass('panel-default');
				$(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
				var token = $('input[name="_token"]').val();
				$.ajax({
					url: 'contact/' + this.value,
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
					alert('{{ trans('back/messages.fail') }}');
				});
			});
		});

	</script>

@stop