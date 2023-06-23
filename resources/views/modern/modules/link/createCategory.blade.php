@extends('layouts.default')

@section('content')


<div class="col-12 basic-section p-xl-5 px-lg-3 py-lg-5">

        <div class="row rowMargin">

            <div id="background-cheat-leftnav" class="col-xl-2 col-lg-3 col_sidebar_active d-flex justify-content-start align-items-strech ps-lg-0 pe-lg-3"> 
                <div class="d-none d-sm-block d-sm-none d-md-block d-md-none d-lg-block ContentLeftNav">
                    @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                </div>
            </div>

            <div class="col-xl-10 col-lg-9 col-12 col_maincontent_active p-lg-5">
                    
                <div class="row">

                    @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])


                    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="collapseTools" aria-labelledby="offcanvasExampleLabel">
                        <div class="offcanvas-header">
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            @include('layouts.partials.sidebar',['is_editor' => $is_editor])
                        </div>
                    </div>


                    @include('layouts.partials.legend_view',['is_editor' => $is_editor, 'course_code' => $course_code])
                    
                    {!! isset($action_bar) ?  $action_bar : '' !!}

                    @if(Session::has('message'))
                    <div class='col-12 all-alerts'>
                        <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">
                            @if(is_array(Session::get('message')))
                                @php $messageArray = array(); $messageArray = Session::get('message'); @endphp
                                @foreach($messageArray as $message)
                                    {!! $message !!}
                                @endforeach
                            @else
                                {!! Session::get('message') !!}
                            @endif
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                    @endif
                    
                    

                    <div class='col-12'>
                        <div class='form-wrapper form-edit rounded'>
                            
                            <form class = 'form-horizontal' role='form' method='post' action='index.php?course={{ $course_code }}&urlview={{ $urlview }}'>
                                @if ($action == 'editcategory')
                                    <input type='hidden' name='id' value='{{ getIndirectReference($id) }}'>
                                @endif
                                <fieldset>

                                    <div class="form-group{{ $categoryNameError ? ' has-error' : ''}}">
                                    <label for='CatName' class='col-sm-6 control-label-notes'>{{ trans('langCategoryName') }}</label>
                                    <div class='col-sm-12'>
                                        <input class='form-control' type='text' name='categoryname' size='53' placeholder='{{ trans('langCategoryName') }}' value='{{ isset($category) ? $category->name : "" }}'>
                                        {!! Session::getError('categoryname', "<span class='help-block'>:message</span>") !!}
                                    </div>
                                    </div>

                                  

                                    <div class='form-group mt-4'>
                                        <label for='CatDesc' class='col-sm-6 control-label-notes'>{{ trans('langDescription') }}</label>
                                        <div class='col-sm-12'>
                                            <textarea class='form-control' rows='5' name='description'>{{ isset($category) ? $category->description : "" }}</textarea>
                                        </div>
                                    </div>

                                  
                                    
                                    <div class='form-group mt-5'>
                                        <div class='col-12 d-flex justify-content-center align-items-center'>
                                           
                                                
                                                    <input type='submit' class='btn submitAdminBtn' name='submitCategory' value="{{ $form_legend }}">
                                               
                                                    <a href='index.php?course={{ $course_code }}' class='btn cancelAdminBtn ms-1'>{{ trans('langCancel') }}</a>
                                               
                                           
                                        </div>
                                    </div>
                                </fieldset>
                                {!! generate_csrf_token_form_field() !!}
                            </form>
                        </div>
                    </div>

                </div>
            </div>


        </div>
    
</div>
@endsection