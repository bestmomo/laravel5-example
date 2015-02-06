@extends('back.template')

@section('main')

 <!-- Page header -->
  @include('back.partials.entete', ['title' => trans('back/blog.dashboard') . link_to_route('blog.create', trans('back/blog.add'), [], ['class' => 'btn btn-info pull-right']), 'icone' => 'pencil', 'fil' => trans('back/blog.posts')])

	@if(session()->has('ok'))
    @include('partials/error', ['type' => 'success', 'message' => session('ok')])
	@endif

  <div class="row col-lg-12">
    <div class="pull-right link">{!! $links !!}</div>
  </div>

  <div class="row col-lg-12">
  	<div class="table-responsive">
  		<table class="table">
  			<thead>
  				<tr>
  					<th>{{ trans('back/blog.title') }} <a href="#" name="title" class="order"><span class="fa fa-fw fa-unsorted"></span></a></th>
  					<th>{{ trans('back/blog.date') }} <a href="#" name="created_at" class="order"><span class="fa fa-fw fa-sort-desc"></th>
            <th>{{ trans('back/blog.published') }} <a href="#" name="active" class="order"><span class="fa fa-fw fa-unsorted"></th> 
            @if(session('statut') == 'admin')
              <th>{{ trans('back/blog.author') }} <a href="#" name="username" class="order"><span class="fa fa-fw fa-unsorted"></th>            
              <th>{{ trans('back/blog.seen') }} <a href="#" name="posts.seen" class="order"><span class="fa fa-fw fa-unsorted"></th>
            @endif
  				</tr>
  			</thead>
  			<tbody>
  			  @include('back.blog.table')
    		</tbody>
  		</table>
  	</div>
  </div>

  <div class="row col-lg-12">
    <div class="pull-right link">{!! $links !!}</div>
  </div>

@stop

@section('scripts')

  <script>
    
    $(function() {

      // Processing the 'seen' value
      $(document).on('change', ':checkbox[name="seen"]', function() {
        $(this).parents('tr').toggleClass('warning');
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: 'postseen/' + this.value,
          type: 'PUT',
          data: "seen=" + this.checked + "&_token=" + token
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="seen"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="seen"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/blog.fail') }}');
        });
      });

      // Processing the 'active' value
      $(document).on('change', ':checkbox[name="active"]', function() {
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: 'postactive/' + this.value,
          type: 'PUT',
          data: "active=" + this.checked + "&_token=" + token
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="active"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="active"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/blog.fail') }}');
        });
      });

      // Sorting
      $('a.order').click(function(e) {
        e.preventDefault();
        // Current sort direction
        var sens;
        if($('span', this).hasClass('fa-unsorted')) sens = 'unsorted';
        else if ($('span', this).hasClass('fa-sort-desc')) sens = 'desc';
        else if ($('span', this).hasClass('fa-sort-asc')) sens = 'asc';
        // Reset all css
        $('a.order span').removeClass().addClass('fa fa-fw fa-unsorted');
        // updating CSS
        $('span', this).removeClass();
        var tri;
        if(sens == 'aucun' || sens == 'asc') {
          $('span', this).addClass('fa fa-fw fa-sort-desc');
          tri = 'desc';
        } else if(sens == 'desc') {
          $('span', this).addClass('fa fa-fw fa-sort-asc');
          tri = 'asc';
        }
        // Icone d'attente
        $('.breadcrumb li').append('<span id="tempo" class="fa fa-refresh fa-spin"></span>');       
        // Sending ajax
        $.ajax({
          url: 'blog/order',
          type: 'GET',
          dataType: 'json',
          data: "name=" + $(this).attr('name') + "&sens=" + tri
        })
        .done(function(data) {
          $('tbody').html(data.view);
          $('.link').html(data.links);
          $('#tempo').remove();
        })
        .fail(function() {
          alert('{{ trans('back/blog.fail') }}');
        });
      })

    });

  </script>

@stop
