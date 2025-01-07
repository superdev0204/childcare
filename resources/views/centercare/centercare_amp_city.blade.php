@push('meta')
    <meta name="description"
        content="There are {{ $city->center_count }} daycare centers, preschools and childcare centers in {{ $city->city }} {{ $city->state }} child care center database.{{ $page > 1 ? ' This is page ' . $page : '' }}">
@endpush

@push('link')
    <link rel="canonical" href="https://childcarecenter.us/{{ $state->statefile }}/{{ $city->filename }}_childcare">
@endpush

@push('title')
    <title>Child Care Centers and Preschools in {{ ucwords(strtolower($city->city)) }}
        {{ $city->state . ($page > 1 ? ', Page ' . $page : '') }}</title>
@endpush

@extends('layouts.app_amp_old')

@section('content')
    <div class="container">
        <!-- Ezoic - AMP Header - top_of_page -->
        <div id="ezoic-pub-ad-placeholder-146"><amp-ad layout="fixed-height" height=100 type="adsense"
                data-ad-client="ca-pub-8651736830870146" data-ad-slot="3018812480">
            </amp-ad>
        </div>
        <!-- End Ezoic - AMP Header - top_of_page -->
        <div class="breadcrumbs">
            <ul>
                <li><a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> Child Care Centers</a> >> </li>
                <?php if ($city->county && !empty($city->countyfile)):?>
                <li><a href="<?php echo route('centercare_county', ['countyname' => $city->countyfile]); ?>"><?php echo ucwords(strtolower($city->county)); ?> County</a> &gt;&gt; </li>
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
                            alt="<?php echo htmlentities($provider->name); ?>" height="120" width="156" /></a>
                    <h3><a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo htmlentities($provider->name); ?></a>
                        <div class="review">
                            <?php if($provider->avg_rating<>'') : ?>
                            <?php for ($j=0;  $j<5; $j++):?>
                            <?php if ($provider->avg_rating-$j>0.5):?>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <?php elseif ($provider->avg_rating-$j==0.5):?>
                            <i class="fa fa-star-half-o" aria-hidden="true"></i>
                            <?php else: ?>
                            <i class="fa fa-star-o" aria-hidden="true"></i>
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
            <!-- Ezoic - AMP Inarticle - under_first_paragraph -->
            <div id="ezoic-pub-ad-placeholder-152">
                <amp-ad layout="responsive" width=300 height=250 type="adsense" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="9209526755">
                </amp-ad>
            </div>
            <!-- End Ezoic - AMP Inarticle - under_first_paragraph -->
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
                    <a href="/provider_detail/<?php echo $provider->filename; ?>">
                        <amp-img src="<?php echo $provider->logo; ?>" alt="<?php echo htmlentities($provider->name); ?>" height="150" width="200"
                            layout="responsive"></amp-img>
                    </a>

                    <h3>
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo htmlentities($provider->name); ?></a>
                        <div class="review">
                            <?php if($provider->avg_rating<>'') : ?>
                            <?php for ($j=0;  $j<5; $j++):?>
                            <?php if ($provider->avg_rating-$j>0.5):?>
                            <i class="fa fa-star" aria-hidden="true"></i>
                            <?php elseif ($provider->avg_rating-$j==0.5):?>
                            <i class="fa fa-star-half-o" aria-hidden="true"></i>
                            <?php else: ?>
                            <i class="fa fa-star-o" aria-hidden="true"></i>
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
                <div class="update">
                    <?php if ($i == 4):?>
                    <!-- Ezoic - AMP Middle 1 - mid_content -->
                    <div id="ezoic-pub-ad-placeholder-153">
                        <amp-ad layout="responsive" width=300 height=250 type="adsense"
                            data-ad-client="ca-pub-8651736830870146" data-ad-slot="6874455148">
                        </amp-ad>
                    </div>
                    <!-- End Ezoic - AMP Middle 1 - mid_content -->
                    <?php endif;?>
                    <?php if ($i == 9):?>
                    <!-- Ezoic - AMP Middle 2 - mid_content -->
                    <div id="ezoic-pub-ad-placeholder-154">
                        <amp-ad layout="responsive" width=300 height=250 type="adsense"
                            data-ad-client="ca-pub-8651736830870146" data-ad-slot="1099864188">
                        </amp-ad>
                    </div>
                    <!-- End Ezoic - AMP Middle 2 - mid_content -->
                    <?php endif;?>
                    <?php if ($i == 15):?>
                    <!-- Ezoic - AMP Middle 3 - mid_content -->
                    <div id="ezoic-pub-ad-placeholder-155">
                        <amp-ad layout="responsive" width=300 height=250 type="adsense"
                            data-ad-client="ca-pub-8651736830870146" data-ad-slot="6874455148">
                        </amp-ad>
                    </div>
                    <!-- End Ezoic - AMP Middle 3 - mid_content -->
                    <?php endif;?>
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

            <div class="media">
                <h3>Share With Us:</h3>
                <amp-addthis width="360" height="80" data-pub-id="ra-5b79303edadadd72" data-widget-id="1vr8"
                    class="social_share"></amp-addthis>
            </div>

            <?php if (count($zipcodes) > 1) :?>
            <div class="listSidebar">
                <h3>Childcare Center Search</h3>
                <?php if ($city->center_count >= 20 ) :?>
                Zip Codes in <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?><br />
                <?php else : ?>
                Zip Codes Near <?php echo ucwords(strtolower($city->city)) . ', ' . $city->state; ?><br />
                <?php endif;?>

                <?php           /** @var \Application\Domain\Entity\Zipcode $zipcode */
                foreach ($zipcodes as $zipcode): ?>
                <?php if($zipcode->center_count > 0):?>
                <a href="<?php echo $zipcode->zipcode; ?>_childcare"><?php echo $zipcode->zipcode; ?></a>
                <?php endif; ?>
                <?php endforeach;?>
            </div>
            <?php endif; ?>

            <div class="listSidebar">
                <h3>Quick Links</h3>
                <div class="quick-links">
                    <?php if ($city->countyfile):?>
                    <a href="<?php echo route('centercare_county', ['countyname' => $city->countyfile]); ?>"><?php echo ucwords(strtolower($city->county)) . ', ' . $city->state; ?> County </a>
                    <?php endif;?>
                    <a href="<?php echo route('centercare_allcities', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> City List</a>
                    <a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> County List</a>
                    <?php if ($city->homebase_count>0) :?>
                    <a href="<?php echo route('homecare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Home Daycare</a>
                    <?php endif; ?>
                    <!-- Ezoic - AMP Sidebar Bottom - link_side -->
                    <div id="ezoic-pub-ad-placeholder-150">
                        <amp-ad layout="responsive" width=300 height=250 type="adsense"
                            data-ad-client="ca-pub-8651736830870146" data-ad-slot="1215610375">
                        </amp-ad>
                    </div>
                    <!-- End Ezoic - AMP Sidebar Bottom - link_side -->
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
