@push('meta')
    <meta name="description"
        content="ChildcareCenter.us is the foremost destination online for qualified, family-friendly childcare centers across the United States. We are the largest directory for childcare services in the nation, with over 250,000 childcare centers, home daycare providers, nannies, and babysitters to give you the best choices possible for your child’s care.">
@endpush

@push('title')
    <title>About Child Care Center .Us</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>About Us</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>About Childcare Center</h1>
            <img src="{{ asset('/images/childcare-logo.png') }}" alt="Child Care Center .Us" width="200" height="200" />
            <p>ChildcareCenter.us is the foremost destination online for qualified, family-friendly childcare centers across
                the United States. We are the largest directory for childcare services in the nation, with over 250,000
                childcare centers, home daycare providers, nannies, and babysitters to give you the best choices possible
                for your child’s care.</p>
            <p>Created in 2009, ChildcareCenter.us was designed to be a helpful and friendly resource for parents in search
                of safe and reliable childcare services for their children. The creators of this company are parents
                themselves and understand firsthand just how difficult, time-consuming, and expensive searching for
                acceptable child care can be.</p>
            <p>ChildcareCenter.us aims to make the process the exact opposite – quick, convenient, and free. Parents may
                browse through over 250,000 listings and not only find childcare providers, but also obtain access to
                informative reviews from other parents. All of this is in an effort to give you something other directory
                services do not provide – information about childcare providers that is complete, thorough, and free of
                charge.</p>
            <p>Providers also benefit by updating their services and qualifications on the directory for free, allowing them
                exposure to over 500,000 visitors per month who are searching for the best in affordable and responsible
                child care.</p>
            <p>Use our exhaustive database for free to search for a childcare provider in your state or local area. Read
                reviews for the options provided and learn as much as you can about the providers who are available for your
                child. Share your thoughts and ratings about the providers you use with other parents, who will definitely
                benefit from your opinion. Also, leave feedback for us so that we may continue to offer the highest quality
                in directory assistance for a very important need – reliable childcare services.</p>
            <p>Our goal is to give you the best options available for you. Search for childcare providers today.</p>
        </section>
        <!---------right container------>
        <section class="right-sect">
            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>

            <!-- Ezoic - ChildcareCenter Responsive Low - longer_content -->
            <div id="ezoic-pub-ad-placeholder-110">
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- ChildcareCenter Responsive Low -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="3831518374" data-ad-format="auto"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
            <!-- End Ezoic - ChildcareCenter Responsive Low - longer_content -->

        </section>
        <!-------right container ends------>
    </div>
@endsection
