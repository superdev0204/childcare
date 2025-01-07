@push('meta')
    <meta name="robots" content="noindex,follow">
@endpush

@extends('layouts.app')

@section('content')
    <div id="content" class="clearfix">
        <div id="center">
            <form method="post">
                @csrf
                <dl class="zend_form">
                    <dt id="challenge-label">&nbsp;</dt>
                    <dd id="challenge-element">
                        <input type="hidden" name="challenge" value="g-recaptcha-response">
                        <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>
                        <div class="g-recaptcha" data-sitekey="{{ env('DATA_SITEKEY') }}" data-theme="light"
                            data-type="image" data-size="normal">
                        </div>
                        @error('recaptcha-token')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="verify-label">&nbsp;</dt>
                    <dd id="verify-element"><input type="submit" name="submit" value="Verify"></dd>
                </dl>
            </form>
        </div>
    </div>
@endsection
