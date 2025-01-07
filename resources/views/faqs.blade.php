@push('title')
    <title>FAQs - Frequently Asked Questions</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>FAQs</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Frequently Asked Questions</h1>

            <!--accordion start-->
            <div class="accordion-sec">
                <h2>Providers</h2>
                <h3 class="accordion">How much does it cost to list my daycare on Childcare Center?</h3>
                <div class="panel">
                    <p>It’s free!</p>
                </div>
                <h3 class="accordion">How do I list my daycare on Childcare Center?</h3>
                <div class="panel">
                    <p>Create an account by entering your name and a few basic details (make “create an account” clickable
                        to the page). IMPORTANT: Under account type, be sure to list yourself as a childcare center or home
                        daycare provider, whichever best describes your service. Users who register as “Parent” cannot list
                        a daycare facility. Enter the ZIP code of your facility, not your mailing address. Check your email
                        for an activation link. This will take you directly to the login page. Use the email and password
                        you just created to log in. You will be taken automatically to a list of providers in your zip code.
                        Check to see if your facility is already listed. If it is, click “Claim/Update,” and you will be
                        taken directly to your listing, where you can start updating information. If you do not see your
                        listing, click on Add New Daycare. You will be taken directly to the online form to add your
                        information. Just answer the questions and click submit.” You will receive an email within 48 hours
                        indicting if your listing has been approved.</p>
                </div>
                <h3 class="accordion">How do I change my profile status from Parent to Provider?</h3>
                <div class="panel">
                    <p>Contact us and we will change your status.</p>
                </div>

                <h3 class="accordion">Why can’t I see my listing?</h3>
                <div class="panel">
                    <p>If you just registered, your listing will need to be approved before it is posted. You will receive
                        an email within 48 hours indicating whether your listing is approved or not. If it is not approved,
                        we will let you know what needs to be changed or added.</p>
                </div>

                <h3 class="accordion">Why was my listing request not approved?</h3>
                <div class="panel">
                    <p>Here are the most common reasons a request is not approved:</p>
                    <ul class="circle">
                        <li>Incomplete information – Please fill in required fields.</li>
                        <li>Invalid email address – When registering, use a working email address.</li>
                        <li>Your daycare is not registered – We only list registered daycares.</li>
                        <li>A home daycare registers as a daycare center</li>
                        <li>A daycare center registers as a home provider</li>
                        <li>You are unlicensed or your license has expired</li>
                    </ul>
                </div>
                <h3 class="accordion">I am trying to list my daycare, but I can’t access the registration page.</h3>
                <div class="panel">
                    <p>When you registered, did you indicate you are a parent or a provider? Only provider accounts can
                        claim, add, or update a daycare. Contact us to change your status.</p>
                </div>

                <h3 class="accordion">When registering, I accidentally chose Home Daycare/Childcare Center instead of the
                    other option. How do I change my status?</h3>
                <div class="panel">
                    <p>Contact us and we will change your status.</p>
                </div>

                <h3 class="accordion">I accidentally claimed someone else’s day care. How do I un-claim it?</h3>
                <div class="panel">
                    <p>Contact us, and we will un-claim your listing.</p>
                </div>

                <h3 class="accordion">How can I update my current listing?</h3>
                <div class="panel">
                    <p>While logged in, go to your provider page. At the top, click the UPDATE link. Fill in your changes
                        then click the update button at the bottom of the page.</p>
                </div>

                <h3 class="accordion">Why are my updates not showing?</h3>
                <div class="panel">
                    <p>Updates need to be approved. They should be visible on your profile page in less than 48 hours.</p>
                </div>

                <h3 class="accordion">I no longer provide daycare. How do I remove my listing from your website
                    ChildcareCenter?</h3>
                <div class="panel">
                    <p>Email your daycare information and let us know you would like us to remove your listing using this
                        Contact (contact) form.</p>
                </div>

                <h3 class="accordion">How do I edit the photos on my listing?</h3>
                <div class="panel">
                    <p>Why am I taken to another provider’s listing when I click on my own?</p>
                </div>

                <h3 class="accordion">How do I add my daycare to your listing?</h3>
                <div class="panel">
                    <p>Please go to Providers and follow the steps listed on that page.</p>
                </div>

                <h3 class="accordion">My daycare is already listed. How do I make an update?</h3>
                <div class="panel">
                    <p>Go to Provider page and follow the instruction there.</p>
                </div>

                <h3 class="accordion">I no longer provide daycare. How do I remove my listing from your website?</h3>
                <div class="panel">
                    <p>Email your daycare information using this Contact form.</p>
                </div>

                <h3 class="accordion">I’m a provider, and a parent posted a negative review. Will you remove it?</h3>
                <div class="panel">
                    <p>We do not remove reviews because they are negative.</p>
                </div>

                <h2>Parents</h2>
                <h3 class="accordion">How can I find out about rates and available openings for my child from a specific
                    provider?</h3>
                <div class="panel">
                    <p>In some cases, rate range is included in a provider’s listing. For specific rates and available child
                        openings, you must contact the provider directly.</p>
                </div>

                <h3 class="accordion">How do I directly contact a daycare provider I find on ChildcareCenter?</h3>
                <div class="panel">
                    <p>In most cases, the direct number to the daycare provider is included in their listing. When it is
                        not, we list the number of the Child Care Licensing Program. You may call this number for more
                        information, or search for the provider online.</p>
                </div>

                <h3 class="accordion">Will ChildcareCenter relay a message to a daycare provider?</h3>
                <div class="panel">
                    <p>No. ChildcareCenter only provides information. You will need to contact the provider directly.</p>
                </div>

                <h3 class="accordion">The provider’s phone number is not working. Can you send me their email address?</h3>
                <div class="panel">
                    <p>ChildcareCenter cannot provide any information about providers other than what the provider has
                        listed on their page.</p>
                </div>

                <h3 class="accordion">Why was my review removed?</h3>
                <div class="panel">
                    <p>We reserve the right to remove reviews that do not reflect our guidelines. If your review was
                        removed, please read our guidelines and submit another review with these in mind. We do not remove
                        reviews simply because they are negative.</p>
                </div>

                <h2>General Questions</h2>
                <h3 class="accordion">Why I can’t log in to my account?</h3>
                <div class="panel">
                    <p>Wrong/forgotten password – Contact us if you need to change your password Email address is different
                        than the one you used to register – Contact us if you need to change your email.</p>
                </div>

                <h3 class="accordion">Do you have information about employment opportunities?</h3>
                <div class="panel">
                    <p>No. You must contact the provider directly.</p>
                </div>

                <h3 class="accordion">Can I register as both a parent and a provider?</h3>
                <div class="panel">
                    <p>Only one type of listing or account is permitted per email. If you need to register as both a parent
                        and a provider, you will need to register each account separately, using two different emails.</p>
                </div>

            </div>

            <!--accordion start-->

        </section>
        <!---------right container------>
        <section class="right-sect">
            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>

            <!-- Ezoic - ChildcareCenter Responsive Low - longer_content -->
            <div id="ezoic-pub-ad-placeholder-110">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- ChildcareCenter Responsive Low -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="3831518374" data-ad-format="auto"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <!-- End Ezoic - ChildcareCenter Responsive Low - longer_content -->
        </section>
        <!-------right container ends------>
    </div>

    <!--add accordion js by Anil kumar--->
    <script>
        $(document).ready(function() {
            var acc = document.getElementsByClassName("accordion");
            var i;
            for (i = 0; i < acc.length; i++) {
                acc[i].addEventListener("click", function() {
                    this.classList.toggle("active");
                    var panel = this.nextElementSibling;
                    if (panel.style.display === "block") {
                        panel.style.display = "none";
                    } else {
                        panel.style.display = "block";
                    }
                });
            }
            $('.panel').css("display", "none");
        });
    </script>

    <!--add accordion js by Anil kumar--->
@endsection
