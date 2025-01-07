<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::pattern('state', '[a-z_]+');
Route::pattern('countyname', '[a-z_-]+');
Route::pattern('zipcode', '[0-9]+');
Route::pattern('city', '[a-z_-]+');
Route::pattern('filename', '[%a-zA-Z0-9_-]+');
Route::pattern('id', '\d+');
Route::pattern('pid', '\d+');
Route::pattern('rc', '\d+');
Route::pattern('uid', '\d+');
Route::pattern('action', '[a-z_-]+');
// Route::pattern('commentableId', '\d+');

Route::get('/', 'App\Http\Controllers\IndexController@index')->name('home');
Route::get('/rss', 'App\Http\Controllers\RssController@index')->name('rss');
Route::get('/verify', 'App\Http\Controllers\IndexController@verify')->name('verify');
Route::post('/verify', 'App\Http\Controllers\IndexController@verify');
Route::get('/about', 'App\Http\Controllers\IndexController@about');
Route::get('/search', 'App\Http\Controllers\IndexController@search');
Route::post('/search', 'App\Http\Controllers\IndexController@search_results');
Route::get('/homecare', 'App\Http\Controllers\HomeCareController@index');
Route::get('/{state}_homecare', 'App\Http\Controllers\HomeCareController@state')->name('homecare_state');
Route::get('/{state}_homecare/allcities', 'App\Http\Controllers\HomeCareController@all_cities')->name('homecare_allcities');
Route::get('/{state}_homecare/{countyname}_county', 'App\Http\Controllers\HomeCareController@county')->name('homecare_county');
Route::get('/{state}_homecare/{city}_city', 'App\Http\Controllers\HomeCareController@city')->name('homecare_city');
Route::get('/{state}_homecare/{zipcode}_zipcode', 'App\Http\Controllers\HomeCareController@zipcode')->name('homecare_zip');
Route::get('/state', 'App\Http\Controllers\CenterCareController@index');
Route::get('/state/{state}', 'App\Http\Controllers\CenterCareController@state')->name('centercare_state');
Route::get('/{state}/allcities', 'App\Http\Controllers\CenterCareController@all_cities')->name('centercare_allcities');
Route::get('/county/{countyname}', 'App\Http\Controllers\CenterCareController@county')->name('centercare_county');
Route::get('/{state}/{city}_childcare', 'App\Http\Controllers\CenterCareController@city')->name('centercare_city');
Route::get('/{state}/{city}_childcare.{format?}', 'App\Http\Controllers\CenterCareController@city')->where(['format' => 'amp'])->name('centercare_city_format');
Route::get('/{state}/{zipcode}_childcare', 'App\Http\Controllers\CenterCareController@zipcode')->name('centercare_zipcode');

Route::get('/classifieds', 'App\Http\Controllers\ClassifiedController@index');
Route::get('/{state}_classifieds', 'App\Http\Controllers\ClassifiedController@state')->name('classified_state');
Route::get('/classifieds/addetails', 'App\Http\Controllers\ClassifiedController@adDetails')->name('classified_addetails');
Route::get('/classifieds/newad', 'App\Http\Controllers\ClassifiedController@newAd')->name('classified_newad');
Route::post('/classifieds/newad', 'App\Http\Controllers\ClassifiedController@newAd');
Route::get('/classifieds/verifyad', 'App\Http\Controllers\ClassifiedController@verifyAd')->name('classified_verifyad');

Route::get('/about', 'App\Http\Controllers\IndexController@about');
Route::get('/faqs', 'App\Http\Controllers\IndexController@faqs');
Route::get('/privacy', 'App\Http\Controllers\IndexController@privacy');
Route::get('/wesupport', 'App\Http\Controllers\IndexController@wesupport');
Route::get('/review/guidelines', 'App\Http\Controllers\IndexController@guidelines');
Route::get('/feedback', 'App\Http\Controllers\FeedbackController@index');
Route::post('/feedback', 'App\Http\Controllers\FeedbackController@index');
Route::get('/contact', 'App\Http\Controllers\ContactController@index');
Route::post('/contact', 'App\Http\Controllers\ContactController@index');

Route::get('/user/new', 'App\Http\Controllers\RegisterController@index');
Route::post('/user/new', 'App\Http\Controllers\RegisterController@index');
Route::get('/user/resend', 'App\Http\Controllers\RegisterController@resendActivation');
Route::post('/user/resend', 'App\Http\Controllers\RegisterController@resendActivation');
Route::get('/user/activate', 'App\Http\Controllers\RegisterController@activate');
Route::get('/user/login', 'App\Http\Controllers\LoginController@login');
Route::post('/user/login', 'App\Http\Controllers\LoginController@login');
Route::get('/user/logout', 'App\Http\Controllers\LoginController@logout');
Route::get('/user/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/user/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('/user/pwdreset/{rc}/{uid}', 'App\Http\Controllers\Auth\ResetPasswordController@reset');
Route::post('/user/pwdreset/{rc}/{uid}', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');
Route::get('/user/profile', 'App\Http\Controllers\UserController@index');
Route::get('/user/update', 'App\Http\Controllers\UserController@update');
Route::post('/user/update', 'App\Http\Controllers\UserController@update');
Route::get('/user/password', 'App\Http\Controllers\UserController@password');
Route::post('/user/password', 'App\Http\Controllers\UserController@password');

Route::GET('/provider', 'App\Http\Controllers\ProviderController@index')->name('provider');
Route::GET('/provider/find', 'App\Http\Controllers\ProviderController@find');
Route::POST('/provider/find', 'App\Http\Controllers\ProviderController@find');
Route::GET('/provider/new', 'App\Http\Controllers\ProviderController@new');
Route::POST('/provider/new', 'App\Http\Controllers\ProviderController@new');
Route::GET('/provider/update', 'App\Http\Controllers\ProviderController@update');
Route::POST('/provider/update', 'App\Http\Controllers\ProviderController@update');
Route::GET('/provider/update-operation-hours', 'App\Http\Controllers\ProviderController@updateOperationHours');
Route::POST('/provider/update-operation-hours', 'App\Http\Controllers\ProviderController@updateOperationHours');
Route::GET('/provider_detail/{filename}', 'App\Http\Controllers\ProviderController@view');
Route::GET('/provider/view', 'App\Http\Controllers\ProviderController@view');
Route::POST('/provider/claim', 'App\Http\Controllers\ProviderController@claim');
Route::GET('/provider/imageupload', 'App\Http\Controllers\ImageController@upload');
Route::POST('/provider/imageupload', 'App\Http\Controllers\ImageController@upload');
Route::GET('/provider/imagedelete', 'App\Http\Controllers\ImageController@delete');

Route::get('/send_question', 'App\Http\Controllers\QuestionController@send_question');
Route::post('/send_question', 'App\Http\Controllers\QuestionController@send_question');
Route::get('/send_answer', 'App\Http\Controllers\QuestionController@send_answer');
Route::post('/send_answer', 'App\Http\Controllers\QuestionController@send_answer');

Route::GET('/inspection/view', 'App\Http\Controllers\InspectionController@index');
Route::GET('/reviews/view', 'App\Http\Controllers\ReviewController@index');
Route::GET('/review/new', 'App\Http\Controllers\ReviewController@new');
Route::POST('/review/new', 'App\Http\Controllers\ReviewController@new');
Route::GET('/review/verify/{id}/{pid}', 'App\Http\Controllers\ReviewController@verify');
Route::GET('/review/{id}/response', 'App\Http\Controllers\ReviewController@response')->name('review_owner_response');
Route::POST('/review/{id}/response', 'App\Http\Controllers\ReviewController@response');
Route::GET('/review/{id}/vote', 'App\Http\Controllers\ReviewController@vote')->name('review_vote');

Route::GET('/jobs', 'App\Http\Controllers\JobController@index');
Route::get('/{state}_jobs', 'App\Http\Controllers\JobController@state');
Route::get('/{state}_jobs/{city}_city', 'App\Http\Controllers\JobController@city');
Route::get('/jobs/detail', 'App\Http\Controllers\JobController@view');
Route::get('/jobs/verifyjob', 'App\Http\Controllers\JobController@verify');
Route::get('/jobs/new', 'App\Http\Controllers\JobController@create');
Route::post('/jobs/new', 'App\Http\Controllers\JobController@create');
Route::get('/jobs/update', 'App\Http\Controllers\JobController@update');
Route::post('/jobs/update', 'App\Http\Controllers\JobController@update');
Route::GET('/resumes', 'App\Http\Controllers\ResumeController@index');
Route::get('/{state}_resumes', 'App\Http\Controllers\ResumeController@state');
Route::get('/resumes/detail', 'App\Http\Controllers\ResumeController@view');
Route::get('/resumes/new', 'App\Http\Controllers\ResumeController@create');
Route::post('/resumes/new', 'App\Http\Controllers\ResumeController@create');

Route::get('/admin', 'App\Http\Controllers\Admin\IndexController@index')->name('admin');
Route::get('/admin/provider/search', 'App\Http\Controllers\Admin\ProviderController@search');
Route::post('/admin/provider/search', 'App\Http\Controllers\Admin\ProviderController@search');
Route::post('/admin/provider/approve', 'App\Http\Controllers\Admin\ProviderController@approve');
Route::post('/admin/provider/disapprove', 'App\Http\Controllers\Admin\ProviderController@disapprove');
Route::post('/admin/provider/inactivate', 'App\Http\Controllers\Admin\ProviderController@inactivate');
Route::post('/admin/provider/delete', 'App\Http\Controllers\Admin\ProviderController@delete');
Route::get('/admin/provider/edit', 'App\Http\Controllers\Admin\ProviderController@edit');
Route::post('/admin/provider/edit', 'App\Http\Controllers\Admin\ProviderController@edit');
Route::get('/admin/provider/update-operation-hours', 'App\Http\Controllers\Admin\ProviderController@updateOperationHours');
Route::post('/admin/provider/update-operation-hours', 'App\Http\Controllers\Admin\ProviderController@updateOperationHours');
Route::get('/admin/provider-log/show/id/{id}', 'App\Http\Controllers\Admin\ProviderLogController@show');
Route::post('/admin/provider-log/approve', 'App\Http\Controllers\Admin\ProviderLogController@approve');
Route::post('/admin/provider-log/disapprove', 'App\Http\Controllers\Admin\ProviderLogController@disapprove');
Route::post('/admin/provider-log/delete', 'App\Http\Controllers\Admin\ProviderLogController@delete');
Route::post('/admin/classified/approve', 'App\Http\Controllers\Admin\ClassifiedController@approve');
Route::post('/admin/classified/disapprove', 'App\Http\Controllers\Admin\ClassifiedController@disapprove');
Route::get('/admin/review/', 'App\Http\Controllers\Admin\ReviewController@index');
Route::get('/admin/review/find', 'App\Http\Controllers\Admin\ReviewController@find');
// Route::post('/admin/review/find', 'App\Http\Controllers\Admin\ReviewController@find');
Route::post('/admin/review/approve', 'App\Http\Controllers\Admin\ReviewController@approve');
Route::post('/admin/review/remove', 'App\Http\Controllers\Admin\ReviewController@remove');
Route::post('/admin/review/disapprove', 'App\Http\Controllers\Admin\ReviewController@disapprove');
Route::get('/admin/review/responses', 'App\Http\Controllers\Admin\ReviewController@response');
Route::post('/admin/review/approve-response', 'App\Http\Controllers\Admin\ReviewController@approveResponse');
Route::post('/admin/review/disapprove-response', 'App\Http\Controllers\Admin\ReviewController@disapproveResponse');
Route::get('/admin/job/', 'App\Http\Controllers\Admin\JobController@index');
Route::post('/admin/job/approve', 'App\Http\Controllers\Admin\JobController@approve');
Route::post('/admin/job/disapprove', 'App\Http\Controllers\Admin\JobController@disapprove');
Route::get('/admin/resume/', 'App\Http\Controllers\Admin\ResumeController@index');
Route::post('/admin/resume/approve', 'App\Http\Controllers\Admin\ResumeController@approve');
Route::post('/admin/resume/disapprove', 'App\Http\Controllers\Admin\ResumeController@disapprove');
Route::get('/admin/iptracker/', 'App\Http\Controllers\Admin\IptrackerController@index');
Route::get('/admin/image/', 'App\Http\Controllers\Admin\ImageController@index');
Route::post('/admin/image/approve', 'App\Http\Controllers\Admin\ImageController@approve');
Route::post('/admin/image/disapprove', 'App\Http\Controllers\Admin\ImageController@disapprove');
Route::post('/admin/image/delete', 'App\Http\Controllers\Admin\ImageController@delete');
Route::get('/admin/user/find', 'App\Http\Controllers\Admin\UserController@find');
Route::get('/admin/user/switch', 'App\Http\Controllers\Admin\UserController@switch');
Route::post('/admin/user/activate', 'App\Http\Controllers\Admin\UserController@activate');
Route::post('/admin/user/reset', 'App\Http\Controllers\Admin\UserController@reset');
Route::get('/admin/feedback/', 'App\Http\Controllers\Admin\FeedbackController@index');
Route::post('/admin/feedback/spam', 'App\Http\Controllers\Admin\FeedbackController@spam');
Route::post('/admin/feedback/disapprove', 'App\Http\Controllers\Admin\FeedbackController@disapprove');

Route::get('/admin/visitor_counts', 'App\Http\Controllers\Admin\VisitorController@visitor_counts');
Route::get('/admin/visitor_delete', 'App\Http\Controllers\Admin\VisitorController@delete_visitor');
Route::get('/admin/visitor_summary', 'App\Http\Controllers\Admin\VisitorController@visitor_summary');
Route::post('/admin/visitor_summary', 'App\Http\Controllers\Admin\VisitorController@visitor_summary');
Route::get('/admin/visitor_summary_delete', 'App\Http\Controllers\Admin\VisitorController@delete_visitor_summary');

Route::get('/admin/question', 'App\Http\Controllers\Admin\QuestionController@index')->name('admin.question');
Route::get('/admin/question_editor', 'App\Http\Controllers\Admin\QuestionController@question_editor');
Route::post('/admin/question_update', 'App\Http\Controllers\Admin\QuestionController@question_update');
Route::get('/admin/answer_editor', 'App\Http\Controllers\Admin\QuestionController@answer_editor');
Route::post('/admin/answer_update', 'App\Http\Controllers\Admin\QuestionController@answer_update');
Route::post('/admin/question/approve', 'App\Http\Controllers\Admin\QuestionController@approve_question');
Route::post('/admin/question/disapprove', 'App\Http\Controllers\Admin\QuestionController@disapprove_question');
Route::post('/admin/question/delete', 'App\Http\Controllers\Admin\QuestionController@delete_question');
Route::post('/admin/answer/approve', 'App\Http\Controllers\Admin\QuestionController@approve_answer');
Route::post('/admin/answer/disapprove', 'App\Http\Controllers\Admin\QuestionController@disapprove_answer');
Route::post('/admin/answer/delete', 'App\Http\Controllers\Admin\QuestionController@delete_answer');


// Route::get('/jobs', function () {
//     return Redirect::to('https://childcarejob.org', 301);
// });

// Route::get('/jobs/newjob', function () {
//     return Redirect::to('https://childcarejob.org/jobs/new', 301);
// });

// Route::get('/jobs/newresume', function () {
//     return Redirect::to('https://childcarejob.org/resumes/new', 301);
// });

// Route::get('/jobs/jobdetail', function () {
//     return Redirect::to('https://childcarejob.org/jobs/detail', 301);
// });

// Route::get('/jobs/resume', function () {
//     return Redirect::to('https://childcarejob.org/resumes/detail', 301);
// });