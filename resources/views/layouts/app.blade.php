<!DOCTYPE html>
<html lang="en">

<head>
    @push('meta')
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="msvalidate.01" content="626DBBF9555D7C56B70A2C26325EF8C3" />
    @endpush

    @push('link')
        <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap"
            rel="stylesheet">
    @endpush

    @stack('meta')
    @stack('title')
    @stack('link')

    <!-- jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/tytabs.jquery.min.js') }}"></script>

    @if (Request::path() == 'search' || ((strpos(Request::path(), 'provider_detail/') === true && !$provider->is_center) || (strpos(Request::path(), 'provider/view') === true && !$provider->is_center)))
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({
                google_ad_client: "ca-pub-8651736830870146",
                enable_page_level_ads: true
            });
        </script>
    @endif

    <script>
        jQuery(document).ready(function($) {
            $(document).ready(function() {
                $("#tabsholder").tytabs({
                    tabinit: "1",
                    fadespeed: "fast"
                });
            });
        });
    </script>

    @stack('initializeMap')

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-EQMNFB9RB4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-EQMNFB9RB4');
    </script>
</head>

<body>
    <section class="header">
        <div class="container">
            <div class="logo">
                <a href="/" title="Childcare Center, Preschools, and Home Daycare">
                    <img src="{{ asset('images/logo.png') }}" width="383" height="63" alt="Childcare Centers, Home Daycare, Child Development Center">
                </a>
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
                                <li><a href="https://nanny.us/" title="Find Nannies, Babysitter, Search Nanny Agencies">Nanny/Sitters</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="/resources/">More Resources</a>
                            <ul>
                                <li><a href="/resources/" title="Child Care Resources">Childcare Resources</a></li>
                                <li><a href="/classifieds" title="Child Care Classified">Miscellaneous</a></li>
                                <li><a href="/jobs" title="Childcare Jobs">Child Care Jobs</a></li>
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
                                <li><a href="/faqs" title="Frequenly Asked Questions">FAQs</a></li>
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
        @if (Request::path() != 'user/login' && Request::path() != 'user/new' && Request::path() != 'contact' && Request::path() != 'user/reset' && strpos(Request::path(), 'user/pwdreset?') === false && Request::path() != 'wesupport')
            <div class="container" align="center">
                <!-- Ezoic - CCC MOINSBD Link End - link_bottom -->
                <div id="ezoic-pub-ad-placeholder-106">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- CCC MOINSBD Link End -->
                    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                        data-ad-slot="6851866237" data-ad-format="link"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <!-- End Ezoic - CCC MOINSBD Link End - link_bottom -->
            </div>
        @endif
        <ul>
            <li><a href="/feedback" title="Feedback,Suggestions">Feedback </a></li>
            <li><a href="/provider" title="Add and Update Free Listing">Add / Update Listings</a></li>
            <li><a href="/wesupport">Support</a></li>
            <li><a href="/faqs" title="Frequently Asked Questions">FAQs</a></li>
            <li><a href="https://childcarecenter.us/direct-sales.html" target="blank">Advertise</a></li>
        </ul>
        <div class="copyrights">
            <div class="container">

                <div class="nhv">
                    <span>© <?php echo date('Y'); ?> Child Care Center US </span>
                    <div class="links">
                        <a href="/privacy">Privacy Policy</a>
                        <a href="/rss">RSS</a>
                        <a href="https://childcarecenter.us/direct-sales.html" target="blank">Advertise</a>
                    </div>
                </div>
                <div class="social-icons">
                    <span>Follow US:</span>
                    <a class="fb" href="https://www.facebook.com/childcarecenter"></a>
                    <a class="twitter" href="https://twitter.com/childcareUS"></a>
                    <!-- <a class="pint" href="#" ></a> -->
                </div>
                <p class="text-muted fs-12">Disclaimer: We at ChildcareCenter strive daily to keep our listings accurate
                    and up-to-date, and to provide top-level,
                    practical information that you can use and trust. However, ChildcareCenter.us does not endorse or
                    recommend any of the childcare providers listed on its
                    site, cannot be held responsible or liable in any way for your dealings with them, and does not
                    guarantee the accuracy of listings on its site. We provide
                    this site as a directory to assist you in locating childcare providers in your area. We do not own
                    or operate any child care facility, and make no representation
                    of any of the listings contained within ChildcareCenter.us.</p>
            </div>
        </div>
    </footer>
    <!----------footer ends---------->
</body>
</html>
