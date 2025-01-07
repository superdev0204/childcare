@push('title')
    <title>Password Reset</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>Password Reset</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Password Reset!</h1>
            <p><?php echo $message; ?></p>
            
            <form method="POST">
                @csrf
                <dl class="zend_form">
                    <dt id="email-label"><label for="email">Username (email):</label></dt>
                    <dd id="email-element">
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
                    </dd>
                    <dt id="recaptcha-label">&nbsp;</dt>
                    <dd id="recaptcha-element">
                        <input type="hidden" name="challenge" value="g-recaptcha-response">
                        <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>
                        <div class="g-recaptcha" data-sitekey="{{ env('DATA_SITEKEY') }}" data-theme="light" data-type="image"
                            data-size="normal">
                        </div>
                        @error('recaptcha-token')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="submit-label">&nbsp;</dt>
                    <dd id="submit-element">
                        <input type="submit" name="submit" value="Submit">
                    </dd>
                </dl>
            </form>
        </section>
    </div>
@endsection
