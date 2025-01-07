@push('meta')
    <meta name="description"
        content="We’re interested in connecting the best child care professionals in {{ $state->state_name }} with one another.">
    <meta name="keywords" content="{{ $state->state_name }} child care jobs">
@endpush

@push('title')
    <title>Child Care Jobs in {{ $state->state_name }} | {{ $state->state_name }} Childcare Employment</title>
@endpush

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> >> </li>
                <li><a href="/jobs">Child Care Job</a> >> </li>
                <li><?php echo $state->state_name; ?> Child Care Jobs</li>
            </ul>
        </div>
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- ChildcareJob All Pages Top Responsive -->
        <style type="text/css">
            .adslot_1 {
                display: inline-block;
                height: 90px;
            }

            @media (max-width:600px) {
                .adslot_1 {
                    width: 320px;
                    height: 100px;
                }
            }
        </style>
        <ins class="adsbygoogle adslot_1" style="display:block" data-ad-client="ca-pub-8651736830870146"
            data-ad-slot="8676971974"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script><br />

        <!---------left container------>
        <section class="left-sect">
            <h1>Find Childcare Jobs in <?php echo $state->state_name; ?></h1><br />
            <p>Looking for <?php echo $state->state_name; ?> child care jobs? Perhaps you’re hiring a teacher or nanny?
                Part time or full-time, evening and weekends included, we have all of your needs covered.
                We’re interested in connecting the best professionals in <?php echo $state->state_name; ?> with one another.
                Our large databases of job-seekers and employers connects nannies and teachers with families and educational
                programs.
                We’re confident that once you take advantage of the traffic our database attracts you won’t need to look
                anywhere else. Stop worrying about finding that right match anywhere else.
                Post a resume or job description online today to find the best fit available for your needs!</p>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- ChildcareJob State 1 -->
            <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="8452385975" data-ad-format="auto"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            <div class="cbyc head">
                <?php if (isset($cities)):?>
                <p>We currently have <?php echo $state->jobs_count; ?> child care jobs available in our database.
                    Please select from our list to view available jobs in that city.</p>
                <div class="cities">
                    <ul>
                        <?php
                        /** @var \Application\Domain\Entity\County $county */
                        foreach ($cities as $city): ?>
                        <li>
                            <a href="/<?php echo $state->statefile; ?>_jobs/<?php echo $city->filename; ?>_city"><?php echo ucwords(strtolower($city->city)); ?> </a> (
                            <?php echo $city->jobs_count; ?> )
                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>
                <?php else: ?>
                <p>We are in the process of building our child care jobs database to help find jobs and employee easier and
                    cheaper. Please be patient as we're improving our system to serve you better. </p>
                <?php endif; ?>
            </div>
            <?php if(count($jobs)>=1):?>
            <h2>Current list of child care jobs available in <?php echo $state->state_name; ?>:</h2>
            <div class="up-section head">
                <?php
                $i=0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($jobs as $job): $i++;?>
                <div class="update">
                    <h3>
                        <a href="/jobs/detail?id=<?php echo $job->id; ?>"><?php echo $job->title; ?></a><br />
                    </h3>

                    <span>
                        <?php echo $job->company . ' <br/> ' . $job->city . ', ' . $job->state; ?>
                    </span>

                    <p>
                        <strong>Detail:</strong>
                        <?php
                        $description = strip_tags($job->description);
                        if (strlen($description) > 260) {
                            $description = substr($description, 0, 250) . ' ... <a href="/jobs/detail?id=' . $job->id . '">more</a>';
                        }
                        echo $description . '</br>';
                        ?>
                    </p>
                </div>
                <?php if($i==2):?>
                <div class="update">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- ChildcareJob State 2 -->
                    <ins class="adsbygoogle" style="display:inline-block;width:336px;height:280px"
                        data-ad-client="ca-pub-8651736830870146" data-ad-slot="9789518379"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <?php endif;?>
                <?php endforeach;?>
            </div>
            <?php endif;?>

        </section>
        <!---------right container------>
        <section class="right-sect">
            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>
            <div class="listSidebar">
                <h3>Quick Links</h3>
                <div class="quick-links">
                    <a href="/resumes" title="Find Child Care Resumes">Find Resumes</a>
                    <a href="/jobs/new" title="Post Child Care Job">Post Job</a>
                    <a href="/resumes/new" title="Post Child Care Resume">Post Resume</a>
                </div>
            </div>

        </section>
        <!-------right container ends------>
        <!---------right container ends------>
        {{-- <section class="right-sect">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- ChildcareJob All Pages Adlinks -->
            <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="1153705179" data-ad-format="link"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            <div id="fb-root"></div>
            <script>
                (function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {
                        return;
                    }
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            </script>

            <div class="fb-like-box" data-href="https://www.facebook.com/pages/ChildcareJoborg/100655986709828"
                data-width="250" data-show-faces="true" data-stream="false" data-header="true"></div>
            <br />
            <div class="clear"></div>
        </section> --}}
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
