<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Questions;
use App\Models\Answers;
use App\Models\Facility;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            $questions = Questions::where('approved', '0')
                                ->orderBy('created_at', 'desc')
                                ->get();
            
            foreach ($questions as $question) {
                if($question->facility_id){
                    $provider = Facility::where('id', $question->facility_id)->first();
                    $question->name = $provider->name;
                    $question->link = "/provider_detail/" . $provider->filename;
                }
                else{
                    $question->name = $question->page_url;
                    $question->link = $question->page_url;
                }
            }

            $answers = Answers::where('approved', '0')
                            ->orderBy('created_at', 'desc')
                            ->get();
            
            foreach ($answers as $answer) {
                if($answer->facility_id){
                    $provider = Facility::where('id', $answer->facility_id)->first();
                    $answer->name = $provider->name;
                    $answer->link = "/provider_detail/" . $provider->filename;
                }
                else{
                    $answer->name = $answer->page_url;
                    $answer->link = $answer->page_url;
                }
            }

            return view('admin.questionlog', compact('questions', 'answers', 'user'))->with('success', session('success'));
        }
        else{
            return redirect('/user/login');
        }      
    }

    public function approve_question(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype)){
            $questionid = $request->id;
            if(!empty($questionid)){
                $question = Questions::where('id', $questionid)->first();
                $question->approved = '1';
                $question->save();
                return redirect('/admin/question')->with('success', 'The question is approved successfully');
            }
            else{
                return redirect('/');
            }
        }
        else{
            return redirect('/user/login?return_url='.request()->path());
        }
    }

    public function disapprove_question(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype)){
            $questionid = $request->id;
            if(!empty($questionid)){
                $question = Questions::where('id', $questionid)->first();
                $question->approved = '-1';
                $question->save();
                return redirect('/admin/question')->with('success', 'The question is not approved successfully');
            }
            else{
                return redirect('/');
            }
        }
        else{
            return redirect('/user/login');
        }
    }

    public function approve_answer(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype)){
            $answerid = $request->id;
            if(!empty($answerid)){
                $answer = Answers::where('id', $answerid)->first();
                $answer->approved = '1';
                $answer->save();
                return redirect('/admin/question')->with('success', 'The answer is approved successfully');
            }
            else{
                return redirect('/');
            }
        }
        else{
            return redirect('/user/login');
        }
    }

    public function disapprove_answer(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype)){
            $answerid = $request->id;
            if(!empty($answerid)){
                $answer = Answers::where('id', $answerid)->first();
                $answer->approved = '-1';
                $answer->save();
                return redirect('/admin/question')->with('success', 'The answer is not approved successfully');
            }
            else{
                return redirect('/');
            }
        }
        else{
            return redirect('/user/login');
        }
    }

    public function question_editor(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && ($user->caretype == 'ADMIN')){
            if(isset($request->id) && !empty($request->id)){
                $question = Questions::find($request->id);
                if(!empty($question)){
                    return view('admin.question_editor', compact('user', 'question'));
                }
                else{
                    return redirect('/admin/question');
                }
            }
            else{
                return view('admin.question_editor', compact('user'));
            }
        }
    }

    public function question_update(Request $request)
    {
        $user = Auth::user();
        
        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            if(isset($request->question_id) && !empty($request->question_id)){
                $question = Questions::where('id', $request->question_id)->first();
                $question->question = strip_tags($request->question);
                $question->save();
                
                return redirect('/admin/question')->with('success', 'The Question updated successfully');
            }
            else{
                $question = Questions::create([
                    'facility_id' => 0,
                    'question' => strip_tags($request->question),
                    'user_id' => $user->id,
                    'question_by' => $user->firstname . ' ' . $user->lastname,
                    'question_email' => $user->email,
                    'approved' => '1'
                ]);

                return redirect('/admin/question')->with('success', 'The Question created successfully');
            }
        }
        else{
            return redirect('/');
        }
    }

    public function answer_editor(Request $request)
    {
        $user = Auth::user();

        if(isset($user->caretype) && ($user->caretype == 'ADMIN')){
            if(isset($request->id) && !empty($request->id)){
                $answer = Answers::find($request->id);
                if(!empty($answer)){
                    return view('admin.answer_editor', compact('user', 'answer'));
                }
                else{
                    return redirect('/admin/question');
                }
            }
            else{
                return redirect('/admin');
            }
        }
    }

    public function answer_update(Request $request)
    {
        $user = Auth::user();
        
        if(isset($user->caretype) && $user->caretype == 'ADMIN'){
            if(isset($request->answer_id) && !empty($request->answer_id)){
                $answer = Answers::where('id', $request->answer_id)->first();
                $answer->answer = strip_tags($request->answer);
                $answer->save();
                return redirect('/admin/question')->with('success', 'The Answer updated successfully');
            }            
        }
        else{
            return redirect('/');
        }
    }
}
