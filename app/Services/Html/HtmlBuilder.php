<?php namespace App\Services\Html;

use Illuminate\Html\HtmlBuilder as IlluminateHtmlBuilder;

class HtmlBuilder extends IlluminateHtmlBuilder {

	public function backEntete($titre, $icone, $fil)
	{
		return sprintf('
		  <div class="row">
		      <div class="col-lg-12">
		          <h1 class="page-header">
		              %s 
		          </h1>
		          <ol class="breadcrumb">
		              <li class="active">
		                  <span class="fa fa-%s"></span> %s
		              </li>
		          </ol>
		      </div>
		  </div>',
		  $titre,
		  $icone,
			$fil
		);		
	}

	public function alert($type, $error)
	{
		return sprintf('
			<div class="alert alert-%s alert-dismissible" role="alert">
	  		<button type="button" class="close" data-dismiss="alert">
	  			<span aria-hidden="true">&times;</span>
	  			<span class="sr-only">Close</span>
	  		</button>
	  		%s
			</div>',
			$type,
		  $error
		);			
	}

	public function panneauAdmin($color, $icone, $nbr, $nom, $url, $detail)
	{
		return sprintf('
      <div class="col-lg-4 col-md-6">
          <div class="panel panel-%s">
              <div class="panel-heading">
                  <div class="row">
                      <div class="col-xs-3">
                          <span class="fa fa-%s fa-5x"></span>
                      </div>
                      <div class="col-xs-9 text-right">
                          <div class="huge">%s</div>
                          <div>%s</div>
                      </div>
                  </div>
              </div>
              <a href="%s">
                  <div class="panel-footer">
                      <span class="pull-left">%s</span>
                      <span class="pull-right fa fa-arrow-circle-right"></span>
                      <div class="clearfix"></div>
                  </div>
              </a>
          </div>
      </div>',
      $color,
			$icone,
		  $nbr,
		  $nom,
		  $url,
		  $detail
		);			
	}

}
