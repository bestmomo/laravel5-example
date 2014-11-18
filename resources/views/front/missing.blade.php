<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur 404</title>

    {!! HTML::style('css/main_front.css') !!}

    <style type="text/css">

      a,
      a:focus,
      a:hover {
        color: #fff;
      }

      .btn-default,
      .btn-default:hover,
      .btn-default:focus {
        color: #333;
        text-shadow: none;
        background-color: #fff;
        border: 1px solid #fff;
      }

      html,
      body {
        height: 100%;
        background-color: #333;
      }
      body {
        color: #fff;
        text-align: center;
        text-shadow: 0 1px 3px rgba(0,0,0,.5);
      }

      .site-wrapper {
        display: table;
        width: 100%;
        height: 100%; /* For at least Firefox */
        min-height: 100%;
        -webkit-box-shadow: inset 0 0 100px rgba(0,0,0,.5);
                box-shadow: inset 0 0 100px rgba(0,0,0,.5);
      }
      .site-wrapper-inner {
        display: table-cell;
        vertical-align: top;
      }
      .cover-container {
        margin-right: auto;
        margin-left: auto;
      }

      .inner {
        padding: 30px;
      }

      .cover {
        padding: 0 20px;
      }
      .cover .btn-lg {
        padding: 10px 20px;
        font-weight: bold;
      }

      @media (min-width: 768px) {
        .site-wrapper-inner {
          vertical-align: middle;
        }
        .cover-container {
          width: 100%; 
        }
      }

      @media (min-width: 992px) {
        .cover-container {
          width: 700px;
        }
      }

    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

  <body>

    <div class="site-wrapper">

      <div class="site-wrapper-inner">

        <div class="cover-container">

          <div class="inner cover">
            <h1 class="cover-heading">{{ trans('front/missing.error-404') }}</h1>
            <p class="lead">{{ trans('front/missing.info') }}</p>
            <p class="lead">
              <a href="{!! url('/') !!}" class="btn btn-lg btn-default">{{ trans('front/missing.button') }}</a>
            </p>
          </div>

        </div>

      </div>

    </div>

    {!! HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js') !!}
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
    {!! HTML::script('js/vendor/docs.min.js') !!}
    {!! HTML::script('js/vendor/ie10-viewport-bug-workaround.js') !!}
    
  </body>
</html>
