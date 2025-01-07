<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Facilitylog;
use App\Models\Facility;
use DOMDocument;

class ProviderLogController extends Controller
{
    public function show(Request $request, string $id)
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $providerLog = Facilitylog::with('provider')->find($id);

            if (!$providerLog) {
                return redirect('/admin');
            }

            return view('admin.providerlog.index', compact('providerLog', 'user'));
        }
        else{
            return redirect('/user/login');
        }
    }

    public function approve(Request $request)
    {
        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        $providerLog = Facilitylog::with('provider')->find($request->id);

        $fields = array_keys($providerLog->getEditableFields());

        $update_provider = [];

        $provider = Facility::find($providerLog->provider_id);
        
        foreach ($fields as $key => $field) {
            if ($providerLog->$field == $providerLog->provider->$field) {
                continue;
            }

            if ($field == 'introduction') {
                if (strlen($provider->introduction) < 50  &&
                    strlen($providerLog->introduction) >= 50) {
                        $update_provider['created_date'] = new \DateTime();
                }

                $update_provider['introduction'] = $providerLog->introduction ? $this->filter($providerLog->introduction) : '';

                continue;
            }

            $update_provider[$field] = $providerLog->$field;
        }
        
        if (!$provider->is_center) {
            $update_provider['approved'] = 2;
        }

        $providerLog->approved = 1;
        $providerLog->save();

        $user = $providerLog->user;

        if (!$provider->user_id) {
            $update_provider['user_id'] = $providerLog->user_id;
        }
        
        $provider->update($update_provider);

        if ($user && !$user->provider) {
            $user->provider_id = $provider->id;
            $user->save();
        }

        return redirect()->route('admin');
    }

    public function disapprove(Request $request)
    {
        $providerLog = Facilitylog::with('provider')->find($request->id);

        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        $providerLog->update(['approved' => -2]);

        return redirect()->route('admin');
    }

    public function delete(Request $request)
    {
        $schoolLog = Schoollogs::with('school')->find($request->id);

        if (!$request->isMethod('post')){

            return response()->json(['error' => true, 'data' => []], 404);
        }

        $schoolLog->delete($schoolLog->id);

        return redirect()->route('admin');
    }

    public function filter($value)
    {
        $dom = new DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $anchors = $dom->getElementsByTagName('a');

        foreach($anchors as $anchor) {
            $rel = [];

            if (preg_match("/childcarecenter/", $anchor->getAttribute('href')) ) {
                continue;
            }

            if (!$anchor->hasAttribute('target')) {
                $anchor->setAttribute('target','_blank');
            }

            if ($anchor->hasAttribute('rel') && ($relAtt = $anchor->getAttribute('rel')) !== '') {
                $rel = preg_split('/\s+/', trim($relAtt));
            }

            if (in_array('nofollow', $rel)) {
                continue;
            }

            $rel[] = 'nofollow';
            $anchor->setAttribute('rel', implode(' ', $rel));
        }

        $dom->saveHTML();

        $html = '';

        $childNodes = $dom->getElementsByTagName('body')->item(0)->childNodes;

        if ($childNodes) {
            foreach($childNodes as $element) {
                $html .= $dom->saveXML($element, LIBXML_NOEMPTYTAG);
            }
        }

        return $html;
    }
}
