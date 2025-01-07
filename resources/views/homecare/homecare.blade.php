@push('meta')
    <meta name="description"
        content="ChildcareCenter.us provides over 150,000 family childcare and group home daycare providers.">
    <meta name="keywords" content="home daycare, group home day care, family child care">
@endpush

@push('title')
    <title>Home Day Care | Group Home Daycare | Family Child Care</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <style>
            .CCC_MOINSBD_HEADER {
                width: 320px;
                height: 100px;
            }

            @media(min-width: 500px) {
                .CCC_MOINSBD_HEADER {
                    width: 468px;
                    height: 60px;
                }
            }

            @media(min-width: 800px) {
                .CCC_MOINSBD_HEADER {
                    width: 970px;
                    height: 90px;
                }
            }
        </style>
        <!-- Ezoic - CCC MOINSBD HEADER - top_of_page -->
        <div id="ezoic-pub-ad-placeholder-104">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- CCC_MOINSBD_HEADER -->
            <ins class="adsbygoogle CCC_MOINSBD_HEADER" style="display:inline-block" data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="1034013575"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <!-- End Ezoic - CCC MOINSBD HEADER - top_of_page -->
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home </a> &gt;&gt; </li>
                <li>Family Daycare</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Find a Home Daycare Provider</h1>
            <p>Parents agonize over the decision to leave their child in another person's care when they go to work.
                Thousands of choices for child care abound from preschools to institutional day care facilities. A more
                intimate option is a home daycare, also called family daycare. These take place inside homes rather than
                businesses,
                and more closely resemble play groups of other young children from the surrounding neighborhood. Unlike
                commercial
                centers or preschools, home child care offers a smaller child to "teacher" ratio, enabling each child to
                receive the highest level of care possible without his parents.</p>
            <p>ChildcareCenter.us has a listing of over <?php echo number_format($summary->homebase_count); ?> home daycares,
                with about 10 new providers added to our database daily. Feel free to use the <strong>Home Daycare</strong>
                Search box on the
                page to find the perfect child care solution for you!</p>
            <div class="child-srch">
                <h3>Home Daycare Search</h3>
                <form id="searchform" enctype="application/x-www-form-urlencoded" method="post" action="/search">
                    @csrf
                    <input type="hidden" name="type" value="home" id="type" />
                    <div class="form-group"><label for="name">In ZIP Code (i.e. 33781):</label>
                        <input type="text" value="" name="zip" />
                    </div>
                    <div class="form-group"><label for="name">Or City/State (i.e Orlando,FL):</label>
                        <input type="text" value="" name="location" />
                    </div>
                    <input type="submit" class="" value="Search" />
                </form>
            </div>
            <div align="center"><br />
                <style>
                    .CCC_MOINSBD_MIDDLE {
                        width: 300px;
                        height: 250px;
                    }

                    @media(min-width: 500px) {
                        .CCC_MOINSBD_MIDDLE {
                            width: 300px;
                            height: 250px;
                        }
                    }

                    @media(min-width: 800px) {
                        .CCC_MOINSBD_MIDDLE {
                            width: 336px;
                            height: 280px;
                        }
                    }
                </style>
                <!-- Ezoic - CCC MOINSBD MIDDLE - long_content -->
                <div id="ezoic-pub-ad-placeholder-103">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- CCC_MOINSBD_MIDDLE -->
                    <ins class="adsbygoogle CCC_MOINSBD_MIDDLE" style="display:inline-block"
                        data-ad-client="ca-pub-8651736830870146" data-ad-slot="2510746772"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <!-- End Ezoic - CCC MOINSBD MIDDLE - long_content -->
            </div>
            <div class="map-section2 head">
                <h2>Browse by State</h2>
                <div class="map2"><img src="{{ asset('/images/childcare-map.png') }}"width="636" height="310"
                        alt="Map of Family Childcare in the United States" usemap="#map" /></div>
                <map name="map" id="map">
                    <?php 
                    /** @var \Application\Domain\Entity\State $state */
                    foreach ($states as $state): ?>
                    <area shape="poly" alt="<?php echo $state->state_name; ?> Family Daycare"
                        title="<?php echo $state->state_name; ?> Home Daycare (<?php echo number_format($state->homebase_count); ?> providers)"
                        coords="<?php echo $state->coords; ?>" href="/<?php echo $state->statefile; ?>_homecare" />
                    <?php endforeach;?>
                </map>
            </div>
            <p>When researching whether to place a child with a home daycare, the parents should check their state's
                guidelines governing the licensing of
                family daycare providers. Not all states require the providers to hold a license, and some states only
                require a license for providers caring for
                six or more children. Looking into the state's regulations and then following up by researching the
                credentials of the home child care
                provider will ensure the safety of the child.</p>

            <p>Parents should also know that many family daycare providers participate in state subsidies program which can
                make paying for the
                cost easier for those short on funds. Find out more information about covering the costs of preschool or
                early child care by reading both
                articles: <a href="/resources/child-care-assistance-programs-in-the-us">Child Care Assistance Programs</a>
                and
                <a href="/resources/apply-for-childcare-assistance-programs">Appy for Child Care Assistance</a>.
            </p>
            <!-- Ezoic - ChildcareCenter Home State InArticle - longest_content -->
            <div id="ezoic-pub-ad-placeholder-111">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle" style="display:block; text-align:center;" data-ad-layout="in-article"
                    data-ad-format="fluid" data-ad-client="ca-pub-8651736830870146" data-ad-slot="5224096048"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <!-- End Ezoic - ChildcareCenter Home State InArticle - longest_content -->

            <div class="up-section">
                <h2>Latest Home Daycare Updates</h2>
                <?php $i=0; 
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider):$i++; ?>
                <div class="update">
                    <img src="<?php echo $provider->logo; ?>" alt="<?php echo $provider->name; ?>" height="120" width="156" />
                    <h3>
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a>
                    </h3>
                    <span><?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone; ?></span>
                    <p><?php echo substr(strip_tags($provider->introduction), 0, 250); ?>...</p>
                </div>
                <?php endforeach;?>
            </div>
        </section>
        <!---------right container------>
        <section class="right-sect">

            <div class="social-links">
                <iframe
                    src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                    width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                    allowTransparency="true" allow="encrypted-media"></iframe>
                <!-- Ezoic - ChildcareCenter Quicklinks LinkAds - link_top -->
                <div id="ezoic-pub-ad-placeholder-108">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- ChildcareCenter Quicklinks LinkAds -->
                    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                        data-ad-slot="8798383174" data-ad-format="link"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <!-- End Ezoic - ChildcareCenter Quicklinks LinkAds - link_top -->
            </div>

            <div class="re-rgt rgt">
                <h3>Child Care Resources:</h3>
                <div class="re-bg2">
                    <?php 
                    /** @var \Application\Domain\Entity\Page $resource */
                    foreach ($resources as $resource):?>
                    <div class="re-box2">
                        <h3><a href="/resources/<?php echo $resource->pagename; ?>"><?php echo $resource->header; ?></a></h3>
                        <p><?php echo substr(strip_tags($resource->body), 0, 200); ?>...</p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a class="success-btn" href="/resources/">All Resources</a>
            </div>
            <a href="https://nanny.us"><img src="{{ asset('/images/nanny.gif') }}"
                    alt="Find Nannies, Sitters and Nanny Agencies" /></a>
            <div class="list-section rgt">
                <h3>Top Family Daycare Searches:</h3>
                <div class="list2">
                    <ul>
                        <?php
                        /** @var \Application\Domain\Entity\City $city */
                        foreach ($cities as $city): ?>
                        <li> <a href="/<?php echo $city->statefile; ?>_homecare/<?php echo $city->filename; ?>_city"><?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?> Home
                                Daycare </a> </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
