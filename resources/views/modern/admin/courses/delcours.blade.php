@extends('layouts.default')

@section('content')

<div class="col-12 basic-section p-xl-5 px-lg-3 py-lg-5">

        <div class="row rowMargin">

            <div class="col-12 col_maincontent_active_Homepage">

                <div class="row">

                    @include('layouts.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

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
                        <div class='alert alert-danger'>
                            {{ trans('langCourseDelConfirm2') }}
                            <em>{{ course_id_to_title($course_id) }}</em>
                            <br><br>
                            <i>{{ trans('langNoticeDel') }}</i>
                            <br>
                        </div>
                    </div>

                    <div class='col-12'>
                        <ul class='list-group'>
                            <li class='list-group-item'>
                                <a href='{{ $_SERVER['SCRIPT_NAME'] }}?c={{ $course_id }}&amp;delete=yes&amp;{{ generate_csrf_token_link_parameter() }}' {!! $asktotp !!}>
                                <b>{{ trans('langYes') }}</b>
                                </a>
                            </li>
                            <li class='list-group-item'>
                                <a href='listcours.php'>
                                    <b>{{ trans('langNo') }}</b>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    
</div>
@endsection
