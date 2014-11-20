@extends('back.template')

@section('main')

 <!-- Entête de page -->
  {!!  HTML::backEntete(
  trans('back/admin.dashboard'),
  'dashboard',
  trans('back/admin.dashboard')
  ) !!}

  <div class="row">

    @if(is_int($nbrMessages))
      {!! HTML::panneauAdmin('primary', 'envelope', $nbrMessages, trans('back/admin.new-messages'), 'contact', trans('back/admin.details')) !!}
    @endif

    @if(is_int($nbrUsers))
      {!! HTML::panneauAdmin('green', 'user', $nbrUsers, trans('back/admin.new-registers'), 'user', trans('back/admin.details')) !!}
    @endif

    @if(is_int($nbrPosts))
      {!! HTML::panneauAdmin('yellow', 'pencil', $nbrPosts, trans('back/admin.new-posts'), 'blog', trans('back/admin.details')) !!}
    @endif

    @if(is_int($nbrComments))
      {!! HTML::panneauAdmin('red', 'comment', $nbrComments, trans('back/admin.new-comments'), 'comment', trans('back/admin.details')) !!}
    @endif

  </div>
  <!-- /.row -->

@stop


