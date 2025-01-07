@extends('layouts.app_old')

@section('content')
    <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>

    <style>
        #typeofcare-element label{
            display:contents;
        }
    </style>

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
            <a href="/admin">Admin</a> &gt;&gt; Update<br />
            <a href="/admin/provider/update-operation-hours?pid=<?php echo $provider->id; ?>">Update Operation Hours</a><br />
            <?php if (!$form): ?>
            <h2>Updates successful!</h2>
            <strong>Name:</strong> <a target="_blank"
                href="/provider_detail/<?php echo $provider->filename; ?>"><?php echo $provider->name; ?></a><br />
            <strong>Address:</strong> <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br />
            <strong>County:</strong> <?php echo $provider->county; ?><br>
            <strong>Contact Phone:</strong> <?php echo $provider->formatPhone; ?><br>
            <?php if($provider->is_center) : ?>
            <strong>Center Type:</strong> <?php echo $provider->type; ?>
            <?php else: ?>
            <strong>Home Type:</strong> <?php echo $provider->type; ?>
            <?php endif; ?>
            <br />
            <?php if($provider->website != "") : ?>
            <strong>Website:</strong> <?php echo $provider->website; ?><br />
            <?php endif; ?>
            <?php if($provider->email != "") : ?>
            <strong>Email:</strong> <?php echo $provider->email; ?><br />
            <?php endif; ?>
            <?php if($provider->introduction != "") : ?>
            <strong>Introduction:</strong> <?php echo $provider->introduction; ?><br />
            <?php endif; ?>
            <?php if($provider->operation_id != "") : ?>
            <strong>License Number:</strong> <?php echo $provider->operation_id; ?><br />
            <?php endif; ?>
            <?php if($provider->capacity > 0) : ?>
            <strong>Capacity:</strong> <?php echo $provider->capacity; ?><br />
            <?php endif; ?>
            <?php if($provider->age_range != "") : ?>
            <strong>Age Range:</strong> <?php echo $provider->age_range; ?><br />
            <?php endif; ?>
            <?php if($provider->pricing != "") : ?>
            <strong>Rate Range:</strong> <?php echo $provider->pricing; ?><br />
            <?php endif; ?>
            <?php if($provider->accreditation != "") : ?>
            <strong>Achievement and/or Accreditations: </strong><?php echo $provider->accreditation; ?><br />
            <?php endif; ?>
            <?php if($provider->daysopen != "") : ?>
            <strong>Days of Operation: </strong><?php echo $provider->daysopen; ?><br />
            <?php endif; ?>
            <?php if($provider->hoursopen != "") : ?>
            <strong>Normal Open Hours:</strong> <?php echo $provider->hoursopen; ?><br />
            <?php endif; ?>
            <?php if($provider->subsidized != "") : ?>
            <strong>Enrolled in Subsidized Child Care Program: </strong><?php echo $provider->subsidized == 1 ? 'Yes' : 'No'; ?><br />
            <?php endif; ?>
            <?php if($provider->language != "") : ?>
            <strong>Languages Supported: </strong><?php echo $provider->language; ?><br />
            <?php endif; ?>
            <?php if($provider->schools_served != "") : ?>
            <strong>Schools Served: </strong><?php echo $provider->schools_served; ?><br />
            <?php endif; ?>
            <?php if($provider->typeofcare != "") : ?>
            <strong>Type of Care: </strong><?php echo $provider->typeofcare; ?><br />
            <?php endif; ?>
            <?php if($provider->transportation != "") : ?>
            <strong>Transportation: </strong><?php echo $provider->transportation; ?><br />
            <?php endif; ?>
            <?php if($provider->additionalInfo != "") : ?>
            <strong>Additional Information: </strong><?php echo $provider->additionalInfo; ?><br />
            <?php endif; ?>
            <form method="get" action="/admin/provider/edit">
                <input type="hidden" name="id" value="<?php echo $provider->id; ?>" />
                <input type="submit" value="Update More" />
            </form>
            <?php else: ?>
            <strong>Name: </strong><?php echo $provider->name; ?><br />
            <strong>Address:</strong> <?php echo $provider->address . ', ' . $provider->city . ' ' . $provider->state . ' ' . $provider->zip; ?><br />

            <?php if (isset($message)): ?>
            <div class="error">
                <h2><?php echo $message; ?></h2>
            </div><br />
            <?php endif;?>

            <form method="post" action="/admin/provider/edit?id={{ $provider->id }}">
                @csrf
                <dl class="zend_form">
                    <dt id="name-label"><label class="required" for="name">Name:</label></dt>
                    <dd id="name-element">
                        @if (isset($request->name))
                            <input type="text" class="textfield" id="name" name="name" value="{{ $request->name }}">
                        @else
                            @if (!empty(old('name')))
                                <input type="text" class="textfield" id="name" name="name" value="{{ old('name') }}">
                            @else
                                <input type="text" class="textfield" id="name" name="name" value="{{ $provider->name }}">
                            @endif
                        @endif
                        @error('name')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="address-label"><label class="required" for="address">Address:</label></dt>
                    <dd id="address-element">
                        @if (isset($request->address))
                            <input type="text" class="textfield" id="address" name="address" value="{{ $request->address }}">
                        @else
                            @if (!empty(old('address')))
                                <input type="text" class="textfield" id="address" name="address" value="{{ old('address') }}">
                            @else
                                <input type="text" class="textfield" id="address" name="address" value="{{ $provider->address }}">
                            @endif
                        @endif
                        @error('address')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="city-label"><label class="required" for="city">City:</label></dt>
                    <dd id="city-element">
                        @if (isset($request->city))
                            <input type="text" class="textfield" id="city" name="city" value="{{ $request->city }}">
                        @else
                            @if (!empty(old('city')))
                                <input type="text" class="textfield" id="city" name="city" value="{{ old('city') }}">
                            @else
                                <input type="text" class="textfield" id="city" name="city" value="{{ $provider->city }}">
                            @endif
                        @endif
                        @error('city')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="zip-label"><label class="required" for="zip">Zip:</label></dt>
                    <dd id="zip-element">
                        @if (isset($request->zip))
                            <input type="text" class="textfield" id="zip" name="zip" value="{{ $request->zip }}">
                        @else
                            @if (!empty(old('zip')))
                                <input type="text" class="textfield" id="zip" name="zip" value="{{ old('zip') }}">
                            @else
                                <input type="text" class="textfield" id="zip" name="zip" value="{{ $provider->zip }}">
                            @endif
                        @endif
                        @error('zip')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="county-label"><label class="required" for="county">County:</label></dt>
                    <dd id="county-element">
                        @if (isset($request->county))
                            <input type="text" class="textfield" id="county" name="county" value="{{ $request->county }}">
                        @else
                            @if (!empty(old('county')))
                                <input type="text" class="textfield" id="county" name="county" value="{{ old('county') }}">
                            @else
                                <input type="text" class="textfield" id="county" name="county" value="{{ $provider->county }}">
                            @endif
                        @endif
                        @error('county')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="phone-label"><label class="required" for="phone">Contact Phone:</label></dt>
                    <dd id="phone-element">
                        @if (isset($request->phone))
                            <input type="text" class="textfield" id="phone" name="phone" value="{{ $request->phone }}">
                        @else
                            @if (!empty(old('phone')))
                                <input type="text" class="textfield" id="phone" name="phone" value="{{ old('phone') }}">
                            @else
                                <input type="text" class="textfield" id="phone" name="phone" value="{{ $provider->phone }}">
                            @endif
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
                            <input type="text" class="textfield" id="website" name="website" value="{{ $request->website }}">
                        @else
                            @if (!empty(old('website')))
                                <input type="text" class="textfield" id="website" name="website" value="{{ old('website') }}">
                            @else
                                <input type="text" class="textfield" id="website" name="website" value="{{ $provider->website }}">
                            @endif
                        @endif
                        @error('website')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="email-label"><label for="email">Email address:</label></dt>
                    <dd id="email-element">
                        @if (isset($request->email))
                            <input type="email" class="textfield" id="email" name="email" value="{{ $request->email }}">
                        @else
                            @if (!empty(old('email')))
                                <input type="email" class="textfield" id="email" name="email" value="{{ old('email') }}">
                            @else
                                <input type="email" class="textfield" id="email" name="email" value="{{ $provider->email }}">
                            @endif
                        @endif
                        @error('email')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="introduction-label"><label for="introduction">Introduction (150 - 1000 characters):</label></dt>
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
                            <input type="text" class="textfield" id="operationId" name="operationId" value="{{ $request->operationId }}">
                        @else
                            @if (!empty(old('operationId')))
                                <input type="text" class="textfield" id="operationId" name="operationId" value="{{ old('operationId') }}">
                            @else
                                <input type="text" class="textfield" id="operationId" name="operationId" value="{{ $provider->operation_id }}">
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
                            <input type="text" class="textfield" id="capacity" name="capacity" value="{{ $request->capacity }}">
                        @else
                            @if (!empty(old('capacity')))
                                <input type="text" class="textfield" id="capacity" name="capacity" value="{{ old('capacity') }}">
                            @else
                                <input type="text" class="textfield" id="capacity" name="capacity" value="{{ $provider->capacity }}">
                            @endif
                        @endif
                        @error('capacity')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="isCenter-label"><label class="required" for="isCenter">Provider Type:</label></dt>
                    <dd id="isCenter-element">
                        <select class="textfield" id="isCenter" name="isCenter">
                            @if( isset($request->isCenter) )
                                @if($request->isCenter == "1")
                                    <option value="0">Home Daycare</option>
                                    <option value="1" selected>Childcare Center</option>
                                @elseif($request->isCenter == "0")
                                    <option value="0" selected>Home Daycare</option>
                                    <option value="1">Childcare Center</option>
                                @else
                                    <option value="0">Home Daycare</option>
                                    <option value="1">Childcare Center</option>
                                @endif
                            @else
                                @if( old('isCenter') != "" )
                                    @if(old('isCenter') == "1")
                                        <option value="0">Home Daycare</option>
                                        <option value="1" selected>Childcare Center</option>
                                    @elseif(old('isCenter') == "0")
                                        <option value="0" selected>Home Daycare</option>
                                        <option value="1">Childcare Center</option>
                                    @else
                                        <option value="0">Home Daycare</option>
                                        <option value="1">Childcare Center</option>
                                    @endif
                                @else
                                    @if($provider->is_center == "1")
                                        <option value="0">Home Daycare</option>
                                        <option value="1" selected>Childcare Center</option>
                                    @elseif($provider->is_center == "0")
                                        <option value="0" selected>Home Daycare</option>
                                        <option value="1">Childcare Center</option>
                                    @else
                                        <option value="0">Home Daycare</option>
                                        <option value="1">Childcare Center</option>
                                    @endif
                                @endif
                            @endif
                        </select>
                        @error('isCenter')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="ageRange-label"><label for="ageRange">Ages Willing to Serve:</label></dt>
                    <dd id="ageRange-element">
                        @if (isset($request->ageRange))
                            <input type="text" class="textfield" id="ageRange" name="ageRange" value="{{ $request->ageRange }}">
                        @else
                            @if (!empty(old('ageRange')))
                                <input type="text" class="textfield" id="ageRange" name="ageRange" value="{{ old('ageRange') }}">
                            @else
                                <input type="text" class="textfield" id="ageRange" name="ageRange" value="{{ $provider->age_range }}">
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
                            <input type="text" class="textfield" id="pricing" name="pricing" value="{{ $request->pricing }}">
                        @else
                            @if (!empty(old('pricing')))
                                <input type="text" class="textfield" id="pricing" name="pricing" value="{{ old('pricing') }}">
                            @else
                                <input type="text" class="textfield" id="pricing" name="pricing" value="{{ $provider->pricing }}">
                            @endif
                        @endif
                        @error('pricing')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="typeofcare-label"><label class="required" for="typeofcare">Type of Care:</label></dt>
                    <dd id="typeofcare-element">
                        {{-- @if (isset($request->typeofcare))
                            <input type="text" id="typeofcare" name="typeofcare" value="{{ $request->typeofcare }}">
                        @else
                            @if (!empty(old('typeofcare')))
                                <input type="text" id="typeofcare" name="typeofcare" value="{{ old('typeofcare') }}">
                            @else
                                <input type="text" id="typeofcare" name="typeofcare" value="{{ $provider->typeofcare }}">
                            @endif
                        @endif
                        @error('typeofcare')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror --}}
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
                            <input type="text" class="textfield" id="schoolsServed" name="schoolsServed" value="{{ $request->schoolsServed }}">
                        @else
                            @if (!empty(old('schoolsServed')))
                                <input type="text" class="textfield" id="schoolsServed" name="schoolsServed" value="{{ old('schoolsServed') }}">
                            @else
                                <input type="text" class="textfield" id="schoolsServed" name="schoolsServed" value="{{ $provider->schools_served }}">
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
                            <input type="text" class="textfield" id="language" name="language" value="{{ $request->language }}">
                        @else
                            @if (!empty(old('language')))
                                <input type="text" class="textfield" id="language" name="language" value="{{ old('language') }}">
                            @else
                                <input type="text" class="textfield" id="language" name="language" value="{{ $provider->language }}">
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
                            <input type="text" class="textfield" id="accreditation" name="accreditation" value="{{ $request->accreditation }}">
                        @else
                            @if (!empty(old('accreditation')))
                                <input type="text" class="textfield" id="accreditation" name="accreditation" value="{{ old('accreditation') }}">
                            @else
                                <input type="text" class="textfield" id="accreditation" name="accreditation" value="{{ $provider->accreditation }}">
                            @endif
                        @endif
                        @error('accreditation')
                            <ul>
                                <li>{{ $message }}</li>
                            </ul>
                        @enderror
                    </dd>
                    <dt id="subsidized-label"><label class="required" for="subsidized">Enrolled in Subsidized Child Care
                            Program?:</label></dt>
                    <dd id="subsidized-element">
                        <select class="textfield" id="subsidized" name="subsidized">
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
                    <dt id="transportation-label"><label for="transportation">Transportation (select all that
                            apply):</label></dt>
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
                    <dt id="additionalinfo-label"><label for="additionalinfo">Additional Information (Max 1000
                            characters):</label></dt>
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
                    <input type="hidden" name="id" value="<?php echo $provider->id; ?>">
                    <dt id="update-label">&nbsp;</dt>
                    <dd id="update-element">
                        <input type="submit" name="submit" value="Update">
                    </dd>
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
            <?php endif;?>
        </div>
    </div>
@endsection
