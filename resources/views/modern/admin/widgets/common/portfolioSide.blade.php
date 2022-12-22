<div class="panel panel-primary mt-3">
    <div class="panel-heading">
        <div class="panel-title">
            {{ trans('langPortfolioSidebarContent') }}
        </div>
    </div>
    <div class="panel-body Borders" id="portfolio_widget_sidebar" data-widget-area-id="4">
        @foreach ($portfolio_sidebar_widgets as $key => $portfolio_sidebar_widget)
        <div class="panel panel-success widget mt-3" data-widget-id="{{ $portfolio_sidebar_widget->id }}" data-widget-widget-area-id="{{ $key }}">
            <div class="panel-heading">
                <a class='text-white' data-bs-toggle="collapse" data-bs-target="#widget_desc_{{ $key }}" href="#widget_desc_{{ $key }}" class="widget_title">
                    {{ $portfolio_sidebar_widget->getName() }} <span></span>
                </a>
            </div>
            <div id="widget_desc_{{ $key }}" class="panel-collapse collapse in collapsed">
                <div class="panel-body">
                    {!! $portfolio_sidebar_widget->getOptionsForm($key) !!}
                </div>
                <div class="panel-footer clearfix d-flex justify-content-center align-items-center">
                    <a href="#" class="remove btn deleteAdminBtn">
                        {{ trans('langDelete') }}
                    </a>
                   
                    <a href="#" class="btn submitAdminBtn submitOptions ms-1">
                        {{ trans('langSubmit') }}
                    </a>
                    
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
