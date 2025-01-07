@push('meta')
    <meta name="description" content="Search for child care providers from our inventory of over 270,000 listings">
@endpush

@push('title')
    <title>Search for Child Care Providers</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <?php if (isset($providers)): ?>
                <li><a href="/search">Search Form</a> &gt;&gt; </li>
                <li>Search Results</li>
                <?php else: ?>
                <li>Search Form</li>
                <?php endif; ?>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Search for Childcare or Home Daycare Provider</h1>

            <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
            <?php endif ?>

            <form method="post">
                @csrf
                <dl class="child-srch">
                    <dd id="requiredfields-element">
                        <fieldset id="fieldset-requiredfields" style="padding:10px">
                            <legend>Required Fields (Enter ZIP Code or City/State)</legend>
                            <dl>
                                <dt id="type-label"><label for="type">I'm looking for:</label></dt>
                                <dd id="type-element">
                                    <label><input type="radio" name="type" value="center" checked="">Childcare Center</label>
                                    <label><input type="radio" name="type" value="home">Home Daycare</label>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group-bs">
                                                <div id="zip-label"><label for="zip">Zip Code:</label></div>
                                                <div id="zip-element">
                                                    @if (isset($request->zip))
                                                        <input type="text" id="zip" name="zip" value="{{ $request->zip }}">
                                                    @else
                                                        <input type="text" id="zip" name="zip" value="{{ old('zip') }}">
                                                    @endif
                                                    @error('zip')
                                                        <ul>
                                                            <li>{{ $message }}</li>
                                                        </ul>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6">
                                            <div class="form-group-bs">
                                                <div id="location-label"><label for="location">Or City/State (i.e Orlando,FL):</label></div>
                                                <div id="location-element">
                                                    @if (isset($request->location))
                                                        <input type="text" id="location" name="location" value="{{ $request->location }}">
                                                    @else
                                                        <input type="text" id="location" name="location" value="{{ old('location') }}">
                                                    @endif
                                                    @error('location')
                                                        <ul>
                                                            <li>{{ $message }}</li>
                                                        </ul>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </dd>
                            </dl>
                        </fieldset>
                    </dd>
                    <dt id="optionalfields-label">&nbsp;</dt>
                    <dd id="optionalfields-element">
                        <fieldset id="fieldset-optionalfields" style="padding:10px">
                            <legend>Optional Fields</legend>
                            <dl>
                                <dt id="name-label"><label for="name">Name:</label></dt>
                                <dd id="name-element">
                                    @if (isset($request->name))
                                        <input type="text" id="name" name="name" value="{{ $request->name }}">
                                    @else
                                        <input type="text" id="name" name="name" value="{{ old('name') }}">
                                    @endif
                                    @error('name')
                                        <ul>
                                            <li>{{ $message }}</li>
                                        </ul>
                                    @enderror
                                </dd>
                                <dt id="address-label"><label for="address">Address:</label></dt>
                                <dd id="address-element">
                                    @if (isset($request->address))
                                        <input type="text" id="address" name="address" value="{{ $request->address }}">
                                    @else
                                        <input type="text" id="address" name="address" value="{{ old('address') }}">
                                    @endif
                                    @error('address')
                                        <ul>
                                            <li>{{ $message }}</li>
                                        </ul>
                                    @enderror
                                </dd>
                                <dt id="phone-label"><label for="phone">Phone:</label></dt>
                                <dd id="phone-element">
                                    @if (isset($request->phone))
                                        <input type="text" id="phone" name="phone" value="{{ $request->phone }}">
                                    @else
                                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                                    @endif
                                    @error('phone')
                                        <ul>
                                            <li>{{ $message }}</li>
                                        </ul>
                                    @enderror
                                </dd>
                            </dl>
                        </fieldset>
                    </dd>
                    <dt id="search-label">&nbsp;</dt>
                    <dd id="search-element"><input type="submit" name="submit" id="search" value="Search"></dd>
                </dl>
            </form>

            <?php if (isset($providers) && count($providers)):?>
            <div class="up-section head">
                <?php
                $i=0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider):
                    $i++;
                    ?>
                <div class="update">
                    <a href="/provider_detail/<?php echo $provider->filename; ?>"><img src="<?php echo $this->formatLogoURL($provider->logo, $provider->id, $i % 9); ?>" alt="<?php echo $provider->name; ?>"
                            height="150" width="200" /></a>
                    <h3><a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo htmlentities($provider->name); ?></a>
                        <div class="review">
                            <?php if($provider->avg_rating<>'') : ?>
                            <?php for ($j=0;  $j<5; $j++):?>
                            <?php if ($provider->avg_rating-$j>0.5):?>
                            <i class="zmdi zmdi-star star"></i>
                            <?php elseif ($provider->avg_rating-$j==0.5):?>
                            <i class="zmdi zmdi-star-half star"></i>
                            <?php else: ?>
                            <i class="zmdi zmdi-star-outline star"></i>
                            <?php endif; ?>
                            <?php endfor;?>
                            <?php endif; ?>
                        </div>
                    </h3>
                    <span>
                        <?php echo ucwords(strtolower($provider->address)) . ', ' . ucwords(strtolower($provider->city)) . ' ' . $provider->state . ' | ' . $this->formatPhoneNumber($provider->phone); ?>
                        <?php if($provider->capacity > 0) :?>
                        | Capacity: <?php echo $provider->capacity; ?> Children
                        <?php endif;?>
                    </span>
                    <p>
                        <?php
                        $description = strip_tags($this->formatProviderDescription($provider));
                        if (strlen($description) > 300) {
                            $description = substr($description, 0, strpos($description, ' ', 290)) . ' ...';
                        }
                        echo $description;
                        ?>
                    </p>
                </div>
                <?php endforeach;?>
            </div>
            <?php endif;?>

            <?php if(isset($cities) && count($cities)):?>
            <ul>
                <?php
            /** @var \Application\Domain\Entity\City $city */
            foreach ($cities as $city): ?>
                <?php if($type == "center"):?>
                <li><a href="/<?php echo $city->statefile; ?>/<?php echo $city->filename; ?>_childcare"><?php echo $city->city . ', ' . $city->state; ?></a></li>
                <?php else: ?>
                <li><a href="/<?php echo $city->statefile; ?>_homecare/<?php echo $city->filename; ?>_city"><?php echo $city->city . ', ' . $city->state; ?></a></li>
                <?php endif; ?>
                <?php endforeach;?>
            </ul>
            <?php endif;?>

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
