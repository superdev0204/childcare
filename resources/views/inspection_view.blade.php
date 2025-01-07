@push('title')
    <title>Inspection History</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/provider/update?pid=<?php echo $provider->id; ?>"><?php echo $provider->name; ?></a> &gt;&gt; </li>
                <li>Inspection History</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Inspection History</h1>


            <strong>Name: </strong><?php echo ucwords(strtolower($provider->name)); ?><br />
            <strong>Address:</strong> <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br />

            <br />
            <h3>Inspection History</h3>
            <?php if(count($inspections)) : ?>
            <table cellpadding="1" border="1" width="100%">
                <tr>
                    <?php if (preg_match("/(MI|CA)/",$provider->state) == false): ?>
                    <th>Report/Inspection Date</th>
                    <?php endif;?>
                    <?php if (preg_match("/(MI|CA|SC)/",$provider->state) == false): ?>
                    <th>Report/Inspection Type</th>
                    <?php endif;?>
                    <?php if (preg_match("/(DE)/",$provider->state)): ?>
                    <th>Correction Status</th>
                    <th>Corrective Action</th>
                    <?php elseif (preg_match("/(SC)/",$provider->state)): ?>
                    <th>Process</th>
                    <th>Resolved</th>
                    <th>Type</th>
                    <?php elseif (preg_match("/(GA)/",$provider->state)): ?>
                    <th width="20%">Arrival Time</th>
                    <?php elseif (preg_match("/(AR)/",$provider->state)): ?>
                    <th>Details</th>
                    <?php elseif (preg_match("/(KY)/",$provider->state)): ?>
                    <th>Start Time - End time</th>
                    <?php elseif (preg_match("/(CA)/",$provider->state)): ?>
                    <th width="40%">Inspection Dates</th>
                    <th>Citations</th>
                    <?php elseif (preg_match("/(NE)/",$provider->state)): ?>
                    <th width="20%">End Date</th>
                    <?php elseif (preg_match("/(OH)/",$provider->state)): ?>
                    <th>Inspection Status</th>
                    <th>Corrective Action</th>
                    <th>Status Updated</th>
                    <?php elseif (preg_match("/(MI)/",$provider->state) == false): ?>
                    <th>Report Status</th>
                    <?php endif;?>
                </tr>

                <?php
                /** @var \Application\Domain\Entity\Inspection $inspection */
                foreach ($inspections as $inspection): ?>
                <tr>
                    <?php if (preg_match("/(MI|CA)/",$provider->state) == false): ?>
                    <td><?php echo $inspection->report_date; ?></td>
                    <?php endif;?>
                    <td>
                        <?php if($inspection->report_url <> ""):?>
                        <a href="<?php echo $inspection->report_url; ?>" target="blank" rel="nofollow"><?php echo $inspection->report_type; ?></a>
                        <?php else:?>
                        <?php echo $inspection->report_type; ?>
                        <?php endif; ?>
                    </td>

                    <?php if (preg_match("/(MI|AR|KY|CA|NE)/",$provider->state) == false): ?>
                    <td><?php echo $inspection->report_status; ?></td>
                    <?php endif;?>
                    <?php if (preg_match("/(MI|NE|OH)/",$provider->state) == false): ?>
                    <?php if ($inspection->rule_description == ""):?>
                    <td>No Citation</td>
                    <?php else: ?>
                    <td><?php echo $inspection->rule_description; ?></td>
                    <?php endif; ?>
                    <?php endif;?>

                    <?php if (preg_match("/(NE)/",$provider->state)): ?>
                    <td><?php echo $inspection->status_date; ?></td>

                    <?php elseif (preg_match("/(OH)/",$provider->state)): ?>
                    <td><?php echo $inspection->current_status; ?></td>
                    <td><?php echo $inspection->status_date; ?></td>

                    <?php endif; ?>
                </tr>

                <?php if ($inspection->provider_response <> ""):?>
                <tr>
                    <td colspan="5">
                        <strong>Provider Response:</strong> (Contact the State Licensing Office for more information.)<br />
                        <?php echo $inspection->provider_response; ?>
                    </td>
                </tr>
                <?php endif;?>

                <?php endforeach; ?>
            </table>
            <?php else :?>
            <p>No Inspection Reports Available</p>
            <?php endif;?>

        </section>
        <!---------right container------>
        <section class="right-sect">

            Hello, <strong><?php echo $user->firstname ?: $user->email; ?></strong>

            <div class="listSidebar">
                <h2>Actions</h2>
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
