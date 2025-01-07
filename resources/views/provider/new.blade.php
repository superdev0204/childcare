@push('title')
    <title>Childcare Providers - Add New Provider</title>
@endpush

@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>

    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/">Home</a> &gt;&gt; </li>
                <li><a href="/provider">Providers</a>&gt;&gt; </li>
                <li>Add New Listing </li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <?php if (isset($message)) :?>
            <div class="error">
                <p><?php echo $message; ?></p>
            </div>
            <?php endif;?>

            <form method="post">
                @csrf
                <dl class="zend_form">
                    <dt id="name-label"><label class="required" for="name">Daycare Name*:</label></dt>
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
                    <dt id="address-label"><label class="required" for="address">Address*:</label></dt>
                    <dd id="address-element">
                        @if (isset($request->address))
                            <input type="text" id="address" name="address" value="{{ $request->address }}">
                        @else
                            <input type="text" id="address" name="address" value="{{ old('address') }}">
                        @endif
                        @error('address')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="city-label"><label class="required" for="city">City*:</label></dt>
                    <dd id="city-element">
                        @if (isset($request->city))
                            <input type="text" id="city" name="city" value="{{ $request->city }}">
                        @else
                            @if (!empty(old('city')))
                                <input type="text" id="city" name="city" value="{{ old('city') }}">
                            @else
                                <input type="text" id="city" name="city" value="{{ $user->city }}">
                            @endif
                        @endif
                        @error('city')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="zip-label"><label class="required" for="zip">ZIP Code*:</label></dt>
                    <dd id="zip-element">
                        @if (isset($request->zip))
                            <input type="text" id="zip" name="zip" value="{{ $request->zip }}">
                        @else
                            @if (!empty(old('zip')))
                                <input type="text" id="zip" name="zip" value="{{ old('zip') }}">
                            @else
                                <input type="text" id="zip" name="zip" value="{{ $user->zip }}">
                            @endif
                        @endif
                        @error('zip')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="phone-label"><label class="required" for="phone">Contact Phone*:</label></dt>
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
                    <dt id="website-label"><label for="website">Website:(i.e. http://yourdaycare.com)</label></dt>
                    <dd id="website-element">
                        @if (isset($request->website))
                            <input type="text" id="website" name="website" value="{{ $request->website }}">
                        @else
                            <input type="text" id="website" name="website" value="{{ old('website') }}">
                        @endif
                        @error('website')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="operationId-label"><label for="operationId">License Number:</label></dt>
                    <dd id="operationId-element">
                        @if (isset($request->operationId))
                            <input type="text" id="operationId" name="operationId" value="{{ $request->operationId }}">
                        @else
                            <input type="text" id="operationId" name="operationId" value="{{ old('operationId') }}">
                        @endif
                        @error('operationId')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="introduction-label"><label class="required" for="introduction">Introduction (150 - 1000 characters)*:</label></dt>
                    <dd id="introduction-element">
                        @if (isset($request->introduction))
                            <textarea id="introduction" name="introduction" cols="15" rows="5">{{ $request->introduction }}</textarea>
                        @else
                            <textarea id="introduction" name="introduction" cols="15" rows="5">{{ old('introduction') }}</textarea>
                        @endif
                        @error('introduction')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="capacity-label"><label class="required" for="capacity">Maximum Capacity*:</label></dt>
                    <dd id="capacity-element">
                        @if (isset($request->capacity))
                            <input type="text" id="capacity" name="capacity" value="{{ $request->capacity }}">
                        @else
                            <input type="text" id="capacity" name="capacity" value="{{ old('capacity') }}">
                        @endif
                        @error('capacity')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="ageRange-label"><label for="ageRange">Ages Willing to Serve:</label></dt>
                    <dd id="ageRange-element">
                        @if (isset($request->ageRange))
                            <input type="text" id="ageRange" name="ageRange" value="{{ $request->ageRange }}">
                        @else
                            <input type="text" id="ageRange" name="ageRange" value="{{ old('ageRange') }}">
                        @endif
                        @error('ageRange')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="pricing-label"><label for="pricing">Rate Range:</label></dt>
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
                    <dt id="typeofcare-label"><label class="required" for="typeofcare">Type of Care (select all that apply)*:</label></dt>
                    <dd id="typeofcare-element">
                        @if (isset($request->typeofcare))
                            @if (in_array('After School', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="After School" checked="">After School</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="After School">After School</label>
                            @endif
                            @if (in_array('Before School', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="Before School" checked="">Before School</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Before School">Before School</label>
                            @endif
                            @if (in_array('Before and After School', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="Before and After School" checked="">Before and After School</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Before and After School">Before and After School</label>
                            @endif
                            @if (in_array('Daytime', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="Daytime" checked="">Daytime</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Daytime">Daytime</label>
                            @endif
                            @if (in_array('Drop-in Care', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="Drop-in Care" checked="">Drop-in Care</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Drop-in Care">Drop-in Care</label>
                            @endif
                            @if (in_array('Emergency Care', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="Emergency Care" checked="">Emergency Care</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Emergency Care">Emergency Care</label>
                            @endif
                            @if (in_array('Full-Time', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="Full-Time" checked="">Full-Time</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Full-Time">Full-Time</label>
                            @endif
                            @if (in_array('Kindergarten', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="Kindergarten" checked="">Kindergarten</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Kindergarten">Kindergarten</label>
                            @endif
                            @if (in_array('Part-Time', $request->typeofcare))
                                <label><input type="checkbox" name="typeofcare[]" value="Part-Time" checked="">Part-Time</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Part-Time">Part-Time</label>
                            @endif
                        @else
                            @if (!empty(old('typeofcare')) && in_array('After School', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="After School" checked="">After School</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="After School">After School</label>
                            @endif
                            @if (!empty(old('typeofcare')) && in_array('Before School', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="Before School" checked="">Before School</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Before School">Before School</label>
                            @endif
                            @if (!empty(old('typeofcare')) && in_array('Before and After School', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="Before and After School" checked="">Before and After School</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Before and After School">Before and After School</label>
                            @endif
                            @if (!empty(old('typeofcare')) && in_array('Daytime', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="Daytime" checked="">Daytime</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Daytime">Daytime</label>
                            @endif
                            @if (!empty(old('typeofcare')) && in_array('Drop-in Care', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="Drop-in Care" checked="">Drop-in Care</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Drop-in Care">Drop-in Care</label>
                            @endif
                            @if (!empty(old('typeofcare')) && in_array('Emergency Care', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="Emergency Care" checked="">Emergency Care</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Emergency Care">Emergency Care</label>
                            @endif
                            @if (!empty(old('typeofcare')) && in_array('Full-Time', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="Full-Time" checked="">Full-Time</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Full-Time">Full-Time</label>
                            @endif
                            @if (!empty(old('typeofcare')) && in_array('Kindergarten', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="Kindergarten" checked="">Kindergarten</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Kindergarten">Kindergarten</label>
                            @endif
                            @if (!empty(old('typeofcare')) && in_array('Part-Time', old('typeofcare')))
                                <label><input type="checkbox" name="typeofcare[]" value="Part-Time" checked="">Part-Time</label>
                            @else
                                <label><input type="checkbox" name="typeofcare[]" value="Part-Time">Part-Time</label>
                            @endif
                        @endif
                        @error('typeofcare')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="schoolsServed-label"><label for="schoolsServed">Schools Served:</label></dt>
                    <dd id="schoolsServed-element">
                        @if (isset($request->schoolsServed))
                            <input type="text" id="schoolsServed" name="schoolsServed" value="{{ $request->schoolsServed }}">
                        @else
                            <input type="text" id="schoolsServed" name="schoolsServed" value="{{ old('schoolsServed') }}">
                        @endif
                        @error('schoolsServed')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="language-label"><label for="language">Languages Supported Other than English:</label></dt>
                    <dd id="language-element">
                        @if (isset($request->language))
                            <input type="text" id="language" name="language" value="{{ $request->language }}">
                        @else
                            <input type="text" id="language" name="language" value="{{ old('language') }}">
                        @endif
                        @error('language')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="accreditation-label"><label for="accreditation">Achievements/Accreditations:</label></dt>
                    <dd id="accreditation-element">
                        @if (isset($request->accreditation))
                            <input type="text" id="accreditation" name="accreditation" value="{{ $request->accreditation }}">
                        @else
                            <input type="text" id="accreditation" name="accreditation" value="{{ old('accreditation') }}">
                        @endif
                        @error('accreditation')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="subsidized-label"><label class="required" for="subsidized">Enrolled in Subsidized Child Care Program?*:</label></dt>
                    <dd id="subsidized-element">
                        <select id="subsidized" name="subsidized">
                            @if( isset($request->subsidized) )
                                @if($request->subsidized == "1")
                                    <option value="0">No</option>
                                    <option value="1" selected>Yes</option>
                                @elseif($request->subsidized == "0")
                                    <option value="0" selected>No</option>
                                    <option value="1">Yes</option>
                                @else
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                @endif
                            @else
                                @if(old('subsidized') == "1")
                                    <option value="0">No</option>
                                    <option value="1" selected>Yes</option>
                                @elseif(old('subsidized') == "0")
                                    <option value="0" selected>No</option>
                                    <option value="1">Yes</option>
                                @else
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                @endif
                            @endif
                        </select>
                        @error('subsidized')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="transportation-label"><label for="transportation">Transportation (select all that apply):</label></dt>
                    <dd id="transportation-element">
                        @if (isset($request->transportation))
                            @if (in_array('Not Available', $request->transportation))
                                <label><input type="checkbox" name="transportation[]" value="Not Available" checked="">Not Available</label>
                            @else
                                <label><input type="checkbox" name="transportation[]" value="Not Available">Not Available</label>
                            @endif
                            @if (in_array('Emergency Only', $request->transportation))
                                <label><input type="checkbox" name="transportation[]" value="Emergency Only" checked="">Emergency Only</label>
                            @else
                                <label><input type="checkbox" name="transportation[]" value="Emergency Only">Emergency Only</label>
                            @endif
                            @if (in_array('Field Trips', $request->transportation))
                                <label><input type="checkbox" name="transportation[]" value="Field Trips" checked="">Field Trips</label>
                            @else
                                <label><input type="checkbox" name="transportation[]" value="Field Trips">Field Trips</label>
                            @endif
                            @if (in_array('To/From School', $request->transportation))
                                <label><input type="checkbox" name="transportation[]" value="To/From School" checked="">To/From School</label>
                            @else
                                <label><input type="checkbox" name="transportation[]" value="To/From School">To/From School</label>
                            @endif
                        @else
                            @if (!empty(old('transportation')) && in_array('Not Available', old('transportation')))
                                <label><input type="checkbox" name="transportation[]" value="Not Available" checked="">Not Available</label>
                            @else
                                <label><input type="checkbox" name="transportation[]" value="Not Available">Not Available</label>
                            @endif
                            @if (!empty(old('transportation')) && in_array('Emergency Only', old('transportation')))
                                <label><input type="checkbox" name="transportation[]" value="Emergency Only" checked="">Emergency Only</label>
                            @else
                                <label><input type="checkbox" name="transportation[]" value="Emergency Only">Emergency Only</label>
                            @endif
                            @if (!empty(old('transportation')) && in_array('Field Trips', old('transportation')))
                                <label><input type="checkbox" name="transportation[]" value="Field Trips" checked="">Field Trips</label>
                            @else
                                <label><input type="checkbox" name="transportation[]" value="Field Trips">Field Trips</label>
                            @endif
                            @if (!empty(old('transportation')) && in_array('To/From School', old('transportation')))
                                <label><input type="checkbox" name="transportation[]" value="To/From School" checked="">To/From School</label>
                            @else
                                <label><input type="checkbox" name="transportation[]" value="To/From School">To/From School</label>
                            @endif
                        @endif
                    </dd>
                    <dt id="additionalinfo-label"><label for="additionalinfo">Additional Information (Max 1000 characters):</label></dt>
                    <dd id="additionalinfo-element">
                        @if (isset($request->additionalinfo))
                            <textarea id="additionalinfo" name="additionalinfo" cols="15" rows="5">{{ $request->additionalinfo }}</textarea>
                        @else
                            <textarea id="additionalinfo" name="additionalinfo" cols="15" rows="5">{{ old('additionalinfo') }}</textarea>
                        @endif
                        @error('additionalinfo')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="update-label">&nbsp;</dt>
                    <dd id="update-element"><input type="submit" name="submit" value="Submit"></dd>
                </dl>
            </form>
        </section>
        <!---------right container------>
        <section class="right-sect">
            <?php if ($user): ?>
            Hi, <?php echo $user->first_name ?: $user->email; ?>
            <?php endif; ?>
            <a href="/user/logout">Sign Out</a>
            <div class="listSidebar">
                <h2>Notes:</h2>
                <ol>
                    <li> * indicates required field</li>
                    <li>Please fill in as much information as possible.</li>
                    <?php if($user && $user->is_home) : ?>
                    <li>If you prefer not to display your exact address, simply enter the name of your neighborhood, or a
                        nearby intersection or landmark.</li>
                    <?php endif;?>
                </ol>
            </div>
        </section>
        <!-------right container ends------>
    </div>

    <script>
        CKEDITOR.replace('introduction', {
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
        CKEDITOR.replace('additionalinfo', {
            toolbarGroups: [{
                    name: 'clipboard',
                    groups: ['clipboard', 'undo']
                },
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup']
                }
            ]
        });
    </script>
@endsection
