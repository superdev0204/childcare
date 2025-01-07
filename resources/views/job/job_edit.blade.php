@push('title')
    <title>Child Care Jobs - Update Job Posting</title>
@endpush

@extends('layouts.app')

@section('content')
    <script type="text/javascript" src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/user/profile">Job Listings</a>&gt;&gt; </li>
                <li>Update Job Posting </li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <?php if (isset($message)) :?>
            <div class="error">
                <p><?php echo $message; ?></p>
            </div>
            <?php endif;?>

            @if(!$success)
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="title-label"><label class="required" for="title">Job Title:</label></dt>
                        <dd id="title-element">
                            @if (isset($request->title))
                                <input type="text" id="title" name="title" value="{{ $request->title }}">
                            @else
                                @if (!empty(old('title')))
                                    <input type="text" id="title" name="title" value="{{ old('title') }}">
                                @else
                                    <input type="text" id="title" name="title" value="{{ $job->title }}">
                                @endif
                            @endif
                            @error('title')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="description-label"><label class="required" for="description">Job Description:</label></dt>
                        <dd id="description-element">
                            @if (isset($request->description))
                                <textarea id="description" name="description" cols="15" rows="5">{{ $request->description }}</textarea>
                            @else
                                @if (!empty(old('description')))
                                    <textarea id="description" name="description" cols="15" rows="5">{{ old('description') }}</textarea>
                                @else
                                    <textarea id="description" name="description" cols="15" rows="5">{{ $job->description }}</textarea>
                                @endif
                            @endif
                            @error('description')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="company-label"><label class="required" for="company">Company Name:</label></dt>
                        <dd id="company-element">
                            @if (isset($request->company))
                                <input type="text" id="company" name="company" value="{{ $request->company }}">
                            @else
                                @if (!empty(old('company')))
                                    <input type="text" id="company" name="company" value="{{ old('company') }}">
                                @else
                                    <input type="text" id="company" name="company" value="{{ $job->company }}">
                                @endif
                            @endif
                            @error('company')
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
                                @if (!empty(old('phone')))
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                                @else
                                    <input type="text" id="phone" name="phone" value="{{ $job->phone }}">
                                @endif
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
                                @if (!empty(old('phone')))
                                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                                @else
                                    <input type="email" id="email" name="email" value="{{ $job->email }}">
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
                                    <input type="text" id="city" name="city" value="{{ $job->city }}">
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
                                            <option value='{{ $state->state_code }}' selected>{{ $state->state_name }}</option>
                                        @else
                                            <option value='{{ $state->state_code }}'>{{ $state->state_name }}</option>
                                        @endif
                                    @else
                                        @if( !empty(old('state')) )
                                            @if ($state->state_code == old('state'))
                                                <option value='{{ $state->state_code }}' selected>{{ $state->state_name }}</option>
                                            @else
                                                <option value='{{ $state->state_code }}'>{{ $state->state_name }}</option>
                                            @endif
                                        @else
                                            @if ($state->state_code == $job->state)
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
                                    <input type="text" id="zip" name="zip" value="{{ $job->zip }}">
                                @endif
                            @endif
                            @error('zip')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="education-label"><label class="required" for="education">Expected Education Level:</label>
                        </dt>
                        <dd id="education-element">
                            <select id="education" name="education">
                                <option value="">-Select-</option>
                                @foreach ($educations as $key => $value)
                                    @if (isset($request->education))
                                        @if ($key == $request->education)
                                            <option value='{{ $key }}' selected>{{ $value }}</option>
                                        @else
                                            <option value='{{ $key }}'>{{ $value }}</option>
                                        @endif
                                    @else
                                        @if( !empty(old('education')) )
                                            @if ($key == old('education'))
                                                <option value='{{ $key }}' selected>{{ $value }}</option>
                                            @else
                                                <option value='{{ $key }}'>{{ $value }}</option>
                                            @endif
                                        @else
                                            @if ($key == $job->education)
                                                <option value='{{ $key }}' selected>{{ $value }}</option>
                                            @else
                                                <option value='{{ $key }}'>{{ $value }}</option>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            @error('education')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="requirements-label"><label class="required" for="requirements">Requirements:</label></dt>
                        <dd id="requirements-element">
                            @if (isset($request->requirements))
                                <textarea id="requirements" name="requirements" cols="15" rows="5">{{ $request->requirements }}</textarea>
                            @else
                                @if (!empty(old('requirements')))
                                    <textarea id="requirements" name="requirements" cols="15" rows="5">{{ old('requirements') }}</textarea>
                                @else
                                    <textarea id="requirements" name="requirements" cols="15" rows="5">{{ $job->requirements }}</textarea>
                                @endif
                            @endif
                            @error('requirements')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="rateRange-label"><label for="rateRange">Rate Range:</label></dt>
                        <dd id="rateRange-element">
                            @if (isset($request->rateRange))
                                <input type="text" id="rateRange" name="rateRange" value="{{ $request->rateRange }}">
                            @else
                                @if (!empty(old('rateRange')))
                                    <input type="text" id="rateRange" name="rateRange" value="{{ old('rateRange') }}">
                                @else
                                    <input type="text" id="rateRange" name="rateRange" value="{{ $job->rate_range }}">
                                @endif
                            @endif
                            @error('rateRange')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="howtoapply-label"><label class="required" for="howtoapply">How To Apply:</label></dt>
                        <dd id="howtoapply-element">
                            @if (isset($request->howtoapply))
                                <textarea id="howtoapply" name="howtoapply" cols="15" rows="5">{{ $request->howtoapply }}</textarea>
                            @else
                                @if (!empty(old('howtoapply')))
                                    <textarea id="howtoapply" name="howtoapply" cols="15" rows="5">{{ old('howtoapply') }}</textarea>
                                @else
                                    <textarea id="howtoapply" name="howtoapply" cols="15" rows="5">{{ $job->howtoapply }}</textarea>
                                @endif
                            @endif
                            @error('howtoapply')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="challenge-label">&nbsp;</dt>
                        <dd id="challenge-element">
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
                        </dd>
                        <dt id="addNew-label">&nbsp;</dt>
                        <dd id="addNew-element">
                            <input type="submit" name="submit" value="Submit">
                        </dd>
                    </dl>
                </form>

                <script>
                    CKEDITOR.replace('description', {
                        toolbarGroups: [{
                                name: 'clipboard',
                                groups: ['clipboard', 'undo']
                            },
                            {
                                name: 'basicstyles',
                                groups: ['basicstyles', 'cleanup']
                            },
                            {
                                name: 'links'
                            }
                        ]
                    });
                    CKEDITOR.replace('requirements', {
                        toolbarGroups: [{
                                name: 'clipboard',
                                groups: ['clipboard', 'undo']
                            },
                            {
                                name: 'basicstyles',
                                groups: ['basicstyles', 'cleanup']
                            },
                            {
                                name: 'links'
                            }
                        ]
                    });
                </script>
            @endif
        </section>
        <!---------right container------>
        <section class="right-sect">
            <iframe
                src="https://www.facebook.com/plugins/like.php?href=<?php echo 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>&width=450&layout=standard&action=like&size=large&share=true&height=50&appId=155446947822305"
                width="450" height="50" style="border:none;overflow:hidden" scrolling="no" frameborder="0"
                allowTransparency="true" allow="encrypted-media"></iframe>
            <div class="listSidebar">
                <h3>Quick Links</h3>
                <div class="quick-links">
                    <a href="/resumes" title="Find Child Care Resumes">Find Resumes</a>
                    <a href="/jobs/new" title="Post Child Care Job">Post Job</a>
                    <a href="/resumes/new" title="Post Child Care Resume">Post Resume</a>
                </div>
            </div>

        </section>
        <!-------right container ends------>
        <!---------right container------>
        {{-- <section class="right-sect">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- ChildcareJob Responsive -->
            <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="8406225575" data-ad-format="auto"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </section> --}}
        <!-------right container ends------>
    </div>
@endsection
