<?php

namespace App\Http\Controllers\User\Dashboard;

use App\Models\Admin\Announcement;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\User;
use Google2FA;
use Illuminate\Http\Request;
use TechTailor\RPG\Facade\RPG;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
	/**
     * Adding auth middleware to this controller.
     *
     */
    public function __construct()
	{
		$this->middleware(['auth','verified', '2fa']);
	}

    /**
     * Display the main Dashboard page 
     * 
     */
    public function index(Request $request) 
    {   
        $user = Auth::user();

        if($user->first_login)
        {
            $user->first_login = false;
            $user->save();
            return view('main.dashboard.first_login');
        }

        $announcement = Announcement::latest()->first();
        $loginIP = $user->previousLoginIp();
        $loginAt = $user->previousLoginAt();
        
    	return view('main.dashboard.index')->with(compact('announcement', 'loginIP', 'loginAt'));
    }

    public function announcements()
    {
        $announcements = Announcement::latest()->get();
        
        return view('main.dashboard.announcements')->with(compact('announcements'));
    }

    /**
     * Redirect '/' Url to '/dashboard' Url 
     * 
     */
    public function redirect()
    {
        return redirect('/dashboard');
    }
}
