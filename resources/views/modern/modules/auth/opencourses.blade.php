@extends('layouts.default')

@if ($isInOpenCoursesMode)
    @push('head_styles')
        <link rel="stylesheet" type="text/css" href="{{ $urlAppend }}modules/course_metadata/course_metadata.css">
    @endpush
    @push('head_scripts')
        <script type="text/javascript">
            var dialog;

            var showMetadata = function(course) {
                $('.modal-body', dialog).load('anoninfo.php', {course: course}, function(response, status, xhr) {
                    if (status === "error") {
                        $('.modal-body', dialog).html("Sorry but there was an error, please try again");
                        //console.debug("jqxhr Request Failed, status: " + xhr.status + ", statusText: " + xhr.statusText);
                    }
                });
                dialog.modal('show');
            };

            $(document).ready(function() {
                dialog = $('<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{!! trans("langCancel") !!}</span></button><h4 class="modal-title" id="modal-label">{!! trans('langCourseMetadata') !!}</h4></div><div class="modal-body">body</div></div></div></div>');
            });
        </script>
    @endpush
@endif

@section('content')

<div class="col-12 main-section">
<div class='{{ $container }}'>
        <div class="row rowMargin">

                    @if(isset($_SESSION['uid']))
                        @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
                    @endif


                    @if(Session::has('message'))
                    <div class='col-12 all-alerts'>
                        <div class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">
                            @php 
                                $alert_type = '';
                                if(Session::get('alert-class', 'alert-info') == 'alert-success'){
                                    $alert_type = "<i class='fa-solid fa-circle-check fa-lg'></i>";
                                }elseif(Session::get('alert-class', 'alert-info') == 'alert-info'){
                                    $alert_type = "<i class='fa-solid fa-circle-info fa-lg'></i>";
                                }elseif(Session::get('alert-class', 'alert-info') == 'alert-warning'){
                                    $alert_type = "<i class='fa-solid fa-triangle-exclamation fa-lg'></i>";
                                }else{
                                    $alert_type = "<i class='fa-solid fa-circle-xmark fa-lg'></i>";
                                }
                            @endphp
                            
                            @if(is_array(Session::get('message')))
                                @php $messageArray = array(); $messageArray = Session::get('message'); @endphp
                                {!! $alert_type !!}<span>
                                @foreach($messageArray as $message)
                                    {!! $message !!}
                                @endforeach</span>
                            @else
                                {!! $alert_type !!}<span>{!! Session::get('message') !!}</span>
                            @endif
                            
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                    @endif

                    <div class="col-12 @if(isset($_SESSION['uid'])) mt-4 @endif">
                        <h1>{{ trans('langCourses') }}</h1>
                    </div>
                    <div class='col-12 mt-4'>
                        <div class='row rowMargin row-cols-1 row-cols-lg-2 g-5'>
                            <div class='col-lg-6 col-12'>
                                <div class='col-12'>
                                    <div class="card border-card h-100 Borders border-0">
                                        <div class="card-body p-0">

                                            
                                            @if (isset($buildRoots))
                                                {!! $buildRoots !!}
                                            @endif
                                           
                                            
                                            <div class='col-12 mt-4' id='accordion'>
                                                <ul class='list-group list-group-flush list-group-default'>
                                                    <li class="list-group-item">
                                                        <a class='btn list-group-btn collapsed d-flex justify-content-start align-items-start px-0' role='button' data-bs-toggle='collapse' href='#Category'>
                                                            <i class='fa-solid fa-chevron-down'></i>
                                                            &nbsp&nbsp{!! $tree->getFullPath($fc) !!}
                                                            
                                                            {{-- {!! $tree->getFullPath($fc, false, $_SERVER['SCRIPT_NAME'] . '?fc=') !!} --}}
                                                        </a>
                                                    </li>
                                                    <div id='Category' class='panel-collapse accordion-collapse collapse border-0 rounded-0' role='tabpanel' data-bs-parent='#accordion'>
                                                        {!! $childHTML !!}
                                                    </div>
                                                </ul>
                                            </div>
                                               
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-6 col-12'>
                                <img class='form-image' src='{{ $urlAppend }}template/modern/img/CoursesImg.png' />
                            </div>
                        </div>
                    </div>

                    


                    @if (count($courses) > 0)
                        <div class='col-12 mt-5'>
                            <div class='table-responsive'>
                                <table class='table-default' id="myopencourses_table">
                                    <thead>
                                        <tr class='list-header'>
                                            <th class='text-start'>{!! trans('langCourseCode') !!}</th>
                                        @if (isset($isInOpenCoursesMode))
                                            <th class='text-start' width='220'>{!! trans('langTeacher') !!}</th>
                                            <th class='text-start'width='30'>{!! trans('langOpenCoursesLevel') !!}</th>
                                        @else
                                            <th class='text-start' width='220'>{!! trans('langTeacher') !!}</th>
                                            <th width='30'>{!! trans('langType') !!}</th>
                                        @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($courses as $mycourse)
                                            <tr>
                                                <td>
                                                    @if ($mycourse->visible == COURSE_OPEN)
                                                        <a href="../../courses/{!! urlencode($mycourse->k) !!}/">{!! $mycourse->i !!}</a>&nbsp;<small>({!! $mycourse->c !!})</small>
                                                    @else
                                                        {!! $mycourse->i !!}&nbsp;<small>({!! $mycourse->c !!})</small>
                                                    @endif
                                                    @if ($displayGuestLoginLinks)
                                                        @if ($course_data[$mycourse->id]['userguest'])
                                                            <div class='float-end'>
                                                            @if ($course_data[$mycourse->id]['userguest']->password === '')
                                                                    <form method='post' action='{{ $urlAppend }}'>
                                                                        <input type='hidden' name='uname' value='{{ $course_data[$mycourse->id]['userguest']->username }}'>
                                                                        <input type='hidden' name='pass' value=''>
                                                                        <input type='hidden' name='next' value='/courses/{{ $mycourse->k }}/'>
                                                                        <button class='btn submitAdminBtn' type='submit' title='{!! trans('langGuestLogin') !!}' name='submit' data-toggle='tooltip'><span class='fa fa-plane'></span></button>
                                                                    </form>
                                                            @else
                                                                    <a class='btn submitAdminBtn' role='button' href='{{ $urlAppend }}main/login_form.php?user={!! urlencode($course_data[$mycourse->id]['userguest']->username) !!}&amp;next=%2Fcourses%2F{{ $mycourse->k }}%2F' title='{!! trans('langGuestLogin') !!}' data-toggle='tooltip'>
                                                                    <span class='fa fa-plane'></span></a>
                                                            @endif
                                                            </div>
                                                        @endif
                                                    @endif


                                                    <button class="ClickCourse border-0 rounded-pill bg-transparent float-end" id="{{$mycourse->k}}" type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{trans('langPreview')}}&nbsp;{{trans('langOfCourse')}}">
                                                        <i class='fa-solid fa-display'></i>
                                                    </button>

                                                    <!-- The Modal -->
                                                    <div id="myModal{{$mycourse->k}}" class="modal">

                                                        <!-- Modal content -->
                                                        <div class="modal-content modal-content-opencourses overflow-auto px-lg-5 py-lg-5">
                                                            <div class='col-12 d-flex justify-content-between align-items-start'>
                                                                <div>
                                                                    <span class='modal-title TextBold' style='font-size:22px;'>{{$mycourse->i}}</span>
                                                                    <span>({{$mycourse->c}})</span>
                                                                </div>
                                                                <div>
                                                                    <button type='button' class="close border-0 bg-white mt-2"><i class='fa-solid fa-xmark fa-lg Neutral-700-cl'></i></button>
                                                                </div>
                                                            </div>
                                                            
                                                            <hr class='hr-OpenCourses'>

                                                            <div class='row mb-3'>
                                                                <div class='col-9 d-flex justify-content-start align-items-start ps-4'>
                                                                    <p class='small-text TextRegular blackBlueText d-inline-flex align-items-center'>
                                                                        <span class='fa fa-user lightBlueText pe-2 pt-0'></span>
                                                                        <span class='blackBlueText'>{{$mycourse->t}}</span>
                                                                    </p>
                                                                </div>
                                                                <div class='col-3 d-flex justify-content-end align-items-center pe-4 blackBlueText'>
                                                                    {!! course_access_icon($mycourse->visible) !!}
                                                                    @if($mycourse->p == 1)
                                                                        <span class="fa fa-star Primary-600-cl ps-3" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="{{trans('langPopular')}} {{trans('langCourse')}}" aria-label="{{trans('langPopular')}} {{trans('langCourse')}}"></span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        
                                                            
                                                            <div class='col-12 d-flex justify-content-center align-items-start'>
                                                                @if($mycourse->img == NULL)
                                                                    <img class='openCourseImg' src="{{ $urlAppend }}template/modern/img/ph1.jpg" alt="{{ $mycourse->img }}" /></a>
                                                                @else
                                                                    <img class='openCourseImg' src="{{ $urlAppend }}courses/{{$mycourse->k}}/image/{{$mycourse->img}}" alt="{{ $mycourse->img }}" /></a>
                                                                @endif
                                                            </div>

                                                            <div class='col-12 openCourseDes mt-3 blackBlueText pb-3'>
                                                                @if(empty($mycourse->de))
                                                                    <p class='text-center'>{{ trans('langThisCourseDescriptionIsEmpty') }}</p>
                                                                @else
                                                                    {!! $mycourse->de !!}
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                
                                                </td>
                                                <td>
                                                    {!! $mycourse->t !!}
                                                </td>
                                                <td class='text-center'>
                                                @if ($isInOpenCoursesMode)
                                                    {!! CourseXMLElement::getLevel($mycourse->level) !!}&nbsp;
                                                    <a href='javascript:showMetadata("{!! $mycourse->k !!}");'><img src='{{ $themeimg }}/lom.png'/></a>
                                                @else
                                                    {!! course_access_icon($mycourse->visible) !!}
                                                @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class='col-12 mt-5'>
                            <div class='alert alert-warning'><i class='fa-solid fa-triangle-exclamation fa-lg'></i><span>{{ trans('langNoCourses')}}</span></div>
                        </div>
                    @endif
                
        </div>
    
</div>
</div>

<script type="text/javascript">
    var idCourse = '';
    var btn = '';
    var modal = '';
    $(".ClickCourse").click(function() {
        // Get the btn id
        idCourse = this.id;

        // Get the modal
        modal = document.getElementById("myModal"+idCourse);

        // Get the button that opens the modal
        btn = document.getElementById(idCourse);

        // When the user clicks the button, open the modal 
        modal.style.display = "block";

        $('[data-bs-toggle="tooltip"]').tooltip("hide");
    });

    $(".close").click(function() {
        modal.style.display = "none";
    });

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        $('[data-bs-toggle="tooltip"]').tooltip("hide");
    }

</script>

@endsection


