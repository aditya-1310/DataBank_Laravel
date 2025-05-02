<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Market Data: {{ $marketData->market_name }} - {{ $marketData->product_name }}
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
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Submitted By</dt>
                                        <dd class="text-sm text-white col-span-2">{{ $marketData->submitter->name ?? 'Unknown' }}</dd>
                                    </div>
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Created</dt>
                                        <dd class="text-sm text-white col-span-2">{{ $marketData->created_at->format('Y-m-d H:i') }}</dd>
                                    </div>
                                    <div class="py-3 grid grid-cols-3 gap-4">
                                        <dt class="text-sm font-medium text-gray-300">Last Updated</dt>
                                        <dd class="text-sm text-white col-span-2">{{ $marketData->updated_at->format('Y-m-d H:i') }}</dd>
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
                        
                        @if(auth()->user()->is_admin && $marketData->status === 'pending')
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
            
            <!-- Status History -->
            @if(auth()->user()->is_admin && $marketData->statusHistory->count() > 0)
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <h3 class="text-lg font-medium text-white mb-4">Status History</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-600">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Date') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Changed By') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Old Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('New Status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Comments') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-600">
                                    @foreach($marketData->statusHistory->sortByDesc('created_at') as $history)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $history->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $history->user ? $history->user->name : 'Unknown' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($history->old_status)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $history->old_status === 'approved' ? 'bg-green-800 text-green-100' : '' }}
                                                        {{ $history->old_status === 'pending' ? 'bg-yellow-800 text-yellow-100' : '' }}
                                                        {{ $history->old_status === 'rejected' ? 'bg-red-800 text-red-100' : '' }}">
                                                        {{ ucfirst($history->old_status) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">None</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $history->new_status === 'approved' ? 'bg-green-800 text-green-100' : '' }}
                                                    {{ $history->new_status === 'pending' ? 'bg-yellow-800 text-yellow-100' : '' }}
                                                    {{ $history->new_status === 'rejected' ? 'bg-red-800 text-red-100' : '' }}">
                                                    {{ ucfirst($history->new_status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $history->comments ?? 'No comments' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 