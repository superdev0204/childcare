<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\Facilityhours;
use App\Models\States;

class ProviderController extends Controller
{
    public function search(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();

        if(!$user || $user->caretype != 'ADMIN'){
            return redirect('/user/login');
        }

        $states = States::all();
        
        if ($method == "POST") {
            $post = $request->all();
            $post = array_filter($post);
            unset($post['search']);
            unset($post['_token']);

            if (empty($post)) {
                $message = 'Please enter a search criteria!';
                return view('admin.provider.search', compact('user', 'states', 'request', 'message'));
            }

            $query = Facility::orderBy('name', 'asc');
            if($request->name){
                $values = explode(' ', $request->name);
                foreach ($values as $value) {
                    $query->where('name', 'like', '%' . $value . '%');
                }
            }

            if($request->phone){
                $strippedPhoneNumber = preg_replace("/[^0-9]/", "", $request->phone);
                if (strlen($strippedPhoneNumber) == 10) {
                    $query->where('phone', 'like', '%' . substr($strippedPhoneNumber, 0, 3) . '%')
                        ->where('phone', 'like', '%' . substr($strippedPhoneNumber, 3, 3) . '-' . substr($strippedPhoneNumber, -4));
                } else {
                    $query->where('phone', 'like', '%' . $request->phone . '%');
                }
            }

            if($request->address){
                $query->where('address', 'like', '%' . $request->address . '%');
            }

            if($request->zip){
                $query->where('zip', $request->zip);
            }

            if($request->city){
                $query->where('city', $request->city);
            }

            if($request->state){
                $query->where('state', $request->state);
            }

            if($request->type){
                if($request->type == 'center'){
                    $query->where('is_center', 1);
                }
                else{
                    $query->where('is_center', 0);
                }
            }

            if($request->email){
                $query->where('email', $request->email);
            }

            if($request->id){
                $query->where('id', $request->id);
            }

            $providers = $query->limit(100)->get();

            return view('admin.provider.search', compact('user', 'states', 'request', 'providers'));
        }

        return view('admin.provider.search', compact('user', 'states', 'request'));
    }
    
    public function approve(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();
        
        $id = $request->id;

        if (!$method == "POST" || !$id) {
            return redirect('/admin');
        }

        /** @var Facility $provider */
        $provider = Facility::where('id', $id)->first();

        if (!$provider) {
            return redirect('/admin');
        }

        if (strlen($provider->introduction) >= 50) {
            $provider->ranking = $provider->ranking + 1;
        }

        if (strlen($provider->operation_id) >=5 ) {
            $provider->ranking = $provider->ranking + 1;
        }

        if (strlen($provider->website) >= 8) {
            $provider->ranking = $provider->ranking + 1;
        }

        if ($provider->is_center) {
            $provider->approved = 1;
        } else {
            $provider->ranking = $provider->ranking + 1;

            if ($provider->capacity >= 10) {
                $provider->ranking = $provider->ranking + 1;
            }
            $provider->approved = 2;
        }

        $provider->save();

        return redirect('/admin');
    }

    public function disapprove(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();

        $id = $request->id;

        if (!$method == "POST" || !$id) {
            return redirect('/admin');
        }

        /** @var Facility $provider */
        $provider = Facility::where('id', $id)->first();

        if (!$provider) {
            return redirect('/admin');
        }

        $provider->approved = -2;

        $provider->save();

        return redirect('/admin');
    }

    public function inactivate(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();

        $id = $request->id;

        if (!$method == "POST" || !$id) {
            return redirect('/admin');
        }

        /** @var Facility $provider */
        $provider = Facility::where('id', $id)->first();

        if (!$provider) {
            return redirect('/admin');
        }

        $provider->approved = -1;
        $provider->status = 'CLOSED';

        $provider->save();

        return redirect('/admin');
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();

        $id = $request->id;

        if (!$method == "POST" || !$id) {
            return redirect('/admin');
        }

        /** @var Facility $provider */
        $provider = Facility::where('id', $id)->first();
        
        if (!$provider) {
            return redirect('/admin');
        }

        if (!$provider->is_center) {
            $provider->approved = -5;
            $provider->save();
        }
        else{
            $provider->delete();
        }

        return redirect('/admin');
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();

        if(!$user || $user->caretype != 'ADMIN'){
            return redirect('/user/login');
        }

        $providerId = $request->id;
        $form = true;

        if (!$providerId) {
            return redirect('/admin/provider/search');
        }

        $provider = Facility::where('id', $providerId)->first();

        if (!$provider) {
            return redirect('/admin/provider/search');
        }

        if ($method == "POST") {
            $valid_item = [
                'name' => 'required|min:5',
                'address' => 'required|min:5',
                'city' => 'required|min:3',
                'zip' => 'required|min:5',
                'phone' => 'required|min:10',
                'capacity' => 'required',
                'typeofcare' => 'required|array',
            ];

            if($request->introduction){
                $valid_item['introduction'] = 'required|min:10';
            }
            
            if($request->language){
                $valid_item['language'] = 'required|min:4';
            }

            $validated = $request->validate($valid_item);

            $typeofcare = join(', ', $request->typeofcare);
            if ($request->transportation && is_array($request->transportation)) {
                $transportation = join(', ', $request->transportation);
            }
            else{
                $transportation = '';
            }
            
            $provider->name = $request->name;
            $provider->address = $request->address;
            $provider->city = ($request->city) ? $request->city : "";
            $provider->zip = ($request->zip) ? $request->zip : "";
            $provider->county = ($request->county) ? $request->county : "";
            $provider->phone = $request->phone;
            $provider->email = ($request->email) ? $request->email : "";
            $provider->website = ($request->website) ? $request->website : "";
            $provider->operation_id = ($request->operationId) ? $request->operationId : "";
            $provider->introduction = ($request->introduction) ? $request->introduction : "";
            $provider->capacity = $request->capacity;
            $provider->is_center = ($request->isCenter) ? $request->isCenter : 0;
            $provider->age_range = ($request->ageRange) ? $request->ageRange : "";
            $provider->pricing = ($request->pricing) ? $request->pricing : "";
            $provider->typeofcare = $typeofcare;
            $provider->schools_served = ($request->schoolsServed) ? $request->schoolsServed : "";
            $provider->language = ($request->language) ? $request->language : "";
            $provider->accreditation = ($request->accreditation) ? $request->accreditation : "";
            $provider->subsidized = ($request->subsidized) ? $request->subsidized : 0;
            $provider->transportation = $transportation;
            $provider->additionalInfo = ($request->additionalinfo) ? $request->additionalinfo : "";

            $provider->save();

            $form = false;
        }

        $provider->formatPhone = $this->formatPhoneNumber($provider->phone);

        return view('admin.provider.edit', compact('provider', 'user', 'form'));
    }

    public function updateOperationHours(Request $request)
    {
        $user = Auth::user();
        $method = $request->method();
        $message = '';

        if (!$user) {
            return redirect('/provider');
        }

        $providerId = $request->pid;

        if (!$providerId) {
            /** @var Facility $provider */
            $provider = $user->provider;
        } else {
            /** @var Facility $provider */
            $provider = Facility::where('id', $providerId)->first();
        }

        if (!$provider) {
            return redirect('/provider');
        }

        if($provider->operationHours){
            $operationHours = $provider->operationHours;
        }
        else{
            $operationHours = Facilityhours::create([
                'facility_id' => $providerId
            ]);
        }

        if ($method == "POST") {
            if(!$request->monday && !$request->tuesday && !$request->wednesday && !$request->thursday && !$request->friday && !$request->saturday && !$request->sunday){
                $message = 'You must fill out at least one weekday.';
            }
            else{
                $operationHours->monday = $request->monday;
                $operationHours->tuesday = $request->tuesday;
                $operationHours->wednesday = $request->wednesday;
                $operationHours->thursday = $request->thursday;
                $operationHours->friday = $request->friday;
                $operationHours->saturday = $request->saturday;
                $operationHours->sunday = $request->sunday;

                $provider->operationHours = $operationHours;

                $operationHours->save();

                $message = 'Operation hours have been updated successfully.';
            }
        }

        return view('admin.provider.update_operation_hours', compact('user', 'provider', 'message', 'request'));
    }

    public function geocode($street, $city, $state, $zip)
    {
        $url = "http://maps.googleapis.com/maps/api/geocode/xml?sensor=false";

        $address = $street . " " . $city . " " . $state . " " . $zip;

        $requestUrl = $url . "&address=" . urlencode($address);
        $xml = simplexml_load_file($requestUrl);

        if (!$xml) {
            return false;
        }

        $status = $xml->status;

        if (strcmp($status, "OK") == 0) {
            $location = $xml->result->geometry->location;

            return $location->lng . "," . $location->lat;
        } else {
            return false;
        }
    }

    public function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace("/[^0-9]/", "", $phoneNumber);

		if(strlen($phoneNumber) == 7) {
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phoneNumber);
        } elseif(strlen($phoneNumber) == 10) {
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phoneNumber);
        } else {
            return $phoneNumber;
        }
	}
}
