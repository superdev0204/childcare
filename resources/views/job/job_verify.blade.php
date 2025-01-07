@push('title')
    <title>Verify Email Address | Job Posting</title>
@endpush

@extends('layouts.app')

@section('content')
    <div class="container">
        <!---------left container------>
        <section class="left-sect">
            <a href="/">Home</a>  &gt;&gt; <a href="/jobs">Child Care Jobs</a>&gt;&gt; Add New Job
            <div class="widget">
                <h4><?php echo $message ?></h4>
            </div>
        </section>
        <!---------right container------>
        <section class="right-sect">
            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>
            <div class="listSidebar">
                <h3>Quick Links</h3>
                <div class="quick-links">
                    <a href="/resumes" title="Find Child Care Resumes">Find Resumes</a>
                    <a href="/jobs/new" title="Post Child Care Job">Post Job</a>
                    <a href="/resumes/new" title="Post Child Care Resume">Post Resume</a>
                </div>
            </div>

        </section>
        <!-------right container ends------>
    </div>
@endsection
