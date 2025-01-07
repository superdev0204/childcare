@push('title')
    <title>Password Reset</title>
@endpush

@extends('layouts.app_old')

@section('content')
    <div id="content" class="clearfix">
        <div id="right">
            <!-- widget -->
            <div class="widget">

            </div>
        </div>
        <div id="left">
            <a href="/">Home</a> &gt;&gt; Password Reset
            <h1>Password Reset!</h1>
            <?php if (isset($message)) :?>
            <p><?php echo $message; ?></p>
            <?php endif;?>

            <form method="POST">
                @csrf
                <dl class="zend_form">
                    <dt id="password-label"><label for="password">Password:</label></dt>
                    <dd id="password-element">
                        <input type="password" id="password" name="password" autocomplete="off" value="">
                        @error('password')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="confirmpassword-label"><label for="confirmpassword">Retype Password:</label></dt>
                    <dd id="confirmpassword-element">
                        <input type="password" id="password_confirmation" name="password_confirmation" value="">
                        @error('password_confirmation')
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
        </div>
    </div>
@endsection
