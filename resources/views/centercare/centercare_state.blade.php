@push('meta')
    <meta name="description"
        content="ChildcareCenter.us includes all {{ $state->center_count }} child care centers and preschools in {{ $state->state_name }}">
@endpush

@push('link')
    <link rel="canonical" href="https://childcarecenter.us/state/{{ $state->statefile }}">
@endpush

@push('title')
    <title>{{ $state->state_name }} Child Care Centers</title>
@endpush

@extends('layouts.app')

@section('content')
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
                <li><a href="/">Home</a> >> </li>
                <li><a href="/state">Childcare Center</a> >> </li>
                <li><?php echo $state->state_name; ?> Child Care Centers</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect">
            <h1>Find Preschools and Child Care Centers in <?php echo $state->state_name; ?></h1>
            <p>With <?php echo number_format($state->center_count); ?> preschools and child development centers operating in the state of
                <?php echo $state->state_name; ?>, the right daycare option is waiting for you.
                Whether you prefer a larger preschool with an innovative early childhood curriculum or the cozy
                personalization of smaller daycare centers, there are <?php echo $state->state_name; ?> childcare centers to fit every
                preference and budget.
                Check out the Quick Search box on the right hand side of the page in order to search for childcare providers
                by zip code or city/state. You can also click on your <?php echo $state->state_name; ?> <?php echo $state->state_code_lower; ?> and follow the
                links.
                You will be able to access information about scores of different childcare providers in your area, complete
                with reviews, business hours, a street view map, the age ranges the childcare providers service, the size of
                the service, and where the provider is located. </p>
            <div class="child-srch">
                <h3>Childcare Center Search</h3>
                <form id="searchform" enctype="application/x-www-form-urlencoded" method="post" action="/search">
                    @csrf
                    <input type="hidden" name="type" value="center" id="type" />
                    <div class="form-group">
                        <label for="zip">In ZIP Code (i.e. 33781):</label>
                        <input type="text" value="" id="zip" name="zip" />
                    </div>
                    <div class="form-group">
                        <label for="location">Or City/State (i.e Orlando,FL):</label>
                        <input type="text" value="" id="location" name="location" />
                    </div>
                    <input type="submit" class="" value="Search" />
                </form>
            </div>
            <?php if ($state->homebase_count > 2):?>
            <p>If you're looking for a <strong>family day care </strong> provider in <?php echo $state->state_name; ?>, click on <a
                    href="/<?php echo $state->statefile; ?>_homecare"><?php echo $state->state_name; ?> Home Daycare. </a></p>
            <?php endif; ?>
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
            <div class="cbyc head">
                <h2><?php echo $state->state_name; ?> Child Care Centers by <?php echo $state->state_name; ?> <?php echo $state->state_code_normal; ?>:</h2>
                <div class="cities">
                    <ul>
                        <?php
                    /** @var \Application\Domain\Entity\County $county */
                    foreach ($counties as $county): ?>
                        <li><a href="/county/<?php echo $county->county_file; ?>"><?php echo ucwords(strtolower($county->county)); ?> </a>
                            <span>(<?php echo $county->center_count; ?>)</span></li>
                        <?php endforeach;?>
                    </ul>
                </div>
                <a href="?display=all" class="success-btn">All <?php echo $state->state_name; ?> <?php echo $state->state_name; ?>
                    <?php echo $state->state_code_plural; ?></a>

            </div>
            <?php if(count($providers)):?>
            <div class="up-section head">
                <h2>Latest updates to <?php echo $state->state_name; ?> childcare center database:</h2>
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
                <?php
                $i=0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider): $i++;?>
                <div class="update">
                    <img src="<?php echo $provider->logo; ?>" alt="<?php echo $provider->name; ?>" height="120" width="156" />
                    <h3>
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a>
                        <div class="review">
                            <?php if($provider->avg_rating<>'') : ?>
                            <?php for ($i=0;  $i<5; $i++):?>
                            <?php if ($provider->avg_rating-$i>0.5):?>
                            <i class="zmdi zmdi-star star"></i>
                            <?php elseif ($provider->avg_rating-$i==0.5):?>
                            <i class="zmdi zmdi-star-half star"></i>
                            <?php else: ?>
                            <i class="zmdi zmdi-star-outline star"></i>
                            <?php endif; ?>
                            <?php endfor;?>
                            <?php endif; ?>
                        </div>
                    </h3>

                    <span><?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone; ?> </span>
                    <p><?php echo strip_tags($provider->introduction); ?></p>
                </div>
                <?php endforeach;?>
            </div>
            <?php endif;?>

            <style>
                .question_section{
                    width:100%!important;
                }
                .question-title{
                    margin:auto!important;
                    float:none!important;
                }
                .question-wrapper{
                    width:100%!important
                }
                .single-question{
                    padding: 20px!important
                }
                .answer{
                    padding-left:20px!important;
                    clear: both
                }
                .reply{
                    clear: both;
                }
                .ask-question-btn{
                    clear: both;
                }
                .ask-question-btn{
                    margin:auto!important;
                    float:none!important;
                }
                .answer-btn{
                    float:right!important;
                }
            </style>
    
            <div class="section-body">
                <div class="question-title">
                    <h2 class="black-title">Ask the Community</h2>
                    <p>Connect, Seek Advice, Share Knowledge</p>
                </div>
                <div class="ask-question-btn">
                    <input type="button" class="btn" value="Ask a Question" onclick="window.location.href='/send_question?page_url={{ $page_url }}'" />
                </div>
                <div class="question-wrapper">
                    @foreach ($questions as $question)
                        <div class="single-question clinic_table">
                            <div class="question">
                                <p>Question by {{ $question->question_by }} ({{ $question->passed }} ago): <?php echo $question->question;?></p>
                            </div>
                            @foreach ($question->answers as $answer)
                                <div class="answer">
                                    <p>Answer: <?php echo $answer->answer;?></p>
                                </div>
                            @endforeach
                            <div class="answer-btn">
                                <input type="button" class="btn" value="Answer the Question Above" onclick="window.location.href='/send_answer?page_url={{ $page_url }}&questionId={{$question->id}}'" />
                            </div><br/>
                        </div>
                    @endforeach
                </div>            
            </div>
        </section>
        <!---------right container ends------>
        <section class="right-sect">
            <div class="social-links rgt">
                <iframe
                    src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                    width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                    allowTransparency="true" allow="encrypted-media"></iframe>

                <!-- Ezoic - CCC MOINSBD Link Sidebar - link_side -->
                <div id="ezoic-pub-ad-placeholder-102">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- CCC MOINSBD Link Sidebar -->
                    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                        data-ad-slot="8851698836" data-ad-format="link"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <!-- End Ezoic - CCC MOINSBD Link Sidebar - link_side --><br />

            </div>
            <?php if ($state->agency): ?>
            <div class="li-ag rgt">
                <h3><?php echo $state->state_name; ?> Child Care Licensing Agency</h3>
                <?php echo $state->agency; ?>
            </div>
            <?php endif;?>
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
                <a class="success-btn" href="/resources/">All Childcare Resources</a>
            </div>
            <div class="list-section rgt">
                <h3>Top Childcare Center Searches in <?php echo $state->state_name; ?></h3>
                <div class="list2 tece">
                    <ul>
                        <?php
                    /** @var \Application\Domain\Entity\City $city */
                    foreach ($cities as $city): ?>
                        <li><a href="/<?php echo $state->statefile; ?>/<?php echo $city->filename; ?>_childcare"><?php echo ucwords(strtolower($city->city)); ?> Childcare
                            </a></li>
                        <?php endforeach;?>
                    </ul>
                </div>
                <a class="success-btn" href="/<?php echo $state->statefile; ?>/allcities">All <?php echo $state->state_name; ?> Cities</a>
            </div>
            <div id="adcontainer1"></div>
            <script src="//www.google.com/adsense/search/ads.js" type="text/javascript"></script>
            <script type="text/javascript" charset="utf-8">
                var pageOptions = {
                    'pubId': 'pub-8651736830870146',
                    'query': '<?php $randomString = ['child care licensing', 'licensed childcare', 'low income child care', 'free child care'];
                    echo $randomString[rand(0, 3)]; ?>',
                    'channel': '3208213171',
                    'hl': 'en'
                };

                var adblock1 = {
                    'container': 'adcontainer1',
                    'width': '300px',
                    'colorTitleLink': '215C97'
                };

                new google.ads.search.Ads(pageOptions, adblock1);
            </script>
        </section>
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
