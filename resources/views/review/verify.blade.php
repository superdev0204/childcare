@push('title')
    <title>Verify Email Address | Review</title>
@endpush

@extends('layouts.app_old')

@section('content')
    <!------content--------->
    <div id="content" class="clearfix">
        <div id="right">
            <div class="widget">
    
            </div>
        </div>
        <div id="left">
            <a href="/">Home</a>   &gt;&gt; Create Review
            <h1>Create your review for: </h1>
            <?php echo $provider->name ?><br />
            <?php echo $provider->address . " " . $provider->city . " " . $provider->state . " " . $provider->zip ?><br />
            <div class="widget">
                <h4>
                    Thank you for verify your email address.  Your comment will be reviewed for approval within 1-2 business days.<br/> 
                    <a href="/provider_detail/<?php echo  $provider->filename?>">Return to listing details</a>
                </h4>
            </div>
        </div>
    </div>
@endsection
