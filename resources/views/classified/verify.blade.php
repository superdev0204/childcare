@push('title')
    <title>Verify Email Address | Classified Ads Posting</title>
@endpush

@extends('layouts.app_old')

@section('content')
    <div id="content" class="clearfix">
        <div id="right">
            <div class="widget">

            </div>
        </div>
        <div id="left">
            <a href="/">Home</a> &gt;&gt; <a href="/classifieds">Child Care Classifieds</a>&gt;&gt; Verified Email
            Address
            <div class="widget">
                <h4><?php echo $message; ?></h4>
            </div>
        </div>
    </div>
@endsection
