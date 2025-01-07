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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Review Responses Statistics
            <h2>New Review Responses</h2>

            <table width="100%">
                <tr>
                    <th>Comment</th>
                    <th>Response</th>
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

                        <?php if ($review->experience <> ''):?>
                        <strong><?php echo $review->experience; ?></strong><br />
                        <?php endif;?>
                        <?php echo $review->comments; ?>
                    </td>
                    <td>
                        <?php echo $review->owner_comment; ?>
                    </td>
                    <td>
                        <form method="post" action="/admin/review/approve-response">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $review->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Approve" />
                        </form><br />
                        <form method="post" action="/admin/review/disapprove-response">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $review->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Not Approve" />
                        </form><br />
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
        </div>
    </div>
@endsection
