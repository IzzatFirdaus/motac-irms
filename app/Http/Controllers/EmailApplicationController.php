<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EmailApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the specified email application.
     */
    public function show(EmailApplication $emailApplication): View
    {
        return view('email-applications.show', [
            'emailApplication' => $emailApplication
        ]);
    }
}