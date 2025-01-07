@if ($page > 1)
    @push('meta')
        <meta name="description"
            content="There are {{ $city->homebase_count }} group home daycare and family child care providers in {{ $city->city }} {{ $city->state }} home daycare database. This is page {{ $page }}">
        <meta name="keywords" content="{{ $city->city }} {{ $city->state }} home daycare, family day care">
    @endpush

    @push('title')
        <title>Family Child Care and Group Home Daycare in {{ ucwords(strtolower($city->city)) }}, {{ $state->state_code }},
            p{{ $page }}</title>
    @endpush
@else
    @push('meta')
        <meta name="description"
            content="There are {{ $city->homebase_count }} group home daycare and family child care providers in {{ $city->city }} {{ $city->state }} home daycare database.">
        <meta name="keywords" content="{{ $city->city }} {{ $city->state }} home daycare, family day care">
    @endpush

    @push('title')
        <title>In-Home Daycare and Group Home Child Care in {{ ucwords(strtolower($city->city)) }}
            {{ $state->state_code }}</title>
    @endpush
@endif

@push('link')
    @if ($page <= 1)
        <link rel="canonical" href="https://childcarecenter.us/{{ $state->statefile }}_homecare/{{ $city->filename }}_city">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
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
                <li><a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> Family Day Care</a> >> </li>
                <?php if ($city->county): ?>
                <li><a href="<?php echo route('homecare_county', ['state' => $state->statefile, 'countyname' => $city->countyfile]); ?>"><?php echo ucwords(strtolower($city->county)); ?> <?php echo $state->state_code_normal; ?> </a> &gt;&gt; </li>
                <?php endif;?>
                <li><?php echo ucwords(strtolower($city->city)); ?> Home Child Care</li>
            </ul>
        </div>
        <!---------left container------>

        <section class="left-sect">
            <?php if ($page > 1) :?>
            <h1>Family Child Care and Group Home Daycare in <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?> </h1>
            <?php else: ?>
            <h1>In-Home and Group Home Daycare in <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?> </h1>
            <?php endif; ?>

            <p>The <?php echo ucwords(strtolower($city->city)); ?> home daycare options below are dedicated to providing families
                with quality home childcare in a safe and nurturing environment. Group home daycares are personable
                alternatives to large
                centers with hundreds of children. Entrusting your family childcare to a <?php echo ucwords(strtolower($city->city)); ?> home
                daycare gives children the added security of being cared for in a home environment while still giving
                parents the peace of mind
                that comes from knowing their children are under the supervision of licensed professionals. We gathered the
                information for home
                childcare centers in <?php echo ucwords(strtolower($city->city)); ?> into one place in order to help simplify your search
                and make it more enjoyable. Since home daycare information can change often, please help us stay up to date
                by letting us know
                if any of the information on our childcare providers is out of date or incorrect. We want to give you the
                right information
                every time.</p>

            <?php if (isset($referalResources) && count($referalResources)): ?>
            <p>Need more assistance? Simply contact the child care <a href="#agency">referral agency</a> or the licensing
                agency listed on the right!</p>
            <?php endif; ?>

            <?php if (isset($topProviders) && count($topProviders)): ?>
            <div class="up-section head">
                <h2><?php echo ucwords(strtolower($city->city)); ?> Featured Child Care Member:</h2>
                <?php
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($topProviders as $provider):?>
                <div class="update">
                    <a href="/provider_detail/<?php echo $provider->filename; ?>"><img src="<?php echo $provider->logo; ?>"
                            alt="<?php echo $provider->name; ?>" height="120" width="156" /></a>
                    <h3><a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo htmlentities($provider->name); ?></a>
                        <div class="review">
                            <?php if($provider->avg_rating <> '') : ?>
                            <?php for ($j=0; $j<5; $j++):?>
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
                    <span><?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone; ?></span>
                    <p>
                        <?php
                        $description = strip_tags($provider->introduction);
                        if (strlen($description) > 280) {
                            $description = substr($description, 0, strpos($description, ' ', 280)) . ' ...';
                        }
                        echo $description;
                        ?>
                    </p>
                </div>
                <?php endforeach;?>
            </div>
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

            <div align="right">
                @if ($providers instanceof Illuminate\Pagination\LengthAwarePaginator)
                    {{ $providers->links() }}
                @endif
            </div>

            <?php if(count($providers)>0):?>
            <div class="up-section head">
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
                    <span>
                        <?php if ($provider->address) {
                            echo ucwords(strtolower($provider->address)) . ', ';
                        }
                        echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone;
                        ?>
                    </span>

                    <p>
                        <?php
                        $description = strip_tags($provider->introduction);
                        if (strlen($description) > 300) {
                            $description = substr($description, 0, strpos($description, ' ', 260)) . ' ...';
                        }
                        echo $description;
                        ?>
                        <?php if ($i == 5):?>
                        <br />
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
                    <?php endif;?>
                    </p>

                </div>
                <?php endforeach;?>
            </div>
            <?php endif;?>

            <div align="center">
                @if ($providers instanceof Illuminate\Pagination\LengthAwarePaginator)
                    {{ $providers->links() }}
                @endif
            </div>

            <p>Thank you for using <a href="/">ChildCareCenter.us</a>. We are constantly enhancing our website to
                better service you.
                Please check back frequently for more updates. If you have any suggestions, please <a
                    href="/contact">contact</a> us.
                We appreciate your business and feedback very much.
            </p>

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

            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>

            <?php if (count($zipcodes) > 1) :?>
            <div class="listSidebar child-srch2">
                <h3>Home Daycare Search</h3>
                <?php if ($city->center_count >= 20 ) :?>
                Zip Codes in <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?><br />
                <?php else : ?>
                Zip Codes Near <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?><br />
                <?php endif;?>
                <form id="searchform" enctype="application/x-www-form-urlencoded" method="post" action="/search">
                    @csrf
                    <input type="hidden" name="type" value="home" id="type" />
                    <div class="form-city">
                        <select id="zip" name="zip">
                            <option value="">&nbsp; -Select ZIP Code- &nbsp;</option>
                            <?php if ($zipcodes): ?>
                            <?php
                            /** @var \Application\Domain\Entity\Zipcode $zipcode */
                            foreach ($zipcodes as $zipcode): ?>
                            <option value="<?php echo $zipcode->zipcode; ?>">&nbsp; ZIP Code <?php echo $zipcode->zipcode; ?> &nbsp; &nbsp;</option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>
                    </div><br />
                    <input type="submit" name="search" id="search" value="Search" />
                </form>
            </div>
            <?php endif; ?>

            <div class="listSidebar">
                <h3>Quick Links</h3>
                <div class="quick-links">
                    <?php if ($city->countyfile):?>
                    <a href="<?php echo route('homecare_county', ['state' => $state->statefile, 'countyname' => $city->countyfile]); ?>"><?php echo ucwords(strtolower($city->county)) . ', ' . $city->state; ?> <?php echo $state->state_code_normal; ?> </a>
                    <?php endif;?>
                    <a href="<?php echo route('homecare_allcities', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> City List</a>
                    <a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> <?php echo $state->state_code_normal; ?> List</a>
                    <?php if ($city->center_count > 0) :?>
                    <a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Child Care Centers</a>
                    <?php endif; ?>
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
            </div>

            <?php if($state->agency): ?>
            <div class="listSidebar">
                <h3><?php echo $state->state_name; ?> Child Care Licensing Agency</h3>
                <?php echo $state->agency; ?>
            </div>
            <?php endif;?>

            <?php if (($providers instanceof Illuminate\Pagination\LengthAwarePaginator && $page >= 5) || (is_array($providers) && count($providers) >= 5)):?>
            <div id="adcontainer1"></div>
            <script src="//www.google.com/adsense/search/ads.js" type="text/javascript"></script>
            <script type="text/javascript" charset="utf-8">
                var pageOptions = {
                    'pubId': 'pub-8651736830870146',
                    'query': '<?php $randomString = ['preschool', 'daycare', 'montessori', 'child care'];
                    echo strtolower($city->city) . ' ' . $randomString[rand(0, 3)]; ?>',
                    'channel': '2893199979',
                    'hl': 'en'
                };

                var adblock1 = {
                    'container': 'adcontainer1',
                    'width': '300px',
                    'colorTitleLink': '215C97'
                };

                new google.ads.search.Ads(pageOptions, adblock1);
            </script>
            <?php endif;?>
        </section>
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
