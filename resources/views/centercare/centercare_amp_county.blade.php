@push('meta')
    <meta name="description"
        content="There are {{ $county->center_count }} daycare center, childcare centers and preschools in {{ $county->county }} {{ $county->state }} county child care center database.">
@endpush

@push('title')
    <title>Childcare Centers, Daycare and Preschools in {{ ucwords(strtolower($county->county)) }} {{ $county->state }}
        County</title>
@endpush

@extends('layouts.app_amp')

@section('content')
    <div class="breadcrumb-main">
        <div class="container">
            <div class="row">
                <div class="breadcrumb col-md-12">
                    <ul>
                        <li><a href="/state">Childcare Center </a> </li>
                        <li><a href="/state/<?php echo $state->statefile; ?>"><?php echo $state->state_name; ?> Child Care Centers</a> </li>
                        <li><?php echo ucwords(strtolower($county->county)); ?> County</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="main-container">
        <div class="container">
            <h1><?php echo ucwords(strtolower($county->county)); ?> County Child Care Centers</h1>
            <p><?php echo ucwords(strtolower($county->county)); ?> County childcare centers come in sizes, costs, and programs to fit all budgets and
                preferences.
                We know that parents are busy but that selecting the right daycare center or preschool is crucial.
                So we’ve gathered basic information for <?php echo number_format($county->center_count); ?> child care centers in <?php echo ucwords(strtolower($county->county)); ?> County
                into a single location so that you are only a click away from basic information such as address, size, and
                licensing information that can help you refine your search.
                You can narrow down your search even further by selecting a zip code or a city from the list below.
                <?php if(count($referalResources) >= 1): ?>
                Need more assistance? Simply contact the child care <a href="#agency">referral agency</a> or the licensing
                agency listed on the right!
                <?php endif; ?>
            </p>
            <p>
                <?php if ($county->center_count <= 20 && $county->homebase_count > 0) :?>
                You may also want to checkout <?php echo $county->homebase_count; ?> other family daycare providers and group home daycare in
                <a href="/<?php echo $state->statefile; ?>_homecare/<?php echo $county->county_file; ?>_county"><?php echo ucwords(strtolower($county->county)); ?> County Home
                    Daycare. </a>
                <?php endif; ?>
            </p>

            <p>If your ZIP code is not in the dropdown list, use this link to see all <a href="?display=zipcode">ZIP Codes
                    in <?php echo ucwords(strtolower($county->county)); ?> County</a></p>

            <?php if(count($providers)):?>
            <div class="up-section head">
                <h2>Top Childcare Centers in <?php echo ucwords(strtolower($county->county)); ?> County</h2>
                <?php 
                $i=0; 
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider): $i++;?>
                    <div class="row">
                        <div class="content-spac main-wrap col-md-12 col-xs-12 nopadding">
                            <a href="/provider_detail/<?php echo $provider->filename; ?>">
                                <amp-img src="<?php echo $provider->logo; ?>" alt="<?php echo $provider->name; ?>" height="150" width="200"
                                    class="logo" noloading /></amp-img>
                            </a>
                            <h3>
                                <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo htmlentities($provider->name); ?></a>
                                <div class="review">
                                    <?php if($provider->avg_rating<>'') : ?>
                                    <?php for ($j=0;  $j<5; $j++):?>
                                    <?php if ($provider->avg_rating-$j>0.5):?>
                                    <i class="zmdi zmdi-star star"></i>
                                    <?php elseif ($provider->avg_rating-$j==0.5):?>
                                    <i class="zmdi zmdi-star-half star"></i>
                                    <?php else: ?>
                                    <i class="zmdi zmdi-star-outline star"></i>
                                    <?php endif; ?>
                                    <?php endfor;?>
                                    <?php endif; ?>
                                </div>
                            </h3>
                            <span><?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip . ' | ' . $provider->formatPhone; ?> </span>
                            <p><?php
                            $description = strip_tags($provider->introduction);
                            if (strlen($description) > 270) {
                                $description = substr($description, 0, strpos($description, ' ', 260)) . ' ...';
                            }
                            echo $description;
                            ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
            <?php endif;?>


            <?php if(isset($referalResources)): ?>
            <div class="re-rgt rgt">
                <a name="agency"></a>
                <h3><?php echo ucwords(strtolower($county->county)); ?> County Childcare Referral Agencies:</h3>
                <div class="re-bg2">
                    <?php  
                $i = 0;
                /** @var \Application\Domain\Entity\ReferalResource $resource */
                foreach ($referalResources as $resource):
                    $i++; ?>

                    <div class="re-box2">
                        <h3><?php echo $resource->name; ?></h3>

                        <?php echo $resource->address; ?><br />
                        <?php echo $resource->city . ' ' . $resource->state . ' ' . $resource->zip; ?>
                        <p> Call <?php echo $resource->phone; ?> <?php if ($resource->tollfree <> "") :?>or Toll Free
                            <?php echo $resource->tollfree; ?><?php endif;?><br />
                            <?php if ($resource->email <> ""):?>
                            Email: <?php echo $resource->email; ?><br />
                            <?php endif;?>
                            For more information, visit <a target="_blank"
                                href="<?php echo $resource->website; ?>"><?php echo $resource->website; ?></a>
                        </p>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($state->agency): ?>
            <div class="li-ag rgt">
                <h3><?php echo $state->state_name; ?> Child Care Licensing Agency</h3>
                <?php echo $state->agency; ?>
            </div>
            <?php endif;?>

            <?php if($county->center_count >= 5 && count($cities) > 1): ?>
            <div class="list-section rgt">
                <h3>Cities in <?php echo ucwords(strtolower($county->county)); ?> County</h3>
                <div class="list2 tece">
                    <ul>
                        <?php  foreach ($cities as $city): ?>
                        <?php if ($city->center_count <= 0) :?>
                        <li><a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Daycare </a></li>
                        <?php elseif ($city->center_count <= 2) :?>
                        <li><a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Childcare </a></li>
                        <?php else: ?>
                        <li><a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $city->filename]); ?>"><?php echo ucwords(strtolower($city->city)); ?> Child Care </a></li>
                        <?php endif; ?>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
            <?php endif;?> <br />

            <div class="row">
                <div class="content-spac main-wrap col-md-12 col-xs-12 nopadding">

                    <div class="col-md-8">
                        <div class="content-fitst">
                            <h3>About the Provider</h3>
                            <div class="desc">Description</div>
                            <?php echo $provider->introduction; ?><br />

                            <amp-ad width="320" height="250" layout="responsive" type="adsense"
                                media="(max-height: 100px)" data-ad-layout="in-article" data-ad-format="fluid"
                                data-ad-client="ca-pub-8651736830870146" data-ad-slot="6581108420">
                            </amp-ad>
                            <?php if($provider->additionalinfo != "") : ?>
                            <strong>Additional Information</strong>
                            <?php echo $provider->additionalinfo; ?><br />
                            <?php endif; ?>
                        </div>



                        <?php if(isset($images) && count($images)) : ?>
                        <div class="photo-pop clearfix">
                            <p><strong><?php echo $provider->name; ?> Photos:</strong> (Click to enlarge)</p>
                            <div class="photo-galler">

                                <?php
                                /** @var \Application\Domain\Entity\Image $image */
                                foreach ($images as $image): ?>
                                <?php if ($image->imagename <> "") :?>

                                <div class="col-md-4 col-xs-6">
                                    <figure>
                                        <amp-img lightbox="caption" src="<?php echo $image->image_path; ?>" width="230" height="180"
                                            layout="responsive"></amp-img>
                                        <figcaption class="image text-center">
                                            <?php if ($image->altname != ''):
                                                echo $image->altname;
                                            else:
                                                echo $provider->name;
                                            endif; ?>
                                        </figcaption>
                                    </figure>
                                </div>

                                <?php else: ?>

                                <div class="col-md-4 col-xs-6">
                                    <figure>
                                        <amp-img lightbox="caption" src="<?php echo $image->image_url; ?>" width="230" height="180"
                                            layout="responsive"></amp-img>
                                        <figcaption class="image text-center">
                                            <?php if ($image->altname != ''):
                                                echo $image->altname;
                                            else:
                                                echo $provider->name;
                                            endif; ?>
                                        </figcaption>
                                    </figure>
                                </div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif;?>


                        <div class="col-md-4 col-xs-12 col-sm-8 hidden-sm">
                            <div class="sidebar-addthiss">
                                <h5 class="sidebar_title">Share With Us:</h5>
                                <amp-addthis width="360" height="80" data-pub-id="ra-5b79303edadadd72"
                                    data-widget-id="1vr8" class="social_share"></amp-addthis>
                                <a href="/review/new?pid=<?php echo $provider->id; ?>" class="btn btn-review">Write a Review</a>
                            </div>
                            <div class="sidebar-contact sidebar-main">
                                <div class="side-tit">Contact</div>
                                <ul>
                                    <li><i class="fa fa-map-marker" aria-hidden="true"></i>
                                        <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?>
                                    </li>
                                    <li><i class="fa fa-phone" aria-hidden="true"></i>
                                        <?php if ($provider->approved >= 0):
                                            if ($provider->momtrusted_phone != ''):
                                                echo $provider->momtrusted_phone;
                                            else:
                                                echo $provider->formatPhone;
                                            endif;
                                        else:
                                            echo $provider->maskPhone;
                                        endif; ?>
                                    </li>
                                    <?php if($provider->email <> "" && $provider->user_id):?>
                                    <li><i class="fa fa-envelope" aria-hidden="true"></i>
                                        <?php if (isset($user->caretype)): ?>
                                        <a href="mailto:<?php echo $provider->email; ?>"><?php echo $provider->email; ?></a>
                                        <?php else:?>
                                        <a href="/user/login?url=<?php echo $_SERVER['REQUEST_URI']; ?>">Login</a> or
                                        <a href="/user/new">Register</a> to email this provider.
                                        <?php endif; ?>
                                    </li>
                                    <?php endif;?>
                                    <?php if($provider->website != "") : ?>
                                    <li><i class="fa fa-globe" aria-hidden="true"></i>
                                        <?php echo $provider->website; ?>
                                    </li>
                                    <?php endif;?>
                                </ul>
                            </div>
                            <amp-ad layout="responsive" width=300 height=250 type="adsense"
                                data-ad-client="ca-pub-8651736830870146" data-ad-slot="6874455148">
                            </amp-ad>
                            <div class="sidebar-main sidebar-hour">
                                <div class="side-tit">Operation Hours</div>

                                <?php if ($provider->operationHours):?>
                                <div class="list-hou">
                                    <span>Monday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->monday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Tuesday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->tuesday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Wednesday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->wednesday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Thrusday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->thursday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Friday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->friday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Saturday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->saturday != '' ? optional(optional($provider)->operationHours)->saturday : 'Closed'; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Sunday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->sunday != '' ? optional(optional($provider)->operationHours)->sunday : 'Closed'; ?></span>
                                </div>

                                <?php else: ?>
                                <div class="list-hou">
                                    <span>Days of Operation</span>
                                    <?php if($provider->daysopen != "") : ?>
                                    <span><?php echo $provider->daysopen; ?></span>
                                    <?php else: ?>
                                    <span>Monday-Friday</span>
                                    <?php endif; ?>
                                </div>
                                <?php if($provider->hoursopen != "") : ?>
                                <div class="list-hou">
                                    <span>Normal Open Hours</span>
                                    <span><?php echo $provider->hoursopen; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>

                            </div>
                            <div class="sidebarads">
                                <amp-ad width="320" height="250" layout="responsive" type="adsense"
                                    media="(max-height: 100px)" data-ad-format="link"
                                    data-ad-client="ca-pub-8651736830870146" data-ad-slot="8851698836">
                                </amp-ad>
                            </div>
                            <div class="sidebar-main sidebar-note">
                                <div class="side-tit">Notes</div>
                                <ol>
                                    <li>Please be thorough in verifying the quality of this child care provider, and be sure
                                        to read any reviews and inspection records that can help guide you to an informed
                                        decision. You want to be confident your child is in good hands.</li>
                                    <?php if (isset($user->caretype)):?>
                                    <li>Are you the owner or director of this facility? <a class="btn m-t-10"
                                            href="/provider/update?pid=<?php echo $provider->id; ?>">Update Daycare
                                            Information</a></li>
                                    <?php elseif (!$provider->user) :?>
                                    <li>Are you the owner or director of this facility? Update your information here for
                                        free.<a class="btn m-t-10" href="/user/new?pid=<?php echo $provider->id; ?>">Update
                                            Daycare Information</a></li>
                                    <?php endif;?>
                                    <li>If you notice any inaccurate information on this page, please let us know so we can
                                        correct. <a class="btn m-t-10" href="/contact?pid=<?php echo $provider->id; ?>">Report
                                            Incorrect Information</a></li>
                                    <li>ChildcareCenter does not verify business credentials including licensing
                                        information. You are responsible for performing your own research to select an
                                        appropriate care provider.</li>
                                </ol>
                            </div>
                            <div class="sidebar-main sidebar-quick">
                                <div class="side-tit">Quick Links</div>
                                <div class="quick-links">

                                    <?php if ($provider->is_center):?>
                                    <?php if ($provider->zip != null):?>
                                    <a href="<?php echo route('centercare_zipcode', ['state' => $state->statefile, 'zipcode' => $provider->zip]); ?>">ZIP Code <?php echo $provider->zip; ?> </a>
                                    <?php endif;?>
                                    <?php if ($state->nextlevel == "DETAIL"):?>
                                    <a href="<?php echo route('centercare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> </a>
                                    <?php else: ?>
                                    <?php if ($provider->cityfile != null):?>
                                    <a href="<?php echo route('centercare_city', ['state' => $state->statefile, 'city' => $provider->cityfile]); ?>"><?php echo ucwords(strtolower($provider->city)) . ', ' . $state->sate_code; ?> City </a>
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

                                </div>
                            </div>
                            <amp-ad width="300" height="250" layout="responsive" type="adsense"
                                media="(max-height: 100px)" data-ad-format="link"
                                data-ad-client="ca-pub-8651736830870146" data-ad-slot="3987479973">
                            </amp-ad>
                        </div>

                        <div class="content-spac content-second">
                            <h3>Program and Licensing Details</h3>
                            <div class="cont-table">
                                <?php if($provider->capacity > 0) : ?>
                                <div class="con-wrap">
                                    <span>Capacity:</span>
                                    <span><?php echo $provider->capacity; ?></span>
                                </div>
                                <?php endif;?>
                                <?php if($provider->operation_id != "") : ?>
                                <div class="con-wrap">
                                    <span>License Number:</span>
                                    <span><?php echo $provider->operation_id; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($provider->age_range != "") : ?>
                                <div class="con-wrap">
                                    <span>Age Range:</span>
                                    <span><?php echo $provider->age_range; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($provider->accreditation != "") : ?>
                                <div class="con-wrap">
                                    <span>Achievement and/or Accreditations:</span>
                                    <span><?php echo $provider->accreditation; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($provider->pricing != "") : ?>
                                <div class="con-wrap">
                                    <span>Rate Range:</span>
                                    <span><?php echo $provider->pricing; ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="con-wrap">
                                    <span>Enrolled in Subsidized Child Care Program:</span>
                                    <span><?php echo $provider->subsidized == 1 ? 'Yes' : 'No'; ?></span>
                                </div>
                                <?php if($provider->schools_served != "") : ?>
                                <div class="con-wrap">
                                    <span>Schools Served:</span>
                                    <span><?php echo $provider->schools_served; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($provider->district_office != "") : ?>
                                <div class="con-wrap">
                                    <span>District Office:</span>
                                    <span><?php echo $provider->district_office; ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if($provider->language != "") : ?>
                                <div class="con-wrap">
                                    <span>Languages Supported:</span>
                                    <span>English, <?php echo $provider->language; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($provider->typeofcare != "") : ?>
                                <div class="con-wrap">
                                    <span>Type of Care:</span>
                                    <span><?php echo $provider->typeofcare; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($provider->transportation != "") : ?>
                                <div class="con-wrap">
                                    <span>Transportation:</span>
                                    <span><?php echo $provider->transportation; ?></span>
                                </div>
                                <?php endif	?>

                                <?php if($provider->district_office != "") : ?>
                                <div class="con-wrap">
                                    <span>District Office:</span>
                                    <span><?php echo $provider->district_office; ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if($provider->do_phone != "") : ?>
                                <div class="con-wrap">
                                    <span>District Office Phone:</span>
                                    <span><?php echo $provider->do_phone; ?> (Note: This is not the facility phone number.)</span>
                                </div>
                                <?php endif; ?>
                                <?php if($provider->licensor != "") : ?>
                                <div class="con-wrap">
                                    <span>Licensor:</span>
                                    <span><?php echo $provider->licensor; ?> </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (isset($inspections) && count($inspections)) : ?>
                        <div class="content-spac content-four clearfix">
                            <h3>Inspection/Report History</h3>
                            <p>Where possible, ChildcareCenter provides inspection reports as a service to families. This
                                information is deemed reliable, but is not guaranteed. We encourage families to contact the
                                daycare provider directly with any questions or concerns, as the provider may have already
                                addressed some or all issues. Reports can also be verified with your local daycare licensing
                                office.</p>
                            <div class="table-for">
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
                                        /** @var \Application\Domain\Entity\Inspection $inspection */
                                        foreach ($this->inspections as $inspection): ?>
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
                                                    echo ' - ' . $inspection->report_date->format('m/d/Y');
                                                endif; ?>
                                            </td>
                                            <?php elseif (preg_match("/(NJ)/",$provider->state)):?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>

                                            <?php elseif (preg_match("/(FL|RI)/",$provider->state)):?>

                                            <td><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                    rel="nofollow"><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></a></td>

                                            <?php elseif (preg_match("/(WA|MO|IA)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>

                                            <td><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                    rel="nofollow"><?php echo $inspection->report_type; ?></a></td>

                                            <?php elseif (preg_match("/(LA)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <?php if (preg_match("/(Click Here)/",$inspection->rule_description)):?>

                                            <td><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                    rel="nofollow"><?php echo $inspection->rule_description; ?></a></td>

                                            <?php else: ?>
                                            <td><?php echo $inspection->rule_description; ?></td>
                                            <?php endif; ?>
                                            <?php elseif (preg_match("/(GA|WI)/i",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
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
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->rule_description; ?></td>
                                            <td><?php echo $inspection->current_status; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>

                                            <?php elseif (preg_match("/(WY)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td>
                                                <?php if ($inspection->report_url) :?>
                                                <a href="<?php echo $inspection->report_url; ?>" target="_blank" rel="nofollow"
                                                    title="Sign In to view inspection report">Click to View</a>
                                                <?php endif; ?>
                                            </td>
                                            <?php elseif (preg_match("/(TN)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->rule_description; ?></td>

                                            <?php elseif (preg_match("/(AR|NH|KY)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
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
                                                <a href="<?php echo $inspection->report_url; ?>" target="_blank" rel="nofollow">View
                                                    Form</a>
                                                <?php else: ?>
                                                <?php echo $inspection->report_status; ?>
                                                <?php endif;?>
                                            </td>
                                            <td><?php echo $inspection->status_date ? $inspection->report_date->format('Y-m-d') . ' - ' . $inspection->status_date->format('Y-m-d') : $inspection->report_date->format('Y-m-d'); ?></td>
                                            <?php elseif (preg_match("/(AZ)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>

                                            <?php elseif (preg_match("/(MD|NY)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td><?php echo $inspection->current_status; ?></td>

                                            <?php elseif (preg_match("/(KS)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php if($inspection->report_url<>"Not Available"):?><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                    rel="nofollow">View Findings</a><?php else:?>Not
                                                Available<?php endif;?></td>

                                            <?php elseif (preg_match("/(CT)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td><?php echo $inspection->current_status; ?></td>

                                            <?php elseif (preg_match("/(IN)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_type; ?> <br />
                                                <?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?>
                                            </td>
                                            <td><?php echo $inspection->current_status; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>

                                            <?php elseif (preg_match("/(OK)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_status . '<br/>' . $inspection->report_type; ?></td>
                                            <td>
                                                <?php echo $inspection->current_status != '' ? 'Plan: ' . $inspection->current_status : ''; ?>
                                                <?php echo $inspection->status_date ? '<br/>Correction Date: ' . $inspection->status_date->format('Y-m-d') : ''; ?>
                                            </td>

                                            <?php elseif (preg_match("/(NC)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td><?php echo $inspection->current_status; ?></td>

                                            <?php elseif (preg_match("/(AK)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td><?php echo $inspection->complaint_date ? $inspection->complaint_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->current_status; ?></td>

                                            <?php elseif (preg_match("/(VA)/",$provider->state)): ?>
                                            <td>
                                                <a href="<?php echo $inspection->report_url; ?>" target="_blank" rel="nofollow">
                                                    <?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?>
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
                                                    echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : '&mdash;';
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
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>
                                            <td><a href="<?php echo $inspection->report_url; ?>" target="_blank"
                                                    rel="nofollow"><?php echo $inspection->report_type; ?></a></td>

                                            <?php elseif (preg_match("/(MT|NM)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
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
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
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
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
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
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>

                                            <?php elseif (preg_match("/(VT)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->complaint_date ? $inspection->complaint_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <?php elseif (preg_match("/(CO)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td>
                                                <?php if($inspection->report_url):?>

                                                <a target="_blank" rel="nofollow"
                                                    href="<?php echo $inspection->report_url; ?>"><?php echo $inspection->report_status; ?></a>

                                                <?php else: echo $inspection->report_status; endif;?>

                                            </td>
                                            <td><?php echo $inspection->current_status; ?></td>

                                            <?php elseif (preg_match("/(DC)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td>
                                                <?php if ($inspection->report_url): ?>
                                                <a target="_blank" rel="nofollow"
                                                    href="<?php echo env('IDRIVE_BITBUCKET_URL1') . '/' . $inspection->report_url; ?>"><?php echo $inspection->report_type; ?>
                                                    Inspection</a>
                                                <?php else: echo $inspection->report_type; endif;?>

                                            </td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>
                                            <?php elseif (preg_match("/(MS)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td>
                                                <?php if($inspection->report_url):?>

                                                <a target="_blank" rel="nofollow"
                                                    href="<?php echo $inspection->report_url; ?>"><?php echo $inspection->rule_description; ?></a>

                                                <?php endif;?>
                                            </td>

                                            <?php elseif (preg_match("/(WV)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->complaint_date ? $inspection->complaint_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td><?php echo $inspection->status_date ? $inspection->status_date->format('Y-m-d') : ''; ?></td>


                                            <?php elseif (preg_match("/(UT)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td>
                                                <?php echo $inspection->report_type; ?>
                                                <?php if ($inspection->complaint_date) {
                                                    echo '<br/>Complaint Date: ' . $inspection->complaint_date->format('Y-m-d');
                                                } ?>
                                            </td>
                                            <td><?php if ($inspection->status_date) {
                                                echo $inspection->current_status . ': ' . $inspection->status_date->format('Y-m-d');
                                            } ?></td>

                                            <?php elseif (preg_match("/(DE)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>
                                            <td><?php echo $inspection->current_status; ?></td>

                                            <?php elseif (preg_match("/(PA)/",$provider->state)): ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
                                            <td><?php echo $inspection->report_url; ?></td>
                                            <td><?php echo $inspection->report_type; ?></td>
                                            <td><?php echo $inspection->report_status; ?></td>

                                            <?php else: ?>
                                            <td><?php echo $inspection->report_date ? $inspection->report_date->format('Y-m-d') : ''; ?></td>
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
                                                <strong>Provider Response:</strong> (Contact the State Licensing Office for
                                                more information.)<br />
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
                                                <p class="note">*Type A citation is for the most serious type of
                                                    violations in which there is an immediate risk to the health, safety or
                                                    personal rights of those in care. Examples may include lack of care or
                                                    supervision, access to open bodies of water, lack of a fire clearance
                                                    for the building and access to dangerous chemicals. Citations for these
                                                    violations will always be issued even if the violation is corrected on
                                                    the spot.</p>
                                                <?php endif; ?>
                                                <?php if ($typeBnote): ?>
                                                <p class="note">*Type B citation is for a violation that, if not
                                                    corrected, may become an immediate risk to the health, safety or
                                                    personal rights of clients. Examples include faulty medical record
                                                    keeping and lack of adequate staff training.</p>
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
                                                    <b>Summary Disapproval-- </b>when right-of-entry to inspect is denied,
                                                    when an inspection is discontinued at the request of the operator, or
                                                    when a water sample is confirmed positive for fecal coliform, total
                                                    coliform or other chemical constituents.
                                                </p>
                                                <p class="note">If date of inspection is more than 9 months old, call the
                                                    facility directly to ensure this is the most recent report available.
                                                </p>

                                                <?php elseif (preg_match("/(LA)/",$provider->state)): ?>

                                                <p>Inspection visit information is available online. However, if a report is
                                                    not available of if you are unable to access the report,
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
                            <p>If you are a provider and you believe any information is incorrect, please contact us. We
                                will research your concern and make corrections accordingly.</p>
                        </div>
                        <?php endif;?>

                        <div id="review-section" class="content-spac content-five clearfix">
                            <h3>Reviews</h3>
                            <div class="reviews">
                                <div class="review-section clearfix">
                                    <?php if (count($reviews)==0) : ?>
                                    <p>Be the first to review this childcare provider.</p>
                                    <?php endif; ?>
                                    <?php if($provider->approved >= 1) : ?>
                                    <p>Write a review about <?php echo $provider->name; ?>. Let other families know what’s great, or
                                        what could be improved.
                                        Please read our brief <a href="/review/guidelines" target="_blank">review
                                            guidelines</a> to make your review as helpful as possible.</p>
                                    <?php endif; ?>

                                    <div class="clearfix">

                                        <a href="/review/new?pid=<?php echo $provider->id; ?>" class="btn btn-review">Write a
                                            Review</a>
                                    </div>
                                    <?php if ($provider->approved >= 1 && count($reviews)) : ?>
                                    <div class="policy-main">
                                        <div class="desc">Review Policy:</div>
                                        <p>ChildcareCenter.us does not actively screen or monitor user reviews, nor do we
                                            verify or edit content. Reviews reflect
                                            only the opinion of the writer. We ask that users follow our
                                            <a href="https://childcarecenter.us/review/guidelines" target="_blank">review
                                                guidelines</a>. If you see a review that does not reflect these guidelines,
                                            you can email us. We will assess
                                            the review and decide the appropriate next step. Please note – we will not
                                            remove a review simply because it is
                                            negative. Providers are welcome to respond to parental reviews, however we ask
                                            that they identify themselves as
                                            the provider.
                                        </p>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12 col-sm-8 hidden-xs">
                        <div class="sidebar-addthiss">
                            <h5 class="sidebar_title">Share With Us:</h5>
                            <amp-addthis width="360" height="80" data-pub-id="ra-5b79303edadadd72"
                                data-widget-id="1vr8" class="social_share"></amp-addthis>
                            <a href="/review/new?pid=<?php echo $provider->id; ?>" class="btn btn-review">Write a Review</a>
                        </div>
                        <div class="sidebar-contact sidebar-main">
                            <div class="side-tit">Contact</div>
                            <ul>
                                <li><i class="fa fa-map-marker" aria-hidden="true"></i>
                                    <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?>
                                </li>
                                <li><i class="fa fa-phone" aria-hidden="true"></i>
                                    <?php if ($provider->approved >= 0):
                                        if ($provider->momtrusted_phone != ''):
                                            echo $provider->momtrusted_phone;
                                        else:
                                            echo $provider->formatPhone;
                                        endif;
                                    else:
                                        echo $provider->maskPhone;
                                    endif; ?>
                                </li>
                                <?php if($provider->email <> "" && $provider->user_id):?>
                                <li><i class="fa fa-envelope" aria-hidden="true"></i>
                                    <?php if (isset($user->caretype)): ?>
                                    <a href="mailto:<?php echo $provider->email; ?>"><?php echo $provider->email; ?></a>
                                    <?php else:?>
                                    <a href="/user/login?url=<?php echo $_SERVER['REQUEST_URI']; ?>">Login</a> or
                                    <a href="/user/new">Register</a> to email this provider.
                                    <?php endif; ?>
                                </li>
                                <?php endif;?>
                                <?php if($provider->website != "") : ?>
                                <li><i class="fa fa-globe" aria-hidden="true"></i>
                                    <?php echo $provider->website; ?>
                                </li>
                                <?php endif;?>
                            </ul>
                        </div>
                        <div class="sidebar-main sidebar-hour">
                            <div class="side-tit">Operation Hours</div>
                            <?php if ($provider->operationHours):?>
                                <div class="list-hou">
                                    <span>Monday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->monday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Tuesday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->tuesday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Wednesday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->wednesday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Thrusday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->thursday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Friday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->friday; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Saturday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->saturday != '' ? optional(optional($provider)->operationHours)->saturday : 'Closed'; ?></span>
                                </div>
                                <div class="list-hou">
                                    <span>Sunday</span>
                                    <span><?php echo optional(optional($provider)->operationHours)->sunday != '' ? optional(optional($provider)->operationHours)->sunday : 'Closed'; ?></span>
                                </div>
                            <?php else: ?>
                            <div class="list-hou">
                                <span>Days of Operation</span>
                                <?php if($provider->daysopen != "") : ?>
                                <span><?php echo $provider->daysopen; ?></span>
                                <?php else: ?>
                                <span>Monday-Friday</span>
                                <?php endif; ?>
                            </div>
                            <?php if($provider->hoursopen != "") : ?>
                            <div class="list-hou">
                                <span>Normal Open Hours</span>
                                <span><?php echo $provider->hoursopen; ?></span>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="sidebarads">
                            <amp-ad width="320" height="250" layout="responsive" type="adsense"
                                media="(max-height: 100px)" data-ad-format="link"
                                data-ad-client="ca-pub-8651736830870146" data-ad-slot="8851698836">
                            </amp-ad>
                        </div>
                        <div class="sidebar-main sidebar-note">
                            <div class="side-tit">Notes</div>
                            <ol>
                                <li>Please be thorough in verifying the quality of this child care provider, and be sure to
                                    read any reviews and inspection records that can help guide you to an informed decision.
                                    You want to be confident your child is in good hands.</li>
                                <?php if (isset($user->caretype)):?>
                                <li>Are you the owner or director of this facility? <a class="btn m-t-10"
                                        href="/provider/update?pid=<?php echo $provider->id; ?>">Update Daycare Information</a>
                                </li>
                                <?php elseif (!$provider->user_id) :?>
                                <li>Are you the owner or director of this facility? Update your information here for free.<a
                                        class="btn m-t-10" href="/user/new?pid=<?php echo $provider->id; ?>">Update Daycare
                                        Information</a></li>
                                <?php endif;?>
                                <li>If you notice any inaccurate information on this page, please let us know so we can
                                    correct. <a class="btn m-t-10" href="/contact?pid=<?php echo $provider->id; ?>">Report
                                        Incorrect Information</a></li>
                                <li>ChildcareCenter does not verify business credentials including licensing information.
                                    You are responsible for performing your own research to select an appropriate care
                                    provider.</li>
                            </ol>
                        </div>

                        <div class="sidebar-main sidebar-quick">
                            <div class="side-tit">Quick Links</div>
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
                                <?php if (isset($county) && $county->getCountyFile() != null):?>
                                <a href="<?php echo route('homecare_county', ['state' => $state->statefile, 'countyname' => $county->county_file]); ?>"><?php echo ucwords(strtolower($county->county)) . ', ' . $county->state; ?> County </a>
                                <?php endif;?>
                                <a href="<?php echo route('homecare_allcities', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> City List</a>
                                <a href="<?php echo route('homecare_state', ['state' => $state->statefile]); ?>"><?php echo $state->state_name; ?> County List</a>
                                <?php endif; ?>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!$provider->is_featured):?>

                <?php
                if (isset($nearestProviders)) :?>
                <div class="content-spac main-wrap providers col-md-12">
                    <h3>Nearby Providers</h3>
                    <?php
                    /** @var \Application\Domain\Entity\Facility $nearestProvider */
                    foreach ($nearestProviders as $nearestProvider): ?>
                    <div class="pro-wrap">
                        <a href="/provider_detail/<?php echo $nearestProvider->filename; ?>"><?php echo ucwords(strtolower($nearestProvider->name)); ?></a>
                        <?php
                        $distance = round(\Application\Utils::distance($nearestProvider->getLat(), $nearestProvider->getLng(), $provider->getLat(), $provider->getLng()), 1);
                        $distance .= $distance > 1 ? ' miles away' : ' mile away';
                        $distance = ' | ' . $distance;
                        ?>
                        <span><?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' | ' . $provider->formatPhone . $distance; ?></span>
                    </div>

                    <?php endforeach;?>

                </div>
                <?php
                /** @var \Zend\Paginator\Paginator $providers */
                else: ?>
                <div class="content-spac main-wrap providers col-md-12">
                    <h3>Providers in ZIP Code <?php echo $provider->zip; ?></h3>
                    <?php
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider): ?>
                    <div class="pro-wrap">
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo ucwords(strtolower($provider->name)); ?></a>
                        <span><?php echo $provider->address . ' | ' . $provider->formatPhone; ?></span>
                    </div>
                    <?php endforeach;?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            {{-- <style>
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
            </div> --}}
        </div>
    </div>
@endsection
