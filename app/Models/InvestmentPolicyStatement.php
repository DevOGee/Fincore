<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentPolicyStatement extends Model
{
    protected $fillable = [
        'user_id',
        'risk_profile',
        'allocation_targets',
        'max_single_asset_pct',
        'rebalance_frequency',
    ];

    protected $casts = [
        'allocation_targets' => 'array',
        'max_single_asset_pct' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if current portfolio allocation is compliant with IPS targets
     */
    public function checkCompliance(): array
    {
        $investments = Investment::where('user_id', $this->user_id)->get();
        $totalValue = $investments->sum('current_value');

        if ($totalValue == 0) {
            return ['compliant' => true, 'message' => 'No investments to check'];
        }

        $alerts = [];
        $targets = $this->allocation_targets ?? [];

        // Check allocation drift
        $currentAllocation = [];
        foreach ($investments->groupBy('type') as $type => $group) {
            $typeValue = $group->sum('current_value');
            $currentAllocation[$type] = ($typeValue / $totalValue) * 100;
        }

        foreach ($targets as $assetType => $targetPct) {
            $actual = $currentAllocation[strtolower($assetType)] ?? 0;
            $drift = abs($actual - $targetPct);

            if ($drift > 10) { // 10% threshold
                $alerts[] = sprintf(
                    '%s allocation is %.1f%% (target: %.1f%%). Drift: %.1f%%',
                    ucfirst($assetType),
                    $actual,
                    $targetPct,
                    $drift
                );
            }
        }

        // Check concentration risk
        foreach ($investments as $investment) {
            $pct = ($investment->current_value / $totalValue) * 100;
            if ($pct > $this->max_single_asset_pct) {
                $alerts[] = sprintf(
                    '%s represents %.1f%% of portfolio (max allowed: %.1f%%)',
                    $investment->name,
                    $pct,
                    $this->max_single_asset_pct
                );
            }
        }

        return [
            'compliant' => empty($alerts),
            'alerts' => $alerts,
            'current_allocation' => $currentAllocation,
        ];
    }
}
