<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Market Data') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('market-data.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition ease-in-out duration-150">
                    {{ __('Submit New Data') }}
                </a>
                <a href="{{ route('market-data.import') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition ease-in-out duration-150">
                    {{ __('Bulk Import') }}
                </a>
                @if(auth()->user() && auth()->user()->is_admin)
                <a href="{{ route('market-data.pending') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition ease-in-out duration-150">
                    {{ __('Approve Pending Data') }}
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-900">
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

            <!-- Filters -->
            <div class="mb-6 bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Filter Market Data') }}</h3>
                    
                    <form method="GET" action="{{ route('market-data.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Date Range -->
                            <div>
                                <x-input-label for="date_from" :value="__('Date From')" class="text-white" />
                                <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full bg-gray-700 text-white border-gray-600" :value="request('date_from')" />
                            </div>
                            
                            <div>
                                <x-input-label for="date_to" :value="__('Date To')" class="text-white" />
                                <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full bg-gray-700 text-white border-gray-600" :value="request('date_to')" />
                            </div>
                            
                            <!-- Market Name -->
                            <div>
                                <x-input-label for="market_name" :value="__('Market Name')" class="text-white" />
                                <x-text-input id="market_name" name="market_name" type="text" class="mt-1 block w-full bg-gray-700 text-white border-gray-600" :value="request('market_name')" />
                            </div>
                            
                            <!-- Product Name -->
                            <div>
                                <x-input-label for="product_name" :value="__('Product Name')" class="text-white" />
                                <x-text-input id="product_name" name="product_name" type="text" class="mt-1 block w-full bg-gray-700 text-white border-gray-600" :value="request('product_name')" />
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" class="text-white" />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md bg-gray-700 text-white border-gray-600 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            
                            <!-- Filter Button -->
                            <div class="flex items-end">
                                <x-primary-button type="submit" class="bg-blue-600 hover:bg-blue-500">
                                    {{ __('Filter') }}
                                </x-primary-button>
                                
                                @if(request()->anyFilled(['date_from', 'date_to', 'market_name', 'product_name', 'status']))
                                    <a href="{{ route('market-data.index') }}" class="ml-3 inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition ease-in-out duration-150">
                                        {{ __('Clear') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Market Data Records') }}</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Market</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @forelse ($marketData as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ $item->date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ $item->market_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ $item->product_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ number_format($item->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ $item->quantity ? number_format($item->quantity, 2) : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $item->status === 'approved' ? 'bg-green-800 text-green-100' : '' }}
                                                {{ $item->status === 'pending' ? 'bg-yellow-800 text-yellow-100' : '' }}
                                                {{ $item->status === 'rejected' ? 'bg-red-800 text-red-100' : '' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('market-data.show', $item) }}" class="text-blue-400 hover:text-blue-300">
                                                    View
                                                </a>
                                                <a href="{{ route('market-data.edit', $item) }}" class="text-green-400 hover:text-green-300">
                                                    Edit
                                                </a>
                                                @if(auth()->user() && auth()->user()->is_admin && $item->status === 'pending')
                                                <form action="{{ route('market-data.approve-status', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-400 hover:text-green-300">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('market-data.reject-status', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-red-400 hover:text-red-300">
                                                        Reject
                                                    </button>
                                                </form>
                                                @endif
                                                <form action="{{ route('market-data.destroy', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('Are you sure you want to delete this item?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-white text-center">
                                            No market data records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $marketData->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
