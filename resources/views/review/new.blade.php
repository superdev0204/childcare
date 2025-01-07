@if (isset($_GET['pid']))
    @push('meta')
        <meta name="robots" content="noindex,nofollow">
    @endpush
@endif

@push('title')
    <title>Create Your Own Review</title>
@endpush

@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>

    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a> &gt;&gt; </li>
                <li>Add New Review</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Create your review for: </h1>

            <strong><?php echo $provider->name; ?></strong><br />
            <strong><?php echo $provider->address . '<br/> ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br /></strong><br />

            <?php if (isset($message)): ?>
            <h4><?php echo $message; ?></h4>
            <?php endif; ?>

            @if (!isset($review->facility_id))
                <div class="widget">
                    <form method="post">
                        @csrf
                        <dl class="zend_form">
                            <dt id="email-label"><label class="required" for="email">Email address (will not be
                                    published):</label></dt>
                            <dd id="email-element">
                                @if( isset($request->email) )
                                    <input type="email" id="email" name="email" value="{{$request->email}}">
                                @else
                                    <input type="email" id="email" name="email" value="{{ old('email') ? old('email') : ($user ? $user->email : '') }}">
                                @endif
                                @error('email')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="name-label"><label class="required" for="name">Display name:</label></dt>
                            <dd id="name-element">
                                @if( isset($request->name) )
                                    <input type="text" id="name" name="name" value="{{$request->name}}">
                                @else
                                    <input type="text" id="name" name="name" value="{{ old('name') ? old('name') : ($user ? $user->firstname : '') }}">
                                @endif
                                @error('name')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="experience-label"><label class="required" for="experience">Which best describes your experience?:</label></dt>
                            <dd id="experience-element">
                                <select id="experience" name="experience">
                                    <option value="" selected="">Select from below</option>
                                    @if( isset($request->experience) )
                                        @foreach($experience as $key => $value)
                                            @if($key == $request->experience)
                                                <option value='{{ $key }}' selected>{{ $value }}</option>
                                            @else
                                                <option value='{{ $key }}'>{{ $value }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach($experience as $key => $value)
                                            @if($key == old('experience'))
                                                <option value='{{ $key }}' selected>{{ $value }}</option>
                                            @else
                                                <option value='{{ $key }}'>{{ $value }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('experience')
                                    <ul style="clear: both">
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="rating-label"><label class="required" for="rating">Rating (1=poor, 5=excellent):</label>
                            </dt>
                            <dd id="rating-element">
                                <select id="rating" name="rating">
                                    <option value="">Select Your Rating</option>
                                    @if( isset($request->rating) )
                                        @foreach($rating as $key => $value)
                                            @if($key == $request->rating)
                                                <option value='{{ $key }}' selected>{{ $value }}</option>
                                            @else
                                                <option value='{{ $key }}'>{{ $value }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach($rating as $key => $value)
                                            @if($key == old('rating'))
                                                <option value='{{ $key }}' selected>{{ $value }}</option>
                                            @else
                                                <option value='{{ $key }}'>{{ $value }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('rating')
                                    <ul style="clear: both">
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="comments-label"><label class="required" for="comments">Write your review:</label></dt>
                            <dd id="comments-element">
                                @if( isset($request->comments) )
                                    <textarea cols="15" rows="5" id="comments" name="comments">{{$request->comments}}</textarea>
                                @else
                                    <textarea cols="15" rows="5" id="comments" name="comments">{{old('comments')}}</textarea>
                                @endif
                                @error('comments')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="challenge-label">&nbsp;</dt>
                            <dd id="challenge-element">
                                <input type="hidden" name="challenge" value="g-recaptcha-response">
                                <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>
                                <div class="g-recaptcha" data-sitekey="{{ env("DATA_SITEKEY") }}" data-theme="light" data-type="image" data-size="normal">                                
                                </div>
                                @error('recaptcha-token')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                                <input type="hidden" name="pid" value="{{ $provider->id }}">
                            </dd>
                            <dt id="addReview-label">&nbsp;</dt>
                            <dd id="addReview-element"><input type="submit" name="submit" value="Add Review"></dd>
                        </dl>
                    </form>

                    <script>
                        CKEDITOR.replace('comments', {
                            toolbarGroups: [{
                                    name: 'clipboard',
                                    groups: ['clipboard', 'undo']
                                },
                                {
                                    name: 'basicstyles',
                                    groups: ['basicstyles', 'cleanup']
                                },
                                {
                                    name: 'links'
                                }
                            ]
                        });
                    </script>
                </div>
            @endif
        </section>
        <!---------right container------>
        <section class="right-sect">
            <div class="listSidebar">
                <h2>Notes:</h2>
                <ol>
                    <li>
                        <p>Write a review about <?php echo $provider->name; ?>. Let other families know what’s great, or what could be
                            improved.</p>
                    </li>
                    <li>Please read our brief <a href="/review/guidelines" target="_blank">review guidelines</a> to make
                        your review as helpful as possible.</li>
                </ol>

            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
