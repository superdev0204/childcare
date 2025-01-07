<?php
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
    <link rel="canonical" href="https://childcarecenter.us/provider_detail/{{ $provider->filename }}">
@endpush

@if (strlen($provider->name) <= 40)
    @if (strlen($type) > 20)
        @push('title')
            <title>{{ ucwords(strtolower($provider->name)) . ' | ' . $provider->city . ' ' . $provider->state }}</title>
        @endpush
    @else
        @push('title')
            <title>
                {{ ucwords(strtolower($provider->name)) . ' | ' . $provider->city . ' ' . $provider->state . ' ' . $type }}
            </title>
        @endpush
    @endif
@elseif(strlen($provider->name) < 55)
    @push('title')
        <title>{{ ucwords(strtolower($provider->name)) . ' | ' . $provider->city . ' ' . $provider->state }}</title>
    @endpush
@else
    @push('title')
        <title>{{ ucwords(strtolower($provider->name)) }}</title>
    @endpush
@endif

@extends('layouts.app_amp_view')

@section('content')
    <script src="https://cdn.ampproject.org/v0/amp-lightbox-gallery-0.1.js" async custom-element="amp-lightbox-gallery"></script>
    <script src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js" async custom-element="amp-iframe"></script>
    <script src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js" async custom-element="amp-carousel"></script>

    <div class="header-bg">
        <div class="provider-header-detail">

            <div class="container">
                <div class="breadcrumbs">
                    <ul>
                        <?php if($provider->is_center):?>
                        <?php if ($state->nextlevel != "DETAIL"):?>
                        <?php if (isset($county) && !empty($county->county_file)): ?>
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
                        <?php if (isset($county)):?>
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
                        <li><?php echo ucwords(strtolower($provider->name)); ?></li>
                    </ul>
                </div>
                <div class="title-pane">
                    <h1 class="provider-title"><?php echo ucwords(strtolower($provider->name)); ?> -
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
                    <?php endif;?>
                    <?php if($provider->approved == "-1") : ?>
                    <h2>Provider Status: <?php echo $provider->status; ?>.</h2>
                    <?php endif;?>
                    <?php if($provider->approved < 0) : ?>
                    <h2>Provider Status: Provider Is NOT Licensed.</h2>
                    <?php endif;?>

                    <div class="title-address">
                        <i class="fa fa-map-marker"></i> <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br />
                        <i class="fa fa-phone" aria-hidden="true"></i>
                        <?php
                        if ($provider->approved >= 0):
                            echo $provider->formatPhone;
                        else:
                            echo $provider->maskPhone;
                        endif;
                        ?>
                        <?php if($provider->momtrusted_id <> ''):?>
                        <div data-mt-url='https://www.momtrusted.com/widgets/check-availability-form/ccus'
                            data-mt-vid='<?php echo $provider->id; ?>' id='mt-check-availability'></div>
                        <?php endif; ?>

                    </div>
                    <?php if ($provider->avg_rating <> '') : ?>
                    <div class="title-rating">
                        <?php for ($i=0;  $i < 5; $i++):?>
                        <?php if ($provider->avg_rating - $i >0.5):?>
                        <i class="fa fa-star" aria-hidden="true"></i>
                        <?php elseif ($provider->avg_rating - $i == 0.5):?>
                        <i class="fa fa-star-half-o" aria-hidden="true"></i>
                        <?php else: ?>
                        <i class="fa fa-star-o" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?php endfor;?>

                        <?php echo count($reviews);
                        echo count($reviews) == 1 ? ' Review' : ' Reviews'; ?>
                    </div>
                    <?php endif; ?>
                    <amp-img class="provider-logo" src="<?php echo $provider->logo; ?>" alt="<?php echo htmlentities($provider->name); ?>" height="150"
                        width="200" layout="responsive"></amp-img>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <?php if (!$provider->is_featured): ?>
        <!-- Ezoic - AMP Header - top_of_page -->
        <div id="ezoic-pub-ad-placeholder-146">
            <amp-ad layout="fixed-height" height=100 type="adsense" data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="3018812480">
            </amp-ad>
        </div>
        <!-- End Ezoic - AMP Header - top_of_page -->
        <?php endif;?>
        <div class="provider-details">
            <!---------left container------>
            <section class="left-sect head">
                <div>
                    <div class="clearfix"></div>

                    <div class="hidden-sm">
                        <a href="/review/new?pid=<?php echo $provider->id; ?>" class="btn btn-review">Write a Review</a>
                    </div>

                    <div class="section-body">
                        <h2>About the Provider</h2>
                        <p><strong>Description</strong>:
                            <?php echo preg_replace('/<(p|br)[^>]*?(\/?)>/i', '<$1>', strip_tags($provider->introduction, '<p>,<br>')); ?></p>
                        <?php if (!$provider->is_featured):?>
                        <!-- Ezoic - AMP Inarticle - under_first_paragraph -->
                        <div id="ezoic-pub-ad-placeholder-152">
                            <amp-ad layout="responsive" width=300 height=250 type="adsense"
                                data-ad-client="ca-pub-8651736830870146" data-ad-slot="9332939393">
                            </amp-ad>
                        </div>
                        <!-- End Ezoic - AMP Inarticle - under_first_paragraph -->
                        <?php endif;?>
                        <?php if($provider->additionalInfo != "") : ?>
                        <strong>Additional Information</strong>: <?php echo preg_replace('/<(p|br)[^>]*?(\/?)>/i', '<$1>', strip_tags($provider->additionalInfo, '<p>,<br>')); ?><br />
                        <?php endif; ?>
                    </div>

                    <?php if(count($images)) : ?>
                    <strong><?php echo $provider->name; ?> Photos:</strong> (Click to enlarge)<br />

                    <div id="lightgallery" class="img-gallery row">
                        <?php
                            /** @var \Application\Domain\Entity\Image $image */
                            foreach ($images as $image): ?>
                        <div class="col-xs-6 col-sm-4">
                            <?php if ($image->imagename <> "") :?>
                            <amp-img lightbox layout="responsive" height="150" width="200" src="<?php echo env('IDRIVE_BITBUCKET_URL') . '/' . $image->imagename; ?>"
                                alt="<?php if ($image->altname != ''):
                                    echo $image->altname;
                                else:
                                    echo htmlentities($provider->name);
                                endif; ?>">
                            </amp-img>
                            <?php else: ?>

                            <amp-img lightbox layout="responsive" height="150" width="200" src="<?php echo $image->image_url; ?>"
                                alt="<?php if ($image->altname != ''):
                                    echo $image->altname;
                                else:
                                    echo htmlentities($provider->name);
                                endif; ?>">
                            </amp-img>

                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif;?>
                    <div class="hidden-sm">
                        <div class="listSidebar">
                            <h3>Contact</h3>
                            <div class="contactInfo">
                                <ul class="list-unstyled list-address">
                                    <li>
                                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                                        <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?>
                                    </li>
                                    <li>
                                        <i class="fa fa-phone" aria-hidden="true"></i>
                                        <?php if ($provider->approved >= 0):
                                            if ($provider->detail && optional(optional($provider)->detail)->momtrusted_phone != ''):
                                                echo $provider->momtrusted_phone;
                                            else:
                                                echo $provider->formatPhone;
                                            endif;
                                        else:
                                            echo $provider->maskPhone;
                                        endif; ?>
                                    </li>
                                    <?php if($provider->email <> "" && $provider->user_id > 0):?>
                                    <li>
                                        <i class="fa fa-envelope" aria-hidden="true"></i>
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
                                        <i class="fa fa-globe" aria-hidden="true"></i>
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
                        <!-- Ezoic - AMP Sidebar Top - link_side -->
                        <div id="ezoic-pub-ad-placeholder-149">
                            <amp-ad layout="responsive" width=300 height=250 type="adsense"
                                data-ad-client="ca-pub-8651736830870146" data-ad-slot="1215610375">
                            </amp-ad>
                        </div>
                        <!-- End Ezoic - AMP Sidebar Top - link_side -->
                        <br />
                    </div>

                    <div class="section-body">
                        <h2>Program and Licensing Details</h2>
                        <ul class="provider-main-features">
                            <?php if($provider->capacity > 0) : ?>
                            <li>
                                <span>Capacity:</span>
                                <span><?php echo $provider->capacity; ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if($provider->operation_id != "") : ?>
                            <li>
                                <span>License Number:</span>
                                <span><?php echo $provider->operation_id; ?></span>
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
                                <!-- Ezoic - AMP Near Map - mid_content -->
                                <div id="ezoic-pub-ad-placeholder-147">
                                    <amp-ad layout="responsive" width=300 height=250 type="adsense"
                                        data-ad-client="ca-pub-8651736830870146" data-ad-slot="9209526755">
                                    </amp-ad>
                                </div>
                                <!-- End Ezoic - AMP Near Map - mid_content -->
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="google-maps">

                                    <amp-iframe width="330" height="280" sandbox="allow-scripts allow-same-origin"
                                        layout="responsive" frameborder="0"
                                        src="https://www.google.com/maps/embed/v1/place?key=<?php echo env('GOOGLE_API_KEY'); ?>&q=<?php echo urlencode($provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip); ?>">
                                    </amp-iframe>
                                </div>
                            </div>
                            <?php else:?>
                            <div class="col-xs-12">
                                <div class="google-maps">

                                    <amp-iframe width="600" height="450" sandbox="allow-scripts allow-same-origin"
                                        layout="responsive" frameborder="0"
                                        src="https://www.google.com/maps/embed/v1/place?key=<?php echo env('GOOGLE_API_KEY'); ?>&q=<?php echo urlencode($provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip); ?>">
                                    </amp-iframe>
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
                                                echo ' - ' . $inspection->report_date;
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
                    <!-- Ezoic - AMP Inspection - link_mid -->
                    <div id="ezoic-pub-ad-placeholder-151">
                        <amp-ad layout="responsive" width=300 height=250 type="adsense"
                            data-ad-client="ca-pub-8651736830870146" data-ad-slot="1215610375">
                        </amp-ad>
                    </div>
                    <!-- End Ezoic - AMP Inspection - link_mid -->
                    <?php endif;?>

                    <div class="section-body">
                        <h2>Reviews</h2>
                        <?php if(count($reviews)) : ?>
                        <div class="comments">
                            <?php
                                /** @var \Application\Domain\Entity\Review $review */
                                foreach ($reviews as $review): ?>
                            <div class="comment">
                                <div class="comment-header d-flex justify-content-between">
                                    <div class="user d-flex align-items-center">
                                        <div class="image">
                                            <amp-img src="https://d19m59y37dris4.cloudfront.net/places/1-1/img/user.svg"
                                                width="50" height="50" alt="User Review" noloading></amp-img>
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
                                            <i class="fa fa-star" aria-hidden="true"></i>
                                        </li>
                                        <?php else: ?>
                                        <li class="list-inline-item">
                                            <i class="fa fa-star-o" aria-hidden="true"></i>
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
                                            echo preg_replace('/<(p|br)[^>]*?(\/?)>/i', '<$1>', strip_tags($review->comments, '<p>,<br>'));
                                        } else {
                                            echo str_replace("\n", '<br/>', $review->comments);
                                        } ?>
                                    </p>
                                </div>
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
                                Write a review about <?php echo htmlentities($provider->name); ?>. Let other families know what’s great, or what
                                could be improved.
                                Please read our brief <a href="/review/guidelines" target="_blank">review guidelines</a>
                                to make your review as helpful as possible.
                                <?php endif; ?>
                            </p>

                            <a href="/review/new?pid=<?php echo $provider->id; ?>" class="btn btn-review">Write a Review</a>

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
                </div>
            </section>
            <!---------right container------>
            <section class="right-sect hidden-xs">
                <h3>Share With Us:</h3>
                <div class="social-links">
                    <amp-addthis width="360" height="80" data-pub-id="ra-5b79303edadadd72" data-widget-id="1vr8"
                        class="social_share"></amp-addthis>
                </div>
                <a href="/review/new?pid=<?php echo $provider->id; ?>" class="btn btn-review">Write a Review</a>
                <?php if (!$provider->is_featured):?>
                <!-- Ezoic - AMP Sidebar Top - link_side -->
                <div id="ezoic-pub-ad-placeholder-149">
                    <amp-ad layout="responsive" width=300 height=250 type="adsense"
                        data-ad-client="ca-pub-8651736830870146" data-ad-slot="1215610375">
                    </amp-ad>
                </div>
                <!-- End Ezoic - AMP Sidebar Top - link_side -->
                <br />
                <?php endif;?>
                <div class="listSidebar">
                    <h3>Contact</h3>
                    <div class="contactInfo">
                        <ul class="list-unstyled list-address">
                            <li>
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?>
                            </li>
                            <li>
                                <i class="fa fa-phone" aria-hidden="true"></i>
                                <?php if ($provider->approved >= 0):
                                    if ($provider->detail && $provider->momtrusted_phone != ''):
                                        echo $provider->momtrusted_phone;
                                    else:
                                        echo $provider->formatPhone;
                                    endif;
                                else:
                                    echo $provider->maskPhone;
                                endif; ?>
                            </li>
                            <?php if($provider->email <> "" && $provider->user):?>
                            <li>
                                <i class="fa fa-envelope" aria-hidden="true"></i>
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
                                <i class="fa fa-globe" aria-hidden="true"></i>
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
                <!-- Ezoic - AMP Sidebar - sidebar_middle -->
                <div id="ezoic-pub-ad-placeholder-148">
                    <amp-ad layout="responsive" width=300 height=250 type="adsense"
                        data-ad-client="ca-pub-8651736830870146" data-ad-slot="6143539398">
                    </amp-ad>
                </div>
                <!-- End Ezoic - AMP Sidebar - sidebar_middle -->
                <br />
            </section>
            <section class="right-sect">
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
                        <?php if (isset($county) && $county->county_file != null):?>
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
                        <?php if (isset($county) && $county->county_file != null):?>
                        <a href="<?php echo route('homecare_county', ['state' => $state->statefile, 'countyname' => $county->county_file]); ?>"><?php echo ucwords(strtolower($county->county)) . ', ' . $county->state; ?> County </a>
                        <?php endif;?>
                        <a href="<?php echo route('homecare_allcities', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> City List</a>
                        <a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> County List</a>
                        <?php endif; ?>
                        <?php endif;?>
                        <!-- Ezoic - AMP Sidebar Bottom - link_side -->
                        <div id="ezoic-pub-ad-placeholder-150">
                            <amp-ad layout="responsive" width=300 height=250 type="adsense"
                                data-ad-client="ca-pub-8651736830870146" data-ad-slot="1215610375">
                            </amp-ad>
                        </div>
                        <!-- End Ezoic - AMP Sidebar Bottom - link_side -->
                        <br />
                    </div>

                </div>

                <?php if (count($news)):?>
                <div class="listSidebar">
                    <h3>In The News</h3>
                    <?php
    
                        foreach ($news as $newsRow): ?>
                    <a rel="nofollow" target="_blank" href="<?php echo $newsRow->url; ?>"><?php echo $newsRow->title; ?></a>
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
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo ucwords(strtolower($provider->name)); ?></a>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    </div>
@endsection
