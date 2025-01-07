@extends('layouts.app')

@section('content')
    <script type="text/javascript" src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/resumes">Child Care Resumes</a>&gt;&gt; </li>
                <li>Post Resumes </li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <?php if (isset($message)) :?>
            <div class="error">
                <p><?php echo $message; ?></p>
            </div>
            <?php endif;?>

            @if (!$success)
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="name-label"><label class="required" for="name">Your Name:</label></dt>
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
                                <input type="email" id="email" name="email" value="{{ old('email') }}">
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
                                <input type="text" id="city" name="city" value="{{ old('city') }}">
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
                                        @if ($state->state_code == old('state'))
                                            <option value='{{ $state->state_code }}' selected>
                                                {{ $state->state_name }}
                                            </option>
                                        @else
                                            <option value='{{ $state->state_code }}'>{{ $state->state_name }}
                                            </option>
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
                                <input type="text" id="zip" name="zip" value="{{ old('zip') }}">
                            @endif
                            @error('zip')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="position-label"><label for="position">Position to Apply for:</label></dt>
                        <dd id="position-element">
                            @if (isset($request->position))
                                <input type="text" id="position" name="position" value="{{ $request->position }}">
                            @else
                                <input type="text" id="position" name="position" value="{{ old('position') }}">
                            @endif
                            @error('position')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="rateRange-label"><label for="rateRange">Expected Rate:</label></dt>
                        <dd id="rateRange-element">
                            @if (isset($request->rateRange))
                                <input type="text" id="rateRange" name="rateRange" value="{{ $request->rateRange }}">
                            @else
                                <input type="text" id="rateRange" name="rateRange" value="{{ old('rateRange') }}">
                            @endif
                            @error('rateRange')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="objective-label"><label class="required" for="objective">Write your Objective:</label>
                        </dt>
                        <dd id="objective-element">
                            @if (isset($request->objective))
                                <textarea id="objective" name="objective" cols="15" rows="5">{{ $request->objective }}</textarea>
                            @else
                                <textarea id="objective" name="objective" cols="15" rows="5">{{ old('objective') }}</textarea>
                            @endif
                            @error('objective')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="experience-label"><label class="required" for="experience">Write your Experience:</label>
                        </dt>
                        <dd id="experience-element">
                            @if (isset($request->experience))
                                <textarea id="experience" name="experience" cols="15" rows="5">{{ $request->experience }}</textarea>
                            @else
                                <textarea id="experience" name="experience" cols="15" rows="5">{{ old('experience') }}</textarea>
                            @endif
                            @error('experience')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="skillsCertification-label"><label class="required" for="skillsCertification">List your
                                skills and/or certifications:</label></dt>
                        <dd id="skillsCertification-element">
                            @if (isset($request->skillsCertification))
                                <textarea id="skillsCertification" name="skillsCertification" cols="15" rows="5">{{ $request->skillsCertification }}</textarea>
                            @else
                                <textarea id="skillsCertification" name="skillsCertification" cols="15" rows="5">{{ old('skillsCertification') }}</textarea>
                            @endif
                            @error('skillsCertification')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="educationLevel-label"><label class="required" for="educationLevel">Education
                                Level:</label></dt>
                        <dd id="educationLevel-element">
                            <select id="educationLevel" name="educationLevel">
                                <option value="">-Select-</option>
                                @foreach ($educationLevels as $key => $value)
                                    @if (isset($request->educationLevel))
                                        @if ($key == $request->educationLevel)
                                            <option value='{{ $key }}' selected>{{ $value }}</option>
                                        @else
                                            <option value='{{ $key }}'>{{ $value }}</option>
                                        @endif
                                    @else
                                        @if ($key == old('educationLevel'))
                                            <option value='{{ $key }}' selected>{{ $value }}</option>
                                        @else
                                            <option value='{{ $key }}'>{{ $value }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            </select>
                            @error('educationLevel')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="school-label"><label class="required" for="school">School:</label></dt>
                        <dd id="school-element">
                            @if (isset($request->school))
                                <input type="text" id="school" name="school" value="{{ $request->school }}">
                            @else
                                <input type="text" id="school" name="school" value="{{ old('school') }}">
                            @endif
                            @error('school')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="major-label"><label for="major">Major:</label></dt>
                        <dd id="major-element">
                            @if (isset($request->major))
                                <input type="text" id="major" name="major" value="{{ $request->major }}">
                            @else
                                <input type="text" id="major" name="major" value="{{ old('major') }}">
                            @endif
                            @error('major')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="additionalInfo-label"><label class="required" for="additionalInfo">Anything else you want
                                the employer to know?:</label></dt>
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
                    CKEDITOR.replace( 'experience' ,
                        {
                            toolbarGroups: [
                                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                                { name: 'links' }
                            ]
                        }
                    );
                    CKEDITOR.replace( 'skillsCertification' ,
                        {
                            toolbarGroups: [
                                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                                { name: 'links' }
                            ]
                        }
                    );
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
                    <a href="/jobs" title="Find Child Care Jobs">Find Jobs</a>
                    <a href="/jobs/new" title="Post Child Care Job">Post Job</a>
                    <a href="/resumes/new" title="Post Child Care Resume">Post Resume</a>
                </div>
            </div>

        </section>
        <!-------right container ends------>
        <!---------right container------>
        {{-- <section class="right-sect">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- ChildcareJob All Pages Adlinks -->
            <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8651736830870146"
                data-ad-slot="1153705179" data-ad-format="link"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
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
