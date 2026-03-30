<?php

namespace App\Http\Controllers;

use App\Models\EmailFilterResult;
use Illuminate\Http\Request;

class EmailFilterResultController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (strtolower($user?->email ?? '') === 'admin@gmail.com') {
            // Admin sees all results
            $results = EmailFilterResult::with('user')->latest()->paginate(20);
        } else {
            // Other users see only their own results
            $results = EmailFilterResult::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(20);
        }
        return view('email-filter-results.index', compact('results'));
    }

    public function destroy($id)
    {
        $result = EmailFilterResult::findOrFail($id);
        $result->delete();
        return redirect()->route('email-filter.results')->with('success', 'Result deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        // Handle comma-separated string or array
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        if (!empty($ids)) {
            EmailFilterResult::whereIn('id', $ids)->delete();
            return redirect()->route('email-filter.results')->with('success', 'Selected results deleted successfully.');
        }
        return redirect()->route('email-filter.results')->with('error', 'No results selected for deletion.');
    }
}
