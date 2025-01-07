@push('meta')
    <meta name="robots" content="noindex,follow">
@endpush

@push('title')
    <title>Childcare Providers - User Login</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>User Login</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Account Login</h1>
            <?php if (isset($errorMessage)) :?>
            <p><?php echo $errorMessage; ?></p>
            <?php endif;?>
            <?php if (isset($user) && !$user->status) :?>
            <p>If you have not received an email to activate your account, click on <a href="/user/resend">Resend Activation
                    Email</a>. </p>
            <?php elseif (isset($errorMessage)) :?>
            <p>If you forget your password, click <a href="/user/reset">reset password</a>. </p>
            <?php endif; ?>
            <div class="child-srch">
                <form method="POST" id="login">
                    @csrf
                    <dl class="zend_form">
                        <dt id="username-label"><label for="email">Username (email):</label></dt>
                        <dd id="username-element">
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
                        <dt id="password-label"><label for="password">Password:</label></dt>
                        <dd id="password-element">
                            <input type="password" id="password" name="password" required>
                            @error('password')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="login-label">&nbsp;</dt>
                        <dd id="login-element">
                            <input type="submit" name="submit" value="Login">
                        </dd>
                    </dl>
                </form>
            </div>
            <br />
            <p>If you don't have a Login Account yet, please <a href="/user/new">Click here to Register</a>.</p>
        </section>
        <!---------right container------>
        <section class="right-sect">

        </section>
        <!-------right container ends------>
    </div>
@endsection
