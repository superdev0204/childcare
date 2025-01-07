<?php
/** @var \Application\Domain\Entity\Facility $provider */
$provider = $review->provider;
?>

@push('title')
    <title>Review History</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a> &gt;&gt; </li>
                <li><a href="/reviews/view?pid=<?php echo $provider->id; ?>">Review History</a> &gt;&gt; </li>
                <li>Response to Review</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Response to a Review</h1>

            <h2>Write your response for the review below:</h2>

            <div class="comment">
                <div class="comment-header">
                    <div><strong>Author:</strong> <?php echo $review->review_by; ?></div>
                    <div><strong>Date:</strong> <?php echo $review->review_date; ?></div>
                    <div><strong>Stars:</strong> <?php echo $review->rating; ?></div>
                    <div><strong>Experience:</strong><?php if ($review->experience != '' && $review->experience != 'Other') {
                        echo '<i>' . $review->experience . '</i>';
                    } ?></div>
                </div>
                <div class="comment-body">
                    <?php if (preg_match('/(<br>|<p>|<br\/>|<\/p>|<span>)/', $review->comments)) {
                        echo $review->comments;
                    } else {
                        echo str_replace("\n", '<br/>', $review->comments);
                    } ?>
                </div>
                <?php if ($review->owner_comment):?>
                <div class="comment" style="border-bottom: 0; margin: 0 0 0 25px; padding: 0">
                    <div class="comment-header">
                        <div>
                            <strong>Owner Response</strong>
                            <?php if (!$review->owner_comment_approved): ?>
                            <span style="color: #868e96">Waiting For Approving</span>
                            <?php endif; ?>
                        </div>
                        <div><strong>Date:</strong> <?php echo $review->owner_comment_date; ?></div>
                    </div>
                    <div class="comment-body">
                        <?php echo $review->owner_comment; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (isset($message)): ?>
            <h4><?php echo $message; ?></h4>
            <?php endif; ?>

            <form method="post">
                @csrf
                <dl class="zend_form">
                    <dt id="owner_comment-label"><label class="required" for="owner_comment">Write your response:</label></dt>
                    <dd id="owner_comment-element">
                        @if (isset($request->owner_comment))
                            <textarea cols="15" rows="5" id="owner_comment" name="owner_comment">{{ $request->owner_comment }}</textarea>
                        @else
                            <textarea cols="15" rows="5" id="owner_comment" name="owner_comment">{{ old('owner_comment') }}</textarea>
                        @endif
                        @error('owner_comment')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="addReview-label">&nbsp;</dt>
                    <dd id="addReview-element"><input type="submit" name="submit" value="Add Response"></dd>
                </dl>
            </form>
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
                <a class="btn m-t-10" href="/inspection/view?pid=<?php echo $provider->id; ?>">View Inspection History</a>
                <a class="btn m-t-10" href="/reviews/view?pid=<?php echo $provider->id; ?>">View Review History</a>
                <a class="btn m-t-10" href="/jobs/newjob">Post Your Job Requirements</a>
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
