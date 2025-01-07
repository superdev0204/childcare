@push('meta')
    <meta name="description" content="Child Care resumes listings and child care job seekers">
    <meta name="keywords" content="child care workers, childcare resumes">
@endpush

@push('title')
    <title>Child Care Resumes | Childcare Job Seekers</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <!-- End Ezoic - CCC MOINSBD HEADER - top_of_page -->
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home </a> &gt;&gt; </li>
                <li>Child Care Resume</li>
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
                        coords="<?php echo $state->coords; ?>" href="/<?php echo $state->statefile; ?>_resumes"
                        alt="<?php echo $state->state_name; ?> Child Care Center" />
                    <?php endforeach;?>
                </map>
            </div>
            
            <h2>Latest Resumes</h2>
            <table>
                <?php
                /** @var \Application\Domain\Entity\Classified $classified */
                foreach ($resumes as $resume): ?>
                <tr>
                    <td width="40%" valign="top">
                        <a href="/resumes/detail?id=<?php echo $resume->id; ?>"><?php echo $resume->name; ?></a><br />
                        <?php echo $resume->position . ' <br/> ' . $resume->city . ', ' . $resume->state . ' ' . $resume->zip; ?><br />
                    </td>
                    <td valign="top">
                        <strong>Detail:</strong> 
                        <?php
                            $objective = strip_tags($resume->objective);
                            if (strlen($objective) > 180) {
                                $objective = substr($objective, 0, 180) . ' ...';
                            }
                            echo $objective;
                        ?>
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
                    <a href="/jobs" title="Find Child Care Jobs">Find Jobs</a>
                    <a href="/jobs/new" title="Post Child Care Job">Post Job</a>
                    <a href="/resumes/new" title="Post Child Care Resume">Post Resume</a>
                </div>
            </div>

        </section>
        <!-------right container ends------>
        {{-- <section class="right-sect">
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
        </section> --}}
    </div>
@endsection
