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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Answer Editor
            <form method="POST" action="/admin/answer_update">
                @csrf
                <table width="100%">
                    <tr>
                        <td>
                            @if (isset($answer))
                                <div class="form-group">
                                    <label for="userName">Name:</label>
                                    <input type="text" class="textfield" id="userName" name="userName" value="{{ $answer->answer_by }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="userEmail">Email:</label>
                                    <input type="email" class="textfield" id="userEmail" name="userEmail" value="{{ $answer->answer_email }}"
                                        required>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="answer_id" id="answer_id" value="{{ $answer->id }}" required>
                                    <label for="answer">Answer:</label>
                                    <textarea class="textfield" name="answer" id="answer" cols="45" rows="10">{{ $answer->answer }}</textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="form-content-submit-btn">Update</button>
                                </div>
                            @else
                                <div class="form-group">
                                    <label for="answer">Answer:</label>
                                    <textarea class="textfield" name="answer" id="answer" cols="45" rows="10"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="form-content-submit-btn">Update</button>
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </form><br />
        </div>
    </div>
@endsection