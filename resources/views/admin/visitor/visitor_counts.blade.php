@push('link')
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.css') }}">
@endpush

@extends('layouts.app_old')

@section('content')
    <div id="content" class="clearfix" style="width:auto">
        <div id="left">
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Visitor Counts
            <h2>Visitor Counts</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Page Url</th>
                        <th>Date</th>
                        <th>Visitor Count</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0;
                    foreach ($visitor_counts as $visitor_count): ?>
                        <tr class="d<?php echo $i % 2;
                        $i++; ?>">
                            <td>{{ $visitor_count->id }}</td>
                            <td>{{ $visitor_count->page_url }}</td>
                            <td>{{ $visitor_count->date }}</td>
                            <td align="right">{{ $visitor_count->visitor_count }}</td>
                            <td>
                                <a href="/admin/visitor_delete?vID={{ $visitor_count->id }}">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table><br />
            @if ($visitor_counts instanceof Illuminate\Pagination\LengthAwarePaginator)
                <div class="pagination">
                    {{ $visitor_counts->links("pagination::bootstrap-4") }}
                </div>
            @endif
        </div>
        <div id="right">
            <div class="widget">
                Hi, <?php echo $user->firstname ?: $user->email; ?>
                <a href="/user/logout">Sign Out</a>
            </div>
            @include('admin.right_panel')
        </div>
    </div>
@endsection
