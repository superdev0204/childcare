@push('title')
    <title>Childcare Providers - Help Families Find You!</title>
@endpush

@extends('layouts.app')

@section('content')
    <div id="content" class="clearfix">
        <div id="right">
            Hello, <strong><?php echo $user->firstname ?: $user->email; ?></strong>
            <div class="widget">
                <h4>Actions</h4>
                <?php if ($user->multi_listings) :?>
                <a href="/provider/find">Find Your Childcare Listings</a>
                <?php endif; ?>
                <a href="/user/logout">Sign Out</a>
            </div>
        </div>
        <div id="left">
            <a href="/">Home</a> &gt;&gt;
            <a href="/provider">Childcare Providers</a> &gt;&gt; Childcare Listings

            <div>
                <h2>Your Childcare Listings</h2>
            </div>
            <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if (count($providers)):?>
            <table width="100%">
                <tr>
                    <td colspan="2" align="left">
                        @if ($providers instanceof Illuminate\Pagination\LengthAwarePaginator)
                            {{ $providers->links() }}
                        @endif
                    </td>
                    {{-- <td colspan="2" align="right">
                        <?php if (isset($providers->getPages()->next)): ?>
                        <a href="/provider?page=<?php echo $providers->getPages()->next; ?>">
                            Next Page &gt;
                        </a>
                        <?php endif; ?>
                    </td> --}}
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Your Daycare?</th>
                </tr>
                <?php $i = 0;
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="40%"><a target="_blank"
                            href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo ucwords(strtolower($provider->name)); ?></a></td>
                    <td>
                        <?php echo ucwords(strtolower($provider->address)); ?> <br />
                        <?php echo ucwords(strtolower($provider->city)) . ', ' . $provider->state . ' ' . $provider->zip; ?>
                    </td>
                    <td><?php echo $provider->phone; ?></td>
                    <td>
                        <a href="/provider/update?pid=<?php echo $provider->id; ?>">Update</a>
                    </td>
                </tr>

                <?php endforeach;?>
            </table><br />
            <?php endif; ?>
            <div>
                <h2>Find Your Childcare Listings</h2>
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
        </div>
    </div>
@endsection
