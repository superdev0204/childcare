@extends('layouts.app_old')

@section('content')
    <div id="content" class="clearfix">
        <div id="right">
            <div class="widget">
                Hi, <?php echo $user->firstname ?: $user->email; ?>
                <a href="/user/logout">Sign Out</a>
                <h2>Notes:</h2>
                <ol>
                    <li>Please make sure your daycare is not already in the list before adding a new daycare.</li>
                    <li>If your daycare is in the list, click on the <strong>Update</strong> button to make changes.</li>
                </ol>
            </div>
            @include('admin.right_panel')
        </div>
        <div id="left">
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Provider Update Compare
            <h2><a target="_blank" href="/provider_detail/<?php echo optional(optional($providerLog)->provider)->filename ?>"><?php echo optional(optional($providerLog)->provider)->name; ?></a></h2>
            <table width="100%">
                <tr>
                    <th>Field Name</th>
                    <th>Old</th>
                    <th>New</th>
                </tr>
                <?php
                foreach ($providerLog->getEditableFields() as $field => $title):
                    if ($providerLog->$field == $providerLog->provider->$field) continue;
                ?>
                <tr class="d0">
                    <td style="width: 20%"><?php echo $title; ?></td>
                    <td style="width: 40%">
                        <?php echo $providerLog->provider->$field; ?>
                    </td>
                    <td style="width: 40%">
                        <?php echo $providerLog->$field; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2">
                        <form method="post" action="/admin/provider-log/disapprove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $providerLog->id; ?> " />
                            <input type="submit" value="Not Approve" />
                        </form>
                    </td>
                    <td>
                        <form method="post" action="/admin/provider-log/approve">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $providerLog->id; ?> " />
                            <input type="submit" value="Approve" />
                        </form>
                    </td>
                </tr>
            </table><br />
        </div>
    </div>
@endsection
