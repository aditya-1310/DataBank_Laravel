<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Welcome & Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-medium text-gray-900">
                        Welcome back, {{ Auth::user()->name }}!
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('View market data statistics and quick actions below.') }}
                    </p>
                    
                    <div class="mt-6 flex flex-wrap gap-4">
                        <a href="{{ route('market-data.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('Submit New Data') }}
                        </a>
                        
                        <a href="{{ route('market-data.import') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-purple-500 active:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            {{ __('Bulk Import') }}
                        </a>
                        
                        <a href="{{ route('market-data.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            {{ __('Browse Market Data') }}
                        </a>
                        
                        @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.pending-approvals') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            {{ __('Pending Approvals') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Total Entries</h3>
                                <p class="text-3xl font-semibold text-gray-700">{{ number_format($stats['total_entries']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Approved</h3>
                                <p class="text-3xl font-semibold text-gray-700">{{ number_format($stats['approved_entries']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Pending</h3>
                                <p class="text-3xl font-semibold text-gray-700">{{ number_format($stats['pending_entries']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">My Entries</h3>
                                <p class="text-3xl font-semibold text-gray-700">{{ number_format($stats['my_entries']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest Market Data & My Submissions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Latest Market Data -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Latest Market Data</h3>
                        
                        @if($latestMarketData->count() > 0)
                            <div class="divide-y divide-gray-200">
                                @foreach($latestMarketData as $item)
                                    <div class="py-3">
                                        <div class="flex justify-between">
                                            <a href="{{ route('market-data.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">{{ $item->symbol }}</a>
                                            <span class="text-sm text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-600 flex justify-between">
                                            <span>{{ $item->date->format('Y-m-d') }}</span>
                                            <span class="font-medium">Open: ${{ number_format($item->open, 2) }} / Close: ${{ number_format($item->close, 2) }}</span>
                                        </div>
                                        <div class="mt-1 text-xs text-gray-500 flex items-center space-x-2">
                                            <span class="bg-gray-100 px-2 py-1 rounded">Vol: {{ number_format($item->volume) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('market-data.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View all market data &rarr;</a>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-md p-4 text-center text-gray-500">
                                No market data available yet.
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- My Recent Submissions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">My Recent Submissions</h3>
                        
                        @if($myRecentSubmissions->count() > 0)
                            <div class="divide-y divide-gray-200">
                                @foreach($myRecentSubmissions as $item)
                                    <div class="py-3">
                                        <div class="flex justify-between">
                                            <a href="{{ route('market-data.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">{{ $item->symbol }}</a>
                                            <span class="text-sm text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-600 flex justify-between">
                                            <span>{{ $item->date->format('Y-m-d') }}</span>
                                            <span class="font-medium">Open: ${{ number_format($item->open, 2) }} / Close: ${{ number_format($item->close, 2) }}</span>
                                        </div>
                                        <div class="mt-1 text-xs flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="bg-gray-100 px-2 py-1 rounded text-gray-500">Vol: {{ number_format($item->volume) }}</span>
                                            </div>
                                            <span class="{{ $item->is_approved ? 'text-green-600' : 'text-yellow-600' }}">
                                                {{ $item->is_approved ? 'Approved' : 'Pending' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('market-data.create') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Submit new entry &rarr;</a>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-md p-4 text-center text-gray-500">
                                You haven't submitted any market data yet.
                                <div class="mt-2">
                                    <a href="{{ route('market-data.create') }}" class="text-indigo-600 hover:text-indigo-900">Submit your first entry</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Symbol Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Top Symbols -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Symbols</h3>
                        
                        @if($topSymbols->count() > 0)
                            <div class="space-y-2">
                                @foreach($topSymbols as $symbol)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700">{{ $symbol->symbol }}</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $symbol->count }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ ($symbol->count / $topSymbols->max('count')) * 100 }}%"></div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-xs text-gray-500">
                                Total {{ $stats['symbols_count'] }} different symbols
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-md p-4 text-center text-gray-500">
                                No symbol data available.
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Open Price Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Open Price Stats</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Average Open Price</p>
                                <p class="text-xl font-medium text-gray-900">${{ number_format($priceStats['avg_open'], 2) }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Min Open</p>
                                    <p class="text-lg font-medium text-gray-900">${{ number_format($priceStats['min_open'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Max Open</p>
                                    <p class="text-lg font-medium text-gray-900">${{ number_format($priceStats['max_open'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Close Price Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Close Price Stats</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Average Close Price</p>
                                <p class="text-xl font-medium text-gray-900">${{ number_format($priceStats['avg_close'], 2) }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Min Close</p>
                                    <p class="text-lg font-medium text-gray-900">${{ number_format($priceStats['min_close'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Max Close</p>
                                    <p class="text-lg font-medium text-gray-900">${{ number_format($priceStats['max_close'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
