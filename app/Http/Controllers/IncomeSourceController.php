<?php

namespace App\Http\Controllers;

use App\Models\IncomeSource;
use Illuminate\Http\Request;

class IncomeSourceController extends Controller
{
    public function index()
    {
        $incomeSources = IncomeSource::where('user_id', auth()->id())->latest()->paginate(10);
        return view('income_sources.index', compact('incomeSources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'linked_account' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();
        IncomeSource::create($validated);

        return redirect()->route('income_sources.index')->with('success', 'Income Source added successfully.');
    }

    public function edit(IncomeSource $incomeSource)
    {
        if ($incomeSource->user_id !== auth()->id()) {
            abort(403);
        }
        return view('income_sources.edit', compact('incomeSource'));
    }

    public function update(Request $request, IncomeSource $incomeSource)
    {
        if ($incomeSource->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'linked_account' => 'nullable|string|max:255',
        ]);

        $incomeSource->update($validated);

        return redirect()->route('income_sources.index')->with('success', 'Income Source updated successfully.');
    }

    public function destroy(IncomeSource $incomeSource)
    {
        if ($incomeSource->user_id !== auth()->id()) {
            abort(403);
        }
        $incomeSource->delete();
        return redirect()->route('income_sources.index')->with('success', 'Income Source deleted successfully.');
    }
}
