@extends('layouts.default')

@section('content')

<div class="pb-lg-3 pt-lg-3 pb-0 pt-0">

    <div class="container-fluid main-container">

        <div class="row rowMedium">

            <div class="col-12 justify-content-center col_maincontent_active_Homepage">
                    
                <div class="row p-lg-5 p-md-5 ps-1 pe-1 pt-5 pb-5">

                    @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

                    @include('layouts.partials.legend_view',['is_editor' => $is_editor, 'course_code' => $course_code])
                    
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
                    
                    {!! isset($action_bar) ?  $action_bar : '' !!}

                    <div class='col-lg-6 col-12 d-none d-md-none d-lg-block'>
                        <div class='col-12 h-100 left-form'></div>
                    </div>

                    <div class='col-lg-6 col-12'>
                      <div class='form-wrapper form-edit p-3 rounded'>
                          
                          <form role='form' class='form-horizontal' action="{{ $_SERVER['SCRIPT_NAME'] }}?c={{$course->code}}" method='post'>                
                              <div class='form-group'>
                                      <label for='localize' class='col-sm-6 control-label-notes'>{{ trans('langAvailableTypes') }}</label>
                                  
                                      
                                      <div class='row mt-3'>
                                          <div class='col-1'>
                                            <input class='mt-2' id='courseopen' type='radio' name='formvisible' value='2'{!! $course->visible == 2 ? ' checked': '' !!}>
                                          </div>
                                          <div class='col-1'>
                                            {!! course_access_icon(COURSE_OPEN) !!}
                                          </div>     
                                          <div class='col-10'> 
                                            <span class='d-inline'>{{ trans('langPublic') }}</span>
                                          </div>
                                      </div>
                                      
                                      <div class='row mt-3'>
                                        <div class='col-1'>
                                          <input id='coursewithregistration' type='radio' name='formvisible' value='1'{!! $course->visible == 1 ? ' checked': '' !!}>
                                        </div>
                                        <div class='col-1'>
                                          {!! course_access_icon(COURSE_REGISTRATION) !!}
                                        </div>
                                        <div class='col-10'> 
                                          <span class='d-inline'>{{ trans('langPrivOpen') }}</span>
                                        </div> 
                            
                                      </div>


                                      <div class='row mt-3'>
                                        <div class='col-1'>
                                          <input id='courseclose' type='radio' name='formvisible' value='0'{!! $course->visible == 0 ? ' checked': '' !!}>
                                        </div> 
                                        <div class='col-1'>
                                          {!! course_access_icon(COURSE_CLOSED) !!}
                                        </div>
                                        <div class='col-10'>
                                          <span class='d-inline'>
                                              {{ trans('langClosedCourseShort') }}
                                          </span>
                                        </div>
                                      </div>


                                      <div class='row mt-3'>
                                        <div class='col-1'>
                                          <input id='courseinactive' type='radio' name='formvisible' value='3'{!! $course->visible == 3 ? ' checked': '' !!}>
                                        </div> 
                                        <div class='col-1'>
                                          {!!  course_access_icon(COURSE_INACTIVE) !!}
                                        </div>
                                        <div class='col-10'>
                                          <span class='help-block'>
                                              {{ trans('langInactiveCourse') }}
                                          </span>
                                        </div>
                                      </div>                   
                                 
                              </div>
                              <div class='form-group mt-5'>
                                  <div class='col-12'>
                                      <input class='btn btn-primary submitAdminBtn w-100' type='submit' name='submit' value='{{ trans('langModify') }}'>
                                  </div>
                              </div>
                          </form>
                      </div>
                    </div>
              </div>
          </div>
      </div>
    </div>
</div>
@endsection