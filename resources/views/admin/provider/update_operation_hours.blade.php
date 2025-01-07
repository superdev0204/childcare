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
            <a href="/">Home</a> &gt;&gt;
            <a href="/admin">Admin</a> &gt;&gt;
            <a href="/admin/provider/edit?id=<?php echo $provider->id; ?>"><?php echo $provider->name; ?></a> &gt;&gt;
            Update Operation Hours<br />
            <?php if (isset($message)) :?>
            <p><?php echo $message; ?></p>
            <?php endif;?>            
            
            <form method="post">
                @csrf
                <dl class="zend_form">
                    <dt id="monday-label"><label for="monday">Monday:</label></dt>
                    <dd id="monday-element">
                        <input class="textfield" id="monday" name="monday"
                            value="{{ optional(optional($provider)->operationHours)->monday }}">
                    </dd>
                    <dt id="tuesday-label"><label for="tuesday">Tuesday:</label></dt>
                    <dd id="tuesday-element">
                        <input class="textfield" id="tuesday" name="tuesday"
                            value="{{ optional(optional($provider)->operationHours)->tuesday }}">
                    </dd>
                    <dt id="wednesday-label"><label for="wednesday">Wednesday:</label></dt>
                    <dd id="wednesday-element">
                        <input class="textfield" id="wednesday" name="wednesday"
                            value="{{ optional(optional($provider)->operationHours)->wednesday }}">
                    </dd>
                    <dt id="thursday-label"><label for="thursday">Thursday:</label></dt>
                    <dd id="thursday-element">
                        <input class="textfield" id="thursday" name="thursday"
                            value="{{ optional(optional($provider)->operationHours)->thursday }}">
                    </dd>
                    <dt id="friday-label"><label for="friday">Friday:</label></dt>
                    <dd id="friday-element">
                        <input class="textfield" id="friday" name="friday"
                            value="{{ optional(optional($provider)->operationHours)->friday }}">
                    </dd>
                    <dt id="saturday-label"><label for="saturday">Saturday:</label></dt>
                    <dd id="saturday-element">
                        <input class="textfield" id="saturday" name="saturday"
                            value="{{ optional(optional($provider)->operationHours)->saturday }}">
                    </dd>
                    <dt id="sunday-label"><label for="sunday">Sunday:</label></dt>
                    <dd id="sunday-element">
                        <input class="textfield" id="sunday" name="sunday"
                            value="{{ optional(optional($provider)->operationHours)->sunday }}">
                    </dd>
                    <dt id="submit-label">&nbsp;</dt>
                    <dd id="submit-element"><input type="submit" name="submit" value="Update"></dd>
                </dl>
            </form>
            <br />
        </div>
    </div>
@endsection
