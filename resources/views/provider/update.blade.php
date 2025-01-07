@push('title')
    <title>Childcare Providers - Update Provider Information</title>
@endpush

@extends('layouts.app')

@section('content')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>

    <!------content--------->
    <div class="container">
        <div class="breadcrumbs">
            <ul>
                <li><a href="/provider">Provider</a> &gt;&gt; </li>
                <li>Provider Update<br /></li>
            </ul>
        </div>
        <!---------left container------>
        <section class="left-sect head">
            <h1>Update Provider Information</h1>

            <?php if(isset($providerLog)):?>
            <h2>Your update has been submitted. Please allow 2-3 business days for review.</h2>
            <strong>Name:</strong> <?php echo ucwords(strtolower($providerLog->name)); ?><br />
            <strong>Address:</strong> <?php echo $providerLog->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br />
            <strong>Contact Phone:</strong> <?php echo $providerLog->formatPhone; ?><br>
            <strong>Email Address:</strong> <?php echo $providerLog->email; ?><br>
            <?php if($providerLog->website != "") : ?>
            <strong>Website:</strong> <?php echo $providerLog->website; ?><br />
            <?php endif; ?>
            <?php if($providerLog->introduction != "") : ?>
            <strong>Introduction:</strong> <?php echo $providerLog->introduction; ?><br />
            <?php endif; ?>
            <?php if($providerLog->operation_id != "") : ?>
            <strong>License Number:</strong> <?php echo $providerLog->operation_id; ?><br />
            <?php endif; ?>
            <?php if($providerLog->capacity > 0) : ?>
            <strong>Capacity:</strong> <?php echo $providerLog->capacity; ?><br />
            <?php endif; ?>
            <?php if($providerLog->age_range != "") : ?>
            <strong>Age Range:</strong> <?php echo $providerLog->age_range; ?><br />
            <?php endif; ?>
            <?php if($providerLog->pricing != "") : ?>
            <strong>Rate Range:</strong> <?php echo $providerLog->pricing; ?><br />
            <?php endif; ?>
            <?php if($providerLog->accreditation != "") : ?>
            <strong>Achievement and/or Accreditations: </strong><?php echo $providerLog->accreditation; ?><br />
            <?php endif; ?>
            <?php if($providerLog->daysopen != "") : ?>
            <strong>Days of Operation: </strong><?php echo $providerLog->daysopen; ?><br />
            <?php endif; ?>
            <?php if($providerLog->hoursopen != "") : ?>
            <strong>Normal Open Hours:</strong> <?php echo $providerLog->hoursopen; ?><br />
            <?php endif; ?>
            <?php if($providerLog->subsidized != "") : ?>
            <strong>Enrolled in Subsidized Child Care Program: </strong><?php echo $providerLog->subsidized == 1 ? 'Yes' : 'No'; ?><br />
            <?php endif; ?>
            <?php if($providerLog->language != "") : ?>
            <strong>Languages Supported: </strong><?php echo $providerLog->language; ?><br />
            <?php endif; ?>
            <?php if($providerLog->schools_served != "") : ?>
            <strong>Schools Served: </strong><?php echo $providerLog->schools_served; ?><br />
            <?php endif; ?>
            <?php if($providerLog->typeofcare != "") : ?>
            <strong>Type of Care: </strong><?php echo $providerLog->typeofcare; ?><br />
            <?php endif; ?>
            <?php if($providerLog->transportation != "") : ?>
            <strong>Transportation: </strong><?php echo $providerLog->transportation; ?><br />
            <?php endif; ?>
            <?php if($providerLog->additionalInfo != "") : ?>
            <strong>Additional Information: </strong><?php echo $providerLog->additionalInfo; ?><br />
            <?php endif; ?>
            <?php else: ?>
            <strong>Name: </strong><?php echo $provider->name; ?><br />
            <strong>Address:</strong> <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br />

            <?php if (isset($message)) :?>
            <div class="error">
                <h2><?php echo $message; ?></h2>
            </div><br />
            <?php endif;?>

            <?php if (!isset($success)) :?>
                <form method="post">
                    @csrf
                    <dl class="zend_form">
                        <dt id="address-label"><label class="required" for="address">Address*:</label></dt>
                        <dd id="address-element">
                            @if (isset($request->address))
                                <input type="text" id="address" name="address" value="{{ $request->address }}">
                            @else
                                @if (!empty(old('address')))
                                    <input type="text" id="address" name="address" value="{{ old('address') }}">
                                @else
                                    <input type="text" id="address" name="address" value="{{ $provider->address }}">
                                @endif
                            @endif
                            @error('address')
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
                                @if (!empty(old('phone')))
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                                @else
                                    <input type="text" id="phone" name="phone" value="{{ $provider->phone }}">
                                @endif
                            @endif
                            @error('phone')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="email-label"><label class="required" for="email">Email:</label></dt>
                        <dd id="email-element">
                            @if (isset($request->email))
                                <input type="email" id="email" name="email" value="{{ $request->email }}">
                            @else
                                @if (!empty(old('email')))
                                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                                @else
                                    <input type="email" id="email" name="email" value="{{ $provider->email }}">
                                @endif
                            @endif
                            @error('email')
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
                                @if (!empty(old('website')))
                                    <input type="text" id="website" name="website" value="{{ old('website') }}">
                                @else
                                    <input type="text" id="website" name="website" value="{{ $provider->website }}">
                                @endif
                            @endif
                            @error('website')
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
                                @if (!empty(old('introduction')))
                                    <textarea id="introduction" name="introduction" cols="15" rows="5">{{ old('introduction') }}</textarea>
                                @else
                                    <textarea id="introduction" name="introduction" cols="15" rows="5">{{ $provider->introduction }}</textarea>
                                @endif
                            @endif
                            @error('introduction')
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
                                @if (!empty(old('operationId')))
                                    <input type="text" id="operationId" name="operationId" value="{{ old('operationId') }}">
                                @else
                                    <input type="text" id="operationId" name="operationId" value="{{ $provider->operation_id }}">
                                @endif
                            @endif
                            @error('operationId')
                                <ul>
                                    <li>{{ $message }}</li>
                                </ul>
                            @enderror
                        </dd>
                        <dt id="capacity-label"><label class="required" for="capacity">Maximum Capacity:</label></dt>
                        <dd id="capacity-element">
                            @if (isset($request->capacity))
                                <input type="text" id="capacity" name="capacity" value="{{ $request->capacity }}">
                            @else
                                @if (!empty(old('capacity')))
                                    <input type="text" id="capacity" name="capacity" value="{{ old('capacity') }}">
                                @else
                                    <input type="text" id="capacity" name="capacity" value="{{ $provider->capacity }}">
                                @endif
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
                                @if (!empty(old('ageRange')))
                                    <input type="text" id="ageRange" name="ageRange" value="{{ old('ageRange') }}">
                                @else
                                    <input type="text" id="ageRange" name="ageRange" value="{{ $provider->age_range }}">
                                @endif
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
                                @if (!empty(old('pricing')))
                                    <input type="text" id="pricing" name="pricing" value="{{ old('pricing') }}">
                                @else
                                    <input type="text" id="pricing" name="pricing" value="{{ $provider->pricing }}">
                                @endif
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
                                    @if ( in_array('After School', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="After School" checked="">After School</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="After School">After School</label>
                                    @endif                                
                                @endif
                                @if (!empty(old('typeofcare')) && in_array('Before School', old('typeofcare')))
                                    <label><input type="checkbox" name="typeofcare[]" value="Before School" checked="">Before School</label>
                                @else
                                    @if ( in_array('Before School', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="Before School" checked="">Before School</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="Before School">Before School</label>
                                    @endif
                                @endif
                                @if (!empty(old('typeofcare')) && in_array('Before and After School', old('typeofcare')))
                                    <label><input type="checkbox" name="typeofcare[]" value="Before and After School" checked="">Before and After School</label>
                                @else
                                    @if ( in_array('Before and After School', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="Before and After School" checked="">Before and After School</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="Before and After School">Before and After School</label>
                                    @endif
                                @endif
                                @if (!empty(old('typeofcare')) && in_array('Daytime', old('typeofcare')))
                                    <label><input type="checkbox" name="typeofcare[]" value="Daytime" checked="">Daytime</label>
                                @else
                                    @if ( in_array('Daytime', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="Daytime" checked="">Daytime</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="Daytime">Daytime</label>
                                    @endif
                                @endif
                                @if (!empty(old('typeofcare')) && in_array('Drop-in Care', old('typeofcare')))
                                    <label><input type="checkbox" name="typeofcare[]" value="Drop-in Care" checked="">Drop-in Care</label>
                                @else
                                    @if ( in_array('Drop-in Care', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="Drop-in Care" checked="">Drop-in Care</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="Drop-in Care">Drop-in Care</label>
                                    @endif
                                @endif
                                @if (!empty(old('typeofcare')) && in_array('Emergency Care', old('typeofcare')))
                                    <label><input type="checkbox" name="typeofcare[]" value="Emergency Care" checked="">Emergency Care</label>
                                @else
                                    @if ( in_array('Emergency Care', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="Emergency Care" checked="">Emergency Care</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="Emergency Care">Emergency Care</label>
                                    @endif
                                @endif
                                @if (!empty(old('typeofcare')) && in_array('Full-Time', old('typeofcare')))
                                    <label><input type="checkbox" name="typeofcare[]" value="Full-Time" checked="">Full-Time</label>
                                @else
                                    @if ( in_array('Full-Time', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="Full-Time" checked="">Full-Time</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="Full-Time">Full-Time</label>
                                    @endif
                                @endif
                                @if (!empty(old('typeofcare')) && in_array('Kindergarten', old('typeofcare')))
                                    <label><input type="checkbox" name="typeofcare[]" value="Kindergarten" checked="">Kindergarten</label>
                                @else
                                    @if ( in_array('Kindergarten', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="Kindergarten" checked="">Kindergarten</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="Kindergarten">Kindergarten</label>
                                    @endif
                                @endif
                                @if (!empty(old('typeofcare')) && in_array('Part-Time', old('typeofcare')))
                                    <label><input type="checkbox" name="typeofcare[]" value="Part-Time" checked="">Part-Time</label>
                                @else
                                    @if ( in_array('Part-Time', explode(', ', $provider->typeofcare)) )
                                        <label><input type="checkbox" name="typeofcare[]" value="Part-Time" checked="">Part-Time</label>
                                    @else
                                        <label><input type="checkbox" name="typeofcare[]" value="Part-Time">Part-Time</label>
                                    @endif
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
                                @if (!empty(old('schoolsServed')))
                                    <input type="text" id="schoolsServed" name="schoolsServed" value="{{ old('schoolsServed') }}">
                                @else
                                    <input type="text" id="schoolsServed" name="schoolsServed" value="{{ $provider->schools_served }}">
                                @endif
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
                                @if (!empty(old('language')))
                                    <input type="text" id="language" name="language" value="{{ old('language') }}">
                                @else
                                    <input type="text" id="language" name="language" value="{{ $provider->language }}">
                                @endif
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
                                @if (!empty(old('accreditation')))
                                    <input type="text" id="accreditation" name="accreditation" value="{{ old('accreditation') }}">
                                @else
                                    <input type="text" id="accreditation" name="accreditation" value="{{ $provider->accreditation }}">
                                @endif
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
                                    @if( old('subsidized') != "" )
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
                                    @else
                                        @if($provider->subsidized == "1")
                                            <option value="0">No</option>
                                            <option value="1" selected>Yes</option>
                                        @elseif($provider->subsidized == "0")
                                            <option value="0" selected>No</option>
                                            <option value="1">Yes</option>
                                        @else
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        @endif
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
                                    @if ( in_array('Not Available', explode(', ', $provider->transportation)) )
                                        <label><input type="checkbox" name="transportation[]" value="Not Available" checked="">Not Available</label>
                                    @else
                                        <label><input type="checkbox" name="transportation[]" value="Not Available">Not Available</label>
                                    @endif
                                @endif
                                @if (!empty(old('transportation')) && in_array('Emergency Only', old('transportation')))
                                    <label><input type="checkbox" name="transportation[]" value="Emergency Only" checked="">Emergency Only</label>
                                @else
                                    @if ( in_array('Emergency Only', explode(', ', $provider->transportation)) )
                                        <label><input type="checkbox" name="transportation[]" value="Emergency Only" checked="">Emergency Only</label>
                                    @else
                                        <label><input type="checkbox" name="transportation[]" value="Emergency Only">Emergency Only</label>
                                    @endif
                                @endif
                                @if (!empty(old('transportation')) && in_array('Field Trips', old('transportation')))
                                    <label><input type="checkbox" name="transportation[]" value="Field Trips" checked="">Field Trips</label>
                                @else
                                    @if ( in_array('Field Trips', explode(', ', $provider->transportation)) )
                                        <label><input type="checkbox" name="transportation[]" value="Field Trips" checked="">Field Trips</label>
                                    @else
                                        <label><input type="checkbox" name="transportation[]" value="Field Trips">Field Trips</label>
                                    @endif
                                @endif
                                @if (!empty(old('transportation')) && in_array('To/From School', old('transportation')))
                                    <label><input type="checkbox" name="transportation[]" value="To/From School" checked="">To/From School</label>
                                @else
                                    @if ( in_array('To/From School', explode(', ', $provider->transportation)) )
                                        <label><input type="checkbox" name="transportation[]" value="To/From School" checked="">To/From School</label>
                                    @else
                                        <label><input type="checkbox" name="transportation[]" value="To/From School">To/From School</label>
                                    @endif
                                @endif
                            @endif
                        </dd>
                        <dt id="additionalinfo-label"><label for="additionalinfo">Additional Information (Max 1000 characters):</label></dt>
                        <dd id="additionalinfo-element">
                            @if (isset($request->additionalinfo))
                                <textarea id="additionalinfo" name="additionalinfo" cols="15" rows="5">{{ $request->additionalinfo }}</textarea>
                            @else
                                @if (!empty(old('additionalinfo')))
                                    <textarea id="additionalinfo" name="additionalinfo" cols="15" rows="5">{{ old('additionalinfo') }}</textarea>
                                @else
                                    <textarea id="additionalinfo" name="additionalinfo" cols="15" rows="5">{{ $provider->additionalInfo }}</textarea>
                                @endif
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
            <?php endif; ?>
            <?php endif; ?>
        </section>
        <!---------right container------>
        <section class="right-sect">
            <?php if ($user): ?>
            Hello, <strong><?php echo $user->first_name ?: $user->email; ?></strong>
            <?php endif; ?>
            <div class="listSidebar">
                <h2>Actions</h2>
                <!-- <a class="btn m-t-10" href="/user/profile">Profile Information</a> -->
                <a class="btn m-t-10" href="/provider/update?pid=<?php echo $provider->id; ?>">Update Daycare Information</a>

                <?php if ($user && $user->multi_listings) :?>
                <a class="btn m-t-10" href="/provider/find">Find Your Childcare</a>
                <?php endif;?>

                <a class="btn m-t-10" href="/provider/imageupload?pid=<?php echo $provider->id; ?>">Upload Logo and Images</a>
                <a class="btn m-t-10" href="/provider/update-operation-hours?pid=<?php echo $provider->id; ?>">Update Operation
                    Hours</a>
                <a class="btn m-t-10" href="/reviews/view?pid=<?php echo $provider->id; ?>">View Review History</a>
                <a class="btn m-t-10" href="/inspection/view?pid=<?php echo $provider->id; ?>">View Inspection History</a>
                <a class="btn m-t-10" target="blank" href="/jobs/newjob">Post Your Job Requirements</a>
                <a class="btn m-t-10" href="/user/logout">Sign Out</a>
            </div>

            <div class="listSidebar">
                <h2>Notes:</h2>
                <ol>
                    <ol>
                        <?php if ($provider->approved == 0): ?>
                        <li>This daycare has not been approved yet. Please allow 2-3 business days for review.</li>
                        <?php else:?>
                        <li>Your daycare profile page has been visited <?php echo $provider->visits; ?> times.</li>
                        <?php endif;?>
                        <?php if (!$provider->is_center):?>
                        <li>If you prefer not to display your exact address, simply enter the name of your neighborhood, or
                            a nearby intersection or landmark.</li>
                        <?php endif;?>
                    </ol>
                </ol>
            </div>
        </section>
        <!-------right container ends------>
    </div>
    <div id="content" class="clearfix">
        <div id="right">

        </div>
        <div id="left">

        </div>
    </div>
@endsection
