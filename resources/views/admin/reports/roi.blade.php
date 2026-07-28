@extends('admin.layouts.app')

@section('title', 'ROI Report')

@section('content')
<div class="w-full">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">ROI Report</h1>
    </div>

    {{-- Top Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <p class="text-xs text-gray-500 font-medium mb-1">Total ROI Paid</p>
            <h3 class="text-xl font-bold text-emerald-600">{{ format_currency($totalRoiPaid) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <p class="text-xs text-gray-500 font-medium mb-1">Total Credits</p>
            <h3 class="text-xl font-bold text-[var(--theme-primary)]">{{ number_format($totalCredits) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <p class="text-xs text-gray-500 font-medium mb-1">Avg ROI Rate</p>
            <h3 class="text-xl font-bold text-purple-600">{{ number_format($avgRoiRate, 2) }}%</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <p class="text-xs text-gray-500 font-medium mb-1">Unique Investors</p>
            <h3 class="text-xl font-bold text-blue-600">{{ number_format($uniqueInvestors) }}</h3>
        </div>
    </div>

    {{-- Monthly Payouts --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-6 text-sm">Monthly ROI Payouts</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-white">
                <thead class="bg-[var(--theme-primary)] font-medium">
                    <tr>
                        <th class="px-6 py-3.5">Month</th>
                        <th class="px-6 py-3.5 text-center">Credits</th>
                        <th class="px-6 py-3.5 text-right">Total Paid</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y divide-gray-100 bg-white">
                    @forelse($monthlyPayouts as $payout)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $payout->month)->format('F Y') }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                {{ number_format($payout->credits) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">
                                {{ format_currency($payout->total_paid) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400 bg-white">
                                No data available
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
