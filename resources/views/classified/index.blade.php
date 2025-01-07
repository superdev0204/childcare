@push('meta')
    <meta name="description" content="Buy, Sell child care items, classify your ads here">
    <meta name="keywords" content="classifieds, advertise">
@endpush

@push('title')
    <title>Child Care Classified | Place your ads here</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>Child Care Classifieds</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Miscellaneous Child Care Ads</h1>

            <p>Please submit your ads here for free if:</p>
            <ul>
                <li>
                    <p>You provide a service to kids that does not belong to other category</p>
                </li>
                <li>
                    <p>You have something to sell or donate, such as toys, books, etc..</p>
                </li>
            </ul>
            <p>To post an ad, click on <a href="/classifieds/newad">Add New Ad</a>.</p>
            <h4>Use the map below to find classified items in your area:</h4>
            <div align="center">
                <map name="map" id="map">
                    <?php
                /** @var \Application\Domain\Entity\State $state */
                foreach ($states as $state): ?>
                    <area shape="poly" title="<?php echo $state->state_name; ?> Child Care Classified" coords="<?php echo $state->coords; ?>"
                        href="/<?php echo $state->statefile; ?>_classifieds" />
                    <?php endforeach;?>
                </map>
                <img usemap="#map" src="{{ asset('/images/usmap.jpg') }}" border="0"
                    alt="Map of Child Care Jobs in the United States" name="usimage" />
            </div>
            <br />
            <?php if(count($classifieds)):?>
            <h2>Latest additions to the classified database:</h2>
            <table>

                <?php
                /** @var \Application\Domain\Entity\Classified $classified */
                foreach ($classifieds as $classified): ?>
                <tr>
                    <td width="40%" valign="top">
                        <a href="/classifieds/addetails?id=<?php echo $classified->id; ?>"><?php echo $classified->summary; ?></a><br />
                        <?php echo $classified->city . ', ' . $classified->state . ' ' . $classified->zip; ?><br />
                        <?php echo $classified->phone; ?>
                    </td>
                    <td valign="top">
                        <strong>Detail:</strong> <?php echo substr($classified->detail, 0, 180); ?>... <a
                            href="/classifieds/addetails?id=<?php echo $classified->id; ?>">more</a>
                    </td>
                </tr>
                <?php endforeach;?>
            </table>
            <?php endif;?>

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
                    <a href="/classifieds/newad">Post New Classified Ad</a>
                    <a href="/provider">Add New Daycare Listing</a>
                    <!-- Ezoic - CCC MOINSBD Link Sidebar - link_side -->
                    <div id="ezoic-pub-ad-placeholder-102">
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- CCC MOINSBD Link Sidebar -->
                        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                            data-ad-slot="8851698836" data-ad-format="link"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    </div>
                    <!-- End Ezoic - CCC MOINSBD Link Sidebar - link_side -->
                </div>
            </div>

        </section>
        <!-------right container ends------>
    </div>
@endsection
