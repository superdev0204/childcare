<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" lang="en">
<head profile="http://gmpg.org/xfn/11">
    @push('meta')
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="msvalidate.01" content="626DBBF9555D7C56B70A2C26325EF8C3" />
    @endpush

    @push('link')
        <link rel="stylesheet" href="{{ asset('css/default.min.css') }}">
    @endpush

    @stack('meta')
    @stack('title')
    @stack('link')

    <style type="text/css">
        li.current_page_item {background:#222}
        .textfield{
            width:90%
        }
    </style>
    <!--[if IE]>
    <style type="text/css">
        div.date {float:left; position:static; margin:10px 10px 0 0; padding:0;}
        div.preview {margin:15px 0;}
        .comment-link {background:none;}
        #search-submit {margin: 10px 0 0 0; height: 28px;}
    </style>
    <![endif]-->

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <script src="/js/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="/js/jquery.slicknav.min.js"></script> 

    <script type="text/javascript">
        window.google_analytics_uacct = "UA-11548587-3";
    </script>
    
    @stack('initializeMap')

    @if (Request::path() != 'search' /* && strpos(Request::path(), 'user/pwdreset?') === false */)
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({
                google_ad_client: "ca-pub-8651736830870146",
                enable_page_level_ads: true
            });
        </script>
    @endif
</head>

<body onload="initialize();">
    <div id="outline">
        <div id="header">
            <a href="/" class="logo" title="Childcare Center, Preschools, and Home Daycare">
                <img src="{{ asset('/images/childcare_logo.png') }}" width="500" height="117" alt="Childcare Centers, Home Daycare, Child Development Center" />
            </a>
            <div class="header">
                <a class="header" href="/about" title="About Childcare Center . us">About Us</a> |
                <a class="header" href="/contact" title="Contact">Contact Us</a> |
                <a class="header" href="/feedback" title="Feedback,Suggestions">Feedback</a> |
                <a class="header" href="/faqs" title="Frequently Asked Questions">FAQs</a> |
                <?php if (isset($user->caretype)):?>
                    <a class="header" href="/user/logout" title="Log Out">Logout <?php //echo $this->identity()->getFirstname() ?></a>
                <?php else: ?>
                    <a class="header" href="/user/new" title="Register New Account">Register</a> |
                    <a class="header" href="/user/login" title="Log In">Log In</a>
                <?php endif;?>
            </div>
        </div>
        <!-- page navigation -->
        <ul id="nav">
            <li class="page_item"><a href="/" title="Childcare Center, Preschools, Home Daycare">Home</a></li>
            <li class="page_item"><a href="/state" title="Childcare Center, Child Development Centers, Preschools">Childcare Center</a></li>
            <li class="page_item"><a href="/homecare" title="Home Daycare, Group Home Day Care, Family Child Care">Home Daycare</a></li>
            <li class="page_item"><a href="https://nanny.us/" title="Nannies and Babysitters">Nanny/Sitters</a></li>
            <li class="page_item"><a href="/jobs" title="Child Care Jobs">Child Care Jobs</a></li>
            <li class="page_item"><a href="/resources" title="Child Care Resources">Childcare Resources</a></li>
            <li class="page_item"><a href="/provider" title="Add and Update Free Listing">Add/Update Listing</a></li>
            <?php if(!isset($user->caretype)): ?>
                    <!--<li class="page_item"><a href="/provider" title="Benefits">Why Join?</a></li>
            --><?php else:?>
                    <!--<li class="page_item"><a href="/user" title="My Account">My Account</a></li>
            --><?php endif;?>
            <li class="displayNone"><a class="header" href="/about" title="About Childcare Center . us">About Us</a></li>
            <li class="displayNone"><a class="header" href="/contact" title="Contact">Contact Us</a></li>
            <li class="displayNone"><a class="header" href="/feedback" title="Feedback,Suggestions">Feedback</a></li>
            <li class="displayNone"><a class="header" href="/faqs" title="Frequently Asked Questions">FAQs</a></li>
            <?php if (isset($user->caretype)): ?>
                <li class="displayNone"><a class="header" href="/user/logout" title="Log Out">Logout <?php //echo $this->identity()->getFirstname() ?></a></li>
            <?php else: ?>
                <li class="displayNone"><a class="header" href="/user/new" title="Register New Account">Register</a> </li>
                <li class="displayNone"><a class="header" href="/user/login" title="Log In">Log In</a></li>
            <?php endif;?>
        </ul>
        <!-- ending header template -->
        
        @yield('content')
        
        <!-- footer template -->
        <div id="appendix" class="clearfix">
            <div align="center" style="margin:0 50px 0px 50px;" >
                <p>We at ChildcareCenter strive daily to keep our listings accurate and up-to-date, and to provide top-level,
                    practical information that you can use and trust.  However, ChildcareCenter.us does not endorse or recommend
                    any of the childcare providers listed on its site, cannot be held responsible or liable in any way for your dealings
                    with them, and does not guarantee the accuracy of listings on its site. We provide this site as a directory to assist
                    you in locating childcare providers in your area.  We do not own or operate any child care facility, and make no
                    representation of any of the listings contained within ChildcareCenter.us.</p>
    
                <a href="/about">About Us</a> |
                <a href="/privacy">Privacy Policy</a> |
                <a href="/contact">Contact Us</a> |
                <a href="/rss">Rss</a> |
                <a href="/wesupport">Support</a><br/>
                <span >&copy;<?php echo date("Y")?> Child Care Center US.</span> <br/>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        $(document).ready(function(){
            $('#nav').slicknav();
        });
    </script>
    
    </body>
    <!-- Begin google analytic -->
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-11548587-3']);
        _gaq.push(['_trackPageview']);
    
        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <!-- End google analytic -->
</body>
</html>
