@push('title')
    <title>Childcare Providers - Find your child care listing!</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/provider">Childcare Providers</a> &gt;&gt; </li>
                <li>Find Childcare</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <div>
                <h2>Search for Your Childcare Listing</h2>
            </div>
            <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
            <?php endif; ?>

            <form method="post" action="/provider/find">
                @csrf
                <dl class="zend_form">
                    <dt id="name-label"><label for="name">Name (contains):</label></dt>
                    <dd id="name-element">
                        @if (isset($request->name))
                            <input id="name" name="name" type="text" value="{{ $request->name }}">
                        @else
                            <input id="name" name="name" type="text" value="">
                        @endif
                    </dd>
                    <dt id="zip-label"><label for="zip">ZIP Code:</label></dt>
                    <dd id="zip-element">
                        @if (isset($request->zip))
                            <input id="zip" name="zip" type="text" value="{{ $request->zip }}">
                        @else
                            <input id="zip" name="zip" type="text" value="{{ $user->zip }}">
                        @endif
                    </dd>
                    <dt id="city-label"><label for="city">City:</label></dt>
                    <dd id="city-element">
                        @if (isset($request->city))
                            <input id="city" name="city" type="text" value="{{ $request->city }}">
                        @else
                            <input id="city" name="city" type="text" value="{{ $user->city }}">
                        @endif
                    </dd>
                    <dt id="search-label">&nbsp;</dt>
                    <dd id="search-element">
                        <input type="submit" name="submit" id="search" value="Search">
                    </dd>
                </dl>
            </form>
            <?php if(isset($cities) && count($cities)):?>
            <ul>
                <?php
                /** @var \Application\Domain\Entity\City $city */
                foreach ($cities as $city): ?>
                <?php if($type == "center"):?>
                <li><a href="<?php echo route('centercare_city', ['state' => $city->statefile, 'city' => $city->filename]); ?>"><?php echo $city->city . ', ' . $city->state; ?></a></li>
                <?php else: ?>
                <li><a href="<?php echo route('homecare_city', ['state' => $city->statefile, 'city' => $city->filename]); ?>"><?php echo $city->city . ', ' . $city->state; ?></a></li>
                <?php endif; ?>
                <?php endforeach;?>
            </ul>
            <?php endif;?>

            <?php if (isset($providers) && count($providers)):?>
            <table border="1">
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Your Daycare?</th>
                </tr>
                <?php
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider): ?>

                <tr>
                    <td width="45%">
                        <a target="_blank" href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo ucwords(strtolower($provider->name)); ?></a><br />
                        <?php echo $provider->phone; ?>
                    </td>
                    <td>
                        <?php echo ucwords(strtolower($provider->address)); ?> <br />
                        <?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip; ?>
                    </td>
                    <td>
                        <a href="/provider/update?pid=<?php echo $provider->id; ?>">Update</a>
                    </td>
                </tr>

                <?php endforeach;?>
            </table><br />
            <?php endif; ?>

            <?php if (isset($allowAdd) && $allowAdd) : ?>
            <h2>If you child care is not already in our database, you can <a href="/provider/new">Add a New Daycare</a>.
            </h2>
            <?php endif;?>

        </section>
        <!---------right container------>
        <section class="right-sect">
            <?php if ($user): ?>
            Hi, <?php echo $user->first_name ?: $user->email; ?>
            <?php endif; ?>
            <a href="/user/logout">Sign Out</a>
            <div class="listSidebar">

                <h2>Notes:</h2>
                <ol>
                    <li>Please make sure your daycare is not already in the list before adding a new daycare.</li>
                    <li>If your daycare is in the list, click on the <strong>Update</strong> button to make changes.</li>
                </ol>
            </div>

        </section>
        <!-------right container ends------>
    </div>
@endsection
