@push('meta')
    <meta name="description"
        content="ChildcareCenter.us includes all {{ $state->center_count }} child care centers and preschools in {{ $state->state_name }}. This page includes all {{ $state->state_code_plural }} in the state.">
@endpush

@push('title')
    <title>{{ $state->state_name }} Child Care and Daycare Centers | All {{ $state->state_code_plural }} in
        {{ $state->state_name }}</title>
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
                <li><a href="/state">Childcare Center ></a></li>
                <li><?php echo $state->state_name; ?> Child Care Centers</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect">
            <h1><?php echo $state->state_name; ?> Child Care - All <?php echo $state->state_code_plural; ?></h1>
            <p>Below are all <?php echo strtolower($state->state_code_plural); ?> in <?php echo $state->state_name; ?> that have childcare providers listed.
                Select the <?php echo $state->state_code_lower; ?> in which you want to look for a child care provider.</p>
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
            <?php if ($state->homebase_count >2):?>
            <p>If you're looking for a <strong>family day care </strong> provider in <?php echo $state->state_name; ?>, click on <a
                    href="/<?php echo $state->statefile; ?>_homecare"><?php echo $state->state_name; ?> Home Daycare. </a></p>
            <?php endif; ?>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- ChildcareCenter Responsive High -->
            <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="5807779177" data-ad-format="auto"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            <div class="cbyc head">
                <h2><?php echo $state->state_name; ?> Child Care Centers by <?php echo $state->state_code_normal; ?>:</h2>
                <div class="cities">
                    <ul>
                        <?php
                    /** @var \Application\Domain\Entity\County $county */
                    foreach ($counties as $county): ?>
                        <?php if ($county->center_count > 0) : ?>
                        <li><a href="/county/<?php echo $county->county_file; ?>"><?php echo ucwords(strtolower($county->county)); ?> </a>
                            <span>(<?php echo $county->center_count; ?>)</span></li>
                        <?php else:  ?>
                        <li><?php echo ucwords(strtolower($county->county)); ?> Childcare Centers</li>
                        <?php endif; ?>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>

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
            <?php if ($state->agency): ?>
            <div class="li-ag rgt">
                <h3><?php echo $state->state_name; ?> Child Care Licensing Agency</h3>
                <?php echo $state->agency; ?>

                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle" style="display:inline-block;width:180px;height:150px"
                    data-ad-client="ca-pub-8651736830870146" data-ad-slot="4560550338"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <?php endif;?>

            <div class="list-section rgt">
                <h3>Top <?php echo $state->state_name; ?> Cities</h3>
                <div class="list2 tece">
                    <ul>
                        <?php
                    /** @var \Application\Domain\Entity\City $city */
                    foreach ($cities as $city): ?>
                        <li><a href="{{ route('centercare_city', ['state' => $state->statefile, 'city' => $city->filename]) }}"><?php echo ucwords(strtolower($city->city)); ?> Childcare </a></li>
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
