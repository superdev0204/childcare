@push('title')
    <title>Home Daycare in Washington DC | {{ $state->state_name }} Family Child Care</title>
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
                <li><a href="/homecare">Home Daycare</a> >> </li>
                <li><?php echo $state->state_name; ?> In-Home Daycare</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect">
            <h1><?php echo ucwords(strtolower($state->state_name)); ?> Child Care Centers</h1>

            <p>
                <?php if ($state->homebase_count > 20) :?>
                There are <?php echo number_format($state->homebase_count); ?> home daycare in <?php echo ucwords(strtolower($state->state_name)); ?>. Use the ZIP code dropdown on the right
                hand side to narrow the childcare list.<br />
                <?php else :?>
                Below are the list of <?php echo $state->homebase_count; ?> home daycare in <?php echo ucwords(strtolower($state->state_name)); ?>.<br />
                <?php endif; ?>

                <?php if ($state->homebase_count <= 20 && $state->center_count > 0) :?>
                You may also want to checkout <?php echo $state->center_count; ?> other home daycare providers in <a
                    href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo ucwords(strtolower($state->state_name)); ?> Home Daycares</a>.<br />
                <?php elseif($state->center_count > 0): ?>
                If you're looking for a <a href="/state">Childcare Centers</a> in <?php echo $state->state_name; ?>, click on <a
                    href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo ucwords(strtolower($state->state_name)); ?> Childcare Centers</a>.<br />
                <?php endif; ?>

                <?php if(isset($referalResources) && count($referalResources) >= 1): ?>
                If you need help selecting the right child care center, contact your local agency <a
                    href="#agency">here</a>.
                <?php endif; ?>
            </p>

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

            <?php if(count($providers)):?>
            <div class="up-section head">
                <h2><?php echo ucwords(strtolower($state->state_name)); ?> childcare centers:</h2>
                <?php 
            $i=0; 
            /** @var \Application\Domain\Entity\Facility $provider */
            foreach ($providers as $provider): $i++;?>
                <div class="update">
                    <a href="/provider_detail/<?php echo $provider->filename; ?>"><img src="<?php echo $provider->logo; ?>"
                            alt="<?php echo $provider->name; ?>" height="150" width="200" /></a>
                    <h3>
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo htmlentities($provider->name); ?></a>
                        <div class="review">
                            <?php if($provider->avg_rating<>'') : ?>
                            <?php for ($j=0;  $j<5; $j++):?>
                            <?php if ($provider->avg_rating-$j>0.5):?>
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
                    <span><?php echo ucwords(strtolower($provider->address)) . ', ' . ucwords(strtolower($provider->city)) . ' ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone; ?> </span>
                    <p><?php
                        $description = strip_tags($provider->introduction);
                        if (strlen($description) > 270) {
                            $description = substr($description, 0, strpos($description, ' ', 260)) . ' ...';
                        }
                        echo $description;
                    ?>
                        <?php if ($i == 5):?>
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
                    </p>
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
            </div>
            <?php if (isset($zipcodes) && count($zipcodes) > 1) :?>
            <div class="listSidebar child-srch2">
                <h3>Childcare Center Search</h3>
                Zip Codes in <?php echo $state->state_name; ?><br />
                <form id="searchform" enctype="application/x-www-form-urlencoded" method="post" action="/search">
                    @csrf
                    <input type="hidden" name="type" value="home" id="type" />
                    <div class="form-city">
                        <select id="zip" name="zip">
                            <option value="">&nbsp; -Select ZIP Code- &nbsp;</option>
                            <?php
                        /** @var \Application\Domain\Entity\Zipcode $zipcode */
                        foreach ($zipcodes as $zipcode): ?>
                            <?php if($zipcode->center_count > 0):?>
                            <option value="<?php echo $zipcode->zipcode; ?>">&nbsp; Zip Code <?php echo $zipcode->zipcode; ?> &nbsp; &nbsp;</option>
                            <?php else: ?>
                            <option value="<?php echo $zipcode->zipcode; ?>">&nbsp; ZIP Code <?php echo $zipcode->zipcode; ?> &nbsp; &nbsp;</option>
                            <?php endif; ?>
                            <?php endforeach;?>
                        </select>
                    </div><br />
                    <input type="submit" class="" value="Search" />
                </form>
            </div>
            <?php endif; ?>
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
            <?php if(isset($referalResources)): ?>
            <div class="re-rgt rgt">
                <a name="agency"></a>
                <h3><?php echo ucwords(strtolower($county->county)); ?> County Childcare Referral Agencies:</h3>
                <div class="re-bg2">
                    <?php  
                $i = 0;
                /** @var \Application\Domain\Entity\ReferalResource $resource */
                foreach ($referalResources as $resource):
                    $i++; ?>

                    <div class="re-box2">
                        <h3><?php echo $resource->name; ?></h3>

                        <?php echo $resource->address; ?><br />
                        <?php echo $resource->city . ' ' . $resource->state . ' ' . $resource->zip; ?>
                        <p> Call <?php echo $resource->phone; ?> <?php if ($resource->tollfree <> "") :?>or Toll Free
                            <?php echo $resource->tollfree; ?><?php endif;?><br />
                            <?php if ($resource->email <> ""):?>
                            Email: <?php echo $resource->email; ?><br />
                            <?php endif;?>
                            For more information, visit <a target="blank"
                                href="<?php echo $resource->website; ?>"><?php echo $resource->website; ?></a>
                        </p>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($state->agency): ?>
            <div class="li-ag rgt">
                <h3><?php echo $state->state_name; ?> Child Care Licensing Agency</h3>
                <?php echo $state->agency; ?>
            </div><br />
            <?php endif;?>


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
