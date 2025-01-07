@push('meta')
    <meta name="description"
        content="There are {{ $zipcode->center_count }} daycare centers, preschools, and childcare centers in ZIP Code {{ $zipcode->zipcode }} child care center database.{{ $page > 1 ? ' This is page ' . $page : '' }}">
    <meta name="keywords" content="Daycare Centers, ZIP Code {{ $zipcode->zipcode }} preschools, child care centers">
@endpush

@push('link')
    @if ($page <= 1)
        <link rel="canonical" href="https://childcarecenter.us/{{ $state->statefile }}/{{ $zipcode->zipcode }}_childcare">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
@endpush

@push('title')
    <title>Child Daycare Centers in ZIP Code {{ $zipcode->zipcode }} | {{ $zipcode->zipcode }} Preschools{{ ($page > 1 ? ' , Page ' . $page : '') }}</title>
@endpush

@extends('layouts.app')

@section('content')
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
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
                <?php if (!$zipcode->cityfile):?>
                <li><a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> Child Care Centers</a> >> </li>
                <?php endif;?>
                <?php if (!empty($zipcode->countyfile)):?>
                <li><a href="<?php echo route('centercare_county', ['countyname' => $zipcode->countyfile]); ?>"><?php echo ucwords(strtolower($zipcode->county)); ?> <?php echo $state->state_code_normal; ?></a> &gt;&gt; </li>
                <?php endif;?>
                <?php if ($zipcode->cityfile):?>
                <li><a href="<?php echo route('centercare_city', ['state' => $zipcode->statefile, 'city' => $zipcode->cityfile]); ?>"><?php echo ucwords(strtolower($zipcode->city)); ?> Child Care </a> >> </li>
                <?php endif;?>
                <li>Childcare Centers in <?php echo $zipcode->zipcode; ?></li>
            </ul>
        </div>
        <!---------left container------>

        <section class="left-sect">

            <h1>Child Care Centers in ZIP code <?php echo $zipcode->zipcode; ?></h1>

            <?php if ( $zipcode->center_count > 10): ?>
            <p>Deciding which child care center you trust to take care of your child is no easy choice to make.
                With <?php echo $zipcode->center_count; ?> preschools and child development centers in ZIP Code <?php echo $zipcode->zipcode; ?>, we like
                your chances.
                Take a look at the options below and see the great services they have to offer your family. Be sure to check
                out the reviews and past inspection
                information on the provider listing, and if you have any past experience with a provider, please leave an
                honest review to help other parents in a similar situation.</p>

            <?php if($zipcode->homebase_count > 0): ?>
            <p>While you’re at it, you might want to take a look at the <?php echo $zipcode->homebase_count; ?> family child care providers
                and group home daycares in your area over at ZIP Code <a href="<?php echo route('homecare_zip', ['state' => $state->statefile, 'zipcode' => $zipcode->zipcode]); ?>"> <?php echo $zipcode->zipcode; ?>
                    Home Daycare. </a></p>
            <?php endif; ?>

            <?php elseif ($zipcode->center_count >= 1): ?>
            <p>Quality over quantity. <?php echo $zipcode->center_count; ?> child care centers in ZIP Code <?php echo $zipcode->zipcode; ?> may not give
                you the widest selection,
                but that doesn’t mean you won’t find the right people to welcome your child into their expert care. Take a
                look and see if any of them sound right
                for you. Be sure to check out the reviews and past inspection information on the provider listing, and if
                you have any past experience with a provider,
                please leave an honest review to help other parents in a similar situation.</p>

            <p>If none of those seem right, you can always expand your options by searching a neighboring ZIP Code.</p>
            <?php if($zipcode->homebase_count>0): ?>
            <p>Or, you might want to take a look at the <?php echo $zipcode->homebase_count; ?> family child care providers and group home
                daycares in your area over at ZIP Code <a href="<?php echo route('homecare_zip', ['state' => $state->statefile, 'zipcode' => $zipcode->zipcode]); ?>"> <?php echo $zipcode->zipcode; ?></a>.</p>
            <?php endif;?>
            <?php else:?>

            <p>Unfortunately, we couldn’t find any child care centers in ZIP Code <?php echo $zipcode->zipcode; ?>.</p>
            <?php if ($zipcode->center_count <=0 && count($providers) > 0) :?>
            <p>That doesn’t have to mean you’re out of luck, though. We’ve gone ahead and put a list of <?php echo count($providers); ?>
                preschools
                and child development centers in nearby ZIP Codes below. Maybe one of those will do the trick.</p>
            <?php endif; ?>


            <?php if($zipcode->homebase_count > 0): ?>
            <p>You can also take a look at our handy list of family child care providers and group home daycares in your
                area over at
                ZIP Code <a href="<?php echo route('homecare_zip', ['state' => $state->statefile, 'zipcode' => $zipcode->zipcode]); ?>"> <?php echo $zipcode->zipcode; ?>. You might find you like that style of child
                    care even better.</p>
            <?php endif; ?>
            <?php endif; ?>

            <p>If you need assistance in choosing among the childcare centers, preschools or daycare, please read our
                article on <a href="/resources/choosing-a-quality-childcare-provider"> choosing</a> a quality child care
                provider.</p>
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

            <?php if(count($topProviders)):?>
            <div class="up-section head">
                <?php
                $i=0;
                /** @var \Application\Domain\Entity\Facility $topProvider */
                foreach ($topProviders as $topProvider):
                    $i++;
                ?>
                <div class="update">
                    <a href="/provider_detail/<?php echo $topProvider->filename; ?>"><img src="<?php echo $topProvider->logo; ?>"
                            alt="<?php echo $topProvider->name; ?>" height="150" width="200" /></a>
                    <h3><a href="/provider_detail/<?php echo $topProvider->filename; ?>"><?php echo htmlentities($topProvider->name); ?></a>
                        <div class="review">
                            <?php if($topProvider->avg_rating<>'') : ?>
                            <?php for ($j=0;  $j<5; $j++):?>
                            <?php if ($topProvider->avg_rating-$j>0.5):?>
                            <i class="zmdi zmdi-star star"></i>
                            <?php elseif ($topProvider->avg_rating-$j==0.5):?>
                            <i class="zmdi zmdi-star-half star"></i>
                            <?php else: ?>
                            <i class="zmdi zmdi-star-outline star"></i>
                            <?php endif; ?>
                            <?php endfor;?>
                            <?php endif; ?>
                        </div>
                    </h3>
                    <?php
                    if ($zipcode->center_count <= 0) :
                        $distance = $topProvider->distance;
                    ?>
                    <span><?php echo ucwords(strtolower($topProvider->city)) . ', ' . $topProvider->state . ' | ' . $topProvider->formatPhone . $distance; ?></span>
                    <?php else: ?>
                    <span>
                        <?php echo ucwords(strtolower($topProvider->address)) . ', ' . ucwords(strtolower($topProvider->city)) . ' ' . $topProvider->state . ' | ' . $topProvider->formatPhone; ?>
                        <?php if($topProvider->capacity > 0) :?>
                        | Capacity: <?php echo $topProvider->capacity; ?> Children
                        <?php endif;?>
                    </span>
                    <?php endif ?>
                    <p>
                    <?php
                        $description = strip_tags($topProvider->introduction);
                        if (strlen($description) > 300) {
                            $description = substr($description, 0, strpos($description, ' ', 290)) . ' ...';
                        }
                        echo $description;
                    ?>
                    </p>
                </div>
                <?php endforeach;?>
            </div>
            <?php endif;?>

            <?php if(count($providers)):?>
            <div class="up-section head">
                <?php
                $i=0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider):
                    $i++;
                ?>
                <div class="update">
                    <a href="/provider_detail/<?php echo $provider->filename; ?>"><img src="<?php echo $provider->logo; ?>"
                            alt="<?php echo $provider->name; ?>" height="150" width="200" /></a>
                    <h3><a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo htmlentities($provider->name); ?></a>
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
                    <?php
                    if ($zipcode->center_count <= 0) :
                        $distance = $provider->distance;
                    ?>
                    <span><?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' | ' . $provider->formatPhone . $distance; ?></span>
                    <?php else: ?>
                    <span>
                        <?php echo ucwords(strtolower($provider->address)) . ', ' . ucwords(strtolower($provider->city)) . ' ' . $provider->state . ' | ' . $provider->formatPhone; ?>
                        <?php if($provider->capacity > 0) :?>
                        | Capacity: <?php echo $provider->capacity; ?> Children
                        <?php endif;?>
                    </span>
                    <?php endif ?>
                    <p>
                    <?php
                        $description = strip_tags($provider->introduction);
                        if (strlen($description) > 300) {
                            $description = substr($description, 0, strpos($description, ' ', 290)) . ' ...';
                        }
                        echo $description;
                    ?>
                    </p>
                </div>

                <?php if ($i == 3 && $zipcode->center_count>=6):?>
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

            <div align="center">
                @if ($providers instanceof Illuminate\Pagination\LengthAwarePaginator)
                    {{ $providers->links() }}
                @endif
            </div>

            <p>Thank you for using <a href="/">ChildCareCenter.us</a>. We are constantly enhancing our website to
                better service you.
                Please check back frequently for more updates. If you have any questions or suggestions, please get <a
                    href="/contact">in touch</a> with us.
                We appreciate your business and feedback very much.</p>

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

            <?php if ($zipcode->center_count>=5):?>
            <style>
                .CCC_MOINSBD_SIDEBAR {
                    width: 300px;
                    height: 250px;
                }

                @media(min-width: 500px) {
                    .CCC_MOINSBD_SIDEBAR {
                        width: 300px;
                        height: 250px;
                    }
                }

                @media(min-width: 800px) {
                    .CCC_MOINSBD_SIDEBAR {
                        width: 300px;
                        height: 600px;
                    }
                }
            </style>
            <!-- Ezoic - CCC MOINSBD SIDEBAR - sidebar_middle -->
            <div id="ezoic-pub-ad-placeholder-109">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- CCC_MOINSBD_SIDEBAR -->
                <ins class="adsbygoogle CCC_MOINSBD_SIDEBAR" style="display:inline-block"
                    data-ad-client="ca-pub-8651736830870146" data-ad-slot="3987479973"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <!-- End Ezoic - CCC MOINSBD SIDEBAR - sidebar_middle -->
            <?php endif;?>
            <div class="listSidebar">
                <h3>Quick Links</h3>
                <div class="quick-links">
                    <?php if ($state->nextlevel != "DETAIL"):?>
                    <?php if ($zipcode->cityfile):?>
                    <a href="<?php echo route('centercare_city', ['state' => $zipcode->statefile, 'city' => $zipcode->cityfile]); ?>"><?php echo ucwords(strtolower($zipcode->city)) . ', ' . $zipcode->state; ?> City </a>
                    <?php endif;?>
                    <?php if (!empty($zipcode->countyfile)):?>
                    <a href="<?php echo route('centercare_county', ['countyname' => $zipcode->countyfile]); ?>"><?php echo ucwords(strtolower($zipcode->county)) . ', ' . $zipcode->state; ?> <?php echo $state->state_code_normal; ?> </a>
                    <?php endif;?>
                    <a href="<?php echo route('centercare_allcities', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> City List</a>
                    <a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> <?php echo $state->state_code_normal; ?> List</a> <br />
                    <?php endif; ?>
                    <?php if ($zipcode->homebase_count>0 && !empty($zipcode->cityfile)) :?>
                    <a href="<?php echo route('homecare_city', ['state' => $zipcode->statefile, 'city' => $zipcode->cityfile]); ?>"><?php echo ucwords(strtolower($zipcode->city)); ?> Home Daycare</a>
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
        </section>
        <!---------right-container------>
        <!-------right container ends------>
    </div>
@endsection
