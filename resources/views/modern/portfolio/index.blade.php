@extends('layouts.default')

@section('content')

@if($_SESSION['status'] == USER_TEACHER or $is_power_user or $is_departmentmanage_user)
    <style>#btn_create_course{display:block;}</style>
@else
    <style>#btn_create_course{display:none;}</style>
@endif

<div class="col-12 basic-section basic-section-mobile p-xl-5 px-lg-3 py-lg-5">


    <div class="row rowMargin">
        <div class="col-12 px-0">
            <div class='card panelCard BorderSolid px-lg-4 py-lg-3 border-0'>
                <div class='card-header border-0 bg-white d-flex justify-content-between align-items-center'>
                    <div class='text-uppercase normalColorBlueText TextBold fs-6'>{{ trans('langSummaryProfile') }}</div>
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-md-2 col-12'>
                            <img class="user-detals-photo m-auto d-block" src="{{ user_icon($uid, IMAGESIZE_LARGE) }}" alt="{{ $_SESSION['surname'] }} {{ $_SESSION['givenname'] }}">
                            <p class="text-center blackBlueText mt-3"> {{ $_SESSION['uname'] }} </p>
                        </div>
                        <div class='col-md-6 col-12 ps-lg-2 ps-md-5'>
                            <h6 class='text-md-start text-center blackBlueText TextBold mb-0'> {{ $_SESSION['surname'] }} {{ $_SESSION['givenname'] }} </h6>
                            <p class='text-md-start text-center small-text TextMedium blackBlueText mb-4'>
                                @if(($session->status == USER_TEACHER))
                                    {{ trans('langMetaTeacher') }}
                                @elseif(($session->status == USER_STUDENT))
                                    {{ trans('langCStudent') }}
                                @else
                                    {{ trans('langAdministrator')}}
                                @endif
                            </p>
                            <p class='text-md-start text-center blackBlueText TextRegular mb-5'>
                                {{ trans('langProfileLastVisit') }}&nbsp:&nbsp{{ format_locale_date(strtotime($lastVisit->when)) }}
                            </p>
                            <div class='d-flex justify-content-md-start justify-content-center mt-3'>
                                <a class='btn submitAdminBtn' href='{{ $urlAppend }}main/profile/display_profile.php'>{{ trans('langMyProfile') }}</a>
                            </div>
                            
                        </div>
                        <div class='col-md-4 col-12'>
                            <ol class="list-group list-group-numbered mt-md-0 mt-4">
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold blackBlueText">{{ trans('langSumCoursesEnrolled') }}</div>
                                    </div>
                                    <span class="badge bgTheme rounded-pill">{{ $student_courses_count }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold blackBlueText">{{ trans('langSumCoursesSupport') }}</div>
                                    </div>
                                    <span class="badge bgTheme rounded-pill">{{ $teacher_courses_count }}</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
    <div class="row rowMargin">
        <div class='col-xl-8 col-12 Courses-Content pe-lg-0 mt-lg-3 mt-4 px-0'>
            <div class='card panelCard BorderSolid panelCardNoBorder px-lg-4 py-lg-3'>
                <div class='card-header border-0 bg-white d-flex justify-content-between align-items-center'>
                    <span class="text-uppercase normalColorBlueText TextBold fs-6">{{ trans('langMyCoursesSide') }}</span>
                    
                    <div>
                        <div id="bars-active" type='button' class='float-end mt-0' style="display:flex;">
                            <div id="cources-bars-button" class="collapse-cources-button lightBlueText">
                                <span class="list-style active pe-2"><i class="fas fa-custom-size fa-bars custom-font" style='font-size:15px;'></i></span>
                            </div>
                            <div id="cources-pics-button" class="collapse-cources-button text-secondary collapse-cources-button-deactivated" onclick="switch_cources_toggle()">
                                <span class="grid-style"><i class="fas fa-custom-size fa-th-large custom-font" style='font-size:15px;'></i></span>
                            </div>
                        </div>
                        <div id="pics-active" type='button' class='float-end mt-0' style="display:none">
                            <div id="cources-bars-button" class="collapse-cources-button text-secondary collapse-cources-button-deactivated" onclick="switch_cources_toggle()">
                                <span class="list-style active pe-2"><i class="fas fa-custom-size fa-bars custom-font" style='font-size:15px;'></i></span>
                            </div>
                            <div id="cources-pics-button" class="collapse-cources-button lightBlueText">
                                <span class="grid-style"><i class="fas fa-custom-size fa-th-large custom-font" style='font-size:15px;'></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='card-body'>
                    <div class='container-fluid p-0'>
                        <div class='row rowMargin px-lg-2'>
                            @if(Session::has('message'))
                                <div class='col-12 mt-3 px-0'>
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }} alert-dismissible fade show" role="alert">
                                        @if(is_array(Session::get('message')))
                                            @php $messageArray = array(); $messageArray = Session::get('message'); @endphp
                                            @foreach($messageArray as $message)
                                                {!! $message !!}
                                            @endforeach
                                        @else
                                            {!! Session::get('message') !!}
                                        @endif
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </p>
                                </div>
                            @endif
                            <div id="cources-bars" class="col-12 px-lg-1 px-0">
                                {!! $perso_tool_content['lessons_content'] !!}
                            </div>

                            <div id="cources-pics" class="col-12 px-lg-1 px-0" style="display:none;">
                                <div class="row rowMargin cources-pics-page px-lg-2" id="cources-pics-page-1">
                                    @php $i=0; @endphp
                                    @foreach($cources as $cource)
                                    <div class="col-md-6 col-12 @if($i==0 or $i==2) ps-lg-1 ps-md-0 pe-md-2 @else pe-lg-1 ps-md-2 pe-md-0 @endif ps-0 pe-0 portfolioCourseColBar d-flex justify-content-center align-items-strech">
                                        <div class="lesson border-bottom pb-1 pt-3 w-100">
                                            <figure class="lesson-image">
                                                <a href="{{$urlServer}}courses/{{$cource->code}}/index.php">
                                                <picture>
                                                    @if($cource->course_image == NULL)
                                                        <img class="imageCourse mb-md-2 mb-0" src="{{ $urlAppend }}template/modern/img/ph1.jpg" alt="{{ $cource->course_image }}" /></a>
                                                    @else
                                                        <img class="imageCourse mb-md-2 mb-0" src="{{$urlAppend}}courses/{{$cource->code}}/image/{{$cource->course_image}}" alt="{{ $cource->course_image }}" /></a>
                                                    @endif
                                                </picture>
                                            </figure>
                                            <div class="lesson-title">
                                                <a class='TextSemiBold fs-6' href="{{$urlServer}}courses/{{$cource->code}}/index.php">{{ $cource->title }}</a>
                                                <span class="TextSemiBold blackBlueText lesson-id">({{ $cource->public_code }})</span>
                                            </div>
                                            <div class="small-text textgreyColor TextSemiBold mt-0">{{ $cource->professor }}</div>
                                        </div>

                                    </div>
                                        @if( $i>0 && ($i+1)%$items_per_page==0 )
                                </div>
                                <div class="row cources-pics-page ps-lg-1 pe-lg-2 ps-3 pe-3" style="display:none;" id="cources-pics-page-{{ceil($i/$items_per_page)+1}}" >
                                        @endif
                                        @php $i++; @endphp
                                    @endforeach
                                </div>
                                @include('portfolio.portfolio-courcesnavbar', ['paging_type' => 'pics', 'cource_pages' => $cource_pages ,'cources' => $cources])
                            </div>

                        </div>
                    </div>
                </div>

                @if($portfolio_page_main_widgets)
                    <div class='panel panel-admin border-0 bg-white mt-lg-3 mt-3 py-md-4 px-md-4 py-3 px-3 shadow-none'>
                        {!! $portfolio_page_main_widgets !!}
                    </div>
                @endif

            </div>
        </div>
        <div class='col-xl-4 col-12 ColumnCalendarAnnounceMessagePortfolio mt-lg-3 mt-3 ps-xl-3 px-lg-0 px-0 pb-lg-0 pb-3'>
            @include('portfolio.portfolio-calendar')
            <div class='card panelCard border-0 BorderSolid mt-lg-3 mt-4 py-lg-3 px-lg-4 py-0 px-0 shadow-none'>
                <div class='card-header bg-white border-0 text-start'>
                    <span class='text-uppercase normalColorBlueText TextBold fs-6'>{{ trans('langMyPersoAnnouncements') }}</span>
                </div>
                <div class='card-body'>
                    @if(empty($user_announcements))
                        <div class='text-start mb-3'><span class='text-title not_visible'>{{ trans('langNoRecentAnnounce') }}</span></div>
                    @else
                        {!! $user_announcements !!}
                    @endif
                </div>
                <div class='card-footer d-flex justify-content-start border-0 bg-white'>
                    <a class='all_announcements ps-0' href="{{$urlAppend}}modules/announcements/myannouncements.php">
                        {{ trans('langAllAnnouncements') }} <span class='fa fa-chevron-right'></span>
                    </a>
                </div>
            </div>

            <div class='card panelCard border-0 BorderSolid bg-white mt-lg-3 mt-4 py-lg-3 px-lg-4 py-0 px-0 shadow-none'>
                <div class='card-header bg-white border-0 text-start'>
                
                    <span class='text-uppercase normalColorBlueText TextBold fs-6'>{{ trans('langMyPersoMessages') }}</span>
                    
                </div>
                <div class='card-body'>
                    @if(empty($user_messages))
                        <div class='text-start mb-3'><span class='text-title not_visible'>{{ trans('langDropboxNoMessage') }}</span></div>
                    @else
                        {!! $user_messages !!}
                    @endif
                </div>
                <div class='card-footer d-flex justify-content-start border-0 bg-white'>
                    <a class='all_messages ps-0' href="{{$urlAppend}}modules/message/index.php">
                        {{ trans('langAllMessages') }} <span class='fa fa-chevron-right'></span>
                    </a>
                </div>
            </div>

            @if($portfolio_page_sidebar_widgets)
                <div class='card panelCard border-0 BorderSolid bg-white mt-lg-3 mt-4 py-lg-3 px-lg-4 py-0 px-0 shadow-none'>
                    <div class='card-header bg-white border-0 text-start'>
                    
                            <span class='text-uppercase normalColorBlueText TextBold fs-6'>{{ trans('langMyWidgets') }}</span>
                        
                    </div>
                    <div class='card-body'>
                        {!! $portfolio_page_sidebar_widgets !!}
                    </div>
                </div>
            @endif

        </div>
    </div>
    

</div>

<script>
    var user_cources = <?php echo json_encode($cources); ?>;
    var user_cource_pages = <?php echo $cource_pages; ?>;
</script>

<script type="text/javascript">
    var idCoursePortfolio = '';
    var btnPortfolio = '';
    var modal_portfolio = '';
    $(".ClickCoursePortfolio").click(function() {
        // Get the btn id
        idCoursePortfolio = this.id;

        // Get the modal
        modal_portfolio = document.getElementById("PortfolioModal"+idCoursePortfolio);

        // Get the button that opens the modal
        btnPortfolio = document.getElementById(idCoursePortfolio);

        // When the user clicks the button, open the modal 
        modal_portfolio.style.display = "block";

        $('[data-bs-toggle="tooltip"]').tooltip("hide");
    });

    $(".close").click(function() {
        modal_portfolio.style.display = "none";
    });

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal_portfolio) {
            modal_portfolio.style.display = "none";
        }
        $('[data-bs-toggle="tooltip"]').tooltip("hide");
    }

</script>
@endsection
