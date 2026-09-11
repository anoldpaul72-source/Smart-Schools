<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $dashboardRoute = null;
        $dashboardText  = 'Go to My Dashboard';

        if (Auth::check()) {
            $user = Auth::user();
            $dashboardRoute = match ($user->role) {
                'Admin'                       => route('admin.dashboard'),
                'Teacher'                     => route('teacher.marks'),
                'Parent'                      => route('parent.reports'),
                'Accountant'                                                        => route('accountant.fees'),
                'Head of School', 'Head Of School', 'Headmaster', 'Headmistress', 'Academic Master' => route('leader.dashboard'),
                default                                                             => route('home'),
            };
        }

        return view('home', compact('dashboardRoute', 'dashboardText'));
    }
}
