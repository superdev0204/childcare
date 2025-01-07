@extends('layouts.app_old')

@section('content')
    <div id="content" class="clearfix">
        <div id="right">
            <div class="widget">
                Hi, <?php echo $user->firstname ?: $user->email; ?>
                <a href="/user/logout">Sign Out</a>
            </div>
            @include('admin.right_panel')
        </div>
        <div id="left">
            <a href="/">Home</a> &gt;&gt; Childcare Providers
            <h2>New childcare center and home daycares</h2>
            <table width="100%">
                <tr>
                    <th>Action</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
                <?php
            $i = 0;
            /** @var \Application\Domain\Entity\Facility $provider */
            foreach ($providers as $provider): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td>
                        <form method="post" action="/admin/provider/approve">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                            <input type="submit" value="Approve" />
                        </form><br/>
                        <form method="get" action="/admin/provider/edit">
                            <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                            <input type="submit" value="Update" />
                        </form><br/>
                    </td>
                    <td width="30%">
                        <a target="_blank" href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a><br />
                        <?php echo $provider->phone; ?><br/>
                        <?php echo $provider->is_center ? 'CENTER' : 'HOME'; ?>
                    </td>
                    <td>
                        <?php echo $provider->address; ?> <br />
                        <?php echo $provider->city . ', ' . $provider->state . ' ' . $provider->zip; ?>
                    </td>
                    <td>
                        <form method="post" action="/admin/provider/disapprove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                            <input type="submit" value="Not Approve" />
                        </form><br/>
                        <form method="post" action="/admin/provider/delete">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                            <input type="submit" value="Delete" />
                        </form><br/>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
            <h2>New updates</h2>
            <table width="100%">
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                </tr>
                <?php
            $i = 0;
            /** @var \Application\Domain\Entity\FacilityLog $providerLog */
            foreach ($providerLogs as $providerLog): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="40%">
                        <a href="/admin/provider-log/show/id/<?php echo $providerLog->id; ?>"><?php echo $providerLog->name; ?></a>
                    </td>
                    <td>
                        <?php echo $providerLog->address; ?>
                    </td>
                    <td>
                        <?php echo $providerLog->phone; ?>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
            <h2>New Classifieds</h2>
            <table width="100%">
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <th>Name</th>
                    <th>Location</th>
                    <th>Details</th>
                    <th>Approve?</th>
                </tr>
                <?php
            $i = 0;
            /** @var \Application\Domain\Entity\Classified $classified */
            foreach ($classifieds as $classified): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="40%">
                        <a target="_blank"
                            href="/classifieds/addetails?id=<?php echo $classified->id; ?>"><?php echo $classified->summary; ?></a><br />
                        <?php echo $classified->created; ?><br />
                        <?php if ($classified->email_verified):?>
                        Email Verified
                        <?php endif;?>
                    </td>
                    <td>
                        <?php echo $classified->name; ?>
                        <?php echo $classified->city . ', ' . $classified->state . ' ' . $classified->zip; ?>
                    </td>
                    <td>
                        <?php echo $classified->detail; ?>
                    </td>
                    <td>
                        <form method="post" action="/admin/classified/approve">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $classified->id; ?>" />
                            <input type="submit" value="Approve" />
                        </form>
                        <form method="post" action="/admin/classified/disapprove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $classified->id; ?>" />
                            <input type="submit" value="Not Approve" />
                        </form>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
            <h2>New Reviews</h2>
            <table width="100%">
                <tr>
                    <th>Name</th>
                    <th>By</th>
                    <th>Comments</th>
                    <th>Action</th>
                </tr>
                <?php
            $i = 0;
            /** @var \Application\Domain\Entity\Review $review */
            foreach ($reviews as $review): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="20%">
                        <a target="_blank" href="/provider_detail/<?php echo $review->facility_filename; ?>"><?php echo $review->facility_name; ?></a><br />
                    </td>
                    <td width="30%" nowrap>
                        Reviewed by: <?php echo $review->review_by; ?><br />
                        Rated: <?php echo $review->rating . ' star '; ?><br />
                        Email: <?php echo $review->email; ?> <br />
                        Posted Date: <?php echo $review->review_date; ?><br />
                        IP Address: <?php echo $review->ip_address; ?><br />
                        Email Verified: <?php echo $review->email_verified == 1 ? 'Yes' : 'No'; ?>
                    </td>
                    <td>
                        <?php if($review->approved == 0): ?>
                        <?php echo str_replace("\n", '<br/>', $review->comments); ?>
                        <?php else: ?>
                        <?php echo substr($review->comments, 0, 200); ?>
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if($review->approved == 0): ?>
                        <form method="post" action="/admin/review/approve">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $review->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Approve" />
                        </form><br />
                        <a href="/admin/review?pid=<?php echo optional(optional($review)->provider)->id; ?>">All Reviews</a><br /><br />
                        <form method="post" action="/admin/review/disapprove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $review->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Not Approve" />
                        </form><br />
                        <?php else: ?>
                        Status: Approved
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
            <h2>IP Stats</h2>
            <table width="100%">
                <tr>
                    <th>IP</th>
                    <th>User Agent</th>
                    <th>Current / Total</th>
                </tr>
                <?php $i = 0;
                /** @var \Application\Domain\Entity\Iptracker $ipstat */
                foreach ($ips as $ipstat): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="25%">
                        <?php echo $ipstat->ip; ?><br />
                        <?php echo $ipstat->hour; ?>
                    </td>
                    <td>
                        <?php echo $ipstat->user_agent; ?><br />
                        <?php echo $ipstat->ludate; ?>
                    </td>
                    <td>
                        <?php echo $ipstat->current_count; ?> / <?php echo $ipstat->total_count; ?>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
        </div>
    </div>
@endsection
