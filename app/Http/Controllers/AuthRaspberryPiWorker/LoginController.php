<?php

namespace App\Http\Controllers\AuthRaspberryPiWorker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:worker')->except(['logout', 'logoutUser']);
    }
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('authRaspberryPi.login');
    }
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'card' => 'required|min:10',
            'password' => 'required|min:6'
        ]);
        $credential = [
            'card' => $request->card,
            'password' => $request->password
        ];
        // Attempt to log the user in
        if (Auth::guard('worker')->attempt($credential, $request->member)){
            // If login succesful, then redirect to their intended location
            return redirect()->intended(route('raspberryPiWorker.home'));
        }
        // If Unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('card', 'remember'));
    }

    public function logout()
    {
        Auth::guard('worker')->logout();
        return redirect('/productie');
    }

}
