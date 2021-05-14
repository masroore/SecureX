<?php

namespace App\Http\Controllers\Auth;

use App\Models\Users\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Notifications\User\PasswordResetRequested;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     * 
     * Redirect to HOME if the user is authenticated.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        if(Auth::guest())
            return view('auth.passwords.email');
        else
            return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        $user = User::where('email', $request->email)->first();

        $user->notify(new PasswordResetRequested());

        return $request->wantsJson()
            ? new JsonResponse(['message' => trans($response)], 200)
            : back()->with('status', trans($response));
    }
}
