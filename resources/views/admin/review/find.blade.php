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
            <a href="/">Home</a> &gt;&gt; <a href="/admin">Admin Home</a> &gt;&gt; Reviews Statistics
            <h2>Find Reviews</h2>
            <?php if (isset($message)): ?>
            <h4><?php echo $message; ?></h4>
            <?php endif;?>
            <div class="widget">
                <form method="get">
                    <dl class="zend_form">
                        <dt id="rating-label"><label for="rating">Star Rating:</label></dt>
                        <dd id="rating-element">
                            <select class="textfield" id="rating" name="rating">
                                <option value="">All Reviews</option>
                                @if( isset($request->rating) )
                                    @foreach($rating as $key => $value)
                                        @if($key == $request->rating)
                                            <option value='{{ $key }}' selected>{{ $value }}</option>
                                        @else
                                            <option value='{{ $key }}'>{{ $value }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach($rating as $key => $value)
                                        @if($key == old('rating'))
                                            <option value='{{ $key }}' selected>{{ $value }}</option>
                                        @else
                                            <option value='{{ $key }}'>{{ $value }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('rating')
                                <ul style="clear: both">
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="email-label"><label for="email">Review by Email address:</label></dt>
                        <dd id="email-element">
                            @if( isset($request->email) )
                                <input type="email" class="textfield" id="email" name="email" value="{{$request->email}}">
                            @else
                                <input type="email" class="textfield" id="email" name="email" value="{{ old('email') }}">
                            @endif
                            @error('email')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="pname-label"><label for="pname">Provider Name:</label></dt>
                        <dd id="pname-element">
                            @if( isset($request->pname) )
                                <input type="text" class="textfield" id="pname" name="pname" value="{{$request->pname}}">
                            @else
                                <input type="text" class="textfield" id="pname" name="pname" value="{{ old('name') }}">
                            @endif
                            @error('pname')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="pid-label"><label for="pid">Provider ID:</label></dt>
                        <dd id="pid-element">
                            @if( isset($request->pid) )
                                <input type="text" class="textfield" id="pid" name="pid" value="{{$request->pid}}">
                            @else
                                <input type="text" class="textfield" id="pid" name="pid" value="{{ old('pid') }}">
                            @endif
                            @error('pid')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="ip_address-label"><label for="ip_address">IP Address:</label></dt>
                        <dd id="ip_address-element">
                            @if( isset($request->ip_address) )
                                <input type="text" class="textfield" id="ip_address" name="ip_address" value="{{$request->ip_address}}">
                            @else
                                <input type="text" class="textfield" id="ip_address" name="ip_address" value="{{ old('ip_address') }}">
                            @endif
                            @error('ip_address')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="search-label">&nbsp;</dt>
                        <dd id="search-element">
                            <input type="submit" name="search" id="search" value="Search">
                        </dd>
                    </dl>
                </form>
            </div>
            <?php if (isset($reviews)):?>
            <table width="100%">
                <tr>
                    <th>Name</th>
                    <th>By</th>
                    <th>Comments</th>
                </tr>
                <?php $i = 0;
                /** @var \Application\Domain\Entity\Review $review */
                foreach ($reviews as $review): ?>
                <tr class="d<?php echo $i % 2;
                $i++; ?>">
                    <td width="20%">
                        <?php echo $i; ?>. <a target="_blank"
                            href="/provider_detail/<?php echo $review->facility_filename; ?>"><?php echo $review->facility_name; ?></a><br /><br />
                        <?php if (isset($_GET['id'])): ?>
                        <?php if ($review->approved >= 1):?>
                        <form method="post" action="/admin/review/remove">
                            @csrf
                            <input type="hidden" name="id" value="<?php echo $review->id; ?>" />
                            <input type="hidden" name="backUrl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
                            <input type="submit" name="update" value="Remove" />
                        </form>
                        <?php endif; ?>
                        <?php else: ?>
                        <a href="/admin/review/find?id=<?php echo $review->id; ?>&pid=<?php echo optional(optional($review)->provider)->id; ?>">Review Detail</a>
                        <?php endif;?>
                    </td>
                    <td width="30%" nowrap>
                        Reviewed By: <?php echo $review->review_by; ?><br />
                        Email: <?php echo $review->email; ?> <br />
                        Rating: <?php echo $review->rating . ' star '; ?><br />
                        Date: <?php echo $review->review_date; ?><br />
                        IP Address: <?php echo $review->ip_address; ?><br />
                        Email Verified: <?php echo $review->email_verified ? 'Yes' : 'No'; ?><br />
                        Status: <?php if ($review->approved >= 1) {
                            echo 'Approved';
                        }
                        if ($review->approved <= 0) {
                            echo 'Not Approved';
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($_GET['id'])): ?>
                        <?php echo $review->comments; ?>
                        <?php else: ?>
                        <?php echo substr($review->comments, 0, 200); ?>
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach;?>
            </table><br />
            <?php endif; ?>
        </div>
    </div>
@endsection
