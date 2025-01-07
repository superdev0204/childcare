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

            <a href="/">Home</a> &gt;&gt; Profile <br />
            <h2>Below is your profile information. To make changes, please click <a href="/user/update">Update</a>.</h2>
            <?php
            $careType = ['PARENT' => 'Parent', 'CENTER' => 'Childcare Center', 'HOME' => 'Home Daycare'];
            ?>
            <?php if (isset($careType[$user->caretype])): ?>
            <strong>Account Type: </strong><?php echo $careType[$user->caretype]; ?><br />
            <?php else: ?>
            <strong>Account Type: </strong><?php echo $user->caretype; ?><br />
            <?php endif ?>

            <strong>Name: </strong><?php echo $user->firstname . ' ' . $user->lastname; ?><br />
            <strong>Email: </strong><?php echo $user->email; ?><br />
            <strong>City: </strong><?php echo $user->city; ?><br />
            <strong>State: </strong><?php echo $user->state; ?><br />
            <strong>Zip Code: </strong><?php echo $user->zip; ?><br />
            <strong>Last Login: </strong><?php echo $user->login; ?><br /><br />

            <h2>Jobs Posted by You</h2>
            <?php if(count($jobs)>0):?>
                <table width="100%">
                    <tr>
                        <th>Job</th>
                        <th>Description</th>
                        <th>Requirements</th>
                        <th>Action</th>
                    </tr>
                    <?php $i = 0;
                    /** @var \Application\Domain\Entity\Job $job */
                    foreach ($jobs as $job): ?>
                    <tr class="d<?php echo $i % 2;
                    $i++; ?>">
                        <td width="30%">
                            <a target="_blank" href="/jobs/jobdetail?id=<?php echo $job->id; ?>"><?php echo $job->title . ' - ' . $job->company; ?></a><br />
                            <?php echo $job->city . ', ' . $job->state . ' ' . $job->zip; ?><br />
                            <?php echo $job->approved == 1 ? 'Approved' : 'Pending Review'; ?>
                        </td>
                        <td>

                            <?php echo $job->description; ?>
                        </td>
                        <td>
                            <?php echo $job->requirements; ?>
                        </td>
                        <td>
                            <a href="/jobs/update?id=<?php echo $job->id; ?>">Update</a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </table><br />
            <?php endif; ?>

        </section>
        <!---------right container------>
        <section class="right-sect">

            Hello, <strong><?php echo $user->firstname ?: $user->email; ?></strong>

            <div class="listSidebar">
                <h2>Actions</h2>
                <?php if (isset($user->multi_listings)) :?>
                <a class="btn m-t-10" href="/provider/find">Find Your Childcare</a>
                <?php endif;?>

                <a class="btn m-t-10" href="/user/password">Change Password</a>

                <?php if ($user->is_provider): ?>
                <a class="btn m-t-10" href="/provider/update?pid=<?php echo optional(optional($user)->provider)->id; ?>">Update Daycare Information</a>
                <a class="btn m-t-10" href="/provider/imageupload?pid=<?php echo optional(optional($user)->provider)->id; ?>">Upload Logo and Images</a>
                <a class="btn m-t-10" href="/provider/update-operation-hours?pid=<?php echo optional(optional($user)->provider)->id; ?>">Update Operation
                    Hours</a>
                <a class="btn m-t-10" href="/reviews/view?pid=<?php echo optional(optional($user)->provider)->id; ?>">View Review History</a>
                <a class="btn m-t-10" href="/inspection/view?pid=<?php echo optional(optional($user)->provider)->id; ?>">View Inspection History</a>
                <a class="btn m-t-10" target="blank" href="/jobs/newjob">Post Your Job Requirements</a>
                <?php endif ?>

                <a class="btn m-t-10" href="/user/logout">Sign Out</a>

            </div>

            <div class="listSidebar">
                <h2>Notes:</h2>
                <ol>
                    <ol>
                        <?php if(optional(optional($user)->provider)->approved == 0): ?>
                        <li>This daycare has not been approved yet. Please allow 2-3 business days for review.</li>
                        <?php elseif(optional(optional($user)->provider)->approved < 0): ?>
                        <li>Your daycare is not approved for listing on our website.</li>
                        <?php else:?>
                        <li>Your daycare profile page has been visited <?php echo optional(optional($user)->provider)->visits; ?> times.</li>
                        <?php endif;?>
                    </ol>
                </ol>
            </div>

        </section>
        <!-------right container ends------>
    </div>
@endsection
