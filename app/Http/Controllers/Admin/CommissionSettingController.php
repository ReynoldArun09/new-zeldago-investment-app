<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommissionSetting;

class CommissionSettingController extends Controller
{
    /**
     * Show the commission settings form.
     */
    public function edit()
    {
        $settings = CommissionSetting::first() ?? new CommissionSetting();
        return view('admin.settings.commission', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'level_count' => 'required|integer|min:1|max:50',
            'commissions' => 'required|array',
            'commissions.*' => 'required|numeric|min:0|max:100',
        ]);

        $settings = CommissionSetting::first();
        
        $data = [
            'level_count' => $request->level_count,
            'commissions' => $request->commissions
        ];

        if ($settings) {
            $settings->update($data);
        } else {
            CommissionSetting::create($data);
        }

        return redirect()->back()->with('success', 'Commission settings updated successfully.');
    }
}
