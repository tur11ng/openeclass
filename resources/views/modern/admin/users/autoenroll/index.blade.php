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

                    @if ($rules)
                        @foreach ($rules as $key => $rule)
                        <div class='col-12 mb-3'>
                            <div class='panel panel-action-btn-default'>
                                <div class='panel-heading pt-1 pb-1 d-flex justify-content-center align-items-center'>
                                    <div class='row w-100'>
                                        <div class='col-9 d-flex justify-content-start align-items-center ps-0'>
                                            <div class='panel-title'>{{ trans('langAutoEnrollRule') }} {{ $key + 1 }}</div>
                                        </div>
                                        <div class='col-3 d-flex justify-content-end align-items-center pe-0'>
                                            <div>
                                                {!! action_button([
                                                    [
                                                        'title' => trans('langEditChange'),
                                                        'icon' => 'fa-edit',
                                                        'url' => "autoenroll.php?edit=" . getIndirectReference($rule['id'])
                                                    ],
                                                    [
                                                        'title' => trans('langDelete'),
                                                        'icon' => 'fa-times',
                                                        'url' => "autoenroll.php?delete=" . getIndirectReference($rule['id']),
                                                        'confirm' => trans('langSureToDelRule'),
                                                        'btn-class' => 'delete_btn btn-default'
                                                    ],
                                                ]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class='panel-body'>
                                    <div>
                                        {{ trans('langApplyTo') }}: <b>{{ $rule['status'] == USER_STUDENT ? trans('langStudents'): trans('langTeachers') }}</b>
                                        @if ($rule['deps'])
                                            {{ trans('langApplyDepartments') }}:
                                            <ul>
                                            @foreach ($rule['deps'] as $dep)
                                                <li>{{ getSerializedMessage($dep->name) }}</li>
                                            @endforeach
                                            </ul>
                                        @else
                                            {{ trans('langApplyAnyDepartment') }}:
                                            <br>                 
                                        @endif
                                        @if ($rule['courses'])
                                            {{ trans('langAutoEnrollCourse') }}:
                                            <ul>
                                            @foreach ($rule['courses'] as $course)
                                                <li>
                                                    <a href='{{ $urlAppend }}courses/{{ $course->code }}/'>
                                                        {{ $course->title }}
                                                    </a> 
                                                    ({{ $course->public_code }})
                                                </li>
                                            @endforeach
                                            </ul>
                                        @endif
                                        @if ($rule['auto_enroll_deps'])
                                            {{ trans('langAutoEnrollDepartment') }}:
                                            <ul>
                                            @foreach ($rule['auto_enroll_deps'] as $auto_enroll_dep)
                                                <li>
                                                    <a href='{{ $urlAppend }}modules/auth/courses.php?fc={{ $auto_enroll_dep->id }}'>
                                                        {{ getSerializedMessage($auto_enroll_dep->name) }}
                                                    </a>
                                                </li>
                                            @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>   
                        </div>                 
                        @endforeach
                    @else
                        <div class='col-12'>
                            <div class='alert alert-warning text-center'> {{ trans('langNoRules') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection