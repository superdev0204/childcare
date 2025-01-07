@extends('layouts.app_old')

@section('content')
    <div id="content" class="clearfix">
        <div id="right">
            <div class="widget">
                Hi, <?php echo $user->firstname ?: $user->email; ?>
                <a href="/user/logout">Sign Out</a>
            </div>
            @include('admin.right_panel')
        </div>
        <div id="left">
            <a href="/">Home</a> &gt;&gt; Search Form
            <div>
                <h2>Search for Childcare or Home Daycare Provider</h2>
            </div>
            <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
            <?php endif; ?>
            <div class="widget">
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="name-label"><label for="name">Provider Name:</label></dt>
                        <dd id="name-element">
                            @if (isset($request->name))
                                <input class="textfield" id="name" name="name" type="text" value="{{ $request->name }}">
                            @else
                                <input class="textfield" id="name" name="name" type="text" value="">
                            @endif
                        </dd>
                        <dt id="phone-label"><label for="phone">Provider Phone:</label></dt>
                        <dd id="phone-element">
                            @if (isset($request->phone))
                                <input class="textfield" id="phone" name="phone" type="text" value="{{ $request->phone }}">
                            @else
                                <input class="textfield" id="phone" name="phone" type="text" value="">
                            @endif
                        </dd>
                        <dt id="address-label"><label for="address">Address:</label></dt>
                        <dd id="address-element">
                            @if (isset($request->address))
                                <input class="textfield" id="address" name="address" type="text" value="{{ $request->address }}">
                            @else
                                <input class="textfield" id="address" name="address" type="text" value="">
                            @endif
                        </dd>
                        <dt id="zip-label"><label for="zip">In ZIP Code (i.e. 33781):</label></dt>
                        <dd id="zip-element">
                            @if (isset($request->zip))
                                <input class="textfield" id="zip" name="zip" type="text" value="{{ $request->zip }}">
                            @else
                                <input class="textfield" id="zip" name="zip" type="text" value="">
                            @endif
                        </dd>
                        <dt id="city-label"><label for="city">City (i.e Orlando):</label></dt>
                        <dd id="city-element">
                            @if (isset($request->city))
                                <input class="textfield" id="city" name="city" type="text" value="{{ $request->city }}">
                            @else
                                <input class="textfield" id="city" name="city" type="text" value="">
                            @endif
                        </dd>
                        <dt id="state-label"><label for="state">State:</label></dt>
                        <dd id="state-element">
                            <select class="textfield" id="state" name="state">
                                <option value="">-Select-</option>
                                @foreach ($states as $state)
                                    @if (isset($request->state))
                                        @if ($state->state_code == $request->state)
                                            <option value='{{ $state->state_code }}' selected>
                                                {{ $state->state_name }}
                                            </option>
                                        @else
                                            <option value='{{ $state->state_code }}'>{{ $state->state_name }}</option>
                                        @endif
                                    @else
                                        <option value='{{ $state->state_code }}'>{{ $state->state_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </dd>
                        <dt id="type-label"><label for="type">Provider Type:</label></dt>
                        <dd id="type-element">
                            @if (isset($request->type))
                                @if ($request->type == "center")
                                    <label><input type="radio" name="type" value="center" checked="">Childcare Center</label>
                                    <label><input type="radio" name="type" value="home">Home Daycare</label>
                                @else
                                    @if ($request->type == "home")
                                        <label><input type="radio" name="type" value="center">Childcare Center</label>
                                        <label><input type="radio" name="type" value="home" checked="">Home Daycare</label>
                                    @else
                                        <label><input type="radio" name="type" value="center">Childcare Center</label>
                                        <label><input type="radio" name="type" value="home">Home Daycare</label>
                                    @endif
                                @endif
                            @else
                                <label><input type="radio" name="type" value="center">Childcare Center</label>
                                <label><input type="radio" name="type" value="home">Home Daycare</label>
                            @endif
                        </dd>
                        <dt id="email-label"><label for="email">Email address:</label></dt>
                        <dd id="email-element">
                            @if (isset($request->email))
                                <input class="textfield" id="email" name="email" type="email" value="{{ $request->email }}">
                            @else
                                <input class="textfield" id="email" name="email" type="email" value="">
                            @endif
                        </dd>
                        <dt id="id-label"><label for="id">Provider ID:</label></dt>
                        <dd id="id-element">
                            @if (isset($request->id))
                                <input class="textfield" id="id" name="id" type="text" value="{{ $request->id }}">
                            @else
                                <input class="textfield" id="id" name="id" type="text" value="">
                            @endif
                        </dd>
                        <dt id="search-label">&nbsp;</dt>
                        <dd id="search-element">
                            <input type="submit" name="search" id="search" value="Search">
                        </dd>
                    </dl>
                </form>
            </div>

            <?php if (isset($providers)):?>
            <table width="100%">
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
                <?php $i = 0; 
                /** @var \Application\Domain\Entity\Facility $provider */
                foreach ($providers as $provider): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="40%">
                        <a target="_blank" href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a><br />
                        <?php echo $provider->phone; ?>
                    </td>
                    <td>
                        <?php echo $provider->address; ?> <br />
                        <?php echo $provider->city . ', ' . $provider->state . ' ' . $provider->zip; ?>
                    </td>
                    <td>
                        <?php echo $provider->is_center ? 'CENTER' : 'HOME'; ?><br />
                        <a href="/admin/review/find?pid=<?php echo $provider->id; ?>">All Reviews</a>
                    </td>
                    <td>
                        <?php if (count($providers) == 1 && $provider->approved >= 0) : ?>
                        <?php if (!$provider->is_center):?>
                        <form method="post" action="/admin/provider/delete">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                            <input type="submit" name="delete" value="Delete " />
                        </form><br />
                        <?php endif;?>
                        <form method="post" action="/admin/provider/inactivate">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                            <input type="submit" name="update" value=" Close " />
                        </form><br />
                        <?php endif; ?>
                        <form method="get" action="/admin/provider/edit">
                            <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                            <input type="submit" value=" Update " />
                        </form><br />
                        <form method="get" action="/admin/provider/update-operation-hours">
                            <input type="hidden" name="pid" value="<?php echo $provider->id; ?>" />
                            <input type="submit" value="Update Operation Hours" />
                        </form><br />
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
            <?php endif; ?>
        </div>
    </div>
@endsection
