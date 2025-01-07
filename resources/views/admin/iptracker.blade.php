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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Visitors Statistics
            <h2>IP Stats</h2>
            <table width="100%">
                <tr>
                    <th>IP</th>
                    <th>User Agent</th>
                    <th>Current / Total</th>
                </tr>
                <?php
            $i = 0;
            /** @var \Application\Domain\Entity\Iptracker $ipstat */
            foreach ($ips as $ipstat): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="25%">
                        <?php echo $ipstat->ip; ?><br />
                        <?php echo $ipstat->hour; ?>
                    </td>
                    <td>
                        <?php echo $ipstat->user_agent; ?><br />
                        <?php echo $ipstat->ludate; ?>
                    </td>
                    <td>
                        <?php echo $ipstat->current_count; ?> / <?php echo $ipstat->total_count; ?>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
        </div>
    </div>
@endsection
