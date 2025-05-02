<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-black leading-tight">
            {{ __('Market Data') }}
        </h2>
    </x-slot>

    <div class="bg-gray-950 min-h-screen py-12">
        @if(session('success'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        
        @if(isset($error))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ $error }}</span>
                </div>
            </div>
        @endif
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-xl rounded-2xl overflow-hidden p-8">
                <!-- Search and Filters -->
                <div class="mb-10">
                    <form action="{{ route('market-data.index') }}" method="GET" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <x-input-label for="symbol" :value="__('Symbol')" class="text-gray-400 mb-2" />
                                <x-text-input id="symbol" class="w-full bg-gray-800 border-gray-700 text-gray-200" type="text" name="symbol" :value="request('symbol')" placeholder="Enter stock symbol" />
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Date')" class="text-gray-400 mb-2" />
                                <x-text-input id="date" class="w-full bg-gray-800 border-gray-700 text-white" type="date" name="date" :value="request('date')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div>
                                <x-input-label for="date_from" :value="__('Date From')" class="text-gray-400 mb-2" />
                                <x-text-input id="date_from" class="w-full bg-gray-800 border-gray-700 text-white" type="date" name="date_from" :value="request('date_from')" />
                            </div>
                            <div>
                                <x-input-label for="date_to" :value="__('Date To')" class="text-gray-400 mb-2" />
                                <x-text-input id="date_to" class="w-full bg-gray-800 border-gray-700 text-white" type="date" name="date_to" :value="request('date_to')" />
                            </div>
                            <div class="flex items-end gap-4">
                                <x-primary-button class="bg-blue-600 hover:bg-blue-500">{{ __('Filter') }}</x-primary-button>
                                <a href="{{ route('market-data.index') }}" class="bg-gray-700 hover:bg-gray-600 text-gray-100 px-4 py-2 rounded-md text-sm font-semibold">{{ __('Reset') }}</a>
                                @auth
                                    <a href="{{ route('market-data.export', request()->query()) }}" class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-md text-sm font-semibold">{{ __('Export CSV') }}</a>
                                @endauth
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Create New Buttons -->
                @auth
                    <div class="mb-8 flex gap-4">
                        <a href="{{ route('market-data.create') }}" class="bg-blue-600 hover:bg-blue-500 text-black px-6 py-3 rounded-lg font-semibold">{{ __('Submit New Market Data') }}</a>
                        <a href="{{ route('market-data.import') }}" class="bg-cyan-600 hover:bg-cyan-500 text-black px-6 py-3 rounded-lg font-semibold flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            {{ __('Bulk Import') }}
                        </a>
                    </div>
                @endauth

                <!-- Market Data Table -->
                <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-700">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Symbol
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Open
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    High
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Low
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Close
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Volume
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-700">
                            @forelse($marketData as $item)
                                <tr class="hover:bg-gray-800 transition">
                                    <td class="px-6 py-4 text-white">{{ $item->date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-white font-semibold">{{ $item->symbol }}</td>
                                    <td class="px-6 py-4 text-gray-300">${{ number_format($item->open, 2) }}</td>
                                    <td class="px-6 py-4 text-green-300 font-bold">${{ number_format($item->high, 2) }}</td>
                                    <td class="px-6 py-4 text-red-300 font-bold">${{ number_format($item->low, 2) }}</td>
                                    <td class="px-6 py-4 text-blue-300 font-bold">${{ number_format($item->close, 2) }}</td>
                                    <td class="px-6 py-4 text-gray-300">{{ number_format($item->volume) }}</td>
                                    <td class="px-6 py-4">
                                        @if($item->is_approved)
                                            <span class="inline-block px-3 py-1 text-xs font-semibold bg-green-700 text-green-200 rounded-full">Approved</span>
                                        @else
                                            <span class="inline-block px-3 py-1 text-xs font-semibold bg-yellow-700 text-yellow-200 rounded-full">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 flex flex-wrap gap-2">
                                        <a href="{{ route('market-data.show', $item->id) }}" class="text-blue-400 hover:text-blue-200 text-sm font-bold">View</a>
                                        @auth
                                            @if(auth()->user()->is_admin || auth()->id() === $item->user_id)
                                                <a href="{{ route('market-data.edit', $item->id) }}" class="text-yellow-400 hover:text-yellow-200 text-sm font-bold">Edit</a>
                                                <form action="{{ route('market-data.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this item?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-400 hover:text-red-200 text-sm font-bold">Delete</button>
                                                </form>
                                            @endif
                                            @if(auth()->user()->is_admin && !$item->is_approved)
                                                <form action="{{ route('market-data.approve', $item->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="text-green-400 hover:text-green-200 text-sm font-bold">Approve</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-6 text-gray-400">
                                        No market data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $paginator->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
