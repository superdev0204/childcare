@push('meta')
    <meta name="description"
        content="{{ ucwords(strtolower($city->city)) }} {{ $city->state }} child care jobs and resume listing.">
    <meta name="keywords"
        content="{{ ucwords(strtolower($city->city)) }} {{ $city->state }} child care jobs, child care resume">
@endpush

@push('title')
    <title>Child Care jobs in {{ ucwords(strtolower($city->city)) }} {{ $city->state }} |
        {{ ucwords(strtolower($city->city)) }} {{ $city->state }} Child Care job seekers</title>
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
                <li><a href="/jobs">Child Care Jobs</a> >> </li>
                <li><a href="/<?php echo $city->statefile; ?>_jobs"><?php echo $state->state_name; ?> Jobs</a> &gt;&gt; </li>
                <li><?php echo ucwords(strtolower($city->city)); ?> Jobs</li>
            </ul>
        </div>
        <!---------left container------>

        <section class="left-sect">
            <h1>Childcare Jobs in <?php echo ucwords(strtolower($city->city)); ?>,<?php echo ucwords(strtolower($city->state)); ?> </h1>
            <div class="clear"></div>
            <p>We are in the process of building our child care jobs database to help find jobs and employee easier and
                cheaper. Please be patient as we're improving our system to serve you better. </p>

            <?php if(count($jobs)>0):?>
            <h2>Current list of child care jobs available in <?php echo ucwords(strtolower($city->city)); ?>:</h2>
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
                        <?php $i++; if($i==2 && count($jobs)>=4): ?>

                            <div id="adcontainer1"></div>
                            <script src="https://www.google.com/adsense/search/ads.js" type="text/javascript"></script>
                            <script type="text/javascript" charset="utf-8">
                                var pageOptions = {
                                    'pubId': 'pub-8651736830870146',
                                    'query': '<?php $randomString = ['preschool job', 'teaching jobs', 'teacher aid', 'child development school', 'local jobs'];
                                    echo $randomString[rand(0, 4)]; ?>',
                                    'channel': '5212381170',
                                    'hl': 'en'
                                };

                                var adblock1 = {
                                    'container': 'adcontainer1',
                                    'width': '440px',
                                    'colorTitleLink': '215C97',
                                    'colorDomainLink': 'DB6B00'
                                };

                                new google.ads.search.Ads(pageOptions, adblock1);
                            </script>

                        <?php endif; ?>
                    </p>
                </div>
                <?php endforeach;?>
                <div class="update">
                    <div id="adcontainer1"></div>
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <ins class="adsbygoogle"
                         style="display:inline-block;width:468px;height:60px"
                         data-ad-client="ca-pub-8651736830870146"
                         data-ad-slot="6624481538"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            </div>
            <?php else:?>
                <p>There's currently no child care jobs available in our database yet.  However, you may want to post your resume <a href="/resumes/new">here</a>,
                    and employers will give you a call if they see your skill sets meet their needs. </p>
                <script type="text/javascript"><!--
                    google_ad_client = "pub-8651736830870146";
                    /* 336x280, created 9/7/10 */
                    google_ad_slot = "8304269234";
                    google_ad_width = 336;
                    google_ad_height = 280;
                    //-->
                </script>
                <script type="text/javascript"
                        src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
                </script>
                <?php if(isset($centers) && count($centers)>=1):?>
                    <table width="100%" class="widget">
                        <tr>
                            <td colspan="2"><h2>Current list of child care centers in <?php echo ucwords(strtolower($city->city))?>:</h2></td>
                        </tr>
                        <?php $i = 0;
                        /** @var \Application\Domain\Entity\Facility $center */
                        foreach ($centers as $center): ?>
                            <tr>
                                <td width="35%" valign="top">
                                    <strong><?php echo $center->name ?></strong><br/>
                                    <?php echo $center->address . " <br/> " . $center->city . ", " . $center->state ?><br/>
                                </td>
                                <td valign="top">
                                    <strong>Phone:</strong><?php echo $center->phone?><br/>
                                    <strong>Website:</strong> <?php echo $center->website?>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                <?php endif;?>

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
            <ins class="adsbygoogle"
                style="display:block"
                data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="1153705179"
                data-ad-format="link"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            <div id="fb-root"></div>
            <script>(function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {return;}
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>

            <div class="fb-like-box" data-href="https://www.facebook.com/pages/ChildcareJoborg/100655986709828" data-width="250" data-show-faces="true" data-stream="false" data-header="true"></div><br/>
            <div class="clear"></div>
        </section> --}}
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
