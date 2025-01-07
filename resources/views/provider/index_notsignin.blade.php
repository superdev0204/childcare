@push('title')
    <title>Childcare Providers - Help Families Find You!</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>Childcare Providers</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Help Families Find You!</h1>
            <!-- <h2><a href="/user/new">Sign up </a>now for a free account and personalize your personal profile page!</h2> -->

            <h2>What You Will Get:</h2>
            <div class="service-group">
                <div class="ser-item">
                    <img src="{{ asset('images/group.svg') }}" width="40">
                    <h4>Incredible Exposure</h4>
                    <p>More than 500,000 unique visitors per month, and that number is growing daily!</p>
                </div>
                <div class="ser-item">
                    <img src="{{ asset('images/profile.svg') }}" width="40">
                    <h4>Personalized Profile Page</h4>
                    <p>Let people know what makes you their best choice. Upload photos, write your own, personal daycare
                        description, invite parents to leave reviews, and more.</p>
                </div>
                <div class="ser-item">
                    <img src="{{ asset('images/seo.svg') }}" width="40">
                    <h4>Enhanced Search Engine Placement</h4>
                    <p>We place new listings on our main landing page, boosting your position in search engine results.
                        Plus, parents will see your listing when they search their city or zip code on our website.</p>
                </div>
                <div class="ser-item">
                    <img src="{{ asset('images/avatar.svg') }}" width="40">
                    <h4>Excellent Customer Service</h4>
                    <p>Questions? Suggestions? Need help? Send us an email. We’re here for you.</p>
                </div>
                <div class="ser-item">
                    <img src="{{ asset('images/listing.svg') }}" width="40">
                    <h4>Listing Is Free And It’s So Easy!</h4>
                    <p>Just sign up for your login account, activate it from a confirmation email, and start personalizing
                        your profile page for parents to view!</p>
                </div>
                <div class="ser-item last-list">
                    <a href="/user/new" class="arrow-part"><img src="{{ asset('images/right-arrow.svg') }}" width="40"></a>
                    <a href="/user/new">
                        <h4>Get Started</h4>
                    </a>
                    <!-- <p>To update existing or add new listing, <a href="/user/new">sign up </a>for a free login account now.</p> -->
                </div>
            </div>

            <ul>
                <!--<li>Enhanced interactivity will allow you to communicate with existing and prospective customers. </li>
                <li>Email alerts will inform you any time a review about your facility is posted, allowing you to respond directly to negative comments. In turn, you'll be able to take control of your daycare's online image and garner even more business through positive word-of-mouth!</li>
            -->
            </ul>

        </section>
        <!---------right container------>
        <section class="right-sect">
            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>
            <div class="listSidebar child-srch">
                <h4>Provider Sign In</h4>
                <form id="login" enctype="application/x-www-form-urlencoded" method="post" action="/user/login">
                    @csrf
                    <dl class="zend_form">
                        <input type="hidden" name="url" value="" id="url">
                        <dt id="username-label"><label for="email" class="required">Username (email):</label></dt>
                        <dd id="username-element">
                            <input type="email" name="email" id="email" value="">
                        </dd>
                        <dt id="password-label"><label for="password" class="required">Password:</label></dt>
                        <dd id="password-element">
                            <input type="password" name="password" id="password" value="">
                        </dd>
                        <dt id="login-label">&nbsp;</dt>
                        <dd id="login-element">
                            <input type="submit" name="login" id="login" value="Login">
                        </dd>
                    </dl>
                </form>
                <hr>
                <a href="/user/reset">Forgot your password? Reset it</a><br />
                <hr>
                <h4>Don't have account?</h4>
                <a href="/user/new">Signing up is easy and free</a>

            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
