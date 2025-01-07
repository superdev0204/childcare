<?php
    $description = substr(strip_tags($classified->detail), 0, 300);
    $description = substr($description, 0, strrpos($description, ' '));
?>

@push('meta')
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $classified->city }} {{ $classified->state }} child care jobs, resume">
@endpush

@push('title')
    <title>{{ $classified->summary }} - {{ $classified->city }}, {{ $classified->state }}</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/classifieds">Child Care Classifieds </a> &gt;&gt; </li>
                <li><a href="/<?php echo $state->statefile; ?>_classifieds"><?php echo $state->state_name; ?> Classifieds</a> &gt;&gt; </li>
                <li>Ad Details</li>
            </ul>
        </div>
        <!---------right container------>
        <section class="right-sect">
            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>
            <div class="listSidebar">
                <h3>Quick Links</h3>
                <div class="quick-links">
                    <a href="/classifieds/newad">Post New Classified Ad</a>
                    <a href="/provider">Add New Daycare Listing</a>
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

        </section>
        <!-------right container ends------>

        <!---------left container------>
        <section class="left-sect head">

            <?php if($classified->name == ""):?>
            <h1><?php echo $classified->summary; ?> </h1>
            <?php else:?>
            <h1><?php echo $classified->summary . ' at ' . $classified->name; ?> </h1>
            <?php endif;?>

            <div class="section-body">
                <ul class="provider-main-features">
                    <li>
                        <span>Ad Summary:</span>
                        <span><?php echo $classified->summary; ?></span>
                    </li>
                    <?php if($classified->name <> ""):?>
                    <li>
                        <span><strong>Contact Name</strong></span>
                        <span><?php echo $classified->name; ?></span>
                    </li>
                    <?php endif; ?>
                    <li>
                        <span><strong>Location</strong></span>
                        <span><?php echo $classified->city . ', ' . $classified->state . ' ' . $classified->zip; ?></span>
                    </li>
                    <li>
                        <span><strong>Contact Phone</strong></span>
                        <span><?php echo $classified->phone; ?></span>
                    </li>
                    <?php if ($classified->pricing <> ""):?>
                    <li>
                        <span><strong>Pricing</strong></span>
                        <span><?php echo $classified->pricing; ?></span>
                    </li>
                    <?php endif; ?>
                    <li>
                        <!-- Ezoic - CCC MOINSBD InArticle - mid_content -->
                        <div id="ezoic-pub-ad-placeholder-101">
                            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                            <ins class="adsbygoogle" style="display:block; text-align:center;" data-ad-layout="in-article"
                                data-ad-format="fluid" data-ad-client="ca-pub-8651736830870146"
                                data-ad-slot="6581108420"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                        <!-- End Ezoic - CCC MOINSBD InArticle - mid_content-->
                    </li>
                    <li>
                        <strong>Ad Detail</strong>: <?php echo $classified->detail; ?><br />

                    </li>
                    <li>
                        <strong>Additional Information</strong>: <?php echo $classified->additionalinfo; ?><br />
                    </li>
                </ul>
            </div>
        </section>
    </div>
@endsection
