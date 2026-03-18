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

    public function destroy($id)
    {
        $result = EmailFilterResult::findOrFail($id);
        $result->delete();
        return redirect()->route('email-filter.results')->with('success', 'Result deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            EmailFilterResult::whereIn('id', $ids)->delete();
            return redirect()->route('email-filter.results')->with('success', 'Selected results deleted successfully.');
        }
        return redirect()->route('email-filter.results')->with('error', 'No results selected for deletion.');
    }
}
