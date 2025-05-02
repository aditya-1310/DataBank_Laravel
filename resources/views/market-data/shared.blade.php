<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shared Market Data') }}: {{ $marketData->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        This is a shared view of market data. This link will expire after 7 days.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 overflow-hidden shadow rounded-lg divide-y divide-gray-200">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $marketData->title }}
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Detailed information about this market data entry.
                            </p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Product Name
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $marketData->product_name }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Price
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        ${{ number_format($marketData->price, 2) }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Location
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $marketData->location }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Category
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $marketData->category }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Description
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $marketData->description ?? 'No description provided.' }}
                                    </dd>
                                </div>
                                @if($marketData->additional_info)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Additional Information
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <pre class="whitespace-pre-wrap bg-gray-100 p-3 rounded">{{ json_encode(json_decode($marketData->additional_info), JSON_PRETTY_PRINT) }}</pre>
                                    </dd>
                                </div>
                                @endif
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Submitted On
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $marketData->created_at->format('M d, Y h:i A') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <a href="{{ route('market-data.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Browse More Market Data') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 