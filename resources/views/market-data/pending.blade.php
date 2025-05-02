<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Pending Market Data Approval') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="mb-6 p-6 bg-gray-800 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-white mb-4">{{ __('Filter Results') }}</h3>
                <form action="{{ route('market-data.pending') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="market_name" :value="__('Market Name')" class="text-white" />
                            <x-text-input id="market_name" name="market_name" type="text" class="mt-1 w-full bg-gray-700 text-white" 
                                value="{{ request('market_name') }}" />
                        </div>
                        
                        <div>
                            <x-input-label for="product_name" :value="__('Product Name')" class="text-white" />
                            <x-text-input id="product_name" name="product_name" type="text" class="mt-1 w-full bg-gray-700 text-white" 
                                value="{{ request('product_name') }}" />
                        </div>
                        
                        <div>
                            <x-input-label for="date_from" :value="__('Date From')" class="text-white" />
                            <x-text-input id="date_from" name="date_from" type="date" class="mt-1 w-full bg-gray-700 text-white" 
                                value="{{ request('date_from') }}" />
                        </div>
                        
                        <div>
                            <x-input-label for="date_to" :value="__('Date To')" class="text-white" />
                            <x-text-input id="date_to" name="date_to" type="date" class="mt-1 w-full bg-gray-700 text-white" 
                                value="{{ request('date_to') }}" />
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end">
                        <x-primary-button>
                            {{ __('Filter') }}
                        </x-primary-button>
                        
                        @if(count(request()->all()) > 0)
                            <a href="{{ route('market-data.pending') }}" class="ml-4 text-white hover:text-blue-300">
                                {{ __('Clear Filters') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            
            <!-- Results -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    @if(session('success'))
                        <div class="bg-green-800 text-white px-4 py-2 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-800 text-white px-4 py-2 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <h3 class="text-lg font-medium text-white mb-4">{{ __('Pending Market Data') }}</h3>
                    
                    @if(count($pendingData) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-600">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Date') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Market') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Product') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Price') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Quantity') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Source') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            {{ __('Submitted By') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-1/5">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-600">
                                    @foreach($pendingData as $data)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $data->date->format('Y-m-d') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $data->market_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $data->product_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ number_format($data->price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $data->quantity ? number_format($data->quantity, 2) : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $data->source ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                                {{ $data->submitter->name ?? 'Unknown' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex justify-end space-x-3">
                                                    <form action="{{ route('market-data.approve-status', $data) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-500 transition-colors duration-150">
                                                            {{ __('Approve') }}
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('market-data.reject-status', $data) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-500 transition-colors duration-150">
                                                            {{ __('Reject') }}
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="{{ route('market-data.show', $data) }}" class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition-colors duration-150">
                                                        {{ __('View') }}
                                                    </a>
                                                    
                                                    <a href="{{ route('market-data.edit', $data) }}" class="px-3 py-1 bg-yellow-600 text-white rounded-md hover:bg-yellow-500 transition-colors duration-150">
                                                        {{ __('Edit') }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $pendingData->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-300">{{ __('No pending market data entries found.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 