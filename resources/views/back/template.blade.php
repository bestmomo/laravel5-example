<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Mon site</title>
		<meta name="description" content="">	
		<meta name="viewport" content="width=device-width, initial-scale=1">

		{!! HTML::style('css/main_back.css') !!}

		<!--[if (lt IE 9) & (!IEMobile)]>
			{!! HTML::script('js/vendor/respond.min.js') !!}
		<![endif]-->
		<!--[if lt IE 9]>
			{{ HTML::style('https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js') }}
			{{ HTML::style('https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') }}
		<![endif]-->

		{!! HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800') !!}
		{!! HTML::style('http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic') !!}

        @yield('head')

	</head>

  <body>

	<!--[if lte IE 7]>
	    <p class="browsehappy">Vous utilisez un navigateur <strong>obsolète</strong>. S'il vous plaît <a href="http://browsehappy.com/">Mettez le à jour</a> pour améliorer votre navigation.</p>
	<![endif]-->

   <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                @if(session('statut') == 'admin')
                    {!! link_to_route('admin', trans('back/admin.administration'), [], ['class' => 'navbar-brand']) !!}
                @else
                    {!! link_to_route('blog.index', trans('back/admin.redaction'), [], ['class' => 'navbar-brand']) !!}
                @endif
            </div>
            <!-- Menu supérieur -->
            <ul class="nav navbar-right top-nav">
                <li>{!! link_to_route('home', trans('back/admin.home')) !!}</li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user"></span> {{ auth()->user()->username }}<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{!! url('auth/logout') !!}"><span class="fa fa-fw fa-power-off"></span> {{ trans('back/admin.logout') }}</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Menu de la barre latérale -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    @if(session('statut') == 'admin')
                        <li {!! classActivePath('admin') !!}>
                             <a href="{!! route('admin') !!}"><span class="fa fa-fw fa-dashboard"></span> {{ trans('back/admin.dashboard') }}</a>
                        </li>
                        <li {!! classActiveSegment(1, 'user') !!}>
                            <a href="#" data-toggle="collapse" data-target="#usermenu"><span class="fa fa-fw fa-user"></span> {{ trans('back/admin.users') }} <span class="fa fa-fw fa-caret-down"></span></a>
                            <ul id="usermenu" class="collapse">
                                <li><a href="{!! url('user') !!}">{{ trans('back/admin.see-all') }}</a></li>
                                <li><a href="{!! url('user/create') !!}">{{ trans('back/admin.add') }}</a></li>
                                <li><a href="{!! url('user/roles') !!}">{{ trans('back/roles.roles') }}</a></li>
                            </ul>
                        </li>
                        <li {!! classActivePath('contact') !!}>
                            <a href="{!! url('contact') !!}"><span class="fa fa-fw fa-envelope"></span> {{ trans('back/admin.messages') }}</a>
                        </li>  
                        <li {!! classActivePath('comment') !!}>
                            <a href="{!! url('comment') !!}"><span class="fa fa-fw fa-comments"></span> {{ trans('back/admin.comments') }}</a>
                        </li> 
                    @endif                  
                    <li {!! classActivePath('medias') !!}>
                        <a href="{!! route('medias') !!}"><span class="fa fa-fw fa-file-image-o"></span> {{ trans('back/admin.medias') }}</a>
                    </li>
                    <li {!! classActiveSegment(1, 'blog') !!}>
                        <a href="#" data-toggle="collapse" data-target="#articlemenu"><span class="fa fa-fw fa-pencil"></span> {{ trans('back/admin.posts') }} <span class="fa fa-fw fa-caret-down"></a>
                        <ul id="articlemenu" class="collapse">
                            <li><a href="{!! url('blog') !!}">{{ trans('back/admin.see-all') }}</a></li>
                            <li><a href="{!! url('blog/create') !!}">{{ trans('back/admin.add') }}</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">

                @yield('main')

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /.page-wrapper -->

    </div>
    <!-- /.wrapper -->

    	{!! HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') !!}
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
    	{!! HTML::script('js/plugins.js') !!}
    	{!! HTML::script('js/main.js') !!}

        @yield('scripts')

  </body>
</html>