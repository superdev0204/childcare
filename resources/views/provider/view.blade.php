<?php
    $stripName = preg_replace('/[\W]/', '', $provider->name);
    if (ctype_upper($stripName)) {
        $providerName = ucwords(strtolower($provider->name));
    } else {
        $providerName = $provider->name;
    }
    $type = 'Child Care Center';
    if (strlen($provider->type) > 10) {
        $type = $provider->type;
    } elseif (!$provider->is_center) {
        $type = 'Home Daycare';
    }

    if (strlen($provider->type) >= 10) {
        $description = $provider->name . ' is a ' . $provider->type . ' in ' . $provider->city . ' ' . $provider->state . '. ';
    } else {
        if ($provider->is_center) {
            $description = $provider->name . ' is a Childcare Center in ' . $provider->city . ' ' . $provider->state . '. ';
        } else {
            $description = $provider->name . ' is a Family Child Care in ' . $provider->city . ' ' . $provider->state . '. ';
        }
    }

    if ($provider->capacity > 0) {
        $description .= 'It has maximum capacity of ' . $provider->capacity . ' children. ';
    }

    if ($provider->age_range != '') {
        $description .= ' The provider accepts children ages of: ' . $provider->age_range . '. ';
    }

    if ($provider->subsidized == 1) {
        $description .= 'The child care may also participate in the subsidized program. ';
    }

    if ($provider->operation_id != '' && $provider->operation_id != 'N/A') {
        $description .= 'The license number is: ' . $provider->operation_id . '. ';
    } else {
        $description .= 'It is located on ' . $provider->address . '. ';
    }
?>

@push('meta')
    <meta name="description" content="{{ htmlentities($description) }}">
    <meta name="keywords" content="{{ $type . ', ' . $provider->name . ', ' . $provider->city . ' ' . $provider->state }}">
@endpush

@push('link')
    <link rel="stylesheet" href="{{ asset('lightgallery/css/lightgallery.min.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
    <link rel="canonical" href="https://childcarecenter.us/provider_detail/{{ $provider->filename }}">
@endpush

@if (strlen($provider->name) <= 40)
    @if (strlen($type) > 20)
        @push('title')
            <title>{{ $providerName }} | {{ $provider->city }} {{ $provider->state }}</title>
        @endpush
    @else
        @push('title')
            <title>{{ $providerName . ' | ' . $provider->city . ' ' . $provider->state . ' ' . $type }}</title>
        @endpush
    @endif
@elseif(strlen($provider->name) < 55)
    @push('title')
        <title>{{ $providerName . ' | ' . $provider->city . ' ' . $provider->state }}</title>
    @endpush
@else
    @push('title')
        <title>{{ $providerName }}</title>
    @endpush
@endif

@extends('layouts.app')

@section('content')
    <script src="{{ asset('lightgallery/js/lightgallery.min.js') }}"></script>
    {{-- $this->headScript()->setAllowArbitraryAttributes(true); --}}

    <div class="header-bg">
        <div class="provider-header-detail">
            <div class="container">
                <div class="breadcrumbs">
                    <ul>
                        <?php if($state): ?>
                        <?php if($provider->is_center):?>
                        <?php if ($state->nextlevel != "DETAIL"):?>
                        <?php if ($county && !empty($county->county_file)): ?>
                        <li><a href="<?php echo route('centercare_county', ['countyname' => $county->county_file]); ?>"><?php echo ucwords(strtolower($county->county)); ?> County</a> &gt;&gt; </li>
                        <?php endif;?>
                        <?php if ($provider->cityfile):?>
                        <li><a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $provider->cityfile]); ?>"><?php echo ucwords(strtolower($provider->city)); ?> Child Care</a> &gt;&gt; </li>
                        <?php endif;?>
                        <?php if ($provider->zip):?>
                        <li><a href="<?php echo route('centercare_zipcode', ['state' => $state->statefile, 'zipcode' => $provider->zip]); ?>"><?php echo $provider->zip; ?> Child Care Centers</a> &gt;&gt; </li>
                        <?php endif;?>
                        <?php else :?>
                        <li><a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> Childcare Centers</a> &gt;&gt; </li>
                        <?php endif; ?>
                        <?php else: ?>
                        <?php if ($state->nextlevel != "DETAIL"):?>
                        <?php if ($county):?>
                        <li><a href="<?php echo route('homecare_county', ['state' => $state->statefile, 'countyname' => $county->county_file]); ?>"><?php echo ucwords(strtolower($county->county)); ?> County</a> &gt;&gt; </li>
                        <?php endif;?>
                        <?php if ($provider->cityfile):?>
                        <li><a href="<?php echo route('homecare_city', ['state' => $state->statefile, 'city' => $provider->cityfile]); ?>"><?php echo ucwords(strtolower($provider->city)); ?> Day Care</a> &gt;&gt; </li>
                        <?php endif;?>
                        <?php if ($provider->zip):?>
                        <li><a href="<?php echo route('homecare_zip', ['state' => $state->statefile, 'zipcode' => $provider->zip]); ?>"><?php echo $provider->zip; ?> Home Daycare</a> &gt;&gt; </li>
                        <?php endif;?>
                        <?php else:?>
                        <li><a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> Home Daycare</a>
                            &gt;&gt;<?php ?>
                            <?php endif;?>
                            <?php endif;?>
                        <?php endif;?>
                        <li><?php echo $providerName; ?></li>
                    </ul>
                </div>
                <div class="title-pane">
                    <h1 class="provider-title"><?php echo $providerName; ?> -
                        <?php echo ucwords(strtolower($provider->city)) . ' ' . $provider->state; ?>
                        <?php if(strlen($provider->type) > 10):?>
                        <?php echo $provider->type; ?>
                        <?php elseif ($provider->is_center): ?>
                        <?php echo 'Child Care Center '; ?>
                        <?php else:?>
                        <?php echo 'In-Home Daycare'; ?>
                        <?php endif;?>
                    </h1>

                    <?php if($provider->approved == 0): ?>
                    <h2>This provider has not been approved yet.</h2>
                    <?php elseif ($provider->approved < 0): ?>
                    <?php if ($provider->status) : ?>
                    <h2>Provider Status: <?php echo $provider->status; ?>.</h2>
                    <?php elseif ($provider->approved == "-2"): ?>
                    <h2>Provider Status: Provider Is NOT Approved.</h2>
                    <?php endif; ?>
                    <?php endif;?>

                    <div class="title-address">
                        <i class="zmdi zmdi-pin"></i> <?php echo $provider->address . ' ' . $provider->address2 . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br />
                        <i class="zmdi zmdi-phone" aria-hidden="true"></i>
                        <?php
                        if ($provider->approved >= 0):
                            echo $provider->formatPhone;
                        else:
                            echo $provider->maskPhone;
                        endif;
                        ?>

                    </div>
                    <?php if ($provider->avg_rating <> '') : ?>
                    <div class="title-rating">
                        <?php for ($i=0;  $i < 5; $i++):?>
                        <?php if ($provider->avg_rating - $i >0.5):?>
                        <i class="zmdi zmdi-star star"></i>
                        <?php elseif ($provider->avg_rating - $i == 0.5):?>
                        <i class="zmdi zmdi-star-half star"></i>
                        <?php else: ?>
                        <i class="zmdi zmdi-star-outline star"></i>
                        <?php endif; ?>
                        <?php endfor;?>

                        <?php echo count($reviews);
                        echo count($reviews) == 1 ? ' Review' : ' Reviews'; ?>
                    </div>
                    <?php endif; ?>
                    <img class="provider-logo" src="<?php echo $provider->logo; ?>" alt="<?php echo $providerName; ?>" />
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (!$provider->is_featured): ?>
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
        <?php endif;?>
        <div class="provider-details">
            <!---------left container------>
            <section class="left-sect head">
                <div>
                    <div class="clearfix"></div>

                    <div class="hidden-sm">
                        <a href="#review-section" class="btn btn-review">Write a Review</a>
                    </div>

                    <div class="section-body">
                        <h2>About the Provider</h2>
                        <p><strong>Description</strong>: <?php echo $provider->introduction; ?></p>
                        <?php if (!$provider->is_featured):?>
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
                        <?php endif;?>
                        <?php if($provider->additionalInfo != "") : ?>
                        <strong>Additional Information</strong>: <?php echo $provider->additionalInfo; ?><br />
                        <?php endif; ?>
                    </div>

                    <?php if(count($images)) : ?>
                    <strong><?php echo $providerName; ?> Photos:</strong> (Click to enlarge)<br />
                    <div id="lightgallery" class="img-gallery row">
                        <?php
                            /** @var \Application\Domain\Entity\Image $image */
                            foreach ($images as $image): ?>
                        <?php if ($image->imagename <> "") :?>
                        <?php if (strtolower(pathinfo(parse_url(env('IDRIVE_BITBUCKET_URL') . '/' . $image->imagename, PHP_URL_PATH) , PATHINFO_EXTENSION)) === 'pdf'):?>
                        <a target="_blank" href="<?php echo env('IDRIVE_BITBUCKET_URL') . '/' . $image->imagename; ?>" class="col-xs-6 col-sm-4"><img
                                src="https://cdn.pixabay.com/photo/2012/05/04/10/55/pdf-47199_960_720.png" /></a>
                        <?php else: ?>
                        <a href="<?php echo env('IDRIVE_BITBUCKET_URL') . '/' . $image->imagename; ?>" class="col-xs-6 col-sm-4 img-provider">
                            <img src="<?php echo env('IDRIVE_BITBUCKET_URL') . '/' . $image->imagename; ?>" alt="<?php if ($image->altname != ''):
                                echo $image->altname;
                            else:
                                echo $providerName;
                            endif; ?>" />
                        </a>
                        <?php endif ?>
                        <?php else: ?>
                        <?php if (pathinfo($image->image_url, PATHINFO_EXTENSION) == 'pdf'):  ?>
                        <a target="_blank" href="<?php echo env('IDRIVE_BITBUCKET_URL') . '/' . $image->imagename; ?>" class="col-xs-6 col-sm-4"><img
                                src="https://cdn.pixabay.com/photo/2012/05/04/10/55/pdf-47199_960_720.png" /></a>
                        <?php else: ?>
                        <a href="<?php echo $image->image_url; ?>" class="col-xs-6 col-sm-4 img-provider">
                            <img src="<?php echo $image->image_url; ?>" alt="<?php if ($image->altname != ''):
                                echo $image->altname;
                            else:
                                echo $providerName;
                            endif; ?>" />
                        </a>
                        <?php endif ?>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif;?>
                    <div class="hidden-sm">
                        <div class="listSidebar">
                            <h3>Contact</h3>
                            <div class="contactInfo">
                                <ul class="list-unstyled list-address">
                                    <li>
                                        <i class="zmdi zmdi-pin" aria-hidden="true"></i>
                                        <?php echo $provider->address . ', ' . $provider->address . '<br/>' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?>
                                    </li>
                                    <li>
                                        <i class="zmdi zmdi-phone" aria-hidden="true"></i>
                                        <?php if ($provider->approved >= 0):
                                            echo $provider->formatPhone;
                                        else:
                                            echo $provider->maskPhone;
                                        endif; ?>
                                    </li>
                                    <?php if($provider->email <> "" && $provider->user_id > 0):?>
                                    <li>
                                        <i class="zmdi zmdi-email" aria-hidden="true"></i>
                                        <?php if($user):?>
                                        <a href="mailto:<?php echo $provider->email; ?>"><?php echo $provider->email; ?></a>
                                        <?php else:?>
                                        <a href="/user/login?url=<?php echo $_SERVER['REQUEST_URI']; ?>">Login</a> or
                                        <a href="/user/new">Register</a> to email this provider.
                                        <?php endif; ?>

                                    </li>
                                    <?php endif;?>
                                    <?php if($provider->website != "") : ?>
                                    <li>
                                        <i class="zmdi zmdi-globe-alt" aria-hidden="true"></i>
                                        <?php echo $provider->website; ?>
                                    </li>
                                    <?php endif;?>
                                </ul>
                            </div>
                        </div>

                        <div class="listSidebar">
                            <h3>Operation Hours</h3>
                            <ul class="list-unstyled sidebarList condensed">
                                <?php if ($provider->operationHours):?>
                                <li>
                                    <span class="pull-left">Monday</span>
                                    <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->monday; ?></span>
                                </li>
                                <li>
                                    <span class="pull-left">Tuesday</span>
                                    <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->tuesday; ?></span>
                                </li>
                                <li>
                                    <span class="pull-left">Wednesday</span>
                                    <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->wednesday; ?></span>
                                </li>
                                <li>
                                    <span class="pull-left">Thursday</span>
                                    <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->thursday; ?></span>
                                </li>
                                <li>
                                    <span class="pull-left">Friday</span>
                                    <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->friday; ?></span>
                                </li>
                                <li>
                                    <span class="pull-left">Saturday</span>
                                    <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->saturday != '' ? optional(optional($provider)->operationHours)->saturday : 'Closed'; ?></span>
                                </li>
                                <li>
                                    <span class="pull-left">Sunday</span>
                                    <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->sunday != '' ? optional(optional($provider)->operationHours)->sunday : 'Closed'; ?></span>
                                </li>
                                <?php else: ?>
                                <li>
                                    <span class="pull-left">Days of Operation</span>
                                    <?php if($provider->daysopen != "") : ?>
                                    <span class="pull-right"><?php echo $provider->daysopen; ?></span>
                                    <?php else: ?>
                                    <span class="pull-right">Monday-Friday</span>
                                    <?php endif; ?>
                                </li>
                                <?php if($provider->hoursopen != "") : ?>
                                <li>
                                    <span class="pull-left">Normal Open Hours</span>
                                    <span class="pull-right"><?php echo $provider->hoursopen; ?></span>
                                </li>
                                <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
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
                        <!-- End Ezoic - CCC MOINSBD Link Sidebar - link_side --><br />
                    </div>

                    <div class="section-body">
                        <h2>Program and Licensing Details</h2>
                        <ul class="provider-main-features">
                            <?php if($provider->operation_id != "") : ?>
                            <li>
                                <span>License Number:</span>
                                <span><?php echo $provider->operation_id; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->capacity > 0) : ?>
                            <li>
                                <span>Capacity:</span>
                                <span><?php echo $provider->capacity; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->age_range != "") : ?>
                            <li class="full-width">
                                <span>Age Range:</span>
                                <span><?php echo $provider->age_range; ?> </span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->accreditation != "") : ?>
                            <li>
                                <span>Achievement and/or Accreditations</span>
                                <span><?php echo $provider->accreditation; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->pricing != "") : ?>
                            <li>
                                <span>Rate Range</span>
                                <span><?php echo $provider->pricing; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->state_rating) : ?>
                            <li>
                                <span><?php echo $provider->state_rating_text; ?>:</span>
                                <span><?php echo $provider->state_rating; ?></span>
                            </li>
                            <?php endif; ?>
                            <li>
                                <span>Enrolled in Subsidized Child Care Program:</span>
                                <span><?php echo $provider->subsidized == 1 ? 'Yes' : 'No'; ?></span>
                            </li>
                            <?php if($provider->language != "") : ?>
                            <li>
                                <span>Languages Supported:</span>
                                <span>English, <?php echo $provider->language; ?> </span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->typeofcare != "") : ?>
                            <li>
                                <span>Type of Care:</span>
                                <span><?php echo $provider->typeofcare; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->transportation != "") : ?>
                            <li>
                                <span>Transportation:</span>
                                <span><?php echo $provider->transportation; ?></span>
                            </li>
                            <?php endif	?>
                            <?php if($provider->schools_served != "") : ?>
                            <li>
                                <span>Schools Served:</span>
                                <span><?php echo $provider->schools_served; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (optional(optional($provider)->detail)->initial_application_date) : ?>
                            <li>
                                <span>Initial License Issue Date:</span>
                                <span><?php echo optional(optional($provider)->detail)->initial_application_date; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (optional(optional($provider)->detail)->current_license_begin_date) : ?>
                            <li>
                                <span>Current License Issue Date:</span>
                                <span><?php echo optional(optional($provider)->detail)->current_license_begin_date; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (optional(optional($provider)->detail)->current_license_expiration_date && strtotime(optional(optional($provider)->detail)->current_license_expiration_date) > time()) : ?>
                            <li>
                                <span>Current License Expiration Date:</span>
                                <span><?php echo optional(optional($provider)->detail)->current_license_expiration_date; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->district_office != "") : ?>
                            <li>
                                <span>District Office:</span>
                                <span><?php echo $provider->district_office; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->do_phone != "") : ?>
                            <li>
                                <span>District Office Phone:</span>
                                <span><?php echo $provider->do_phone; ?> (Note: This is not the facility phone number.) </span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->licensor != "") : ?>
                            <li>
                                <span>Licensor:</span>
                                <span><?php echo $provider->licensor; ?> </span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="section-body">
                        <h2>Location Map</h2>

                        <div class="row">
                            <?php if (!$provider->is_featured): ?>
                            <div class="col-xs-12 col-sm-6">
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
                            <div class="col-xs-12 col-sm-6">
                                <div class="google-maps">
                                    <iframe width="330" height="280" frameborder="0" style="border:0"
                                        src="https://www.google.com/maps/embed/v1/place?key=<?php echo env('GOOGLE_API_KEY'); ?>&q=<?php echo urlencode($provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip); ?>"></iframe>
                                </div>
                            </div>
                            <?php else:?>
                            <div class="col-xs-12">
                                <div class="google-maps">
                                    <iframe width="600" height="450" frameborder="0" style="border:0"
                                        src="https://www.google.com/maps/embed/v1/place?key=<?php echo env('GOOGLE_API_KEY'); ?>&q=<?php echo urlencode($provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip); ?>"></iframe>
                                </div>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>

                    <?php if (count($inspections)) : ?>
                    <div class="section-body">
                        <h2>Inspection/Report History</h2>
                        <p>Where possible, ChildcareCenter provides inspection reports as a service to families. This
                            information is deemed reliable,
                            but is not guaranteed. We encourage families to contact the daycare provider directly with any
                            questions or concerns,
                            as the provider may have already addressed some or all issues. Reports can also be verified with
                            your local daycare licensing office.</p>
                        <div class="inspections">
                            <?php
                            $header1 = '';
                            $header2 = '';
                            $header3 = '';
                            $header4 = '';
                            $header5 = '';
                            $header6 = '';
                            $secondrow = '';
                            $colspan = 1;
                            if (preg_match('/(FL|RI)/', $provider->state)) {
                                $header1 = 'Report Date';
                            } elseif (preg_match('/(WA|MO|IA)/', $provider->state)) {
                                $header1 = 'Report Date';
                                $header2 = 'Report Type';
                            } elseif (preg_match('/(LA)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Notes';
                            } elseif (preg_match('/(CA)/', $provider->state)) {
                                $header1 = 'Type';
                                $header2 = 'Inspection Dates';
                                $header3 = 'Reports/Citations';
                            } elseif (preg_match('/(GA)/', $provider->state)) {
                                $header1 = 'Report Date';
                                $header2 = 'Arrival Time';
                                $header3 = 'Report Type';
                            } elseif (preg_match('/(WY)/', $provider->state)) {
                                $header1 = 'Visit Date';
                                $header2 = '# of Violations';
                                $header3 = 'Compliance Notices';
                                $secondrow = 'Violation Description';
                            } elseif (preg_match('/(AR|NH)/', $provider->state)) {
                                $header1 = 'Visit/Complaint Date';
                                $header2 = 'Type';
                                $header3 = 'Details';
                            } elseif (preg_match('/(TN)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Violation Corrected';
                                $header3 = 'Violation Narrative';
                            } elseif (preg_match('/(KY)/', $provider->state)) {
                                $header1 = 'Inspection Date';
                                $header2 = 'Inspection Type';
                                $header3 = 'Start Time - End time';
                            } elseif (preg_match('/(NE)/', $provider->state)) {
                                $header1 = 'Start Date';
                                $header2 = 'End Date';
                                $header3 = 'Disciplinary/Non-Disciplinary Action';
                            } elseif (preg_match('/(MT|NM)/', $provider->state)) {
                                $header1 = 'Inspection Date';
                                $header2 = 'Inspection Type';
                                $header3 = 'Inspection Time';
                                $header4 = 'Inspector';
                            } elseif (preg_match('/(SD)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Inspection Type';
                                $header3 = 'Inspection Form';
                                $header4 = 'Inspection Results';
                            } elseif (preg_match('/(WI)/i', $provider->state)) {
                                $header1 = 'Violation Date';
                                $header2 = 'Rule Number';
                                $header3 = 'Rule Summary';
                                $secondrow = 'Description';
                            } elseif (preg_match('/(DE)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Type';
                                $header3 = 'Regulation Code';
                                $header4 = 'Status';
                                $secondrow = 'Corrective Action';
                            } elseif (preg_match('/(IN)/', $provider->state)) {
                                $header1 = 'Inspection Type/Date';
                                $header2 = 'Action needed to correct issue';
                                $header3 = 'Date Resolved';
                                $secondrow = 'Type of correction needed';
                            } elseif (preg_match('/(OK)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Type/Purpose';
                                $header3 = 'Corrections';
                                $secondrow = 'Description';
                            } elseif (preg_match('/(UT)/', $provider->state)) {
                                $header1 = 'Inspection Date';
                                $header2 = 'Inspection Type';
                                $header3 = 'Corrections';
                                $secondrow = 'Finds';
                            } elseif (preg_match('/(TX)/', $provider->state)) {
                                $header1 = 'Inspections';
                                $header2 = 'Assessments';
                                $header3 = 'Self Reported Incidents';
                                $header4 = 'Reports';
                            } elseif (preg_match('/(AL)/', $provider->state)) {
                                $header1 = 'Type/Action';
                                $header2 = 'Description';
                                $header3 = 'Date';
                            } elseif (preg_match('/(AZ)/', $provider->state)) {
                                $header1 = 'Survey Date';
                                $header2 = 'Date Corrected';
                                $header3 = 'Rule/Statute';
                                $header4 = 'Title';
                                $secondrow = 'Findings';
                            } elseif (preg_match('/(MD|NY)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Type';
                                $header3 = 'Regulations';
                                $header4 = 'Status';
                                $secondrow = $provider->state == 'NY' ? 'Brief Description' : 'Findings';
                            } elseif (preg_match('/(KS)/', $provider->state)) {
                                $header1 = 'Date of Survey';
                                $header2 = 'Survey Number';
                                $header3 = 'Survey Reason';
                                $header4 = 'Findings';
                            } elseif (preg_match('/(NC)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Type';
                                $header3 = 'Violations';
                                $header4 = 'Rule';
                            } elseif (preg_match('/(AK)/', $provider->state)) {
                                $header1 = 'Type';
                                $header2 = 'Date';
                                $header3 = 'Finding';
                                $header4 = 'Violation Date';
                                $header5 = 'Compliance Date';
                                $header6 = 'Action Taken';
                            } elseif (preg_match('/(VA)/', $provider->state)) {
                                $header1 = 'Inspection Date';
                                $header2 = 'SHSI';
                                $header3 = 'Complaint Rated';
                                $header4 = 'Violations';
                            } elseif (preg_match('/(SC)/', $provider->state)) {
                                $header1 = 'Inspection Type';
                                $header2 = 'Date';
                                $header3 = 'Deficiency Type';
                                $header4 = 'Severity';
                                $header5 = 'Resolved';
                            } elseif (preg_match('/(OH)/', $provider->state)) {
                                $header1 = 'Inspection Date';
                                $header2 = 'Inspection Type';
                                $header3 = 'Inspection Status';
                                $header4 = 'Corrective Action';
                                $header5 = 'Status Updated';
                            } elseif (preg_match('/(CT)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Type';
                                $header3 = 'Case #';
                                $header4 = 'Resolution Action';
                                $secondrow = 'Violations';
                            } elseif (preg_match('/(WV)/', $provider->state)) {
                                $header1 = 'Corrective Action Plan Start';
                                $header2 = 'Corrective Action Plan End';
                                $header3 = 'Outcome Code';
                                $header4 = 'Issue Completed Date';
                                $secondrow = 'Non Compliance Code';
                            } elseif (preg_match('/(DC)/', $provider->state)) {
                                $header1 = 'Date';
                                $header2 = 'Type';
                                $header3 = 'Code';
                                $header4 = 'Resolution Date';
                                $secondrow = 'Type';
                            } elseif (preg_match('/(VT)/', $provider->state)) {
                                $header1 = 'Type';
                                $header2 = 'Create Date';
                                $header3 = 'Due Date';
                                $header4 = 'Corrected Date';
                                $header5 = 'Status';
                                $secondrow = 'Regulation/Restriction';
                            } elseif (preg_match('/(CO)/', $provider->state)) {
                                $header1 = 'Type';
                                $header2 = 'Report Date';
                                $header3 = 'Category';
                                $header4 = 'Finding';
                            } elseif (preg_match('/(MS)/', $provider->state)) {
                                $header1 = 'Exam Type';
                                $header2 = 'Begin Date';
                                $header3 = 'End Date';
                                $header4 = 'Exam Status';
                                $header5 = 'Document';
                            } elseif (preg_match('/(NJ)/', $provider->state)) {
                                $header1 = 'Date Cited';
                                $header2 = 'Date Abated';
                                $header3 = 'Regulation Number';
                            } elseif (preg_match('/(PA)/', $provider->state)) {
                                $header1 = 'Inspection Date';
                                $header2 = 'Reason';
                                $header3 = 'Description';
                                $header4 = 'Status';
                            } elseif (preg_match('/(MI)/', $provider->state) == false) {
                                $header1 = 'Report Date';
                                $header2 = 'Report Type';
                                $header3 = 'Report Status';
                            }
                            
                            ?>

                            <table border="0" class="g-inspections">
                                <thead>
                                    <tr>
                                        <th><?php echo $header1; ?></th>
                                        <?php if($header2<> ''): $colspan=2; ?>
                                        <th><?php echo $header2; ?></th>
                                        <?php endif; ?>
                                        <?php if($header3<> ''): $colspan=3; ?>
                                        <th><?php echo $header3; ?></th>
                                        <?php endif; ?>
                                        <?php if($header4<> ''): $colspan=4; ?>
                                        <th><?php echo $header4; ?></th>
                                        <?php endif; ?>
                                        <?php if($header5<> ''): $colspan=5; ?>
                                        <th><?php echo $header5; ?></th>
                                        <?php endif; ?>
                                        <?php if($header6<> ''): $colspan=6; ?>
                                        <th><?php echo $header6; ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php $typeAnote=0; $typeBnote = 0;
                                    foreach ($inspections as $inspection): ?>
                                    <tr>
                                        <td colspan="<?php echo $colspan; ?>" class="divider"></td>
                                    </tr>
                                    <tr>
                                        <?php if (preg_match("/(MI)/",$provider->state)):?>
                                        <td>

                                            <a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow"><?php echo $inspection->report_type; ?>
                                                <?php if ($inspection->rule_description):
                                                    echo ' (' . $inspection->rule_description . ') ';
                                                endif; ?>
                                            </a>

                                            <?php if ($inspection->report_date):
                                                echo ' - ' . \Carbon\Carbon::parse($inspection->report_date)->format('m/d/Y');
                                            endif; ?>
                                        </td>
                                        <?php elseif (preg_match("/(NJ)/",$provider->state)):?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>

                                        <?php elseif (preg_match("/(FL|RI)/",$provider->state)):?>

                                        <td><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow"><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></a></td>

                                        <?php elseif (preg_match("/(WA|MO|IA)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>

                                        <td><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow"><?php echo $inspection->report_type; ?></a></td>

                                        <?php elseif (preg_match("/(LA)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <?php if (preg_match("/(Click Here)/",$inspection->rule_description)):?>

                                        <td><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow"><?php echo $inspection->rule_description; ?></a></td>

                                        <?php else: ?>
                                        <td><?php echo $inspection->rule_description; ?></td>
                                        <?php endif; ?>
                                        <?php elseif (preg_match("/(GA|WI)/i",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>

                                        <td><?php if ($inspection->report_url):?>
                                            <a href="<?php echo $provider->state == 'GA' ? env('IDRIVE_BITBUCKET_URL1') . '/' . $inspection->report_url : $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow"><?php echo $inspection->report_type; ?></a>
                                            <?php else: ?>
                                            <?php echo $inspection->report_type; ?>
                                            <?php endif; ?>
                                        </td>

                                        <?php elseif (preg_match("/(SC)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->rule_description; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>

                                        <?php elseif (preg_match("/(WY)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td>
                                            <?php if ($inspection->report_url) :?>
                                            <a href="<?php echo $inspection->report_url; ?>" target="_blank" rel="nofollow"
                                                title="Sign In to view inspection report">Click to View</a>
                                            <?php endif; ?>
                                        </td>
                                        <?php elseif (preg_match("/(TN)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>
                                        <td><?php echo $inspection->rule_description; ?></td>

                                        <?php elseif (preg_match("/(AR|NH|KY)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php
                                        if ($inspection->report_url != '') {
                                            if ($provider->state == 'KY') {
                                                echo '<a href="' . env('IDRIVE_BITBUCKET_URL1') . '/' . $inspection->report_url . '" target="_blank" rel="nofollow">' . $inspection->report_type . '</a>';
                                            } else {
                                                echo '<a href="' . $inspection->report_url . '" target="_blank" rel="nofollow">' . $inspection->report_type . '</a>';
                                            }
                                        } else {
                                            echo $inspection->report_type;
                                        }
                                        ?>
                                        </td>
                                        <td><?php echo $inspection->rule_description; ?></td>

                                        <?php elseif (preg_match("/(TX)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->rule_description; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>
                                        <td><?php echo $inspection->pages; ?>
                                            <?php if($inspection->pages> 0): ?>
                                            <a target="_blank" rel="nofollow" href="<?php echo $inspection->report_url; ?>">View
                                                Report(s)</a>
                                            <?php endif;?>
                                        </td>

                                        <?php elseif (preg_match("/(AL)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php if ($inspection->report_url): ?><a></a>
                                            <a href="<?php echo $inspection->report_url; ?>" target="_blank" rel="nofollow">View Form</a>
                                            <?php else: ?>
                                            <?php echo $inspection->report_status; ?>
                                            <?php endif;?>
                                        </td>
                                        <td><?php echo $inspection->status_date ? $inspection->report_date . ' - ' . $inspection->status_date : $inspection->report_date; ?></td>
                                        <?php elseif (preg_match("/(AZ)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>

                                        <?php elseif (preg_match("/(MD|NY)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>

                                        <?php elseif (preg_match("/(KS)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php if($inspection->report_url<>"Not Available"):?><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow">View Findings</a><?php else:?>Not
                                            Available<?php endif;?></td>

                                        <?php elseif (preg_match("/(CT)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>

                                        <?php elseif (preg_match("/(IN)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?> <br />
                                            <?php echo $inspection->report_date ? $inspection->report_date : ''; ?>
                                        </td>
                                        <td><?php echo $inspection->current_status; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>

                                        <?php elseif (preg_match("/(OK)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_status . '<br/>' . $inspection->report_type; ?></td>
                                        <td>
                                            <?php echo $inspection->current_status != '' ? 'Plan: ' . $inspection->current_status : ''; ?>
                                            <?php echo $inspection->status_date ? '<br/>Correction Date: ' . $inspection->status_date : ''; ?>
                                        </td>

                                        <?php elseif (preg_match("/(NC)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>

                                        <?php elseif (preg_match("/(AK)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->complaint_date ? $inspection->complaint_date : ''; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>

                                        <?php elseif (preg_match("/(VA)/",$provider->state)): ?>
                                        <td>
                                            <a href="<?php echo $inspection->report_url; ?>" target="_blank" rel="nofollow">
                                                <?php echo $inspection->report_date ? $inspection->report_date : ''; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>

                                        <?php elseif (preg_match("/(CA)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td>
                                            <?php
                                            if ($inspection->report_type == 'Summary') {
                                                echo $inspection->report_status;
                                            } else {
                                                echo $inspection->report_date ? $inspection->report_date : '&mdash;';
                                            }
                                            ?>
                                        </td>

                                        <?php if ($inspection->rule_description == ''):?>
                                        <td>No Citation</td>
                                        <?php else:
                                        if (preg_match("/Type A Citation/i",$inspection->rule_description)) $typeAnote = 1;
                                        if (preg_match("/Type B Citation/i",$inspection->rule_description)) $typeBnote = 1;
                                        ?>
                                        <td><?php
                                        if ($inspection->report_url != '') {
                                            echo '<a href="' . $inspection->report_url . '" target="_blank" rel="nofollow">' . $inspection->rule_description . '</a>';
                                        } else {
                                            echo $inspection->rule_description;
                                        }
                                        ?></td>
                                        <?php endif; ?>

                                        <?php elseif (preg_match("/(NE)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>
                                        <td><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow"><?php echo $inspection->report_type; ?></a></td>

                                        <?php elseif (preg_match("/(MT|NM)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td>
                                            <?php if($inspection->report_url<>''):?>

                                            <a href="<?php echo env('IDRIVE_BITBUCKET_URL1'); ?>/<?php echo $inspection->report_url; ?>"
                                                target="_blank" rel="nofollow"><?php echo $inspection->report_type; ?></a>

                                            <?php else:?>
                                            <?php echo $inspection->report_type; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $inspection->current_status; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <?php elseif (preg_match("/(SD)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td>
                                            <?php echo $inspection->report_type; ?>
                                        </td>
                                        <td><?php if($inspection->report_status<>''):?>
                                            <a href="/inspections/<?php echo strtolower($provider->state); ?>/<?php echo $inspection->report_status; ?>"
                                                target="_blank" rel="nofollow">View</a>
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="<?php echo env('IDRIVE_BITBUCKET_URL1'); ?>/<?php echo $inspection->report_url; ?>"
                                                target="_blank" rel="nofollow">View</a></td>

                                        <?php elseif (preg_match("/(OH)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td>
                                            <?php if($inspection->report_url<>''):?>
                                            <a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow"><?php echo $inspection->report_type; ?></a>
                                            <?php else:?>
                                            <?php echo $inspection->report_type; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>

                                        <?php elseif (preg_match("/(VT)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->complaint_date ? $inspection->complaint_date : ''; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <?php elseif (preg_match("/(CO)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td>
                                            <?php if($inspection->report_url):?>

                                            <a target="_blank" rel="nofollow"
                                                href="<?php echo $inspection->report_url; ?>"><?php echo $inspection->report_status; ?></a>

                                            <?php else: echo $inspection->report_status; endif;?>

                                        </td>
                                        <td><?php echo $inspection->current_status; ?></td>

                                        <?php elseif (preg_match("/(DC)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td>
                                            <?php if ($inspection->report_url): ?>
                                            <a target="_blank" rel="nofollow"
                                                href="<?php echo env('IDRIVE_BITBUCKET_URL1') . '/' . $inspection->report_url; ?>"><?php echo $inspection->report_type; ?>
                                                Inspection</a>
                                            <?php else: echo $inspection->report_type; endif;?>

                                        </td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>
                                        <?php elseif (preg_match("/(MS)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td>
                                            <?php if($inspection->report_url):?>

                                            <a target="_blank" rel="nofollow"
                                                href="<?php echo $inspection->report_url; ?>"><?php echo $inspection->rule_description; ?></a>

                                            <?php endif;?>
                                        </td>

                                        <?php elseif (preg_match("/(WV)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->complaint_date ? $inspection->complaint_date : ''; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->status_date ? $inspection->status_date : ''; ?></td>


                                        <?php elseif (preg_match("/(UT)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td>
                                            <?php echo $inspection->report_type; ?>
                                            <?php if ($inspection->complaint_date) {
                                                echo '<br/>Complaint Date: ' . $inspection->complaint_date;
                                            } ?>
                                        </td>
                                        <td><?php if ($inspection->status_date) {
                                            echo $inspection->current_status . ': ' . $inspection->status_date;
                                        } ?></td>

                                        <?php elseif (preg_match("/(DE)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <td><?php echo $inspection->current_status; ?></td>

                                        <?php elseif (preg_match("/(PA)/",$provider->state)): ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td><?php echo $inspection->report_url; ?></td>
                                        <td><?php echo $inspection->report_type; ?></td>
                                        <td><?php echo $inspection->report_status; ?></td>

                                        <?php else: ?>
                                        <td><?php echo $inspection->report_date ? $inspection->report_date : ''; ?></td>
                                        <td>
                                            <?php if($inspection->report_url):?>
                                            <a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                rel="nofollow"><?php echo $inspection->report_type; ?></a>
                                            <?php else: 
                                            echo $inspection->report_type;
                                            endif;?>
                                        </td>
                                        <td><?php echo $inspection->report_status; ?></td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php if ($inspection->rule_description <> ""):?>
                                    <tr>
                                        <?php if (preg_match("/(MD|VT|WV|CT|NY|WI|DE|IN|OK|UT|DC|AZ|AK|NJ|MN|PA|NC|WY)/i",$provider->state)): ?>
                                        <td colspan="<?php echo $colspan; ?>">
                                            <?php if($secondrow<>""):?>
                                            <strong><?php echo $secondrow; ?>:</strong>
                                            <?php endif;?>

                                            <?php 
                                            $ruleDescription = htmlentities($inspection->rule_description);
                                            $ruleDescription = preg_replace("/&lt;(p|\/p)&gt;/i",'<$1>', $ruleDescription);
                                            
                                            if ($provider->state == 'IN') {
                                                echo ($inspection->report_status <> '') ? $inspection->report_status . "<br/>" . $ruleDescription : $ruleDescription;
                                            } elseif ($provider->state == 'UT') {
                                                if ($inspection->rule_description <> '') {
                                                    echo $inspection->report_status?> <br /> <?php echo $ruleDescription;
                                                } else {
                                                    echo "This inspection was passed with no cited findings.";
                                                }
                                            } elseif ($provider->state == 'MN') {
                                                if ($ruleDescription) {
                                                    echo "<strong>Violation Category:</strong> " . $inspection->current_status . "<br/>";
                                                    echo "<strong>Violation Description:</strong> " . $ruleDescription;
                                                }
                                            } elseif ($provider->state == 'PA') {
                                                echo $inspection->rule_description;
                                            } else {
                                                echo ($inspection->rule_description <> '') ? $ruleDescription : "N/A";
                                            }
                                            
                                            if ($provider->state == 'NJ' && $inspection->current_status<> "") {
                                                echo "<br/><br/><strong>Violation Observed:</strong> " . $inspection->current_status;
                                            }
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($inspection->provider_response <> ''):?>
                                    <tr>
                                        <td colspan="<?php echo $colspan; ?>">
                                            <strong>Provider Response:</strong> (Contact the State Licensing Office for more
                                            information.)<br />
                                            <?php echo $inspection->provider_response; ?>
                                        </td>
                                    </tr>
                                    <?php endif;?>

                                    <?php endforeach; ?>

                                    <?php if (preg_match("/(CA|LA)/",$provider->state)): ?>
                                    <tr>
                                        <td colspan="<?php echo $colspan; ?>" class="divider"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="<?php echo $colspan; ?>">
                                            <?php if (preg_match("/(CA)/",$provider->state) && ($typeAnote || $typeBnote)): ?>

                                            <?php if ($typeAnote) : ?>
                                            <p class="note">*Type A citation is for the most serious type of violations
                                                in which there is an immediate risk to the health, safety or personal rights
                                                of those in care. Examples may include lack of care or supervision, access
                                                to open bodies of water, lack of a fire clearance for the building and
                                                access to dangerous chemicals. Citations for these violations will always be
                                                issued even if the violation is corrected on the spot.</p>
                                            <?php endif; ?>
                                            <?php if ($typeBnote): ?>
                                            <p class="note">*Type B citation is for a violation that, if not corrected,
                                                may become an immediate risk to the health, safety or personal rights of
                                                clients. Examples include faulty medical record keeping and lack of adequate
                                                staff training.</p>
                                            <?php endif; ?>

                                            <?php elseif (preg_match("/(NC)/",$provider->state)): ?>

                                            <p> *The following scale shows what the different sanitation ratings mean.
                                                <br>
                                                <b>Superior -- </b>0-15 demerits, no 6-point item<br>
                                                <b>Approved -- </b>15-30 demerits, no 6-point item<br>
                                                <b>Provisional-- </b>31-45 demerits, <b><u>or</u></b> a 6-point item<br>
                                                <b>Disapproved-- </b>46 or more demerits, <b><u>or</u></b> failure to
                                                improve provisional classification
                                                <br />
                                                <b>Summary Disapproval-- </b>when right-of-entry to inspect is denied, when
                                                an inspection is discontinued at the request of the operator, or when a
                                                water sample is confirmed positive for fecal coliform, total coliform or
                                                other chemical constituents.
                                            </p>
                                            <p class="note">If date of inspection is more than 9 months old, call the
                                                facility directly to ensure this is the most recent report available.</p>

                                            <?php elseif (preg_match("/(LA)/",$provider->state)): ?>

                                            <p>Inspection visit information is available online. However, if a report is not
                                                available of if you are unable to access the report,
                                                you may contact the DOE Licensing Division at (225)342-9905 for this
                                                information.
                                            </p>

                                            <?php endif;?>
                                        </td>
                                    </tr>
                                    <?php endif;?>
                                </tbody>
                            </table>

                        </div>
                        <p class="text-muted">If you are a provider and you believe any information is incorrect, please
                            contact us. We will research your concern and make corrections accordingly.</p>
                    </div>
                    <?php endif;?>

                    <?php if(count($inspections)>=5 || (count($inspections) && count($reviews))) :  ?>
                    <!-- Ezoic - ChildcareCenter Center Detail LinkAds - link_mid -->
                    <div id="ezoic-pub-ad-placeholder-107">
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- ChildcareCenter Center Detail LinkAds -->
                        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                            data-ad-slot="1414717170" data-ad-format="link"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                    <!-- End Ezoic - ChildcareCenter Center Detail LinkAds - link_mid -->

                    <?php endif;?>

                    <div class="section-body">
                        <h2>Reviews</h2>
                        <?php if(count($reviews)) : ?>
                        <div class="comments">
                            <?php
                            /** @var \Application\Domain\Entity\Review $review */
                            foreach ($reviews as $review):
                                $provider = $review->provider;
                            ?>
                            <div class="comment">
                                <div class="comment-header d-flex justify-content-between">
                                    <div class="user d-flex align-items-center">
                                        <div class="image">
                                            <img src="https://d19m59y37dris4.cloudfront.net/places/1-1/img/user.svg"
                                                alt="..." class="img-fluid rounded-circle">
                                        </div>
                                        <div class="title">
                                            <strong><?php echo $review->review_by; ?></strong>
                                            <span class="date"><?php echo $review->review_date; ?></span>
                                        </div>
                                    </div>
                                    <ul class="rate list-inline">
                                        <?php for ($i=0; $i<5; $i++):?>
                                        <?php if ($i < $review->rating):?>
                                        <li class="list-inline-item">
                                            <i class="zmdi zmdi-star"></i>
                                        </li>
                                        <?php else: ?>
                                        <li class="list-inline-item">
                                            <i class="zmdi zmdi-star-outline star"></i>
                                        </li>
                                        <?php endif; ?>
                                        <?php endfor;?>
                                    </ul>
                                </div>
                                <div class="comment-body">
                                    <?php if ($review->experience != '' && $review->experience != 'Other') {
                                        echo '<i>' . $review->experience . '</i><br/>';
                                    } ?>
                                    <p>
                                        <?php if (preg_match('/(<br>|<p>|<br\/>|<\/p>|<span>)/', $review->comments)) {
                                            echo $review->comments;
                                        } else {
                                            echo str_replace("\n", '<br/>', $review->comments);
                                        } ?>
                                    </p>
                                </div>
                                <div class="clearfix">
                                    <div class="vote-helpful-text" style="float: left">
                                        <?php echo $review->helpful_text; ?>
                                    </div>
                                    <div style="float:right; margin-bottom: 10px;">
                                        Was this review helpful to you?&nbsp;
                                        <span>
                                            <?php if ((isset($reviewsVoted[$review->id]) && !$reviewsVoted[$review->id]) || !isset($reviewsVoted[$review->id])):?>
                                            <a class="js-vote-link"
                                                href="/review/<?php echo $review->id; ?>/vote?is_helpful=1">Yes</a>&nbsp;
                                            <?php else: ?>
                                            <span>Yes</span>
                                            <?php endif;?>
                                            <?php if ((isset($reviewsVoted[$review->id]) && $reviewsVoted[$review->id]) || !isset($reviewsVoted[$review->id])):?>
                                            <a class="js-vote-link"
                                                href="/review/<?php echo $review->id; ?>/vote?is_helpful=0">No</a>
                                            <?php else: ?>
                                            <span>No</span>
                                            <?php endif;?>
                                        </span>
                                    </div>
                                </div>

                                <?php if ($provider->user && $review->owner_comment && (($user && $user->id == optional(optional($provider)->user)->id) || $review->owner_comment_approved)): ?>
                                <div class="comment" style="border-bottom: 0; margin: 0 0 0 25px">
                                    <div>
                                        Owner Response
                                        <?php if (!$review->owner_comment_approved): ?>
                                        <span style="color: #868e96">Waiting For Approving</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="comment-header d-flex justify-content-between">
                                        <div class="user d-flex align-items-center">
                                            <div class="image">
                                                <img src="https://d19m59y37dris4.cloudfront.net/places/1-1/img/user.svg"
                                                    alt="..." class="img-fluid rounded-circle">
                                            </div>
                                            <div class="title">
                                                <strong><?php echo optional(optional($provider)->user)->firstname . ' ' . optional(optional($provider)->user)->lastname[0]; ?></strong>
                                                <span class="date"><?php echo $review->owner_comment_date; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-body">
                                        <?php echo $review->owner_comment; ?>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div style="text-align: right">
                                    <a href="/review/<?php echo $review->id; ?>/response">Post Owner Response</a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach;?>
                        </div>
                        <?php endif; ?>

                        <div id="review-section">
                            <p>
                                <?php if (count($reviews)==0) : ?>
                                Be the first to review this childcare provider.
                                <?php endif; ?>
                                <?php if($provider->approved >= 1) : ?>
                                Write a review about <?php echo $providerName; ?>. Let other families know what’s great, or what
                                could be improved.
                                Please read our brief <a href="/review/guidelines" target="_blank">review guidelines</a>
                                to make your review as helpful as possible.
                                <?php endif; ?>
                            </p>
                            <div class="review-box">
                                <form method="post" action="/review/new" id="review" class="zend_form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group-bs">
                                                <div id="email-label"><label class="required" for="email">Email
                                                        address (will not be published):</label></div>
                                                <div id="email-element"><input type="email" id="email" name="email"
                                                        class="form-control" value="{{ isset($user->email) ? $user->email : '' }}"></div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group-bs">
                                                <div id="name-label"><label class="required" for="name">Display
                                                        name:</label></div>
                                                <div id="name-element"><input id="name" name="name" class="form-control"
                                                        value="{{ isset($user->email) ? $user->firstname : '' }}"></div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group-bs">
                                                <div id="experience-label"><label class="required" for="experience">Which
                                                        best describes your experience?:</label></div>
                                                <div id="experience-element">
                                                    <select id="experience" name="experience" class="form-control">
                                                        <option value="">Select from below</option>
                                                        <option value="I have used this provider for more than 6 months">I
                                                            have used this provider for more than 6 months</option>
                                                        <option value="I have used this provider for less than 6 months">I
                                                            have used this provider for less than 6 months</option>
                                                        <option
                                                            value="I have toured this provider's facility, but have not used its services">
                                                            I have toured this provider's facility, but have not used its
                                                            services</option>
                                                        <option value="I am the owner">I am the owner</option>
                                                        <option value="I am an employee">I am an employee</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group-bs">
                                                <div id="rating-label"><label class="required" for="rating">Rating
                                                        (1=poor, 5=excellent):</label></div>
                                                <div id="rating-element">
                                                    <select id="rating" name="rating" class="form-control">
                                                        <option value="">Select your Rating</option>
                                                        <option value="1">1 star</option>
                                                        <option value="2">2 star</option>
                                                        <option value="3">3 star</option>
                                                        <option value="4">4 star</option>
                                                        <option value="5">5 star</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <div class="form-group-bs">
                                                <div id="comments-label"><label for="comments" class="required">Write
                                                        your review:</label></div>
                                                <div id="comments-element">
                                                    <textarea id="comments" name="comments" cols="15" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 form-action-row">
                                            <div>
                                                <input type="hidden" name="challenge" value="g-recaptcha-response">
                                                <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>
                                                <div class="g-recaptcha" data-sitekey="{{ env("DATA_SITEKEY") }}" data-theme="light" data-type="image" data-size="normal">                                
                                                </div>
                                                @error('recaptcha-token')
                                                    <ul>
                                                        <li>{{ $message }}</li>
                                                    </ul>
                                                @enderror
                                            </div><input type="hidden" name="pid" value="{{ $provider->id }}">
                                            <div class="form-action-row__submit-container"><input type="submit"
                                                    name="submit" class="btn" value="Add Review"></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php if ($provider->approved >= 1 || count($reviews)) : ?>
                            <div class="review-pane">
                                <div class="caption">Review Policy:</div>
                                <div class="text-muted fs-12">
                                    <div>ChildcareCenter.us does not actively screen or monitor user reviews, nor do we
                                        verify or edit content. Reviews reflect
                                        only the opinion of the writer. We ask that users follow our
                                        <a href="https://childcarecenter.us/review/guidelines" target="_blank">review
                                            guidelines</a>. If you see a review that does not reflect these guidelines, you
                                        can email us. We will assess
                                        the review and decide the appropriate next step. Please note – we will not remove a
                                        review simply because it is
                                        negative. Providers are welcome to respond to parental reviews, however we ask that
                                        they identify themselves as
                                        the provider.
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

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
                            <input type="button" class="btn" value="Ask a Question" onclick="window.location.href='/send_question?id={{ $provider->id }}'" />
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
                                        <input type="button" class="btn" value="Answer the Question Above" onclick="window.location.href='/send_answer?id={{$provider->id}}&questionId={{$question->id}}'" />
                                    </div><br/>
                                </div>
                            @endforeach
                        </div>            
                    </div>
                </div>
            </section>
            <!---------right container------>
            <section class="right-sect hidden-xs">

                <a href="#review-section" class="btn btn-review">Write a Review</a>
                <iframe
                    src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                    width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                    allowTransparency="true" allow="encrypted-media"></iframe>
                <?php if (!$provider->is_featured):?>
                <!-- Ezoic - CCC MOINSBD Link Top - link_top -->
                <div id="ezoic-pub-ad-placeholder-105">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- CCC MOINSBD Link Top -->
                    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                        data-ad-slot="9070001310" data-ad-format="link"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <!-- End Ezoic - CCC MOINSBD Link Top - link_top -->
                <br />
                <?php endif;?>
                <div class="listSidebar">
                    <h3>Contact</h3>
                    <div class="contactInfo">
                        <ul class="list-unstyled list-address">
                            <li>
                                <i class="zmdi zmdi-pin" aria-hidden="true"></i>
                                <?php echo $provider->address . ', ' . $provider->address2 . ' <br/> ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?>
                            </li>
                            <li>
                                <i class="zmdi zmdi-phone" aria-hidden="true"></i>
                                <?php if ($provider->approved >= 0):
                                    echo $provider->formatPhone;
                                else:
                                    echo $provider->maskPhone;
                                endif; ?>
                            </li>
                            <?php if($provider->email <> "" && $provider->user):?>
                            <li>
                                <i class="zmdi zmdi-email" aria-hidden="true"></i>
                                <?php if ($user): ?>
                                <a href="mailto:<?php echo $provider->email; ?>"><?php echo $provider->email; ?></a>
                                <?php else:?>
                                <a href="/user/login?url=<?php echo $_SERVER['REQUEST_URI']; ?>">Login</a> or
                                <a href="/user/new">Register</a> to email this provider.
                                <?php endif; ?>
                            </li>
                            <?php endif;?>
                            <?php if($provider->website != "") : ?>
                            <li>
                                <i class="zmdi zmdi-globe-alt" aria-hidden="true"></i>
                                <?php echo $provider->website; ?>
                            </li>
                            <?php endif;?>
                        </ul>
                    </div>
                </div>

                <div class="listSidebar">
                    <h3>Operation Hours</h3>
                    <ul class="list-unstyled sidebarList condensed">
                        <?php if ($provider->operationHours):?>
                        <li>
                            <span class="pull-left">Monday</span>
                            <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->monday; ?></span>
                        </li>
                        <li>
                            <span class="pull-left">Tuesday</span>
                            <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->tuesday; ?></span>
                        </li>
                        <li>
                            <span class="pull-left">Wednesday</span>
                            <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->wednesday; ?></span>
                        </li>
                        <li>
                            <span class="pull-left">Thursday</span>
                            <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->thursday; ?></span>
                        </li>
                        <li>
                            <span class="pull-left">Friday</span>
                            <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->friday; ?></span>
                        </li>
                        <li>
                            <span class="pull-left">Saturday</span>
                            <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->saturday != '' ? optional(optional($provider)->operationHours)->saturday : 'Closed'; ?></span>
                        </li>
                        <li>
                            <span class="pull-left">Sunday</span>
                            <span class="pull-right"><?php echo optional(optional($provider)->operationHours)->sunday != '' ? optional(optional($provider)->operationHours)->sunday : 'Closed'; ?></span>
                        </li>
                        <?php else: ?>
                        <li>
                            <span class="pull-left">Days of Operation</span>
                            <?php if($provider->daysopen != "") : ?>
                            <span class="pull-right"><?php echo $provider->daysopen; ?></span>
                            <?php else: ?>
                            <span class="pull-right">Monday-Friday</span>
                            <?php endif; ?>
                        </li>
                        <?php if($provider->hoursopen != "") : ?>
                        <li>
                            <span class="pull-left">Normal Open Hours</span>
                            <span class="pull-right"><?php echo $provider->hoursopen; ?></span>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="listSidebar">
                    <h3>Notes</h3>
                    <ol>
                        <li>Please be thorough in verifying the quality of this child care provider, and be sure to read any
                            reviews and inspection records that can help guide you to an informed decision. You want to be
                            confident your child is in good hands.</li>
                        <?php if ($user):?>
                        <li>Are you the owner or director of this facility? <a class="btn m-t-10"
                                href="/provider/update?pid=<?php echo $provider->id; ?>">Update Daycare Information</a></li>
                        <?php elseif (!$provider->user) :?>
                        <li>Are you the owner or director of this facility? Update your information here for free.<a
                                class="btn m-t-10" href="/user/new?pid=<?php echo $provider->id; ?>">Update Daycare
                                Information</a></li>
                        <?php endif;?>
                        <li>If you notice any inaccurate information on this page, please let us know so we can correct. <a
                                class="btn m-t-10" href="/contact?pid=<?php echo $provider->id; ?>">Report Incorrect
                                Information</a></li>
                        <li>ChildcareCenter does not verify business credentials including licensing information. You are
                            responsible for performing your own research to select an appropriate care provider.</li>
                    </ol>
                </div>

                <div class="listSidebar">
                    <h3>Quick Links</h3>
                    <div class="quick-links">
                        <?php if ($state):?>
                        <?php if ($provider->is_center):?>
                        <?php if ($provider->zip != null):?>
                        <a href="<?php echo route('centercare_zipcode', ['state' => $state->statefile, 'zipcode' => $provider->zip]); ?>">ZIP Code <?php echo $provider->zip; ?> </a>
                        <?php endif;?>
                        <?php if ($state->nextlevel == "DETAIL"):?>
                        <a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> </a>
                        <?php else: ?>
                        <?php if ($provider->cityfile != null):?>
                        <a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $provider->cityfile]); ?>"><?php echo ucwords(strtolower($provider->city)) . ', ' . $state->state_code; ?> City </a>
                        <?php endif;?>
                        <?php if ($county && $county->county_file != null):?>
                        <a href="<?php echo route('centercare_county', ['countyname' => $county->county_file]); ?>"><?php echo ucwords(strtolower($county->county)) . ', ' . $county->state; ?> County </a>
                        <?php endif;?>
                        <a href="<?php echo route('centercare_allcities', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> City List</a>
                        <a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> County List</a>
                        <?php endif;?>
                        <?php else:?>
                        <?php if ($provider->zip != null):?>
                        <a href="<?php echo route('homecare_zip', ['state' => $state->statefile, 'zipcode' => $provider->zip]); ?>">Zip Code <?php echo $provider->zip; ?> </a>
                        <?php endif;?>
                        <?php if ($state->nextlevel == "DETAIL"):?>
                        <a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?></a>
                        <?php else: ?>
                        <?php if ($provider->cityfile != null):?>
                        <a href="<?php echo route('homecare_city', ['state' => $state->statefile, 'city' => $provider->cityfile]); ?>"><?php echo ucwords(strtolower($provider->city)) . ', ' . $state->state_code; ?> City </a>
                        <?php endif;?>
                        <?php if ($county && $county->county_file != null):?>
                        <a href="<?php echo route('homecare_county', ['state' => $state->statefile, 'countyname' => $county->county_file]); ?>"><?php echo ucwords(strtolower($county->county)) . ', ' . $county->state; ?> County </a>
                        <?php endif;?>
                        <a href="<?php echo route('homecare_allcities', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> City List</a>
                        <a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> County List</a>
                        <?php endif; ?>
                        <?php endif;?>
                        <?php endif;?>
                    </div>

                </div>
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
                <?php if (count($news)):?>
                <div class="listSidebar">
                    <h3>In The News</h3>
                    <?php    
                        foreach ($news as $newsRow): ?>
                    <a rel="nofollow" target="blank" href="<?php echo $newsRow->url; ?>"><?php echo $newsRow->title; ?></a>
                    <?php endforeach;?>
                </div>
                <?php endif; ?>

            </section>
            <!-------right container ends---->
        </div>

        <?php if (!$provider->is_featured):?>

        <?php
            if (!empty($nearestProviders)) :?>
        <hr />
        <div class="section-body">
            <h2 class="section-title">Nearby Providers</h2>
            <div class="nearby-providers">
                <div class="row">
                    <?php
                    /** @var \Application\Domain\Entity\Facility $nearestProvider */
                    foreach ($nearestProviders as $nearestProvider): ?>
                    <div class="col-xs-12 col-sm-4" style="margin-bottom: 15px">
                        <a style="margin: 0" href="/provider_detail/<?php echo $nearestProvider->filename; ?>"><?php echo ucwords(strtolower($nearestProvider->name)); ?></a>
                        <span style="display:block;"><?php echo ucwords(strtolower($nearestProvider->city)) . ', ' . $nearestProvider->state . ' | ' . $nearestProvider->formatPhone . $nearestProvider->distance; ?></span>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
        <?php
            /** @var \Zend\Paginator\Paginator $providers */
            else: ?>
        <hr />
        <div class="section-body">
            <h2 class="section-title">Providers in ZIP Code <?php echo $provider->zip; ?></h2>
            <div class="nearby-providers">
                <div class="row">
                    <?php
                            /** @var \Application\Domain\Entity\Facility $provider */
                            foreach ($providers as $provider): ?>
                    <div class="col-xs-12 col-sm-4">
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <!-- <div class="declaimer-pane">
                        <div class="caption">Disclaimer:</div>
                        <div class="text-muted fs-10">
                            <div>We at ChildcareCenter strive daily to keep our listings accurate and up-to-date, and to provide top-level, practical
                                information that you can use and trust. However, ChildcareCenter.us does not endorse or recommend any of the childcare
                                providers listed on its site, cannot be held responsible or liable in any way for your dealings with them, and does
                                not guarantee the accuracy of listings on its site. We provide this site as a directory to assist you in locating childcare
                                providers in your area. We do not own or operate any child care facility, and make no representation of any of the listings
                                contained within ChildcareCenter.u</div>
                        </div>
                    </div> -->
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#lightgallery").lightGallery({
                selector: '.img-provider'
            });
        });
    </script>
    <script type="text/javascript">
        $(function() {
            $('body').on('click', '.js-vote-link', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('href'),
                    dataType: 'json',
                    context: $(this),
                    success: function(res) {
                        if (res.hasOwnProperty('helpfulText')) {
                            $(this).closest('div').prev('.vote-helpful-text').text(res
                                .helpfulText);
                            $(this).closest('div').html('Thank you for your feedback!');

                        }
                    }
                })
            })
        });
    </script>
@endsection
