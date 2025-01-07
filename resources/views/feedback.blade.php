@push('title')
    <title>Feedback and Suggestions</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>Feedback/Suggestions</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1> Help Us Improve Our Website</h1>
            <p><?php echo $message; ?></p>

            @if( !isset($testimonial) )
                <div class="widget">
                    <form method="post">
                        @csrf
                        <dl class="zend_form">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group-bs">
                                        <div id="name-label"><label for="name">Your Name :</label></div>
                                        <div id="name-element">
                                            @if (isset($request->name))
                                                <input type="text" id="name" name="name" value="{{ $request->name }}">
                                            @else
                                                <input type="text" id="name" name="name" value="{{ old('name') }}">
                                            @endif
                                            @error('name')
                                                <ul>
                                                    <li>{{ $message }}</li>
                                                </ul>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group-bs">
                                        <div id="email-label"><label for="email">Email Address:</label></div>
                                        <div id="email-element">
                                            @if (isset($request->email))
                                                <input type="email" id="email" name="email" value="{{ $request->email }}">
                                            @else
                                                <input type="email" id="email" name="email" value="{{ old('email') }}">
                                            @endif
                                            @error('email')
                                                <ul>
                                                    <li>{{ $message }}</li>
                                                </ul>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <dt id="location-label"><label for="location">Your Location (city and state):</label></dt>
                            <dd id="location-element">
                                @if (isset($request->location))
                                    <input type="text" id="location" name="location" value="{{ $request->location }}">
                                @else
                                    <input type="text" id="location" name="location" value="{{ old('location') }}">
                                @endif
                                @error('location')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="pros-label"><label for="pros">What do you like about our website?</label></dt>
                            <dd id="pros-element">
                                @if (isset($request->pros))
                                    <textarea id="pros" name="pros" cols="15" rows="5">{{ $request->pros }}</textarea>
                                @else
                                    <textarea id="pros" name="pros" cols="15" rows="5">{{ old('pros') }}</textarea>
                                @endif
                                @error('pros')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="cons-label"><label for="cons">What don't you like about our website?</label></dt>
                            <dd id="cons-element">
                                @if (isset($request->cons))
                                    <textarea id="cons" name="cons" cols="15" rows="5">{{ $request->cons }}</textarea>
                                @else
                                    <textarea id="cons" name="cons" cols="15" rows="5">{{ old('cons') }}</textarea>
                                @endif
                                @error('cons')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="suggestion-label"><label for="suggestion">How should we improve the website?</label></dt>
                            <dd id="suggestion-element">
                                @if (isset($request->suggestion))
                                    <textarea id="suggestion" name="suggestion" cols="15" rows="5">{{ $request->suggestion }}</textarea>
                                @else
                                    <textarea id="suggestion" name="suggestion" cols="15" rows="5">{{ old('suggestion') }}</textarea>
                                @endif
                                @error('suggestion')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                                <div class="form-action-row">
                                    <div id="challenge-element">
                                        <input type="hidden" name="challenge" value="g-recaptcha-response">
                                        <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>
                                        <div class="g-recaptcha" data-sitekey="{{ env("DATA_SITEKEY") }}" data-theme="light" data-type="image" data-size="normal">                                
                                        </div>
                                        @error('recaptcha-token')
                                            <ul>
                                                <li>{{ $message }}</li>
                                            </ul>
                                        @enderror
                                    </div>
                                    <div class="form-action-row__submit-container" id="sendEmail-element"><input type="submit"
                                            name="submit" value="Submit"></div>
                                </div>
                            </dd>
                        </dl>
                    </form>
                    <script ez-screx="true">
                        CKEDITOR.replace('message', {
                            toolbarGroups: [{
                                name: 'clipboard',
                                groups: ['clipboard', 'undo']
                            }, {
                                name: 'basicstyles',
                                groups: ['basicstyles', 'cleanup']
                            }, {
                                name: 'links'
                            }]
                        });
                    </script>
                </div>
            @endif
        </section>
        <!---------right container------>
        <section class="right-sect">
            <div class="listSidebar">
                <h2>Lastest Feedback</h2>
                <?php
                $i = 0;
                /** @var \Application\Domain\Entity\Testimonial $testimonial */
                foreach ($testimonials as $testimonial):
                    $i++;
                ?>
                <p>"<?php echo $testimonial->comments; ?>" - <strong><?php echo $testimonial->name; ?></strong></p>
                <?php if ($i == 2):?>
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle" style="display:inline-block;width:180px;height:150px"
                    data-ad-client="ca-pub-8651736830870146" data-ad-slot="4560550338"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
                <?php endif; ?>
                <?php endforeach;?>
            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
