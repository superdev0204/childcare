@push('meta')
    <meta name="description"
        content="ChildcareCenter.us is is the largest directory for childcare services in the US, with over 270,000 childcare providers to give you the best choices possible for your child’s care.">
    <meta name="keywords" content="childcare center, preschools, home daycare, family day care, child development center">
@endpush

@push('title')
    <title>Child Care Centers | Preschools | Home Daycare</title>
@endpush

@extends('layouts.app')

@section('content')
    <!-------banner section------>
    <section class="banner">
        <div class="container">
            <h1>Find Nearby Childcare!</h1>
            <div class="bttm">
                <div id="tabsholder">
                    <div class="contents marginbot">
                        <div class="bgf">
                            <form id="searchform" enctype="application/x-www-form-urlencoded" method="post"
                                action="/search">
                                @csrf
                                <div class="form-group">
                                    <ul class="radio-tab">
                                        <li>
                                            <input type="radio" id="child-care-center" name="type" value="center" checked>
                                            <label for="child-care-center">Childcare Center</label>
                                        </li>
                                        <li>
                                            <input type="radio" id="home-care-center" name="type" value="home">
                                            <label for="home-care-center">Home Daycare</label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label for="zip">In ZIP Code (i.e. 33781):</label>
                                    <input type="text" id="zip" name="zip" />
                                </div>
                                <div class="form-group">
                                    <label for="location">Or City/State (i.e Orlando,FL):</label>
                                    <input type="text" id="location" name="location" />
                                </div>
                                <input type="submit" value="Search" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-------banner ends-------->
    <!------content--------->
    <div class="container">
        <!---------offer section------>
        <section class="offer-sect">
            <h2>What We Offer</h2>
            <!-- Ezoic - CCC MOINSBD Link Top - link_top -->
            <div id="ezoic-pub-ad-placeholder-105">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- CCC MOINSBD Link Top -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="9070001310" data-ad-format="link"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <!-- End Ezoic - CCC MOINSBD Link Top - link_top -->

            <div class="offer-box-wrapper">
                <a class="offer-box" href="/state" title="Childcare Center, Child Development Centers, Preschools"
                    style="background-image: url(/images/child-care-center.png)">
                    <h3>Child Care Centers</h3>
                    <div class="offer-box-caption bottom-to-top">
                        <p>There are over 115,000 child care centers and preschools nationwide.</p>
                        <span>Childcare Center</span>
                    </div>
                </a>
                <a class="offer-box" href="/homecare" title="Home Daycare, Group Home Day Care, Family Child Care"
                    style="background-image: url(/images/home-daycare.jpg)">
                    <h3>Home Daycare</h3>
                    <div class="offer-box-caption bottom-to-top">
                        <p>Family daycare takes place inside homes, and resemble play groups of other young children from
                            the surrounding neighborhood.</p>
                        <span>Home Daycare</span>
                    </div>
                </a>
                <a class="offer-box" href="https://nanny.us" title="Nannies, Babysitters, Nanny Agencies"
                    style="background-image: url(/images/nanny-sitter.png)">
                    <h3>Nanny/Sitters</h3>
                    <div class="offer-box-caption bottom-to-top">
                        <p>A lot of parents overlook just how helpful a nanny or babysitter can be when it comes to child
                            care.</p>
                        <span>Nanny/Sitters</span>
                    </div>
                </a>
                <a class="offer-box" href="https://childcarecenter.us/jobs" title="Child Care Jobs"
                    style="background-image: url(/images/childcare-job.jpg)">
                    <h3>Child Care Jobs</h3>
                    <div class="offer-box-caption bottom-to-top">
                        <p>childcarecenter.us/jobs include thousands of job listings in the child care field.</p>
                        <span>Childcare Jobs</span>
                    </div>
                </a>
            </div>
        </section>
        <!---------offer ends------>
        <!---------resources section------>
        <section class="resources">
            <h2>Child Care Resources</h2>
            <div class="re-bg">
                <?php  
                /** @var \Application\Domain\Entity\Page $page */
                foreach ($pages as $page): ?>
                    <div class="re-box">
                        <h3><a href="/resources/<?php echo $page->pagename; ?>"><?php echo $page->header; ?> </a></h3>
                        <p><?php echo substr(strip_tags($page->body), 0, 240); ?>...</p>
                        <a href="/resources/<?php echo $page->pagename; ?>">Read More</a>
                    </div>
                <?php endforeach;?>
            </div>
            <a class="success-btn" href="/resources/">> All Child Care Resources</a>
        </section>
        <!---------resources section ends------>

        <!------------reviews section---------->
        <section class="reup">
            <div class="reup-box">
                <h2>Most Recent Top Reviews</h2>
                <?php 
            /** @var \Application\Domain\Entity\Review $review */
            foreach ($reviews as $review): ?>
                <div class="posto">
                    <div class="five"><img src="{{ asset('images/' . $review->rating . 'star.png') }}"
                            alt="<?php echo $review->rating; ?> Stars Rating" /></div>
                    <a href="/provider_detail/<?php echo $review->facility_filename; ?>"><?php echo $review->facility_name; ?></a><br />
                    <span> Rated by <strong> <?php echo $review->review_by; ?></strong> on {{ \Carbon\Carbon::parse($review->review_date)->format('F jS Y') }}</span>
                    <p><?php echo substr(strip_tags($review->comments), 0, 200); ?>...</p>
                </div>
                <?php endforeach;?>
                <p><br />
                    If you have prior experience with any of the providers in our list, please share your experience and
                    rate the provider
                    to help other parents make a better choice for their kids.</p>
            </div>
            <div class="reup-box pull-right">
                <h2>New Childcare Updates:</h2>
                <?php 
                /** @var \Application\Domain\Entity\Facility $facility */
                foreach ($facilities as $facility): ?>
                <div class="posto">
                    <a href="/provider_detail/<?php echo $facility->filename; ?>"><?php echo $facility->name; ?></a> <br />
                    <span><?php echo ucwords(strtolower($facility->city)) . ', ' . $facility->state . ' ' . $facility->zip . ' | ' . $facility->phone; ?></span>
                    <p><?php echo substr(strip_tags($facility->introduction), 0, 240); ?>...</p>
                </div>
                <?php endforeach;?>
            </div>
        </section>
        <!------------reviews section---------->
        <!------------Cities section---------->
        <section class="city-section head">
            <h2>Top US Cities</h2>
            <div id="map_demo">
                <div class="citis">
                    <div class="list">
                        <ul>
                            <li><a href="/georgia/atlanta_ga_childcare">Atlanta, GA Childcare </a></li>
                            <li><a href="/maryland/baltimore_md_childcare">Baltimore, MD Childcare </a></li>
                            <li><a href="/new_york/brooklyn_ny_childcare">Brooklyn, NY Childcare </a></li>
                            <li><a href="/north_carolina/charlotte_nc_childcare">Charlotte, NC Childcare </a></li>
                            <li><a href="/texas/dallas_tx_childcare">Dallas, TX Childcare </a> </li>
                            <li><a href="/iowa/des_moines_ia_childcare">Des Moines, IA Childcare </a></li>
                            <li><a href="/texas/houston_tx_childcare">Houston, TX Childcare </a> </li>
                            <li><a href="/california/los_angeles_ca_childcare">Los Angeles, CA Childcare </a></li>
                            <li><a href="/florida/miami_fl_childcare">Miami, FL Childcare </a></li>
                            <li><a href="/new_york/new_york_ny_childcare">New York, NY Childcare </a></li>
                            <li><a href="/washington/seattle_wa_childcare">Seattle, WA Childcare </a> </li>
                        </ul>
                    </div>
                    <div class="drg" style="width:636px; border:0; overflow: hidden; float:left;">
                        <img id="usa_image" alt="Map of Childcare Centers in the United States"
                            src="{{ asset('images/childcare-map.png') }}" usemap="#childcaremap">
                    </div>
                </div>
            </div>
            <map id="usa_image_map" name="childcaremap">
                <?php 
            /** @var \Application\Domain\Entity\State $state */
            foreach ($states as $state): ?>
                <area shape="poly" title="<?php echo $state->state_name; ?> Childcare Centers (<?php echo number_format($state->center_count); ?> providers)"
                    coords="<?php echo $state->coords; ?>" href="/state/<?php echo $state->statefile; ?>"
                    alt="<?php echo $state->state_name; ?> Child Care Center" />
                <?php endforeach;?>
            </map>
        </section>
        <!------------Cities section---------->
        <!------------about section---------->
        <section class="reup">
            <div class="reup-box about">
                <h2>About Child Care Centers</h2>
                <p>You’ve found the most comprehensive collection of information about childcare centers in the U.S, with
                    <?php echo number_format($summary->center_count); ?>
                    licensed childcare centers and <?php echo number_format($summary->homebase_count); ?> home daycare providers. Selecting a childcare center
                    for our children
                    is one of the most important decisions we make as parents. It’s important to choose a preschool or home
                    daycare to which children love to go and where safety
                    is of the utmost concern. Please use the Quick Search box on this page to find the perfect child care
                    solution for your family!</p>
            </div>
            <div class="reup-box pull-right about">
                <h2>Important Info</h2>
                <p>We at ChildcareCenter strive daily to keep our listings accurate and up-to-date, and to provide
                    top-level,
                    practical information that you can use and trust. However, ChildcareCenter.us does not endorse or
                    recommend
                    any of the childcare providers listed on its site, cannot be held responsible or liable in any way for
                    your dealings
                    with them, and does not guarantee the accuracy of listings on its site. We provide this site as a
                    directory to assist
                    you in locating childcare providers in your area. We do not own or operate any child care facility, and
                    make no
                    representation of any of the listings contained within ChildcareCenter.us.</p>
            </div>
        </section>
    </div>
    <!------------about section---------->
@endsection
