@push('meta')
    <meta name="description" content="{{ $state->state_name }} child care resume and child care professional listings.">
    <meta name="keywords" content="{{ $state->state_name }} child care resume">
@endpush

@push('title')
    <title>Child Care Resumes in {{ $state->state_name }} | {{ $state->state_name }} Childcare Professionals</title>
@endpush

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> >> </li>
                <li><a href="/resumes">Child Care Resumes</a> >> </li>
                <li><?php echo $state->state_name; ?> Resumes</li>
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
            <?php if(count($resumes)>=1):?>
            <h2>Current list of resume available in <?php echo $state->state_name; ?>:</h2>
            <div class="up-section head">
                <?php
                $i=0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($resumes as $resume): $i++;?>
                <div class="update">
                    <h3>
                        <a href="/resumes/detail?id=<?php echo $resume->id; ?>"><?php echo $resume->name; ?></a><br />
                    </h3>

                    <span>
                        <?php echo $resume->phone . ' <br/> ' . $resume->city . ', ' . $resume->state . ' ' . $resume->zip; ?>
                    </span>

                    <p>
                        <strong>Position Applying for:</strong><?php echo $resume->position; ?><br />
                        <strong>Objective:</strong> <?php echo $resume->objective; ?>
                    </p>
                </div>
                <?php if($i==2):?>
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- ChildcareJob State Resume -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="3265306779" data-ad-format="auto"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
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
                    <a href="/jobs" title="Find Child Care Jobs">Find Jobs</a>
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
        </section> --}}
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
