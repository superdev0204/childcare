@push('title')
    <title>Verify Email Address | Job Posting</title>
@endpush

@extends('layouts.app')

@section('content')
    <div class="container">
        <!---------left container------>
        <section class="left-sect">
            <a href="/">Home</a>  &gt;&gt; <a href="/resumes">Child Care Resumes</a>&gt;&gt; Verify Resumes
            <div class="widget">
                <h4><?php echo $message ?></h4>
            </div>
        </section>
        <!---------right container ends------>
        <section class="right-sect">            
        </section>
        <!---------right-container------>

        <!-------right container ends------>
    </div>
@endsection
