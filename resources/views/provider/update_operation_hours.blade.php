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
                <li><a href="/provider?pid=<?php echo $provider->id; ?>">Providers</a> &gt;&gt; </li>
                <li>Update Operation Hours</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Update Operation Hours - <?php echo $provider->name; ?></h1>

            <?php if (isset($message)) :?>
            <p><?php echo $message; ?></p>
            <?php endif;?>

            <form method="post">
                @csrf
                <dl class="zend_form">
                    <dt id="monday-label"><label for="monday">Monday:</label></dt>
                    <dd id="monday-element">
                        <input id="monday" name="monday" value="{{ optional(optional($provider)->operationHours)->monday }}">
                    </dd>
                    <dt id="tuesday-label"><label for="tuesday">Tuesday:</label></dt>
                    <dd id="tuesday-element">
                        <input id="tuesday" name="tuesday" value="{{ optional(optional($provider)->operationHours)->tuesday }}">
                    </dd>
                    <dt id="wednesday-label"><label for="wednesday">Wednesday:</label></dt>
                    <dd id="wednesday-element">
                        <input id="wednesday" name="wednesday" value="{{ optional(optional($provider)->operationHours)->wednesday }}">
                    </dd>
                    <dt id="thursday-label"><label for="thursday">Thursday:</label></dt>
                    <dd id="thursday-element">
                        <input id="thursday" name="thursday" value="{{ optional(optional($provider)->operationHours)->thursday }}">
                    </dd>
                    <dt id="friday-label"><label for="friday">Friday:</label></dt>
                    <dd id="friday-element">
                        <input id="friday" name="friday" value="{{ optional(optional($provider)->operationHours)->friday }}">
                    </dd>
                    <dt id="saturday-label"><label for="saturday">Saturday:</label></dt>
                    <dd id="saturday-element">
                        <input id="saturday" name="saturday" value="{{ optional(optional($provider)->operationHours)->saturday }}">
                    </dd>
                    <dt id="sunday-label"><label for="sunday">Sunday:</label></dt>
                    <dd id="sunday-element">
                        <input id="sunday" name="sunday" value="{{ optional(optional($provider)->operationHours)->sunday }}">
                    </dd>
                    <dt id="submit-label">&nbsp;</dt>
                    <dd id="submit-element"><input type="submit" name="submit" value="Update"></dd>
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
