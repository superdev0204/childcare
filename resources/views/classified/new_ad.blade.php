@push('title')
    <title>Child Care Classified - Add New Advertise</title>
@endpush

@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/tiny_mce/tiny_mce.js') }}"></script>
    <script src="{{ asset('js/tiny_mce/tiny_mce_activate.js') }}"></script>

    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/classifieds">Child Care Classifieds</a> &gt;&gt; </li>
                <li>Post New Classified Ad</li>
            </ul>
        </div>

        <!---------right container------>
        <section class="right-sect">
            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>
            <div class="listSidebar">
                <!-- Ezoic - CCC MOINSBD Link Sidebar - link_side -->
                <div id="ezoic-pub-ad-placeholder-102">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- CCC MOINSBD Link Sidebar -->
                    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                        data-ad-slot="8851698836" data-ad-format="link"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <!-- End Ezoic - CCC MOINSBD Link Sidebar - link_side -->
            </div>


        </section>
        <!-------right container ends------>

        <!---------left container------>
        <section class="left-sect head">

            <h1>Post New Classified Ads</h1>
            <?php if (isset($message)) :?>
            <p><?php echo $message; ?></p>
            <?php endif;?>

            @if( !$classified )
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="summary-label"><label class="required" for="summary">Ad Summary:</label></dt>
                        <dd id="summary-element">
                            @if (isset($request->summary))
                                <input type="text" id="summary" name="summary" value="{{ $request->summary }}">
                            @else
                                <input type="text" id="summary" name="summary" value="{{ old('summary') }}">
                            @endif
                            @error('summary')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="detail-label"><label class="required" for="detail">Ad Detail:</label></dt>
                        <dd id="detail-element">
                            @if (isset($request->detail))
                                <textarea id="detail" name="detail" cols="15" rows="5">{{ $request->detail }}</textarea>
                            @else
                                <textarea id="detail" name="detail" cols="15" rows="5">{{ old('detail') }}</textarea>
                            @endif
                            @error('detail')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="name-label"><label class="required" for="name">Contact Name:</label></dt>
                        <dd id="name-element">
                            @if (isset($request->name))
                                <input type="text" id="name" name="name" value="{{ $request->name }}">
                            @else
                                <input type="text" id="name" name="name" value="{{ old('name') }}">
                            @endif
                            @error('name')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="phone-label"><label class="required" for="phone">Contact Phone:</label></dt>
                        <dd id="phone-element">
                            @if (isset($request->phone))
                                <input type="text" id="phone" name="phone" value="{{ $request->phone }}">
                            @else
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                            @endif
                            @error('phone')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="email-label"><label class="required" for="email">Email Address:</label></dt>
                        <dd id="email-element">
                            @if (isset($request->email))
                                <input type="email" id="email" name="email" value="{{ $request->email }}">
                            @else
                                @if (!empty(old('email')))
                                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                                @else
                                    <input type="email" id="email" name="email" value="<?php echo ($user) ? $user->email : ''; ?>">
                                @endif
                            @endif
                            @error('email')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="city-label"><label class="required" for="city">City:</label></dt>
                        <dd id="city-element">
                            @if (isset($request->city))
                                <input type="text" id="city" name="city" value="{{ $request->city }}">
                            @else
                                @if (!empty(old('city')))
                                    <input type="text" id="city" name="city" value="{{ old('city') }}">
                                @else
                                    <input type="text" id="city" name="city" value="<?php echo ($user) ? $user->city : ''; ?>">
                                @endif
                            @endif
                            @error('city')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="state-label"><label class="required" for="state">State:</label></dt>
                        <dd id="state-element">
                            <select id="state" name="state">
                                <option value="">-Select-</option>
                                @foreach ($states as $state)
                                    @if (isset($request->state))
                                        @if ($state->state_code == $request->state)
                                            <option value='{{ $state->state_code }}' selected>
                                                {{ $state->state_name }}
                                            </option>
                                        @else
                                            <option value='{{ $state->state_code }}'>{{ $state->state_name }}
                                            </option>
                                        @endif
                                    @else
                                        @if( !empty(old('state')) )
                                            @if($state->state_code == old('state'))
                                                <option value='{{ $state->state_code }}' selected>{{ $state->state_name }}</option>
                                            @else
                                                <option value='{{ $state->state_code }}'>{{ $state->state_name }}</option>
                                            @endif
                                        @else
                                            @if($user && $state->state_code == $user->state)
                                                <option value='{{ $state->state_code }}' selected>{{ $state->state_name }}</option>
                                            @else
                                                <option value='{{ $state->state_code }}'>{{ $state->state_name }}</option>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            @error('state')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="zip-label"><label class="required" for="zip">Zip Code:</label></dt>
                        <dd id="zip-element">
                            @if (isset($request->zip))
                                <input type="text" id="zip" name="zip" value="{{ $request->zip }}">
                            @else
                                @if (!empty(old('zip')))
                                    <input type="text" id="zip" name="zip" value="{{ old('zip') }}">
                                @else
                                    <input type="text" id="zip" name="zip" value="<?php echo ($user) ? $user->zip : ''; ?>">
                                @endif
                            @endif
                            @error('zip')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="pricing-label"><label for="pricing">Price/Rate Range:</label></dt>
                        <dd id="pricing-element">
                            @if (isset($request->pricing))
                                <input type="text" id="pricing" name="pricing" value="{{ $request->pricing }}">
                            @else
                                <input type="text" id="pricing" name="pricing" value="{{ old('pricing') }}">
                            @endif
                            @error('pricing')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="additionalInfo-label"><label for="additionalInfo">Additional Information:</label></dt>
                        <dd id="additionalInfo-element">
                            @if (isset($request->additionalInfo))
                                <textarea id="additionalInfo" name="additionalInfo" cols="15" rows="5">{{ $request->additionalInfo }}</textarea>
                            @else
                                <textarea id="additionalInfo" name="additionalInfo" cols="15" rows="5">{{ old('additionalInfo') }}</textarea>
                            @endif
                            @error('additionalInfo')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        @if(!$user)
                            <dt id="challenge-label">&nbsp;</dt>
                            <dd id="challenge-element">
                                <input type="hidden" name="challenge" value="g-recaptcha-response">
                                <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async="" defer=""></script>
                                <div class="g-recaptcha" data-sitekey="{{ env("DATA_SITEKEY") }}" data-theme="light" data-type="image" data-size="normal">                                
                                </div>
                                @error('recaptcha-token')
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                @enderror
                            </dd>
                        @endif
                        <dt id="addNew-label">&nbsp;</dt>
                        <dd id="addNew-element"><input type="submit" name="submit" value="Submit"></dd>
                    </dl>
                </form>
            @endif
        </section>
    </div>
@endsection
