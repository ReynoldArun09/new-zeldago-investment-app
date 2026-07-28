<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateRoiRequests extends Command
{
    protected $signature = 'roi:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate pending ROI requests for active investments based on cycle days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roiSetting = \App\Models\Setting::where('key', 'roi_settings')->first();
        $settings = $roiSetting ? $roiSetting->value : [];
        $cycleDays = (int) ($settings['cycle_days'] ?? 1);

        $investments = \App\Models\Investment::where('status', 'ACTIVE')->get();
        $count = 0;

        foreach ($investments as $investment) {
            $lastRoi = \App\Models\RoiLog::where('investment_id', $investment->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $lastDate = $lastRoi ? $lastRoi->created_at : $investment->created_at;

            $lastDate = \Carbon\Carbon::parse($lastDate);
            // Use next_roi_date if available, fallback to created_at
            $targetDate = $investment->next_roi_date ?: $lastDate->addDays($cycleDays);

            if (now()->greaterThanOrEqualTo($targetDate)) {
                \App\Models\RoiLog::create([
                    'trx_id' => 'ROI-' . strtoupper(\Illuminate\Support\Str::random(10)),
                    'investment_id' => $investment->id,
                    'user_id' => $investment->user_id,
                    'amount' => 0,
                    'rate' => 0,
                    'status' => 'pending',
                ]);
                
                $investment->update([
                    'next_roi_date' => now()->addDays($cycleDays)
                ]);
                
                $count++;
            }
        }

        $this->info("Generated $count new pending ROI requests.");
    }
}
