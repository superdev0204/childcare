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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Reviews Statistics
            <h2>New Reviews</h2>

            <table width="100%">
                <tr>
                    <th>Name</th>
                    <th>Comments</th>
                    <th>Action</th>
                </tr>
                <?php $i = 0;
            /** @var \Application\Domain\Entity\Review $review */
            foreach ($reviews as $review): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="35%">
                        <?php echo $i; ?>. <a target="_blank"
                            href="/provider_detail/<?php echo $review->facility_filename; ?>"><?php echo $review->facility_name; ?></a><br />
                        Rated: <?php echo $review->rating . ' star(s)'; ?> By: <?php echo $review->review_by; ?><br />
                        Email: <?php echo $review->email; ?> <br />
                        Date: <?php echo $review->review_date; ?><br />
                        IP Address: <?php echo $review->ip_address; ?><br />
                        Email Verified: <?php echo $review->email_verified ? 'Yes' : 'No'; ?><br />
                    </td>
                    <td>
                        <?php if ($review->experience <> ''):?>
                        <strong><?php echo $review->experience; ?></strong><br />
                        <?php endif;?>
                        <?php if($review->approved == 0): ?>
                        <?php echo $review->comments; ?>
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
                        <?php if (!isset($provider)): ?>
                        <a href="/admin/review?pid=<?php echo optional(optional($review)->provider)->id; ?>">All Reviews</a><br /><br />
                        <?php endif; ?>
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
        </div>
    </div>
@endsection
