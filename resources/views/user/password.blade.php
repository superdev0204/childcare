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
            <h1>Change Password</h1>
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

            @if( empty($successMsg) )
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="currentPassword-label"><label class="required" for="currentPassword">Current Password:</label>
                        </dt>
                        <dd id="currentPassword-element">
                            <input type="password" id="currentPassword" name="currentPassword">
                            @error('currentPassword')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="newPassword-label"><label class="required" for="password">New Password:</label></dt>
                        <dd id="newPassword-element">
                            <input type="password" id="password" name="password" autocomplete="off">
                            @error('password')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="confirmPassword-label"><label class="required" for="password_confirmation">Retype Password:</label>
                        </dt>
                        <dd id="confirmPassword-element">
                            <input type="password" id="password_confirmation" name="password_confirmation">
                            @error('password_confirmation')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="update-label">&nbsp;</dt>
                        <dd id="update-element"><input type="submit" name="submit" value="Update Password"></dd>
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
