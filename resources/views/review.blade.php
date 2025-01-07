@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/tiny_mce/tiny_mce.js') }}"></script>
    <script src="{{ asset('js/tiny_mce/tiny_mce_activate.js') }}"></script>

    <div id="left-col">
        <a href="/">Home</a> &gt;&gt; <a href="/school/<?php echo $school->filename; ?>.html"><?php echo $school->name; ?></a> &gt;&gt; Create
        Comment
        <h2>Create your comment for: </h2>
        <?php echo $school->name; ?><br />
        <?php echo $school->address . ' ' . $school->city . ' ' . $school->state; ?><br /><br />
        <?php echo $message; ?><br />
        @if (!isset($review->id))
            <form method="post" enctype="application/x-www-form-urlencoded" action="/school/comment">
                @csrf
                <table>
                    <tbody>
                        <tr>
                            <td><label for="email">Email address (will not be published):</label></td>
                            <td>
                                @if (isset($request->email))
                                    <input type="email" id="email" name="email" value="{{ $request->email }}">
                                @else
                                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                                @endif
                                @error('email')
                                    <ul style="clear: both">
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td><label for="name">Display Name:</label></td>
                            <td>
                                @if (isset($request->name))
                                    <input type="text" id="name" name="name" value="{{ $request->name }}">
                                @else
                                    <input type="text" id="name" name="name" value="{{ old('name') }}">
                                @endif
                                @error('name')
                                    <ul style="clear: both">
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td><label for="comment">Write your comment:</label></td>
                            <td>
                                @if (isset($request->comment))
                                    <textarea cols="15" rows="5" id="comment" name="comment">{{ $request->comment }}</textarea>
                                @else
                                    <textarea cols="15" rows="5" id="comment" name="comment">{{ old('comment') }}</textarea>
                                @endif
                                @error('comment')
                                    <ul style="clear: both">
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="hidden" name="challenge" value="g-recaptcha-response">
                                <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>
                                <div class="g-recaptcha" data-sitekey="{{ env('DATA_SITEKEY') }}" data-theme="light"
                                    data-type="image" data-size="normal">
                                </div>
                                @error('recaptcha-token')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="submit" name="submit" value="Add Comment">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="id" value="{{ $school->id }}">
            </form>
        @endif
    </div>
    <div id="right-col">

    </div>
@endsection
