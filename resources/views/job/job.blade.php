@push('meta')
    <meta name="description" content="Child Care jobs and child care resume listings">
    <meta name="keywords" content="child care jobs, child care workers, childcare employee">
@endpush

@push('title')
    <title>Child Care Jobs | Child Care Career | Job Seekers</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <!-- End Ezoic - CCC MOINSBD HEADER - top_of_page -->
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home </a> &gt;&gt; </li>
                <li>Child Care Job</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <div class="map-section2 head">
                <div class="drg">
                    <h2>Browse by State</h2>
                    <img src="{{ asset('/images/childcare-map.png') }}" width="636" height="310"
                        alt="Map of Childcare Centers in the United States" usemap="#map" />
                </div>
                <map name="map" id="map">
                    <?php 
                /** @var \Application\Domain\Entity\State $state */
                foreach ($states as $state): ?>
                    <area shape="poly" title="<?php echo $state->state_name; ?> Childcare Centers (<?php echo number_format($state->center_count); ?> providers)"
                        coords="<?php echo $state->coords; ?>" href="/<?php echo $state->statefile; ?>_jobs"
                        alt="<?php echo $state->state_name; ?> Child Care Center" />
                    <?php endforeach;?>
                </map>
            </div>
            <p>Are you looking to hire a nanny, teacher, or caregiver for your child?</p>
            <p>Maybe you want to use your skills to care for and/or teach children?</p>
            <p>If so, you’ve come to the right place! Our passion is connecting people searching for child care jobs with
                those who are hiring! Whether you are an after school education program, a day school, or a day care, we
                offer a place for you to find the perfect hire to fit your needs.</p>
            <p>For those looking for work, post your resume today. Tell employers exactly why they should hire you to be a
                part of their family or program!</p><br /><br />

            <h2>New JOB Listing</h2>
            <table>
                <?php
                /** @var \Application\Domain\Entity\Classified $classified */
                foreach ($jobs as $job): ?>
                <tr>
                    <td width="40%" valign="top">
                        <a href="/jobs/detail?id=<?php echo $job->id; ?>"><?php echo $job->title; ?></a><br />
                        <?php echo $job->company . ' <br/> ' . $job->city . ', ' . $job->state . ' ' . $job->zip; ?><br />
                    </td>
                    <td valign="top">
                        <?php echo substr(strip_tags($job->description), 0, 240); ?>...
                    </td>
                </tr>
                <?php endforeach;?>
            </table>
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
        {{-- <section class="right-sect">
            <div id="fb-root"></div>
            <script>
                (function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) {
                        return;
                    }
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            </script>

            <div class="fb-like-box" data-href="https://www.facebook.com/pages/ChildcareJoborg/100655986709828"
                data-width="250" data-show-faces="true" data-stream="false" data-header="true"></div>
            <div>
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- ChildcareJob All Pages Adlinks -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="1153705179" data-ad-format="link"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <!-- ChildcareJob Responsive -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                    data-ad-slot="8406225575" data-ad-format="auto"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </section> --}}
    </div>
@endsection
