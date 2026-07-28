<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Get settings value by key.
     */
    public function get(string $key): JsonResponse
    {
        $setting = Setting::where('key', $key)->first();
        return response()->json([
            'success' => true,
            'data'     => $setting ? $setting->value : null
        ]);
    }

    /**
     * Update settings value by key.
     */
    public function update(Request $request, string $key): JsonResponse
    {
        $value = $request->input('value');
        
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $key)) . ' settings updated successfully',
            'data'    => $setting->value
        ]);
    }

    /**
     * Update Logo & Favicon.
     */
    public function updateLogoFavicon(Request $request)
    {
        $settings = Setting::where('key', 'logo_favicon')->first();
        $value = $settings ? $settings->value : [];

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            $value['logo'] = '/storage/' . $path;
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('branding', 'public');
            $value['favicon'] = '/storage/' . $path;
        }

        Setting::updateOrCreate(['key' => 'logo_favicon'], ['value' => $value]);

        return redirect()->back()->with('success', 'Logo & Favicon settings updated successfully.');
    }

    /**
     * Update SEO Config.
     */
    public function updateSeo(Request $request)
    {
        $settings = Setting::where('key', 'seo_configuration')->first();
        $value = $settings ? $settings->value : [];

        if ($request->hasFile('seoImage')) {
            $path = $request->file('seoImage')->store('seo', 'public');
            $value['seoImage'] = '/storage/' . $path;
        }

        $value['metaRobots'] = $request->input('metaRobots');
        $value['metaDescription'] = $request->input('metaDescription');
        $value['socialTitle'] = $request->input('socialTitle');
        $value['socialDescription'] = $request->input('socialDescription');

        // Split keywords
        $keywordsInput = $request->input('keywords', '');
        $value['keywords'] = array_filter(array_map('trim', explode(',', $keywordsInput)));

        Setting::updateOrCreate(['key' => 'seo_configuration'], ['value' => $value]);

        return redirect()->back()->with('success', 'SEO configuration updated successfully.');
    }

    /**
     * Update Theme.
     */
    public function updateTheme(Request $request)
    {
        $theme = $request->except('_token');
        Setting::updateOrCreate(['key' => 'theme_appearance'], ['value' => $theme]);

        return redirect()->back()->with('success', 'Theme settings updated successfully.');
    }

    /**
     * Update Currency Settings.
     */
    public function updateCurrency(Request $request)
    {
        Setting::updateOrCreate(['key' => 'currency_code'], ['value' => $request->input('code')]);
        Setting::updateOrCreate(['key' => 'currency_symbol'], ['value' => $request->input('symbol')]);

        return redirect()->back()->with('success', 'Currency settings updated successfully.');
    }

    /**
     * Update ROI Settings.
     */
    public function updateRoi(Request $request)
    {
        $validated = $request->validate([
            'roi_type'   => 'required|array|min:1',
            'roi_type.*' => 'in:manual,auto',
            'cycle_days' => 'required|integer|min:1',
        ]);

        Setting::updateOrCreate(['key' => 'roi_settings'], ['value' => $validated]);

        return redirect()->back()->with('success', 'ROI settings updated successfully.');
    }
}
