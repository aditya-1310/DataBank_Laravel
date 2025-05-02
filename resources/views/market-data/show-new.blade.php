<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Market Data Details
            </h2>
            <a href="{{ route('market-data.index') }}" class="text-blue-400 hover:text-blue-300">
                &larr; Back to Market Data List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notifications -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-600 rounded-md text-white">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-600 rounded-md text-white">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Market Data Details -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-white mb-4">Market Data Details</h3>
                            <div class="border-t border-gray-700 pt-4">
                                <dl class="divide-y divide-gray-700">
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Date</dt>
                                        <dd class="text-sm text-white col-span-2">{{ $marketData->date->format('Y-m-d') }}</dd>
                                    </div>
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Market</dt>
                                        <dd class="text-sm text-white col-span-2">{{ $marketData->market_name }}</dd>
                                    </div>
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Product</dt>
                                        <dd class="text-sm text-white col-span-2">{{ $marketData->product_name }}</dd>
                                    </div>
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Price</dt>
                                        <dd class="text-sm text-white col-span-2">{{ number_format($marketData->price, 2) }}</dd>
                                    </div>
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Quantity</dt>
                                        <dd class="text-sm text-white col-span-2">{{ $marketData->quantity ? number_format($marketData->quantity, 2) : 'N/A' }}</dd>
                                    </div>
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Source</dt>
                                        <dd class="text-sm text-white col-span-2">{{ $marketData->source ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Status</dt>
                                        <dd class="text-sm text-white col-span-2">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $marketData->status === 'approved' ? 'bg-green-800 text-green-100' : '' }}
                                                {{ $marketData->status === 'pending' ? 'bg-yellow-800 text-yellow-100' : '' }}
                                                {{ $marketData->status === 'rejected' ? 'bg-red-800 text-red-100' : '' }}">
                                                {{ ucfirst($marketData->status) }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('market-data.edit', $marketData) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            {{ __('Edit') }}
                        </a>
                        
                        @if(auth()->user() && auth()->user()->is_admin && $marketData->status === 'pending')
                            <form action="{{ route('market-data.approve-status', $marketData) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    {{ __('Approve') }}
                                </button>
                            </form>
                            
                            <form action="{{ route('market-data.reject-status', $marketData) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    {{ __('Reject') }}
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('market-data.destroy', $marketData) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this entry?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 