@extends('user.layouts.app')

@section('title', 'Transfer Commissions')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Transfer Commissions</h1>
    <p class="text-sm text-gray-500 mt-1">Transfer your available commissions to another agent.</p>
</div>

@if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-100 flex items-start gap-3">
        <i class="ph ph-check-circle text-xl mt-0.5"></i>
        <div>
            <h4 class="font-medium">Success</h4>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-100 flex items-start gap-3">
        <i class="ph ph-x-circle text-xl mt-0.5"></i>
        <div>
            <h4 class="font-medium">Error</h4>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-100 flex items-start gap-3">
        <i class="ph ph-warning text-xl mt-0.5"></i>
        <div>
            <h4 class="font-medium">Please fix the following errors:</h4>
            <ul class="text-sm list-disc list-inside mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Transfer Form -->
    <div class="lg:col-span-2">
        <div x-data="transferForm()">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Send Funds</h3>
            
            <!-- Step 1: Search Agent -->
            <div x-show="!agentFound" x-transition>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Username</label>
                        <input type="text" x-model="searchUsername" class="w-full px-4 py-2 border border-gray-200 rounded-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" placeholder="Enter username">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Mobile Number</label>
                        <input type="text" x-model="searchMobile" class="w-full px-4 py-2 border border-gray-200 rounded-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors" placeholder="Enter mobile number">
                    </div>
                    <button @click="searchAgent" :disabled="loading" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2.5 px-4 rounded-none transition-colors flex items-center justify-center gap-2">
                        <i class="ph ph-magnifying-glass" x-show="!loading"></i>
                        <i class="ph ph-spinner animate-spin" x-show="loading" style="display: none;"></i>
                        <span x-text="loading ? 'Searching...' : 'Search Agent'"></span>
                    </button>
                    <div x-show="searchError" x-text="searchError" class="text-red-500 text-sm text-center mt-2" style="display: none;"></div>
                </div>
            </div>

            <!-- Step 2: Transfer Details -->
            <div x-show="agentFound" x-transition style="display: none;">
                <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Transferring to</p>
                        <p class="font-medium text-gray-900 text-lg" x-text="agentName"></p>
                        <p class="text-xs text-gray-500" x-text="'@' + agentUsername"></p>
                    </div>
                    <button @click="resetSearch" type="button" class="text-sm text-primary hover:underline font-medium">Change</button>
                </div>

                <form action="{{ route('user.finance.transfer.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="recipient_id" :value="agentId">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transfer Amount ({{ get_setting('currency_symbol', 'Rs') }})</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500">{{ get_setting('currency_symbol', 'Rs') }}</span>
                            </div>
                            <input type="number" name="amount" step="0.01" min="1" max="{{ $available_balance }}" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-lg font-medium" placeholder="0.00" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Available to transfer: {{ get_setting('currency_symbol', 'Rs') }}{{ number_format($available_balance, 2) }}</p>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-none transition-colors flex items-center justify-center gap-2 text-lg">
                        <i class="ph ph-paper-plane-right"></i>
                        Confirm Transfer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-none p-6 border border-gray-200 relative overflow-hidden shadow-sm">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <i class="ph ph-wallet text-8xl text-gray-900"></i>
            </div>
            
            <div class="relative z-10">
                <p class="text-gray-500 text-sm font-medium mb-1">Available Balance</p>
                <h3 class="text-3xl font-bold mb-6 text-gray-900">
                    {{ get_setting('currency_symbol', 'Rs') }}{{ number_format($available_balance, 2) }}
                </h3>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function transferForm() {
        return {
            searchUsername: '',
            searchMobile: '',
            loading: false,
            searchError: null,
            agentFound: false,
            agentId: null,
            agentName: '',
            agentUsername: '',

            searchAgent() {
                if (!this.searchUsername || !this.searchMobile) {
                    this.searchError = 'Please enter both username and mobile number.';
                    return;
                }

                this.loading = true;
                this.searchError = null;

                fetch('{{ route('user.finance.transfer.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        username: this.searchUsername,
                        mobile: this.searchMobile
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        this.agentFound = true;
                        this.agentId = data.agent.id;
                        this.agentName = data.agent.name;
                        this.agentUsername = data.agent.username;
                    } else {
                        this.searchError = data.message;
                    }
                })
                .catch(error => {
                    this.loading = false;
                    this.searchError = 'An error occurred while searching. Please try again.';
                });
            },

            resetSearch() {
                this.agentFound = false;
                this.agentId = null;
                this.agentName = '';
                this.agentUsername = '';
            }
        }
    }
</script>
@endpush
@endsection
