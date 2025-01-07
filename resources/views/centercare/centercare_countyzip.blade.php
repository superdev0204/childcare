@push('meta')
    <meta name="description"
        content="There are {{ $county->center_count }} daycare center, childcare centers and preschools in {{ $county->county }} {{ $county->state }} county child care center database. Please select the ZIP code to narrow your search.">
@endpush

@push('title')
    <title>Zip Codes in {{ ucwords(strtolower($county->county)) }}, {{ $county->state }} County</title>
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
                <li><a href="/state/<?php echo $state->statefile; ?>"><?php echo $state->state_name; ?> Child Care Centers</a> >> </li>
                <li><a href="/county/<?php echo $county->county_file; ?>"><?php echo ucwords(strtolower($county->county)); ?> County Child Care </a> >> </li>
                <li>ZIP Codes in <?php echo ucwords(strtolower($county->county)); ?> County</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1><?php echo ucwords(strtolower($county->county)); ?> County Child Care Centers - By ZIP Code</h1>

            <?php if ($state->center_count > 2) :?>
            Below is the list of all ZIP codes <?php echo ucwords(strtolower($county->county)) . ' ' . $state->state_name; ?> county.
            Select the ZIP code in which you want to look for a child care provider. <br />
            <!-- Ezoic - ChildcareCenter County Zip List - long_content -->
            <div id="ezoic-pub-ad-placeholder-113">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- ChildcareCenter County Zip List -->
                <ins class="adsbygoogle" style="display:inline-block;width:336px;height:280px"
                    data-ad-client="ca-pub-8651736830870146" data-ad-slot="7424113176"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <!-- End Ezoic - ChildcareCenter County Zip List - long_content -->

            <br />
            <?php endif;?>

            <div class="cities">
                <ul>
                    <?php 
                /** @var \Application\Domain\Entity\Zipcode $zipcode */
                foreach ($zipcodes as $zipcode): ?>
                    <?php if ($zipcode->center_count > 0) : ?>
                    <li><a href="<?php echo route('centercare_zipcode', ['state' => $state->statefile, 'zipcode' => $zipcode->zipcode]); ?>"><?php echo $zipcode->zipcode; ?> </a> (<?php echo $zipcode->center_count; ?>)</li>
                    <?php else:  ?>
                    <li><a href="<?php echo route('centercare_zipcode', ['state' => $state->statefile, 'zipcode' => $zipcode->zipcode]); ?>"><?php echo $zipcode->zipcode; ?> </a></li>
                    <?php endif; ?>
                    <?php endforeach;?>
                </ul>
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
        <!---------right container------>
        <section class="right-sect">

            <div class="social-links">
                <iframe
                    src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
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

            <div class="list-section rgt">
                <h3>Cities in <?php echo ucwords(strtolower($county->county)); ?> County</h3>
                <div class="list2 tece">
                    <ul>
                        <?php  
            /** @var \Application\Domain\Entity\City $city */
            foreach ($cities as $city): ?>
                        <?php if ($city->center_count <= 0) :?>
                        <li><a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Daycare </a></li>
                        <?php elseif ($city->center_count <= 2) :?>
                        <li><a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Childcare </a></li>
                        <?php else: ?>
                        <li><a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Child Care </a></li>
                        <?php endif; ?>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>

        </section>
        <!-------right container ends------>
    </div>
@endsection
