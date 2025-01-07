@if (isset($_GET['pid']))
    @push('meta')
        <meta name="robots" content="noindex,nofollow">
    @endpush
@endif

@push('title')
    <title>Contact ChildcareCenter.us</title>
@endpush

@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>

    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>Contact Us</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Contact Childcare Center</h1>
            <p><?php echo $message; ?></p>

            @if ($message != 'Email sent successfully')            
                <div class="widget">                
                    <form method="post">
                        @csrf
                        <dl class="zend_form">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group-bs">
                                        <div id="name-label"><label class="required" for="name">Your Name:</label></div>
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
                                        <div id="email-label"><label class="required" for="email">Your e-mail
                                                address:</label></div>
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
                            <dt id="subject-label"><label class="required" for="subject">Subject:</label></dt>
                            <dd id="subject-element">
                                @if (isset($request->subject))
                                    <input type="text" id="subject" name="subject" value="{{ $request->subject }}">
                                @else
                                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}">
                                @endif
                                @error('subject')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="message-label"><label class="required" for="message">Message:</label></dt>
                            <dd id="message-element">
                                @if (isset($request->message))
                                    <textarea id="message" name="message" cols="15" rows="5">{{ $request->message }}</textarea>
                                @else
                                    <textarea id="message" name="message" cols="15" rows="5">{{ old('message') }}</textarea>
                                @endif
                                @error('message')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
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
                                <div class="form-action-row__submit-container" id="sendEmail-element">
                                    <input type="hidden" name="referer" value="{{ isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' }}">
                                    <input type="submit" name="submit" value="Send E-mail">
                                </div>
                            </div>
                        </dl>
                    </form>
                    
                    <script>
                        CKEDITOR.replace('message', {
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
                    <li>To add or update your day care information, click on <b>Manage Listing</b> link on the navigation
                        bar and
                        <a href="provider">Add/Update Daycare Information</a>
                    </li>
                    <li>Need to contact a daycare provider? Please contact them directly through their listing or profile
                        page.</li>
                </ol>
            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
