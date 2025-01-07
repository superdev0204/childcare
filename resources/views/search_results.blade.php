@push('title')
    <title>Search</title>
@endpush

@extends('layouts.app')

@section('content')
    <div id="left-col">
        <a href="/">Home</a> &gt;&gt; <a href="/search">Search Form</a>

        <p><?php echo $message; ?></p>

        <table width="100%" class="widgetized">
            <?php
        $i=0;
        /** @var \Application\Domain\Entity\School $school */
        foreach ($schools as $school): $i++; ?>
            <tr>
                <td valign="top">
                    <a href="/school/<?php echo $school->filename ?>.html"><strong><?php echo ucwords(strtolower($school->name)); ?></strong></a><br />
                    <strong>Location:</strong> <?php echo ucwords(strtolower($school->city)) . ', ' . $school->state . ' - ' . $school->zip; ?> <br />
                    <strong>Contact Phone</strong>: <?php echo $school->phone; ?><br />
                    <strong>Details:</strong>: <?php echo strip_tags($school->description, '<p><a><br>'); ?> <br />

                </td>
            </tr>
            <?php endforeach;?>
        </table>
    </div>
    <div id="right-col">

    </div>
@endsection
