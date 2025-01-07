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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Question and Answers
            <h2>New Questions</h2>
            <a href='/admin/question_editor' style="float:right; margin-right:30px">New Question</a>
            <table class="display" style="width:100%;">
                <thead>
                    <tr>
                        <th width="30%">Name</th>
                        <th width="10%">Date</th>
                        <th width="50%">Questions</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0;
                foreach ($questions as $question): ?>
                    <tr class="d<?php echo $i % 2;
                    $i++; ?>">
                        <td>
                            <a href="{{ $question->link }}">{{ $question->name }}</a><br />
                            Question By: {{ $question->question_by }}
                        </td>
                        <td>{{ $question->created_at }}</td>
                        <td><?php echo $question->question; ?></td>
                        <td>
                            <form method="post" action="/admin/question/approve">
                                @csrf
                                <input type="hidden" name="id" value="<?php echo $question->id; ?>" />
                                <input type="submit" value="Approve" />
                            </form>
                            <form method="post" action="/admin/question/disapprove">
                                @csrf
                                <input type="hidden" name="id" value="<?php echo $question->id; ?>" />
                                <input type="submit" value="Not Approve" />
                            </form>
                            <input type="button" value="Update"
                                onclick="window.open('/admin/question_editor?id={{ $question->id }}', '_self')" />
                        </td>
                        {{-- <td>
                            <form method="post" action="/admin/question/disapprove">
                                @csrf
                                <input type="hidden" name="id" value="<?php echo $question->id; ?>" />
                                <input type="submit" value="Not Approve" />
                            </form>
                        </td>
                        <td>
                            <input type="button" value="Update"
                                onclick="window.open('/admin/question_editor?id={{ $question->id }}', '_self')" />
                        </td> --}}
                    </tr>
                    @endforeach
                </tbody>
            </table><br />
            <h2>New Answers</h2>
            <table class="display" style="width:100%">
                <thead>
                    <tr>
                        <th width="30%">Name</th>
                        <th width="10%">Date</th>
                        <th width="50%">Answers</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0;
                foreach ($answers as $answer): ?>
                    <tr class="d<?php echo $i % 2;
                    $i++; ?>">
                        <td>
                            <a href="{{ $answer->link }}">{{ $answer->name }}</a><br />
                            Answer By: {{ $answer->answer_by }}
                        </td>
                        <td>{{ $answer->created_at }}</td>
                        <td><?php echo $answer->answer; ?></td>
                        <td>
                            <form method="post" action="/admin/answer/approve">
                                @csrf
                                <input type="hidden" name="id" value="<?php echo $answer->id; ?>" />
                                <input type="submit" value="Approve" />
                            </form>
                            <form method="post" action="/admin/answer/disapprove">
                                @csrf
                                <input type="hidden" name="id" value="<?php echo $answer->id; ?>" />
                                <input type="submit" value="Not Approve" />
                            </form>
                            <input type="button" value="Update"
                                onclick="window.open('/admin/answer_editor?id={{ $answer->id }}', '_self')" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table><br />
        </div>
    </div>
@endsection
