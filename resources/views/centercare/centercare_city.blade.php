@push('meta')
    <meta name="description"
        content="There are {{ $city->center_count }} daycare centers, preschools and childcare centers in {{ $city->city }} {{ $city->state }} child care center database.{{ $page > 1 ? ' This is page ' . $page : '' }}">
    <meta name="keywords" content="daycare centers, {{ $city->city }} {{ $city->state }} preschools, child care centers">
@endpush

@push('link')
    @if ($page <= 1)
        <link rel="canonical" href="https://childcarecenter.us/{{ $state->statefile }}/{{ $city->filename }}_childcare">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
@endpush

@push('title')
    <title>Child Care Centers and Preschools in {{ ucwords(strtolower($city->city)) }}
        {{ $city->state . ($page > 1 ? ', Page ' . $page : '') }}</title>
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
                <li><a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> Child Care Centers</a> >> </li>
                <?php if ($city->county && !empty($city->countyfile)):?>
                <li><a href="<?php echo route('centercare_county', ['countyname' => $city->countyfile]); ?>"><?php echo ucwords(strtolower($city->county)); ?> <?php echo $state->state_code_normal; ?></a> &gt;&gt; </li>
                <?php endif;?>
                <li><?php echo ucwords(strtolower($city->city)); ?> Childcare</li>
            </ul>
        </div>
        <!---------left container------>

        <section class="left-sect">

            <h1><?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?> Child Care Centers</h1>

            <p>
                <?php if ($city->center_count>0):?>
                Child development centers in <?php echo ucwords(strtolower($city->city)); ?> vary in size as well as in scope. While some offer
                progressive curriculums and the latest advancements for preschools, others are more intimate daycare centers
                that take a more relaxed approach to childcare.
                Whatever your priorities, finding the right daycare center for your child is important. We’ve made the
                seemingly overwhelming task easier by collecting basic information such as size, location, and licensing
                information for child development centers in <?php echo ucwords(strtolower($city->city)); ?> into a single location.
                Simply click on the links below to learn more about <?php echo ucwords(strtolower($city->city)); ?> childcare centers that are
                dedicated to providing families with safe, quality childcare.

                <?php else: ?>
                There are no child development centers in <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?>.
                Below are some preschools and child care centers nearby.
                Simply click on the links below to learn more about childcare centers near <?php echo ucwords(strtolower($city->city)); ?> that are
                dedicated to providing families with safe, quality childcare.

                <?php endif; ?>
                You can also read reviews about various childcare providers to learn more about which is the right choice
                for your family. We always welcome comments and corrections, to better the browsing experience on our site.
            </p>
            <p>
                <?php if ((is_array($providers) && count($providers) < 20) && $city->homebase_count > 0): ?>
                You may also want to check out <?php echo $city->homebase_count; ?> family child care providers and group home daycare in <a
                    href="<?php echo route('homecare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"> <?php echo ucwords(strtolower($city->city)); ?> Home Daycare. </a>
                <?php endif; ?>
            </p>

            <?php if (isset($topProviders) && count($topProviders) > 0): ?>
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
                    <span>
                        <?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone; ?>
                    </span>

                    <p><?php
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
                    <?php
                    if ($city->center_count <= 0):
                        $distance = $provider->distance;
                    endif;
                    ?>
                    <span>
                        <?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone . (isset($distance) ? $distance : ''); ?>
                    </span>

                    <p>
                        <?php
                        $description = strip_tags($provider->introduction);
                        if (strlen($description) > 260) {
                            $description = substr($description, 0, 250) . ' ...';
                        }
                        echo $description;
                        ?>
                    </p>
                </div>
                <?php if ($i == 4):?>
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
                <h3>Childcare Center Search</h3>
                <?php if ($city->center_count >= 20 ) :?>
                Zip Codes in <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?><br />
                <?php else : ?>
                Zip Codes Near <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?><br />
                <?php endif;?>
                <form id="searchform" enctype="application/x-www-form-urlencoded" method="post" action="/search">
                    @csrf
                    <input type="hidden" name="type" value="center" id="type" />
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

            <div class="listSidebar">
                <h3>Quick Links</h3>
                <div class="quick-links">
                    <?php if ($city->countyfile):?>
                    <a href="<?php echo route('centercare_county', ['countyname' => $city->countyfile]); ?>"><?php echo ucwords(strtolower($city->county)) . ', ' . $city->state; ?> <?php echo $state->state_code_normal; ?> </a>
                    <?php endif;?>
                    <a href="<?php echo route('centercare_allcities', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> City List</a>
                    <a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> <?php echo $state->state_code_normal; ?> List</a>
                    <?php if ($city->homebase_count>0) :?>
                    <a href="<?php echo route('homecare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Home Daycare</a>
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
                    <!-- End Ezoic - CCC MOINSBD Link Sidebar - link_side -->
                </div>
            </div>

            <?php if($state->agency): ?>
            <div class="listSidebar">
                <h3><?php echo $state->state_name; ?> Child Care Licensing Agency</h3>
                <?php echo $state->agency; ?>
            </div>
            <?php endif;?>

            <?php if (is_array($providers) && count($providers) >= 5):?>
            <!-- Ezoic - CCC MOINSBD SIDEBAR - sidebar_middle -->
            <div id="ezoic-pub-ad-placeholder-109">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- CCC MOINSBD SIDEBAR -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="3987479973" data-ad-format="auto"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <!-- End Ezoic - CCC MOINSBD SIDEBAR - sidebar_middle -->
            <?php endif;?>

        </section>
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
