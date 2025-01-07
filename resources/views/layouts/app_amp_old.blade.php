<!DOCTYPE html>
<html lang="en">

<head>
    @push('meta')
        <meta charset="utf-8">
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @endpush

    @push('link')
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    @endpush

    @stack('meta')
    @stack('title')
    @stack('link')

    <script async custom-element="amp-addthis" src="https://cdn.ampproject.org/v0/amp-addthis-0.1.js"></script>
    <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
    <script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
    <script async src="https://cdn.ampproject.org/v0.js"></script>

    <style amp-custom>
        .noliststyle {
            list-style: none;
            margin: 0;
            padding: 0
        }

        .clearfix:after,
        .update:after {
            clear: both;
            content: '';
            display: block;
            height: 0;
            visibility: hidden
        }

        html body .hidden {
            display: none
        }

        html body .m0 {
            margin: 0
        }

        html body .mt0 {
            margin-top: 0
        }

        html body .mb0 {
            margin-bottom: 0
        }

        html body .ml0 {
            margin-left: 0
        }

        html body .mr0 {
            margin-right: 0
        }

        html body .p0 {
            padding: 0
        }

        html body .pt0 {
            padding-top: 0
        }

        html body .pb0 {
            padding-bottom: 0
        }

        html body .pl0 {
            padding-left: 0
        }

        html body .pr0 {
            padding-right: 0
        }

        .ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .tl {
            text-align: left
        }

        .tr {
            text-align: right
        }

        .tc {
            text-align: center
        }

        @font-face {
            font-family: "trebuchet_msregular";
            font-style: normal;
            font-weight: 400;
            src: url("/fonts/trebuc-webfont.eot?") format("eot"), url("/fonts/trebuc-webfont.woff") format("woff"), url("/fonts/trebuc-webfont.ttf") format("truetype"), url('/fonts/trebuc-webfont.svg#str-replace("trebuchet_msregular", " ", "_")') format("svg")
        }

        @font-face {
            font-family: "freestyle_scriptregular";
            font-style: normal;
            font-weight: 400;
            src: url("/fonts/freescpt-webfont.eot?") format("eot"), url("/fonts/freescpt-webfont.woff") format("woff"), url("/fonts/freescpt-webfont.ttf") format("truetype"), url('/fonts/freescpt-webfont.svg#str-replace("freestyle_scriptregular", " ", "_")') format("svg")
        }

        .header,
        .logo {
            display: inline-block
        }

        .header amp-img,
        .logo amp-img {
            max-width: 383px;
            margin: 0 auto
        }

        .banner,
        .header,
        .login {
            position: relative
        }

        .banner h1,
        body {
            font-weight: 400
        }

        .breadcrumbs ul,
        .cities ul,
        .nav ul,
        footer ul,
        ul.list li,
        ul.tabs li {
            list-style-type: none
        }

        * {
            margin: 0;
            padding: 0
        }

        ::-moz-placeholder {
            color: #ccc;
            font-size: 18px
        }

        a {
            text-decoration: none;
            -moz-transition: all .2s ease-in-out 0s;
            -o-transition: all .2s ease-in-out 0s;
            -webkit-transition: all .2s ease-in-out;
            -webkit-transition-delay: 0s;
            transition: all .2s ease-in-out 0s
        }

        p {
            line-height: 20px;
            margin: 0 0 20px
        }

        img {
            border: none
        }

        body {
            background: #fff;
            color: #222;
            font-family: trebuchet_msregular;
            font-size: 13px
        }

        .container {
            position: relative;
            width: 100%;
            max-width: 1020px;
            margin: 0 auto;
            padding-left: 10px;
            padding-right: 10px
        }

        .header {
            width: 100%;
            padding: 5px 0;
            border-bottom: 2px solid #b4d496;
            background: #95C5F6;
            height: 120px
        }

        .header>.container {
            height: 100%;
            display: -webkit-flex;
            -webkit-align-items: center;
            display: flex;
            align-items: center
        }

        .logo {
            width: 38%
        }

        .nav {
            -webkit-flex-grow: 1;
            flex-grow: 1;
            margin: 10px 0 0 10px;
            white-space: nowrap
        }

        .login {
            position: absolute;
            top: 0;
            right: 10px
        }

        .login a {
            display: inline-block;
            background: #B4D496;
            color: #000;
            margin: 0 4px;
            padding: 7px 14px
        }

        .login a:hover {
            color: #fff;
            text-decoration: none;
            background: #4f9a29
        }

        .banner,
        .logo img {
            width: 100%;
            height: auto
        }

        .nav ul li:last-child {
            padding-right: 0
        }

        .nav ul li a:hover {
            text-decoration: none
        }

        .head h2,
        .left-sect h1,
        .offer-sect h2,
        .resources h2,
        .reup-box>h2 {
            font-family: freestyle_scriptregular;
            color: #09357a;
            font-weight: 400;
            padding: 1% 0
        }

        .left-sect {
            text-align: left;
            padding-right: 20px;
            width: 70%;
            float: left
        }

        .left-sect h1 {
            font-size: 50px
        }

        .left-sect img {
            max-width: 100%;
            height: auto;
            float: left;
            margin: 0 15px 10px 0
        }

        .left-sect p>a {
            color: #468a23
        }

        .left-sect.head p>a:hover {
            border-bottom: 1px dashed
        }

        .left-sect.head>span {
            padding: 12px 0
        }

        .head h2,
        .offer-sect h2,
        .resources h2,
        .reup-box>h2 {
            font-size: 40px
        }

        a.non-hover2 {
            margin: 3px 0 0
        }

        #pano,
        .offer-box,
        .on-top,
        .social-icons {
            position: relative
        }

        .media img:hover,
        .on-top img:hover {
            opacity: 0.8
        }

        .posto>a:hover,
        footer ul li a:hover {
            border-bottom: 1px dashed #09357A
        }

        .green {
            color: #468A23;
            font-size: 12px;
            padding: 5px 0
        }

        .cust span,
        .green,
        .posto span,
        .rgt>h3,
        .update>span {
            font-weight: 400
        }

        .city-section,
        .list,
        .map-section,
        footer {
            display: inline-block
        }

        footer {
            width: 100%;
            margin-top: 35px
        }

        footer ul {
            text-align: center;
            margin-top: 15px
        }

        footer ul li {
            display: inline-block;
            padding: 0 18px;
            border-right: 1px solid #B4D496;
            margin-bottom: 15px
        }

        footer ul li a {
            font-size: 15px;
            color: #09357a;
            text-transform: capitalize;
            display: block;
            border-bottom: 1px dashed transparent
        }

        footer ul li:last-child {
            border: none
        }

        .copyrights {
            border-top: 2px solid #b4d496;
            padding: 1.5% 0;
            color: #999;
            width: 100%
        }

        .copyrights>.container {
            display: -webkit-flex;
            -webkit-justify-content: space-between;
            -webkit-align-items: center;
            -webkit-flex-wrap: wrap;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap
        }

        .fb,
        .pint,
        .twitter {
            height: 28px;
            width: 28px
        }

        .links {
            display: inline
        }

        .links a {
            color: #999;
            padding: 0 17px;
            border-right: 1px solid #999
        }

        .links a:hover {
            color: #78a566;
            text-decoration: underline
        }

        .citis,
        .fb,
        .pint,
        .twitter {
            display: inline-block;
            vertical-align: middle
        }

        .social-icons>span {
            display: inline-block;
            padding: 8px 7px;
            vertical-align: middle
        }

        .fb {
            background: url(/images/fb.png) no-repeat transparent
        }

        .fb:hover {
            background: url(/images/fbh.png) no-repeat transparent
        }

        .twitter {
            background: url(/images/twitter.png) no-repeat transparent
        }

        .twitter:hover {
            background: url(/images/twitterh.png) no-repeat transparent
        }

        .pint {
            background: url(/images/pint.png) no-repeat transparent
        }

        .pint:hover {
            background: url(/images/pinth.png) no-repeat transparent
        }

        .facebook iframe {
            height: 21px
        }

        .map-section2,
        .up-section {
            display: inline-block;
            width: 100%;
            margin-top: 4%
        }

        .update {
            margin: 5px 0 23px;
            width: 100%
        }

        .update>a {
            float: left;
            margin: 0 15px 10px 0;
            width: 100px
        }

        .update>h3 {
            color: #09357a;
            font-size: 14px;
            border-bottom: 1px solid #B4D496;
            overflow: hidden
        }

        .update>h3>a {
            color: #09357A;
            font-size: 14px;
            font-weight: 700
        }

        .update>h3>a:hover {
            border-bottom: 1px dashed #09357A
        }

        .update>span {
            color: #468a23;
            font-size: 12px;
            margin: 6px 0;
            display: block;
            font-family: trebuchet_msregular;
            overflow: hidden
        }

        .review {
            float: right;
            color: #ffb400;
            font-size: 18px
        }

        .right-sect {
            width: 30%;
            float: right
        }

        .facebook2 {
            background: url(/images/fb-bg.png) transparent;
            padding: 9px 0;
            width: 83%;
            margin-left: 15px
        }

        .facebook2 iframe {
            height: 21px
        }

        .up-section.head>span {
            padding: 10px 0
        }

        .breadcrumbs {
            display: inline-block;
            width: 100%;
            margin: 3% 0 1%
        }

        .breadcrumbs ul li {
            display: inline
        }

        .breadcrumbs ul li a {
            color: #468a23
        }

        .breadcrumbs ul li a.current {
            color: #09357a
        }

        .ads-section,
        .adv,
        .cbyc,
        .cities,
        .li-ag i,
        .li-ag.rgt>a,
        .share,
        ul.up-section li {
            display: inline-block
        }

        ul.up-section li {
            margin: 5px 0 23px;
            width: 100%
        }

        ul.up-section li h3,
        ul.up-section li h3>a {
            color: #09357A;
            font-size: 14px
        }

        ul.up-section li h3>a {
            font-weight: 700
        }

        ul.up-section li img {
            float: left;
            margin-right: 5%;
            margin-bottom: 9%
        }

        ul.up-section li>a {
            color: #468A23
        }

        #nav {
            margin: 0
        }

        #nav li {
            position: relative
        }

        #nav li a {
            color: #09357A;
            display: block
        }

        #nav li ul {
            background-color: #daeacb;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 9;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 2px 2px #D5D5D5
        }

        #nav li ul a {
            font-size: 13px;
            border-top: 1px solid #468A23;
            padding: .75em
        }

        #nav li:hover ul {
            display: block;
            width: 100%
        }

        #nav>ul {
            float: right
        }

        #nav>ul>li {
            border-right: 1px solid #B4D496;
            display: inline-block;
            padding: 0 15px
        }

        #nav>ul>li>a {
            height: 100%;
            font-size: 18px;
            text-align: center
        }

        #nav ul li:last-child {
            border: none
        }

        #nav li ul li a:hover,
        #nav li ul:not(:hover) li.active a {
            color: #559236;
            border-bottom: 0
        }

        #nav li ul,
        #nav>a {
            display: none
        }

        *,
        .left-sect,
        .listSidebar,
        .provider-main-features,
        ::after,
        ::before {
            box-sizing: border-box
        }

        h2 {
            color: #09357a;
            font-size: 50px;
            padding: 1% 0;
            font-family: freestyle_scriptregular;
            font-weight: 400
        }

        .listSidebar {
            padding: 15px;
            display: block;
            width: 100%;
            margin-bottom: 25px;
            background-color: #daeacb
        }

        .listSidebar h3 {
            font-weight: 400;
            font-size: 16px;
            text-transform: capitalize;
            margin: 0 0 25px;
            color: #09357a
        }

        .listSidebar input:not([type="submit"]) {
            width: 100%;
            max-width: 270px
        }

        .listSidebar a {
            padding: 4px 5px;
            margin: 0 0 25px;
            color: #09357a
        }

        .btn {
            background-color: #468A23;
            color: #FFF;
            padding: 10px 15px;
            font-size: 12px;
            display: inline-block;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            text-align: center
        }

        .btn:hover {
            background-color: #4d9826
        }

        ol {
            margin-left: 15px
        }

        ol>li:not(:last-child) {
            padding-bottom: 20px
        }

        .sidebarList li {
            display: block;
            width: 100%;
            padding: 17px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1)
        }

        .sidebarList li:last-child {
            border-bottom: 0
        }

        .sidebarList li span a {
            color: #2196f3;
            text-decoration: underline
        }

        .sidebarList.condensed li {
            padding: 14px 0
        }

        .city-links>a,
        .quick-links>a {
            display: inline-block;
            font-size: 12px;
            width: 100%
        }

        .city-links>a,
        .quick-links>a,
        .section-body,
        .section-body p:last-child {
            margin-bottom: 10px
        }

        .section-body {
            padding-bottom: 10px;
            border-bottom: 1px solid #eee
        }

        .quick-links>a {
            padding: 7px 10px;
            border: 1px solid #468A23;
            color: #468A23;
            margin-right: 10px;
            border-radius: 20px;
            background-color: rgba(255, 255, 255, 0.6);
            text-align: center
        }

        .quick-links>a:hover {
            background-color: rgba(255, 255, 255, 0.8)
        }

        .text-muted {
            color: #848484
        }

        .fs-12 {
            font-size: 12px
        }

        hr {
            margin-bottom: 20px;
            margin-top: 20px;
            border-width: 1px 0 0;
            border-color: #eee;
            border-style: solid
        }

        @media (max-width: 991px) {
            .nav {
                width: 70%
            }
        }

        @media (max-width: 767px) {
            .nav ul li a {
                font-size: 17px
            }

            .header,
            .nav,
            .right-half {
                text-align: center
            }

            .header {
                height: auto
            }

            .header>.container {
                display: block
            }

            .nav {
                width: 100%;
                margin: 40px 0 35px
            }

            .logo {
                width: 100%;
                margin-top: 30px
            }

            #nav>ul {
                float: none
            }

            .head h1,
            .offer-sect h1,
            .resources h1,
            .reup-box>h1 {
                font-size: 38px
            }

            .left-sect {
                float: none;
                width: 100%;
                padding: 0
            }

            .right-sect {
                float: none;
                width: 100%;
                margin-top: 30px
            }

            .hidden {
                display: none
            }

            .hidden-xs {
                display: none
            }

            .inspections .info-block .info {
                margin-bottom: 20px
            }
        }

        @media (max-width: 575px) {
            .facebook {
                width: 80%
            }

            .logo {
                margin: 50px 0 10px
            }

            .nav {
                margin: 0;
                white-space: normal
            }

            #nav {
                width: 100%;
                position: static;
                margin: 0
            }

            #nav>a {
                position: absolute;
                top: 0;
                left: 10px;
                width: 3.125em;
                height: 3.125em;
                text-align: left;
                text-indent: -9999px;
                background: url(/images/mobile-btn.png) no-repeat
            }

            #nav>a:after {
                top: 60%
            }

            #nav:not(:target)>a:first-of-type,
            #nav:target>a:last-of-type {
                display: block
            }

            #nav>ul {
                position: relative;
                height: auto;
                display: none;
                left: 0;
                right: 0;
                margin: 0;
                padding-bottom: 10px
            }

            #nav>ul>li {
                display: block;
                width: 100%;
                float: none;
                padding: 0;
                border-right: none
            }

            #nav>ul>li>a {
                height: auto;
                text-align: left;
                padding: 8px 0
            }

            #nav>ul>li:not(:last-child)>a {
                border-right: none;
                border-bottom: 1px solid #468A23
            }

            #nav li ul {
                padding: 0;
                text-align: left;
                border-radius: 0;
                box-shadow: none;
                display: block;
                position: static
            }

            #nav:target>ul {
                display: block
            }

            body {
                overflow-x: hidden
            }

            .facebook {
                width: 85%;
                padding: 9px 0 9px 1px
            }

            .facebook>img {
                width: 100%
            }

            .cities,
            .up-section {
                text-align: justify
            }

            .update>span {
                display: inline
            }

            .countyads {
                float: left;
                margin: 0
            }

            .copyrights {
                padding: 10px 0
            }

            .copyrights .nhv,
            .copyrights .social-icons {
                width: 100%;
                text-align: center
            }

            .copyrights .nhv {
                margin-bottom: 15px
            }
        }
    </style>

    <style amp-boilerplate>
        body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}
    </style>
    <noscript>
        <style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style>
    </noscript>    
</head>

<body>
    <amp-analytics type="googleanalytics">
        <script type="application/json">
        {
          "vars": {
            "account": "UA-11548587-3"
          },
          "triggers": {
            "trackPageview": {
              "on": "visible",
              "request": "pageview"
            }
          }
        }
        </script>
    </amp-analytics>
    <section class="header">
        <div class="container">
            <div class="logo">
                <a href="/" title="Childcare Center, Preschools, and Home Daycare">
                    <amp-img src="{{ asset('/images/logo.png') }}" width="383" height="63" layout="responsive" alt="Childcare Centers, Home Daycare, Child Development Center"></a>
            </div>
            <div class="login">
                <?php if( isset($user) ):?>
                    <a href="/user/logout" title="Log Out">Logout <?php echo $user->firstname; ?></a>
                <?php else: ?>
                    <a href="/user/login" title="Log In">Log In</a>
                    <a href="/user/new">Signup</a>
                <?php endif;?>
            </div>
            <div class="nav">
                <nav id="nav" role="navigation">
                    <a href="#nav" title="Show navigation">Show Navigation</a>
                    <a href="#" title="Hide navigation">Hide Navigation</a>
                    <ul class="clearfix">
                        <li>
                            <a href="/search">Find Providers</a>
                            <ul>
                                <li><a href="/state" title="Childcare Center, Child Development Centers, Preschools">Childcare Center</a></li>
                                <li><a href="/homecare" title="Home Daycare, Group Home Day Care, Family Child Care">Home Daycare</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/resources/">More Resources</a>
                            <ul>
                                <li><a href="/resources/" title="Child Care Resources">Childcare Resources</a></li>
                                <li><a href="/classifieds" title="Child Care Classified">Miscellaneous</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/provider">Manage Listing</a>
                            <ul>
                                <li><a href="/provider" title="Add New Listing">Add Free Listing</a></li>
                                <li><a href="/provider" title="Update Listing">Update Listing</a></li>
                            </ul>
                        </li>
                        {{-- <li>
                            <a href="/jobs">Child Care Jobs</a>
                            <ul>
                                <li><a href="/jobs" title="Find Child Care Jobs">Find Jobs</a></li>
                                <li><a href="/resumes" title="Find Child Care Resumes">Find Resumes</a></li>
                                <li><a href="/jobs/new" title="Post Child Care Job">Post Job</a></li>
                                <li><a href="/resumes/new" title="Post Child Care Resume">Post Resume</a></li>
                            </ul>
                        </li> --}}
                        <li>
                            <a href="/about">About</a>
                            <ul>
                                <li><a href="/contact" title="Contact Child Care Center">Contact</a></li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>
        </div>
    </section>
    <!-------header ends----->

    @yield('content')

    <!----------footer--------------->
    <footer>
        <ul>
            <li><a href="/feedback" title="Feedback,Suggestions">Feedback </a></li>
            <li><a href="/provider" title="Add and Update Free Listing">Add / Update Listings</a></li>
            <li><a href="/wesupport">Support</a></li>
            <li><a href="/faqs" title="Frequently Asked Questions">FAQs</a></li>
        </ul>
        <div class="copyrights">
            <div class="container">

                <div class="nhv">
                    <span>В© <?php echo date("Y")?> Child Care Center US </span>
                    <div class="links">
                        <a href="/privacy" >Privacy Policy</a>
                        <a href="/rss" >RSS</a>
                    </div>
                </div>
                <div class="social-icons">
                    <span>Follow US:</span>
                    <a class="fb" href="https://www.facebook.com/childcarecenter"></a>
                    <a class="twitter" href="https://twitter.com/childcareUS"></a>
                    <a class="pint" href="#" ></a>
                </div>
                <p class="text-muted fs-12">Disclaimer: We at ChildcareCenter strive daily to keep our listings accurate and up-to-date, and to provide top-level, 
                practical information that you can use and trust. However, ChildcareCenter.us does not endorse or recommend any of the childcare providers listed on its 
                site, cannot be held responsible or liable in any way for your dealings with them, and does not guarantee the accuracy of listings on its site. We provide 
                this site as a directory to assist you in locating childcare providers in your area. We do not own or operate any child care facility, and make no representation 
                of any of the listings contained within ChildcareCenter.us.</p>
            </div>
        </div>
    </footer>
    <!----------footer ends---------->
</body>

</html>
