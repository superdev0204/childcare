@extends('layouts.app')

@section('content')
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li>
                    @if ($provider)
                        <a href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a> &gt;&gt;
                    @else
                        <a href="<?php echo $page_url; ?>">Previous Page</a> &gt;&gt;
                    @endif
                </li>
                <li>Create Answer<br /></li>
            </ul>
        </div>

        <section class="left-sect head">
            <h2>Create your question for: </h2>
            @if ($provider)
                <?php echo $provider->name; ?><br />
                <?php echo $provider->address . ' ' . $provider->city . ' ' . $provider->state; ?><br /><br />
            @endif
            Q: <?php echo $question->question; ?><br /><br />
            <?php echo $message; ?><br />
            @if (!isset($request->answer))
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        @if (!$user)
                            <dt id="user-label"><label for="answer_userName">Your Name</label></dt>
                            <dd id="user-element">
                                @if (isset($request->answer_userName))
                                    <input type="text" id="answer_userName" name="answer_userName"
                                        value="{{ $request->answer_userName }}">
                                @else
                                    <input type="text" id="answer_userName" name="answer_userName"
                                        value="{{ old('answer_userName') }}">
                                @endif
                                @error('answer_userName')
                                    <ul style="clear: both">
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                            <dt id="user-label"><label for="answer_userEmail">Your Email</label></dt>
                            <dd id="user-element">
                                @if (isset($request->answer_userEmail))
                                    <input type="email" id="answer_userEmail" name="answer_userEmail"
                                        value="{{ $request->answer_userEmail }}">
                                @else
                                    <input type="email" id="answer_userEmail" name="answer_userEmail"
                                        value="{{ old('answer_userEmail') }}">
                                @endif
                                @error('answer_userEmail')
                                    <ul style="clear: both">
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                        @endif
                        <dt id="answer-label"><label for="answer">Your Answer</label></dt>
                        <dd id="answer-element">
                            @if (isset($request->answer))
                                <textarea id="answer" name="answer" cols="15" rows="5">{{ $request->answer }}</textarea>
                            @else
                                <textarea id="answer" name="answer" cols="15" rows="5">{{ old('answer') }}</textarea>
                            @endif
                            @error('answer')
                                <ul style="clear: both">
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        @if (!$user)
                            <dt id="challenge-label">&nbsp;</dt>
                            <dd id="challenge-element">
                                <input type="hidden" name="challenge" value="g-recaptcha-response">
                                <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>
                                <div class="g-recaptcha" data-sitekey="{{ env('DATA_SITEKEY') }}" data-theme="light"
                                    data-type="image" data-size="normal">
                                </div>
                                @error('recaptcha-token')
                                    <ul style="clear: both">
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                        @endif
                        <dt id="addComment-label">&nbsp;</dt>
                        <dd id="addComment-element">
                            <input type="submit" name="submit" value="Submit">
                        </dd>
                    </dl>
                </form>
            @endif
        </section>

        <!-------right container ends------>
        <section class="right-sect">
        </section>
    </div>
@endsection
