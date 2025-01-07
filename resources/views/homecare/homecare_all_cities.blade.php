@push('title')
    <title>{{ $state->state_name }} Home Daycare | All Cities in {{ $state->state_name }}</title>
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
                <li><a href="/<?php echo $state->statefile; ?>_homecare"><?php echo $state->state_name; ?> Home Daycare</a> >> </li>
                <li><?php echo $state->state_name; ?> Cities</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect">
            <h1><?php echo $state->state_name; ?> Home Daycare - All Cities</h1>

            <p>Below are all cities in <?php echo $state->state_name; ?> in which we have at least one home daycare listed.
                Select the city in which you want to look for a child care provider.</p>

            <!-- Ezoic - ChildcareCenter Responsive Medium - mid_content -->
            <div id="ezoic-pub-ad-placeholder-114">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- ChildcareCenter Responsive Medium -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="9738451173" data-ad-format="auto"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <!-- End Ezoic - ChildcareCenter Responsive Medium - mid_content -->
            <div class="cbyc head">
                <div class="cities">
                    <ul>
                        <?php
                        /** @var \Application\Domain\Entity\City $city */
                        foreach ($cities as $city): ?>
                        <?php if ($city->homebase_count > 0) : ?>
                        <li><a href="<?php echo route('homecare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> </a> (<?php echo $city->homebase_count; ?>)</li>
                        <?php else:  ?>
                        <li><?php echo ucwords(strtolower($city->county)); ?> Home Daycare</li>
                        <?php endif; ?>
                        <?php endforeach;?>
                    </ul>
                </div>

                <?php if ($state->homebase_count > 2):?>
                <p>If you're looking for a <strong>center-based</strong> provider in <?php echo $state->state_name; ?>, click on <a
                        href="/state/<?php echo $state->statefile; ?>"><?php echo $state->state_name; ?> Childcare Centers. </a></p>
                <?php endif; ?>
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
                    src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                    width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>
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

            <div class="child-srch">
                <h3>Home Daycare Search</h3>
                <form id="searchform" enctype="application/x-www-form-urlencoded" method="post" action="/search">
                    @csrf
                    <input type="hidden" name="type" value="home" id="type" />
                    <div>
                        <label for="name">In ZIP Code (i.e. 33781):</label>
                        <input type="text" value="" name="zip" />
                    </div>
                    <div>
                        <label for="name">Or City/State (i.e Orlando,FL):</label>
                        <input type="text" value="" name="location" />
                    </div>
                    <input type="submit" class="" value="Search" />
                </form>
            </div><br /><br />

            <?php if($state->agency): ?>
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
                <h3>Top <?php echo $state->state_name; ?> Counties</h3>
                <div class="list2 tece">
                    <ul>
                        <?php
                        /** @var \Application\Domain\Entity\County $county */
                        foreach ($counties as $county): ?>
                            <li><a href="<?php echo route('homecare_county', ['state' => $state->statefile, 'countyname' => $county->county_file]); ?>"><?php echo ucwords(strtolower($county->county)); ?> Home Daycares</a></li>
                        <?php endforeach;?>
                        <li><a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>">More <?php echo $state->state_name; ?> Counties</a></li>
                    </ul>
                </div>
            </div>
        </section>
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
