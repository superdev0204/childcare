@push('meta')
    <meta name="description" content="{{ $state->state_name }} child care jobs and resume listing.">
    <meta name="keywords" content="{{ $state->state_name . $state->state_name }} child care jobs, child care resume">
@endpush

@push('title')
    <title>Childcare Classifieds in {{ $state->state_name }} | {{ $state->state_name }} Classified Ads</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/classifieds">Child Care Classifieds</a> &gt;&gt; </li>
                <li><?php echo $state->state_name; ?> Ads</li>
            </ul>
        </div>
        <!---------right container------>
        <section class="right-sect">

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
            <h1>Current list of classified ads available in <?php echo $state->state_name; ?></h1>

            <?php if(count($classifieds)):?>
            <div class="up-section head">

                <?php
                $i=0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($classifieds as $classified): $i++;?>
                <div class="update">
                    <h3>
                        <a href="/classifieds/addetails?id=<?php echo $classified->id; ?>"><?php echo htmlentities($classified->summary); ?></a>
                    </h3>

                    <span>
                        <?php echo ucwords(strtolower($classified->city)) . ' ' . $classified->state . ' | ' . $classified->phone; ?>
                    </span>

                    <p>
                        <?php
                        $description = strip_tags($classified->detail);
                        if (strlen($description) > 260) {
                            $description = substr($description, 0, 250) . ' ...';
                        }
                        echo $description;
                        ?>
                        <?php if ($i == 4 || $i == 10 || $i == 20):?>
                        <br />
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
                    <?php endif;?>
                    </p>
                </div>
                <?php endforeach;?>
            </div>

            <?php else:?>
            <p>There's currently no classified ads available in our database yet. To place an ad now, <a
                    href="/classifieds/newad">Click Here</a>. </p>
            <?php endif;?><br />

            <p>We are in the process of collecting classified ads. Please be patient as we're improving our system to serve
                you better. </p>
        </section>

    </div>
@endsection
