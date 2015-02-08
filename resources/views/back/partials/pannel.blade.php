<div class="col-lg-4 col-md-6">
    <div class="panel panel-{{ $color }}">
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-3">
                    <span class="fa fa-{{ $icone }} fa-5x"></span>
                </div>
                <div class="col-xs-9 text-right">
                <div class="huge">{{ $nbr['new'] }}</div>
                <div>{{ $name }}</div>
                </div>
            </div>
        </div>
        <a href="{{ $url }}">
        <div class="panel-footer">
            <span class="pull-left">{{ $nbr['total'] . ' ' . $total }}</span>
            <span class="pull-right fa fa-arrow-circle-right"></span>
            <div class="clearfix"></div>
        </div>
        </a>
    </div>
</div>