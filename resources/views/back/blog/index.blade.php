@extends('back.template')

@section('main')

 <!-- Entête de page -->
  {!!  HTML::backEntete(
  trans('back/blog.dashboard') . link_to_route('blog.create', trans('back/blog.add'), [], ['class' => 'btn btn-info pull-right']),
  'pencil',
  trans('back/blog.posts')
  ) !!}

	@if(Session::has('ok'))
    {!! HTML::alert('success', Session::get('ok')) !!}
	@endif

  <div class="row col-lg-12">
    <div class="pull-right link">{!! $links !!}</div>
  </div>

  <div class="row col-lg-12">
  	<div class="table-responsive">
  		<table class="table">
  			<thead>
  				<tr>
  					<th>{{ trans('back/blog.title') }} <a href="#" name="titre" class="order"><span class="fa fa-fw fa-unsorted"></span></a></th>
  					<th>{{ trans('back/blog.date') }} <a href="#" name="created_at" class="order"><span class="fa fa-fw fa-sort-desc"></th>
            <th>{{ trans('back/blog.published') }} <a href="#" name="actif" class="order"><span class="fa fa-fw fa-unsorted"></th> 
            @if(Session::get('statut') == 'admin')
              <th>{{ trans('back/blog.author') }} <a href="#" name="username" class="order"><span class="fa fa-fw fa-unsorted"></th>            
              <th>{{ trans('back/blog.seen') }} <a href="#" name="posts.vu" class="order"><span class="fa fa-fw fa-unsorted"></th>
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

      // Traitement du vu
      $(document).on('change', ':checkbox[name="vu"]', function() {
        $(this).parents('tr').toggleClass('warning');
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: 'postvu/' + this.value,
          type: 'PUT',
          data: "vu=" + this.checked + "&_token=" + token,
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="vu"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="vu"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/blog.fail') }}');
        });
      });

      // Traitement du actif
      $(document).on('change', ':checkbox[name="actif"]', function() {
        $(this).parents('tr').toggleClass('warning');
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: 'postactif/' + this.value,
          type: 'PUT',
          data: "actif=" + this.checked + "&_token=" + token,
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="actif"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="actif"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/blog.fail') }}');
        });
      });

      // Traitement du tri
      $('a.order').click(function(e) {
        e.preventDefault();
        // Sens actuel du tri
        var sens;
        if($('span', this).hasClass('fa-unsorted')) sens = 'aucun';
        else if ($('span', this).hasClass('fa-sort-desc')) sens = 'desc';
        else if ($('span', this).hasClass('fa-sort-asc')) sens = 'asc';
        // Remise à zéro de l'ensemble
        $('a.order span').removeClass().addClass('fa fa-fw fa-unsorted');
        // Ajustement du sélectionné
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
        // Envoi ajax
        $.ajax({
          url: 'blog/order',
          type: 'GET',
          dataType: 'json',
          data: "name=" + $(this).attr('name') + "&sens=" + tri,
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