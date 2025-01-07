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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Visitor Summary
            <h2>Visitor Summary</h2>
            <form method="post">
                @csrf
                <input type="text" name="dateYM" value="{{ $dateYM }}" class="form-control">
                <input type="submit" name="search" id="search" value="Search">
            </form>
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
                    foreach ($visitor_summarys as $visitor_summary): ?>
                        <tr class="d<?php echo $i % 2;
                        $i++; ?>">
                            <td>{{ $visitor_summary->id }}</td>
                            <td>{{ $visitor_summary->page_url }}</td>
                            <td>{{ $visitor_summary->date }}</td>
                            <td align="right">{{ $visitor_summary->visitor_count }}</td>
                            <td>
                                <a href="/admin/visitor_summary_delete?vID={{ $visitor_summary->id }}">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table><br />
            <div style="text-align:center">
                @if ($visitor_summarys instanceof Illuminate\Pagination\LengthAwarePaginator)
                    {{ $visitor_summarys->links() }}
                @endif
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('input[name="dateYM"]').datepicker({
                dateFormat: "yy-mm",
                changeMonth: true,
                changeYear: true
            });
        })
    </script>
@endsection
