@push('meta')
    <meta name="description" content="{{ substr(strip_tags($job->description), 0, 160) }}">
    <meta name="keywords" content="{{ $job->city }} {{ $job->state }} child care jobs, resume">
@endpush

@push('title')
    <title>Job Detail for {{ $job->title }} in {{ $job->city }}, {{ $job->state }} {{ $job->zip }}</title>
@endpush

@extends('layouts.app')

@section('content')
    <div class="container">
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

        <!-- End Ezoic - CCC MOINSBD HEADER - top_of_page -->
        <div class="breadcrumbs">
            <ul>
                <li><a href="/state">Child Care Jobs </a> >> </li>
                <?php if ($state): ?><li><a href="/<?php echo $state->statefile; ?>_jobs"><?php echo $state->state_name; ?> Jobs</a> >> </li>
                <?php endif ?>
                <li>Job Detail</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect">

            <h1 class="page-title">
                <?php if($job->company == ""):?>
                <?php echo $job->title; ?>
                <?php else:?>
                <?php echo $job->title . ' at ' . $job->company; ?>
                <?php endif;?>
            </h1>
            <div class="clear"></div>
            <div class="section-body">
                <ul class="provider-main-features">
                    <?php if($job->company<>""):?>
                    <li>
                        <span><strong>Company Name</strong></span>
                        <span><?php echo $job->company; ?></span>
                    </li>
                    <?php endif; ?>
                    <li>
                        <span><strong>Contact Information </strong></span>
                        <span>
                            <?php if($job->applyURL):?>
                            <a target="blank" href="<?php echo $job->applyURL; ?>">Apply Now</a>
                            <?php else: ?>
                            <?php echo $job->phone; ?>
                            <?php endif;?>
                        </span>
                    </li>
                    <?php if ($job->education<>""):?>
                    <li>
                        <span><strong>Required Education</strong></span>
                        <span><?php echo $job->education; ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($job->experienceRequired <> ""):?>
                    <li>
                        <span><strong>Required Experience </strong></span>
                        <span><?php echo $job->experienceRequired; ?></span>
                    </li>
                    <?php endif;?>
                    <?php if ($job->rate_range <> "" && $job->rate_range <> "N/A"):?>
                    <li>
                        <span><strong>Rate Range</strong></span>
                        <span><?php echo $job->rate_range; ?></span>
                    </li>
                    <?php endif; ?>
                    <li>
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- ChildcareJob Detail -->
                        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                            data-ad-slot="9010789175" data-ad-format="auto"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </li>
                    <li>
                        <span><strong>Job Description</strong></span>
                        <span><?php echo $job->description; ?></span>
                    </li>
                    <li>
                        <span></span>
                        <span>
                            <?php if (rand(0,1) == 0): ?>
                            <div id="adcontainer1"></div>
                            <script src="https://www.google.com/adsense/search/ads.js" type="text/javascript"></script>
                            <script type="text/javascript" charset="utf-8">
                                var pageOptions = {
                                    'pubId': 'pub-8651736830870146',
                                    'query': '<?php $randomString = ['preschool job', 'teaching jobs', 'teacher aid', 'child development school'];
                                    echo $randomString[rand(0, 3)]; ?>',
                                    'channel': '3735647977',
                                    'hl': 'en'
                                };
    
                                var adblock1 = {
                                    'container': 'adcontainer1',
                                    'width': '430px',
                                    'colorTitleLink': '215C97',
                                    'colorDomainLink': 'db6b00'
                                };
    
                                new google.ads.search.Ads(pageOptions, adblock1);
                            </script>
                            <?php else: ?>
                            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                            <ins class="adsbygoogle" style="display:inline-block;width:468px;height:60px"
                                data-ad-client="ca-pub-8651736830870146" data-ad-slot="6624481538"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                            <?php endif;?>
                        </span>
                    </li>
                    <li>
                        <span><strong>Job Requirements</strong></span>
                        <span><?php echo $job->requirements; ?></span>
                    </li>
                    <?php if ($job->applyURL<>""):?>
                    <li>
                        <span><strong>Application </strong></span>
                        <span><a target="blank" href="<?php echo $job->applyURL; ?>">Click Here</a></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($job->howtoapply<>""):?>
                    <li>
                        <span><strong>How to Apply </strong></span>
                        <span><?php echo $job->howtoapply; ?></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- / end# content-box  -->
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
            <div class="clear"></div>
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- ChildcareJob All Pages Adlinks -->
            <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="1153705179" data-ad-format="link"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </section> --}}
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
