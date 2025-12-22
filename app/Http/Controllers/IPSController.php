<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPolicyStatement;
use Illuminate\Http\Request;

class IPSController extends Controller
{
    public function index()
    {
        $ips = InvestmentPolicyStatement::where('user_id', auth()->id())->first();
        $compliance = $ips ? $ips->checkCompliance() : null;

        return view('ips.index', compact('ips', 'compliance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'risk_profile' => 'required|in:conservative,moderate,aggressive',
            'allocation_targets' => 'nullable|array',
            'max_single_asset_pct' => 'required|numeric|min:5|max:100',
            'rebalance_frequency' => 'required|in:monthly,quarterly,annually',
        ]);

        $validated['user_id'] = auth()->id();

        // Upsert - create or update
        InvestmentPolicyStatement::updateOrCreate(
            ['user_id' => auth()->id()],
            $validated
        );

        return redirect()->route('ips.index')->with('success', 'Investment Policy Statement saved successfully.');
    }
}
