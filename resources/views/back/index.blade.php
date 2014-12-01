@extends('back.template')

@section('main')

 <!-- Entête de page -->
  @include('back.partials.entete', ['titre' => trans('back/admin.dashboard'), 'icone' => 'dashboard', 'fil' => trans('back/admin.dashboard')])

  <div class="row">

    @include('back/partials/pannel', ['color' => 'primary', 'icone' => 'envelope', 'nbr' => $nbrMessages, 'nom' => trans('back/admin.new-messages'), 'url' => 'contact', 'total' => trans('back/admin.messages')])

    @include('back/partials/pannel', ['color' => 'green', 'icone' => 'user', 'nbr' => $nbrUsers, 'nom' => trans('back/admin.new-registers'), 'url' => 'user', 'total' => trans('back/admin.users')])

    @include('back/partials/pannel', ['color' => 'yellow', 'icone' => 'pencil', 'nbr' => $nbrPosts, 'nom' => trans('back/admin.new-posts'), 'url' => 'blog', 'total' => trans('back/admin.posts')])

    @include('back/partials/pannel', ['color' => 'red', 'icone' => 'comment', 'nbr' => $nbrComments, 'nom' => trans('back/admin.new-comments'), 'url' => 'comment', 'total' => trans('back/admin.comments')])

  </div>
  <!-- /.row -->

@stop


