@push('title')
    <title>Childcare Providers - Help Families Find You!</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>Your Daycare Information</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Provider Information</h1>

            <h2>Below is your daycare profile information. To make changes, please click <a
                    href="/provider/update?pid=<?php echo $provider->id; ?>">Update</a>.</h2>
            <strong>Name: </strong><?php echo ucwords(strtolower($provider->name)); ?><br />
            <strong>Address:</strong> <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br />

            <strong>Contact Phone: </strong><?php echo $provider->formatPhone; ?><br>
            <?php if($provider->website != "") : ?>
            <strong>Website:</strong> <?php echo $provider->website; ?><br />
            <?php endif; ?>
            <?php if($provider->introduction != "") : ?>
            <strong>Introduction: </strong><?php echo $provider->introduction; ?><br />
            <?php endif; ?>
            <?php if($provider->operation_id != "") : ?>
            <strong>License Number: </strong><?php echo $provider->operation_id; ?><br />
            <?php endif; ?>
            <?php if($provider->capacity > 0) : ?>
            <strong>Capacity: </strong><?php echo $provider->capacity; ?><br />
            <?php endif; ?>
            <?php if($provider->age_range != "") : ?>
            <strong>Age Range:</strong> <?php echo $provider->age_range; ?><br />
            <?php endif; ?>
            <?php if($provider->pricing != "") : ?>
            <strong>Rate Range:</strong> <?php echo $provider->pricing; ?><br />
            <?php endif; ?>
            <?php if($provider->accreditation != "") : ?>
            <strong>Achievement and/or Accreditations:</strong> <?php echo $provider->accreditation; ?><br />
            <?php endif; ?>
            <?php if($provider->daysopen != "") : ?>
            <strong>Days of Operation: </strong><?php echo $provider->daysopen; ?><br />
            <?php endif; ?>
            <?php if($provider->hoursopen != "") : ?>
            <strong>Normal Open Hours: </strong><?php echo $provider->hoursopen; ?><br />
            <?php endif; ?>
            <?php if($provider->subsidized != "") : ?>
            <strong>Enrolled in Subsidized Child Care Program:</strong> <?php echo $provider->subsidized == 1 ? 'Yes' : 'No'; ?><br />
            <?php endif; ?>
            <?php if($provider->language != "") : ?>
            <strong>Languages Supported: </strong><?php echo $provider->language; ?><br />
            <?php endif; ?>
            <?php if($provider->schools_served != "") : ?>
            <strong>Schools Served: </strong><?php echo $provider->schools_served; ?><br />
            <?php endif; ?>
            <?php if($provider->typeofcare != "") : ?>
            <strong>Type of Care:</strong> <?php echo $provider->typeofcare; ?><br />
            <?php endif; ?>
            <?php if($provider->transportation != "") : ?>
            <strong>Transportation: </strong><?php echo $provider->transportation; ?><br />
            <?php endif; ?>
            <?php if($provider->additionalinfo != "") : ?>
            <strong>Additional Information: </strong><?php echo $provider->additionalinfo; ?><br />
            <?php endif; ?>

        </section>
        <!---------right container------>
        <section class="right-sect">

            Hello, <strong><?php echo $user->firstname ?: $user->email; ?></strong>

            <div class="listSidebar">
                <h2>Actions</h2>
                <!-- <a class="btn m-t-10" href="/user/profile">Profile Information</a> -->
                <a class="btn m-t-10" href="/provider/update?pid=<?php echo $provider->id; ?>">Update Daycare Information</a>

                <?php if ($user && $user->multi_listings) :?>
                <a class="btn m-t-10" href="/provider/find">Find Your Childcare</a>
                <?php endif;?>

                <a class="btn m-t-10" href="/provider/imageupload?pid=<?php echo $provider->id; ?>">Upload Logo and Images</a>
                <a class="btn m-t-10" href="/provider/update-operation-hours?pid=<?php echo $provider->id; ?>">Update Operation
                    Hours</a>
                <a class="btn m-t-10" href="/reviews/view?pid=<?php echo $provider->id; ?>">View Review History</a>
                <a class="btn m-t-10" href="/inspection/view?pid=<?php echo $provider->id; ?>">View Inspection History</a>
                <a class="btn m-t-10" target="blank" href="/jobs/newjob">Post Your Job Requirements</a>
                <a class="btn m-t-10" href="/user/logout">Sign Out</a>

            </div>

            <div class="listSidebar">
                <h2>Notes:</h2>
                <ol>
                    <ol>
                        <?php if($provider->approved == 0): ?>
                        <li>This daycare has not been approved yet. Please allow 2-3 business days for review.</li>
                        <?php elseif($provider->approved < 0): ?>
                        <li>Your daycare is not approved for listing on our website.</li>
                        <?php else:?>
                        <li>Your daycare profile page has been visited <?php echo $provider->visits; ?> times.</li>
                        <?php endif;?>
                    </ol>
                </ol>
            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
