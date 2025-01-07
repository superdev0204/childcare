@push('meta')
    <meta name="description" content="{{ strip_tags($resume->objective) }}">
    <meta name="keywords" content="{{ $resume->city }} {{ $resume->state }} child care jobs, resume">
@endpush

@push('title')
    <title>{{ $resume->name }}, a {{ $resume->position }} in {{ $resume->city }}, {{ $resume->state }} {{ $resume->zip }}
    </title>
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
                <li><a href="/state">Child Care Resumes </a> >> </li>
                <?php if ($state): ?><li><a href="/<?php echo $state->statefile; ?>_resumes"><?php echo $state->state_name; ?> Resumes</a> >> </li>
                <?php endif ?>
                <li>Resume Detail for <?php echo $resume->name; ?></li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect">

            <h1 class="page-title">
                <?php echo $resume->name . ', a ' . $resume->position . ' in ' . $resume->city . ', ' . $resume->state . ' ' . $resume->zip; ?>
            </h1>
            <div class="clear"></div>
            <div class="section-body">
                <ul class="provider-main-features">
                    <li>
                        <span><strong>Contact Phone</strong></span>
                        <span><?php echo $resume->phone; ?></span>
                    </li>
                    <li>
                        <span><strong>Objective</strong></span>
                        <span><?php echo $resume->objective; ?></span>
                    </li>
                    <li>
                        <span><strong>Experience</strong></span>
                        <span><?php echo $resume->experience; ?></span>
                    </li>
                    <li>
                        <span><strong>Rate Range</strong></span>
                        <span><?php echo $resume->rate_range; ?></span>
                    </li>
                    <li>
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- ChildcareJob Resume Detail -->
                        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                            data-ad-slot="4742039972" data-ad-format="auto"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </li>
                    <?php if ($resume->skillscertification<>""):?>
                    <li>
                        <span><strong>Skills and Certification</strong></span>
                        <span><?php echo $resume->skillscertification; ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($resume->school<>""):?>
                    <li>
                        <span><strong>School</strong></span>
                        <span><?php echo $resume->school; ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($resume->major <>""):?>
                    <li>
                        <span><strong>Major</strong></span>
                        <span><?php echo $resume->major; ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($resume->additionalinfo <>""):?>
                    <li>
                        <span><strong>Major</strong></span>
                        <span><?php echo $resume->additionalinfo; ?></span>
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
                    <a href="/jobs" title="Find Child Care Jobs">Find Jobs</a>
                    <a href="/jobs/new" title="Post Child Care Job">Post Job</a>
                    <a href="/resumes/new" title="Post Child Care Resume">Post Resume</a>
                </div>
            </div>

        </section>
        <!-------right container ends------>
        <!---------right container ends------>
        {{-- <section class="right-sect">
            <!-- / end# postresunme -->
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
