@push('title')
    <title>Childcare Providers - Help Families Find You!</title>
@endpush

@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>Childcare Provider</li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h3>You child care may already be listed. Use the search below to find your child care listing.</h3>
            <?php if (isset($message)) :?>
            <div class="error">
                <p><?php echo $message; ?></p>
            </div>
            <?php endif;?>

            <h2>Is your day care already listed below?</h2>
            <table width="100%">
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Your Daycare?</th>
                </tr>
                <?php
                $i = 0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="40%"><a target="_blank" href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo ucwords(strtolower($provider->name)); ?></a>
                    </td>
                    <td>
                        <?php echo ucwords(strtolower($provider->address)); ?> <br />
                        <?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip; ?>
                    </td>
                    <td><?php echo $provider->phone; ?></td>
                    <td>
                        <form method="post" action="/provider/claim">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                            <input type="submit" name="update" value="Claim/Update" />
                        </form>
                    </td>
                </tr>

                <?php endforeach;?>
            </table><br />

            <div>
                <h2>Don't see your childcare above? Use the form below to find your listing</h2>
            </div>
            <form method="post" action="/provider/find">
                @csrf
                <dl class="zend_form">
                    <dt id="name-label"><label for="name">Name (contains):</label></dt>
                    <dd id="name-element">
                        <input id="name" name="name" type="text" value="">
                    </dd>
                    <dt id="zip-label"><label for="zip">ZIP Code:</label></dt>
                    <dd id="zip-element">
                        <input id="zip" name="zip" type="text" value="{{ $user->zip }}">
                    </dd>
                    <dt id="city-label"><label for="city">City:</label></dt>
                    <dd id="city-element">
                        <input id="city" name="city" type="text" value="{{ $user->city }}">
                    </dd>
                    <dt id="search-label">&nbsp;</dt>
                    <dd id="search-element">
                        <input type="submit" name="submit" id="search" value="Search">
                    </dd>
                </dl>
            </form>
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
                    <li>You may already be here! Please check to see if your daycare is already listed before choosing Add
                        New Daycare.</li>
                    <li>If your daycare is already listed, click the <strong>Claim/Update</strong> button to claim your
                        listing and start creating your profile page!</li>
                </ol>
            </div>
        </section>
        <!-------right container ends------>
    </div>
@endsection
