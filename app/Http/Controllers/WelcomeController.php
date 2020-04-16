<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Show the landing homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('admin.home');
        }

        $plans = Role::whereNotNull('stripe_plan_id')->get();

        if ($plans) {
            $planClass = 12 / ($plans->count() + 1);
        }

        return view('welcome', compact('plans', 'planClass'));
    }

}
