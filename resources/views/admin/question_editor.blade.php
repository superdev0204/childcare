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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Question Editor
            <form method="POST" action="/admin/question_update">
                @csrf
                <table width="100%">
                    <tr>
                        <td>
                            @if (isset($question))
                                <div class="form-group">
                                    <label for="userName">Name:</label>
                                    <input type="text" class="textfield" id="userName" name="userName" value="{{ $question->question_by }}"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="userEmail">Email:</label>
                                    <input type="email" class="textfield" id="userEmail" name="userEmail" value="{{ $question->question_email }}"
                                        required>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="question_id" id="question_id" value="{{ $question->id }}" required>
                                    <label for="question">Question:</label>
                                    <textarea class="textfield" name="question" id="question" cols="45" rows="10">{{ $question->question }}</textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="form-content-submit-btn">Update</button>
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="question">Question:</label>
                                    <textarea class="textfield" name="question" id="question" cols="45" rows="10"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="form-content-submit-btn">Submit Question</button>
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </form><br />
        </div>
    </div>
@endsection
