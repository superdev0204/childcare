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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Find Users
            <h2>Find Users</h2>
            <?php if (isset($message)): ?>
            <h4><?php echo $message; ?></h4>
            <?php endif; ?>
            <div class="widget">
                <form method="get">
                    <dl class="zend_form">
                        <dt id="email-label"><label class="required" for="email">Email address:</label></dt>
                        <dd id="email-element">
                            @if (isset($request->email))
                                <input type="email" class="textfield" id="email" name="email" value="{{ $request->email }}">
                            @else
                                <input class="textfield" id="email" name="email" type="email" value="{{ old('email') }}">
                            @endif
                            @error('email')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="firstname-label"><label class="required" for="firstname">First Name:</label></dt>
                        <dd id="firstname-element">
                            @if (isset($request->firstname))
                                <input type="text" class="textfield" id="firstname" name="firstname" value="{{ $request->firstname }}">
                            @else
                                <input class="textfield" id="firstname" name="firstname" type="text" value="{{ old('firstname') }}">
                            @endif
                            @error('firstname')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="lastname-label"><label class="required" for="lastname">Last Name:</label></dt>
                        <dd id="lastname-element">
                            @if (isset($request->lastname))
                                <input type="text" class="textfield" id="lastname" name="lastname" value="{{ $request->lastname }}">
                            @else
                                <input class="textfield" id="lastname" name="lastname" type="text" value="{{ old('lastname') }}">
                            @endif
                            @error('lastname')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="city-label"><label class="required" for="city">City:</label></dt>
                        <dd id="city-element">
                            @if (isset($request->city))
                                <input type="text" class="textfield" id="city" name="city" value="{{ $request->city }}">
                            @else
                                <input class="textfield" id="city" name="city" type="text" value="{{ old('city') }}">
                            @endif
                            @error('city')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
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
                                            <option value='{{ $state->state_code }}'>{{ $state->state_name }}
                                            </option>
                                        @endif
                                    @else
                                        @if ($state->state_code == old('state'))
                                            <option value='{{ $state->state_code }}' selected>
                                                {{ $state->state_name }}
                                            </option>
                                        @else
                                            <option value='{{ $state->state_code }}'>{{ $state->state_name }}
                                            </option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            @error('state')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="zip-label"><label class="required" for="zip">Zip Code:</label></dt>
                        <dd id="zip-element">
                            @if (isset($request->zip))
                                <input type="text" class="textfield" id="zip" name="zip" value="{{ $request->zip }}">
                            @else
                                <input class="textfield" id="zip" name="zip" type="text" value="{{ old('zip') }}">
                            @endif
                            @error('zip')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="search-label">&nbsp;</dt>
                        <dd id="search-element">
                            <input type="submit" name="search" id="search" value="Find Users">
                        </dd>
                    </dl>
                </form>
            </div>
            <?php if (isset($users)):?>
            <table width="100%">
                <tr>
                    <th>User</th>
                    <th>Type/Location</th>
                    <th>Status</th>
                    <th>Misc</th>
                </tr>
                <?php
                $i = 0;
                /** @var \Application\Domain\Entity\User $user */
                foreach ($users as $user): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="20%">
                        <a
                            href="/admin/user/find?id=<?php echo $user->id; ?>&email=<?php echo $user->email; ?>"><?php echo $user->firstname . ' ' . $user->lastname; ?></a><br />
                        <?php echo $user->email; ?> <br />
                        <?php if ($user->provider):?>
                        <a href="/provider/view?id=<?php echo optional(optional($user)->provider)->id; ?>" target="blank"><?php echo optional(optional($user)->provider)->id; ?></a><br />
                        <?php endif; ?>
                    </td>
                    <td align="center">
                        <?php echo $user->caretype; ?><br />
                        <?php echo $user->city . ' ' . $user->state . ' ' . $user->zip; ?>
                    </td>
                    <td>
                        <?php if ($user->multi_listings) {
                            echo 'Multi Listings';
                        } ?><br />
                        <?php if ($user->status == 1) {
                            echo 'Activated';
                        } ?>
                        <?php if ($user->status < 0) {
                            echo 'Not Activated';
                        } ?>
                        <?php if($user->status == 0): ?>
                        Not Activated
                        <br />
                        <form method="post" action="/admin/user/activate">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $user->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Activate Account" />
                        </form>
                        <?php endif; ?>
                    </td>
                    <td>
                        Created: <?php echo $user->created; ?><br />
                        IP: <?php echo $user->ip_address; ?><br />
                        Login Times: <?php echo $user->logintime; ?><br />
                        Last Login: <?php echo $user->login; ?><br />
                        <input type="button" name="switch" onclick="window.location='/admin/user/switch?id=<?php echo $user->id; ?>'"
                            value="&nbsp;Login As This User&nbsp;" />
                    </td>
                </tr>
                <?php if (isset($_GET['id']) && $user->provider): ?>
                <tr>
                    <td colspan="4">
                        <strong>Linked Provider Name</strong>:<?php echo optional(optional($user)->provider)->name; ?>
                        <form method="post" action="/admin/user/reset">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $user->id; ?>" />
                            <input type="hidden" name="pid" value="<?php echo optional(optional($user)->provider)->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Reset Account" />
                        </form>
                    </td>
                </tr>
                <?php endif;?>
                <?php endforeach;?>
            </table><br />
            <?php endif; ?>
        </div>
    </div>
@endsection
