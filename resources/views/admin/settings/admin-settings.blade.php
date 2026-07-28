@extends('admin.layouts.app')

@section('title', 'Admin Settings')

@section('content')
@php
$cards = [
    ['icon' => 'settings',     'title' => 'Logo and Favicon',      'description' => 'Upload and update the admin panel logo and browser favicon.',                      'href' => route('admin.settings.logo')],
    ['icon' => 'globe',        'title' => 'SEO Configuration',     'description' => 'Configure meta title, description and keywords for admin pages.',                  'href' => route('admin.settings.seo')],
    ['icon' => 'settings',     'title' => 'Theme & Appearance',    'description' => 'Customize sidebar color, primary color and font family.',                          'href' => route('admin.settings.theme')],
    ['icon' => 'coins',        'title' => 'Currency Settings',     'description' => 'Configure system-wide currency code and symbol.',                                  'href' => route('admin.settings.currency')],
    ['icon' => 'money',        'title' => 'Commission Settings',   'description' => 'Configure referral and affiliate commission structures.',                            'href' => route('admin.settings.commission')],
    ['icon' => 'chart-line-up','title' => 'ROI Settings',          'description' => 'Configure Return on Investment percentage and frequency.',                         'href' => route('admin.settings.roi')],
];
@endphp
@include('admin.partials.settings-page', ['title' => 'Admin Settings', 'cards' => $cards])
@endsection
