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
                    <th>Name/Misc</th>
                    <th>Comments</th>
                </tr>
                <?php
            $i = 0;
            /** @var \Application\Domain\Entity\Testimonial $feedback */
            foreach ($feedbacks as $feedback): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td>
                        <?php echo $i; ?>. Name: <?php echo $feedback->name; ?><br />
                        Location: <?php echo $feedback->location; ?><br />
                        Date: <?php echo $feedback->date; ?> <br />
                        Email: <?php echo $feedback->email; ?> <br />
                        Email Verified: <?php echo $feedback->email_verified == 1 ? 'Yes' : 'No'; ?><br /><br />

                        <form method="post" action="/admin/feedback/disapprove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $feedback->id; ?>" />
                            <input type="hidden" name="type" value="FEEDBACKU" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Valid Request - Info Updated" />
                        </form><br />

                        <form method="post" action="/admin/feedback/spam">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $feedback->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="SPAM - Delete" />
                        </form><br />
                    </td>
                    <td>
                        <?php if ($feedback->pros <> ''):?>
                        <strong>Like:</strong> <?php echo $feedback->pros; ?><br />
                        <?php endif; ?>
                        <?php if ($feedback->cons <> ''):?>
                        <strong>Dislike:</strong> <?php echo $feedback->cons; ?><br />
                        <?php endif;?>
                        <?php if ($feedback->suggestion <> ''):?>
                        <strong>Suggestion:</strong> <?php echo $feedback->suggestion; ?><br />
                        <?php endif;?>
                        <?php if ($feedback->comments <> 'suggestion'):?>
                        <strong>Comments:</strong> <?php echo $feedback->comments; ?>
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
        </div>
    </div>
@endsection
