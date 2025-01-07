@push('title')
    <title>Childcare Providers - Update Provider Information</title>
@endpush

@extends('layouts.app')

@section('content')
    {{-- <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script> --}}

    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/user/profile">Profile</a> &gt;&gt; </li>
                <li>Profile Update<br /></li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Update Profile Information</h1>
            <?php if (isset($errorMsg)) :?>
            <div class="error">
                <h2><?php echo $errorMsg; ?></h2>
            </div><br />
            <?php endif ?>
            <?php if (isset($successMsg)) :?>
            <div class="success">
                <h2><?php echo $successMsg; ?></h2>
            </div><br />
            <?php endif?>

            <?php
            $careType = ['PARENT' => 'Parent', 'CENTER' => 'Childcare Center', 'HOME' => 'Home Daycare'];
            ?>
            <?php if (isset($careType[$user->caretype])): ?>
            <strong>Account Type: </strong><?php echo $careType[$user->caretype]; ?><br />
            <?php else: ?>
            <strong>Account Type: </strong><?php echo $user->caretype; ?><br />
            <?php endif ?>

            <strong>Email: </strong><?php echo $user->email; ?><br />

            @if( empty($successMsg) )
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="firstname-label"><label class="required" for="firstname">First Name:</label></dt>
                        <dd id="firstname-element">
                            @if (isset($request->firstname))
                                <input type="text" id="firstname" name="firstname" value="{{ $request->firstname }}">
                            @else
                                @if (!empty(old('firstname')))
                                    <input type="text" id="firstname" name="firstname" value="{{ old('firstname') }}">
                                @else
                                    <input type="text" id="firstname" name="firstname" value="{{ $user->firstname }}">
                                @endif
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
                                <input type="text" id="lastname" name="lastname" value="{{ $request->lastname }}">
                            @else
                                @if (!empty(old('lastname')))
                                    <input type="text" id="lastname" name="lastname" value="{{ old('lastname') }}">
                                @else
                                    <input type="text" id="lastname" name="lastname" value="{{ $user->lastname }}">
                                @endif
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
                                <input type="text" id="city" name="city" value="{{ $request->city }}">
                            @else
                                @if (!empty(old('city')))
                                    <input type="text" id="city" name="city" value="{{ old('city') }}">
                                @else
                                    <input type="text" id="city" name="city" value="{{ $user->city }}">
                                @endif
                            @endif
                            @error('city')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="state-label"><label class="required" for="state">State:</label></dt>
                        <dd id="state-element">
                            <select class="textinput" id="state" name="state">
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
                                        @if( !empty(old('state')) )
                                            @if($state->state_code == old('state'))
                                                <option value='{{ $state->state_code }}' selected>{{ $state->state_name }}</option>
                                            @else
                                                <option value='{{ $state->state_code }}'>{{ $state->state_name }}</option>
                                            @endif
                                        @else
                                            @if($state->state_code == $user->state)
                                                <option value='{{ $state->state_code }}' selected>{{ $state->state_name }}</option>
                                            @else
                                                <option value='{{ $state->state_code }}'>{{ $state->state_name }}</option>
                                            @endif
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
                                <input type="text" id="zip" name="zip" value="{{ $request->zip }}">
                            @else
                                @if (!empty(old('zip')))
                                    <input type="text" id="zip" name="zip" value="{{ old('zip') }}">
                                @else
                                    <input type="text" id="zip" name="zip" value="{{ $user->zip }}">
                                @endif
                            @endif
                            @error('zip')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                            <input type="hidden" name="pid" value="{{ $user->id }}"></dd>
                        <dt id="update-label">&nbsp;</dt>
                        <dd id="update-element"><input type="submit" name="submit" value="Update"></dd>
                    </dl>
                </form>

                {{-- <script>
                    CKEDITOR.replace('introduction', {
                        toolbarGroups: [{
                                name: 'clipboard',
                                groups: ['clipboard', 'undo']
                            },
                            {
                                name: 'basicstyles',
                                groups: ['basicstyles', 'cleanup']
                            },
                            {
                                name: 'links'
                            }
                        ]
                    });
                    CKEDITOR.replace('additionalinfo', {
                        toolbarGroups: [{
                                name: 'clipboard',
                                groups: ['clipboard', 'undo']
                            },
                            {
                                name: 'basicstyles',
                                groups: ['basicstyles', 'cleanup']
                            }
                        ]
                    });
                </script> --}}
            @endif
        </section>
        <!---------right container------>
        <section class="right-sect">

            Hello, <strong><?php echo $user->firstname ?: $user->email; ?></strong>

            <div class="listSidebar">
                <h2>Actions</h2>
                <a class="btn m-t-10" href="/user/profile">Profile Information</a>
                <a class="btn m-t-10" href="/user/password">Change Password</a>
                <?php if ($user->multi_listings) :?>
                <a class="btn m-t-10" href="/provider/find">Find Your Childcare</a>
                <?php endif;?>

                <?php if ($user->is_provider): ?>
                <a class="btn m-t-10" href="/provider/update?pid=<?php echo optional(optional($user)->provider)->id; ?>">Update Daycare Information</a>
                <a class="btn m-t-10" href="/provider/imageupload?pid=<?php echo optional(optional($user)->provider)->id; ?>">Upload Logo and Images</a>
                <a class="btn m-t-10" href="/provider/update-operation-hours?pid=<?php echo optional(optional($user)->provider)->id; ?>">Update Operation
                    Hours</a>
                <a class="btn m-t-10" href="/reviews/view?pid=<?php echo optional(optional($user)->provider)->id; ?>">View Review History</a>
                <a class="btn m-t-10" href="/inspection/view?pid=<?php echo optional(optional($user)->provider)->id; ?>">View Inspection History</a>
                <a class="btn m-t-10" target="blank" href="/jobs/newjob">Post Your Job Requirements</a>
                <?php endif ?>

                <a class="btn m-t-10" href="/user/logout">Sign Out</a>
            </div>

            <div class="listSidebar">
                <h2>Notes:</h2>
                <ol>
                    <ol>
                        <?php if(optional(optional($user)->provider)->approved == 0): ?>
                        <li>This daycare has not been approved yet. Please allow 2-3 business days for review.</li>
                        <?php elseif(optional(optional($user)->provider)->approved < 0): ?>
                        <li>Your daycare is not approved for listing on our website.</li>
                        <?php else:?>
                        <li>Your daycare profile page has been visited <?php echo optional(optional($user)->provider)->visits; ?> times.</li>
                        <?php endif;?>
                    </ol>
                </ol>
            </div>
        </section>
        <!-------right container ends------>
    </div>

    <div id="content" class="clearfix">
        <div id="right">

        </div>
        <div id="left">

        </div>
    </div>
@endsection
