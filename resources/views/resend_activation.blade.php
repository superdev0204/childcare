@push('title')
    <title>Resend Activation Email</title>
@endpush

@extends('layouts.app_old')

@section('content')
    <div id="content" class="clearfix">
        <div id="right">
            <!-- widget -->
            <div class="widget">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle" style="display:inline-block;width:160px;height:600px"
                    data-ad-client="ca-pub-8651736830870146" data-ad-slot="3476733769"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </div>

        <div id="left">
            <a href="/">Home</a> &gt;&gt; Resend Activation Email Reset
            <h1>Resend Activation Email</h1>
            <?php if (isset($message)) :?>
            <p><?php echo $message; ?></p>
            <?php endif;?>

            @if (!$user_info)
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="email-label"><label class="required" for="email">Username (email):</label></dt>
                        <dd id="email-element">
                            @if (isset($request->email))
                                <input type="email" id="email" name="email" value="{{ $request->email }}">
                            @else
                                <input id="email" name="email" type="email" value="{{ old('email') }}">
                            @endif
                            @error('email')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="submit-label">&nbsp;</dt>
                        <dd id="submit-element"><input type="submit" name="submit" value="Submit"></dd>
                    </dl>
                </form>
            @endif
        </div>
    </div>
@endsection
