@push('meta')
    <meta name="description"
        content="There are {{ $county->homebase_count }} providers in {{ $county->county }} {{ $county->state }} {{ $state->state_code_lower }} home daycare database.">
    <meta name="keywords"
        content="Group home day care, {{ $county->county }} {{ $county->state }} county home daycare, family child care">
@endpush

@push('link')
    <link rel="canonical" href="https://childcarecenter.us/{{ $state->statefile }}_homecare/{{ $county->county_file }}_county">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
@endpush

@push('title')
    <title>Home Daycare in {{ ucwords(strtolower($county->county)) }} {{ $state->state_code_normal }} |
        {{ ucwords(strtolower($county->county)) }} {{ $county->state }} Group Home Child Care</title>
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
            <ins class="adsbygoogle CCC_MOINSBD_HEADER" style="display:inline-block"
                data-ad-client="ca-pub-8651736830870146" data-ad-slot="1034013575"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <!-- End Ezoic - CCC MOINSBD HEADER - top_of_page -->
        <div class="breadcrumbs">
            <ul>
                <li><a href="/homecare">Home Daycare</a> >> </li>
                <li><a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> Family Day Care</a> >> </li>
                <li><?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?> Home Child Care</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect">
            <h1>In-Home Daycare and Group Home Child Care in <?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?></h1>
            <p>For busy parents, choosing the right type of family childcare can be challenging.
                More and more parents are finding that they prefer the intimate setting and personal touches of
                <?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?> home daycare or group home daycare to traditional preschools or
                daycare centers.
                If you’re interested in learning more about home childcare in <?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?>,
                you’ve come to the right place!
                We’ve done the initial leg work for you by collecting basic information of <?php echo number_format($county->homebase_count) . ' ' . ucwords(strtolower($county->county)); ?>
                <?php echo $state->state_code_normal; ?> home and group home daycares into a single location.

                <?php if (count($referalResources)): ?>
                If you need extra assistance with selecting the right child care provider for your family, simply contact
                the <a href="#agency">local referral agency</a> listed on the right!
                <?php endif; ?></p>
            <p> If you're looking for a <strong>Child Care Center </strong> in <?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?>, go
                to <a href="/county/<?php echo $county->county_file; ?>"><?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?> Childcare Centers. </a>
            </p>

            <?php if (isset($zipcodes) && count($zipcodes)>1): ?>
            <div class="child-srch">
                <h3>In-Home Daycare Search</h3>
                <form id="searchform" enctype="application/x-www-form-urlencoded" method="post" action="/search">
                    @csrf
                    <input type="hidden" name="type" value="home" id="type" />
                    <div class="form-group">
                        <select id="zip" name="zip">
                            <option value="">&nbsp; -Select ZIP Code- &nbsp;</option>
                            <?php
                            /** @var \Application\Domain\Entity\Zipcode $zipcode */
                            foreach ($zipcodes as $zipcode): ?>
                            <?php if($zipcode->center_count > 0): ?>
                            <option value="<?php echo $zipcode->zipcode; ?>">&nbsp; Zip Code <?php echo $zipcode->zipcode; ?> &nbsp; &nbsp;</option>
                            <?php else: ?>
                            <option value="<?php echo $zipcode->zipcode; ?>">&nbsp; ZIP Code <?php echo $zipcode->zipcode; ?> &nbsp; &nbsp;</option>
                            <?php endif; ?>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="form-group">
                        Or &nbsp;&nbsp;
                        <select id="location" name="location">
                            <option value="">&nbsp; -Select City- &nbsp;</option>
                            <?php if (isset($cities)): ?>
                            <?php
                            /** @var \Application\Domain\Entity\City $city */
                            foreach ($cities as $city): ?>
                            <option value="<?php echo ucwords(strtolower($city->city)) . ',' . $city->state; ?>">&nbsp; <?php echo ucwords(strtolower($city->city)); ?> &nbsp; &nbsp;</option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>
                    </div>
                    <input type="submit" name="search" id="search" value="Search" />
                </form>
            </div>

            <!--  <p>If your ZIP code is not in the dropdown list, use this link to see all <a href="?display=zipcode">ZIP Codes in <?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?></a></p> -->
            <?php endif;?>

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
            <?php if (count($providers)):?>
            <div class="up-section head">
                <?php if($county->homebase_count > 30): ?>
                <h2>Top <?php echo count($providers); ?> Home-based Providers in <?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?></h2>
                <?php else : ?>
                <h2>In-Home Daycare in <?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?></h2>
                <?php endif; ?>
                <?php
                $i=0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider): $i++;?>
                <div class="update">
                    <a href="/provider_detail/<?php echo $provider->filename; ?>"><img src="<?php echo $provider->logo; ?>"
                            alt="<?php echo $provider->name; ?>" height="120" width="156" /></a>
                    <h3><a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo htmlentities($provider->name); ?></a>
                        <div class="review">
                            <?php if($provider->avg_rating <> '') : ?>
                            <?php for ($j=0;  $j<5; $j++):?>
                            <?php if ($provider->avg_rating>0.5):?>
                            <i class="zmdi zmdi-star star"></i>
                            <?php elseif ($provider->avg_rating-$j==0.5):?>
                            <i class="zmdi zmdi-star-half star"></i>
                            <?php else: ?>
                            <i class="zmdi zmdi-star-outline star"></i>
                            <?php endif; ?>
                            <?php endfor;?>
                            <?php endif; ?>
                        </div>
                    </h3>
                    <span><?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone; ?></span>
                    <p>
                        <?php
                        $description = strip_tags($provider->introduction);
                        if (strlen($description) > 270) {
                            $description = substr($description, 0, strpos($description, ' ', 260)) . ' ...';
                        }
                        echo $description;
                        ?>
                    </p>
                </div>

                <?php if ($i == 4 && $county->homebase_count>=10):?>
                <!-- Ezoic - CCC MOINSBD InArticle - mid_content -->
                <div id="ezoic-pub-ad-placeholder-101">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <ins class="adsbygoogle" style="display:block; text-align:center;" data-ad-layout="in-article"
                        data-ad-format="fluid" data-ad-client="ca-pub-8651736830870146" data-ad-slot="6581108420"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <!-- End Ezoic - CCC MOINSBD InArticle - mid_content--><br />
                <?php endif;?>
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
                <?php if (isset($cities) && count($cities) > 10) : ?>
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
                <?php endif;?>
            </div>

            <?php if($county->homebase_count >= 10 && isset($cities) && count($cities) > 1): ?>
            <div class="listSidebar">
                <h3>Cities in <?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?></h3>
                <div class="<?php echo count($cities) < 10 ? 'quick-links' : 'city-links'; ?>">
                    <?php  foreach ($cities as $city): ?>
                    <?php if ($city->center_count <= 2) :?>
                    <a href="<?php echo route('homecare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Family Childcare </a>
                    <?php else: ?>
                    <a href="<?php echo route('homecare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Home Daycare </a>
                    <?php endif; ?>
                    <?php endforeach;?>
                </div>
                <?php if (count($cities)<=10) : ?>
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
                <?php endif;?>
            </div>
            <?php endif;?>

            <?php if (isset($referalResources) && count($referalResources)>=1): ?>
            <div class="listSidebar">
                <a name="agency"></a>
                <h3><?php echo ucwords(strtolower($county->county)); ?> <?php echo $state->state_code_normal; ?> Childcare Referral Agencies:</h3>
                <?php
                /** @var \Application\Domain\Entity\ReferalResource $resource */
                foreach ($referalResources as $resource): ?>
                <hr>
                <h4><?php echo $resource->name; ?></h4>

                <?php echo $resource->address; ?><br />
                <?php echo $resource->city . ' ' . $resource->state . ' ' . $resource->zip; ?>
                <p>
                    Call <?php echo $resource->phone; ?> <?php if($resource->tollfree <> ""):?>or Toll Free
                    <?php echo $resource->tollfree; ?><?php endif;?><br />
                    <?php if($resource->email <> ""):?>
                    Email: <?php echo $resource->email; ?><br />
                    <?php endif;?>
                    For more information, visit <a target="blank" href="<?php echo $resource->website; ?>"><?php echo $resource->website; ?></a>
                </p>
                <?php endforeach;?>
            </div>
            <?php endif; ?>

            <?php if($state->agency): ?>
            <div class="listSidebar">
                <h3><?php echo $state->state_name; ?> Child Care Licensing Agency</h3>
                <?php echo $state->agency; ?>
            </div>
            <?php endif;?>
            <br />
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
