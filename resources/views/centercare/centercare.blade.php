@push('meta')
    <meta name="description"
        content="There are over 110,000 child care centers nationwide, and you can search through all of them here at ChildcareCenter.us.">
    <meta name="keywords" content="childcare centers, preschools, child development centers">
@endpush

@push('title')
    <title>Child Care Centers | Child Development Center | Daycare Preschools</title>
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
                <li>Child Care Center</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Find a Quality Child Care Center</h1>
            <p>Entrusting your child with one of the many available childcare centers or preschools is a difficult decision
                for a parent to make.
                There are over <?php echo number_format($summary->center_count); ?> providers nationwide, and you can search through all of them, here.
                Narrow down your options based upon your child's needs and your desires for what you want in one of the many
                child development centers.
                Feel free to use the Childcare Center Search box on the page to find the perfect child care solution for
                you!</p>
            <div class="child-srch">
                <h3>Childcare Center Search</h3>
                <form id="searchform" enctype="application/x-www-form-urlencoded" method="post" action="/search">
                    @csrf
                    <input type="hidden" name="type" value="center" id="type" />
                    <div class="form-group">
                        <label for="name">In ZIP Code (i.e. 33781):</label>
                        <input type="text" value="" name="zip" />
                    </div>
                    <div class="form-group">
                        <label for="name">Or City/State (i.e Orlando,FL):</label>
                        <input type="text" value="" name="location" />
                    </div>
                    <input type="submit" class="" value="Search" />
                </form>
            </div>

            <div align="center">
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
                <div class="drg">
                    <h2>Browse by State</h2>
                    <img src="{{ asset('/images/childcare-map.png') }}" width="636" height="310"
                        alt="Map of Childcare Centers in the United States" usemap="#map" />
                </div>
                <map name="map" id="map">
                    <?php 
                /** @var \Application\Domain\Entity\State $state */
                foreach ($states as $state): ?>
                    <area shape="poly" title="<?php echo $state->state_name; ?> Childcare Centers (<?php echo number_format($state->center_count); ?> providers)"
                        coords="<?php echo $state->coords; ?>" href="/state/<?php echo $state->statefile; ?>"
                        alt="<?php echo $state->state_name; ?> Child Care Center" />
                    <?php endforeach;?>
                </map>
            </div>
            <p>If you need assistance in choosing among the childcare centers, preschools or child development centers,
                please read our article on <a href="/resources/choosing-a-quality-childcare-provider"> Choosing a Quality
                    Child Care Provider</a>.</p>
            <p>Paying for childcare can create another hurdle, but help is out there. Find out more information about
                covering the costs of preschool or early child care by reading both articles: <a
                    href="/resources/child-care-assistance-programs-in-the-us">Child Care Assistance Programs</a> and <a
                    href="/resources/apply-for-childcare-assistance-programs">Apply for Child Care Assistance</a>.</p>
            <p>Once you have found a childcare provider for your child, please leave feedback and reviews about the provider
                you sent your child to. This will help to either warn parents away from faulty facilities or to laud the
                praises of an exceptional preschool or child development center.</p>

            <div class="up-section">
                <div class="heading">
                    <h2>Latest Child Care Center Updates</h2>
                </div>
                <?php $i=0; 
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider):
                    $i++; 
                ?>
                <div class="update">
                    <img src="<?php echo $provider->logo; ?>" alt="<?php echo $provider->name; ?>" height="120" width="156" />
                    <h3>
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a>
                    </h3>
                    <span><?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone; ?></span>
                    <p><?php echo substr(strip_tags($provider->introduction), 0, 250); ?>...</p>
                </div>
                <?php if ($i == 3):?>
                <div class="update">
                    <!-- Ezoic - CCC MOINSBD InArticle - mid_content -->
                    <div id="ezoic-pub-ad-placeholder-101">
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <ins class="adsbygoogle" style="display:block; text-align:center;" data-ad-layout="in-article"
                            data-ad-format="fluid" data-ad-client="ca-pub-8651736830870146" data-ad-slot="6581108420"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                    <!-- End Ezoic - CCC MOINSBD InArticle - mid_content-->
                </div>
                <?php endif;?>
                <?php endforeach;?>
            </div>
        </section>
        <!---------right-container------>
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
                <h3>Childcare Center Resources:</h3>
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
            <div class="list-section rgt">
                <h3>Top Child Care Searches:</h3>
                <div class="list2">
                    <ul>
                        <?php  
                    /** @var \Application\Domain\Entity\City $city */
                    foreach ($cities as $city): ?>
                        <li><a href="{{ route('centercare_city', ['state' => $city->statefile, 'city' => $city->filename]) }}"><?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?> Childcare </a></li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
