<?php

namespace App\Http\Controllers;

use App\Models\EmailFilterResult;
use Illuminate\Http\Request;

class EmailFilterResultController extends Controller
{
    public function index()
    {
        $results = EmailFilterResult::with('user')->latest()->paginate(20);
        return view('email-filter-results.index', compact('results'));
    }
}
