@push('meta')
    <meta name="robots" content="noindex,nofollow">
@endpush

@push('title')
    <title>Create New User</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>Sign Up</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Sign up now for free!</h1>
            <?php if (isset($message)) :?>
            <p><?php echo $message; ?></p>
            <?php endif;?>

            @if (!$new_user)
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="caretype-label"><label class="account-radio" for="caretype">Account Type:</label></dt>
                        <dd id="caretype-element">
                            <div class="account-radio">
                                @if (isset($request->caretype))
                                    @if ($request->caretype == "PARENT")
                                        <div class="input-radio-div">
                                            <input type="radio" id="parent" name="caretype" value="PARENT" checked="">
                                            <label for="parent">Parent</label>
                                        </div>
                                        <div class="input-radio-div">
                                            <input type="radio" id="center" name="caretype" value="CENTER">
                                            <label for="center">Childcare Center</label>
                                        </div>
                                        <div class="input-radio-div">
                                            <input type="radio" id="home" name="caretype" value="HOME">
                                            <label for="home">Home Daycare</label>
                                        </div>
                                    @elseif ($request->caretype == "CENTER")
                                        <div class="input-radio-div">
                                            <input type="radio" id="parent" name="caretype" value="PARENT">
                                            <label for="parent">Parent</label>
                                        </div>
                                        <div class="input-radio-div">
                                            <input type="radio" id="center" name="caretype" value="CENTER" checked="">
                                            <label for="center">Childcare Center</label>
                                        </div>
                                        <div class="input-radio-div">
                                            <input type="radio" id="home" name="caretype" value="HOME">
                                            <label for="home">Home Daycare</label>
                                        </div>
                                    @else
                                        <div class="input-radio-div">
                                            <input type="radio" id="parent" name="caretype" value="PARENT">
                                            <label for="parent">Parent</label>
                                        </div>
                                        <div class="input-radio-div">
                                            <input type="radio" id="center" name="caretype" value="CENTER">
                                            <label for="center">Childcare Center</label>
                                        </div>
                                        <div class="input-radio-div">
                                            <input type="radio" id="home" name="caretype" value="HOME" checked="">
                                            <label for="home">Home Daycare</label>
                                        </div>
                                    @endif
                                @else
                                    @if (old('caretype') != "")
                                        @if (old('caretype') == "PARENT")
                                            <div class="input-radio-div">
                                                <input type="radio" id="parent" name="caretype" value="PARENT" checked="">
                                                <label for="parent">Parent</label>
                                            </div>
                                            <div class="input-radio-div">
                                                <input type="radio" id="center" name="caretype" value="CENTER">
                                                <label for="center">Childcare Center</label>
                                            </div>
                                            <div class="input-radio-div">
                                                <input type="radio" id="home" name="caretype" value="HOME">
                                                <label for="home">Home Daycare</label>
                                            </div>
                                        @elseif (old('caretype') == "CENTER")
                                            <div class="input-radio-div">
                                                <input type="radio" id="parent" name="caretype" value="PARENT">
                                                <label for="parent">Parent</label>
                                            </div>
                                            <div class="input-radio-div">
                                                <input type="radio" id="center" name="caretype" value="CENTER" checked="">
                                                <label for="center">Childcare Center</label>
                                            </div>
                                            <div class="input-radio-div">
                                                <input type="radio" id="home" name="caretype" value="HOME">
                                                <label for="home">Home Daycare</label>
                                            </div>
                                        @else
                                            <div class="input-radio-div">
                                                <input type="radio" id="parent" name="caretype" value="PARENT">
                                                <label for="parent">Parent</label>
                                            </div>
                                            <div class="input-radio-div">
                                                <input type="radio" id="center" name="caretype" value="CENTER">
                                                <label for="center">Childcare Center</label>
                                            </div>
                                            <div class="input-radio-div">
                                                <input type="radio" id="home" name="caretype" value="HOME" checked="">
                                                <label for="home">Home Daycare</label>
                                            </div>
                                        @endif
                                    @else
                                        <div class="input-radio-div">
                                            <input type="radio" id="parent" name="caretype" value="PARENT" checked="">
                                            <label for="parent">Parent</label>
                                        </div>
                                        <div class="input-radio-div">
                                            <input type="radio" id="center" name="caretype" value="CENTER">
                                            <label for="center">Childcare Center</label>
                                        </div>
                                        <div class="input-radio-div">
                                            <input type="radio" id="home" name="caretype" value="HOME">
                                            <label for="home">Home Daycare</label>
                                        </div>
                                    @endif
                                @endif
                                
                            </div>
                        </dd>
                        <dt id="firstname-label"><label class="required" for="firstname">First Name:</label></dt>
                        <dd id="firstname-element">
                            @if (isset($request->firstname))
                                <input type="text" id="firstname" name="firstname" value="{{ $request->firstname }}">
                            @else
                                <input id="firstname" name="firstname" type="text" value="{{ old('firstname') }}">
                            @endif
                            @error('firstname')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="lastname-label"><label class="required" for="lastname">Last Name:</label></dt>
                        <dd id="lastname-element">
                            @if (isset($request->lastname))
                                <input type="text" id="lastname" name="lastname" value="{{ $request->lastname }}">
                            @else
                                <input id="lastname" name="lastname" type="text" value="{{ old('lastname') }}">
                            @endif
                            @error('lastname')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="email-label"><label class="required" for="email">Email address (will be your
                                username):</label></dt>
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
                        <dt id="pwd-label"><label class="required" for="pwd">Password:</label></dt>
                        <dd id="pwd-element">
                            <input type="password" id="password" name="password" autocomplete="off">
                            @error('password')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="confirmpassword-label"><label class="required" for="confirmpassword">Retype
                                Password:</label></dt>
                        <dd id="confirmpassword-element">
                            <input type="password" id="password_confirmation" name="password_confirmation">
                            @error('password_confirmation')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="city-label"><label class="required" for="city">City:</label></dt>
                        <dd id="city-element">
                            @if (isset($request->city))
                                <input type="text" id="city" name="city" value="{{ $request->city }}">
                            @else
                                <input id="city" name="city" type="text" value="{{ old('city') }}">
                            @endif
                            @error('city')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="state-label"><label class="required" for="state">State:</label></dt>
                        <dd id="state-element">
                            <select class="textinput" id="state" name="state">
                                <option value="">-Select-</option>
                                @foreach ($states as $state)
                                    @if (isset($request->state))
                                        @if ($state->state_code == $request->state)
                                            <option value='{{ $state->state_code }}' selected>
                                                {{ $state->state_name }}
                                            </option>
                                        @else
                                            <option value='{{ $state->state_code }}'>{{ $state->state_name }}
                                            </option>
                                        @endif
                                    @else
                                        @if ($state->state_code == old('state'))
                                            <option value='{{ $state->state_code }}' selected>
                                                {{ $state->state_name }}
                                            </option>
                                        @else
                                            <option value='{{ $state->state_code }}'>{{ $state->state_name }}
                                            </option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            @error('state')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="zip-label"><label class="required" for="zip">Zip Code:</label></dt>
                        <dd id="zip-element">
                            @if (isset($request->zip))
                                <input type="text" id="zip" name="zip" value="{{ $request->zip }}">
                            @else
                                <input id="zip" name="zip" type="text" value="{{ old('zip') }}">
                            @endif
                            @error('zip')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
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
                        <dt id="register-label">&nbsp;</dt>
                        <dd id="register-element"><input type="submit" name="submit" value="Register"></dd>
                    </dl>
                </form>
            @endif
        </section>
        <!---------right container------>
        <section class="right-sect">
            <div class="listSidebar">
                <h2>Notes:</h2>
                <ol>
                    <li>You must be <strong>18 or older</strong> to post your daycare information on ChildcareCenter.us.
                    </li>
                    <li>You must use a <strong>valid Email address</strong> to register. </li>
                    <li>Once you have entered your information, we will send you an email with a clickable link to complete
                        your registration.</li>
                    <li>Open this email and click on the link provided. You will then be able to log in and create your
                        profile page.</li>
                    <li>If you do not see an email from us within 5 minutes, please check your <strong>spam or junk mail
                            folder</strong>. The email will be sent from ChildcareCenter.us.</li>
                </ol>
            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
