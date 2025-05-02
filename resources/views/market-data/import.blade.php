<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bulk Import Market Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if (session('failures'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Import Errors!</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach (session('failures') as $failure)
                                    <li>Row {{ $failure['row'] }}: {{ implode(', ', $failure['errors']) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('market-data.import.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="file" :value="__('Import File')" />
                            <input id="file" name="file" type="file" accept=".csv,.xlsx,.xls" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Upload a CSV or Excel file containing market data. Maximum size: 10MB</p>
                        </div>
                        
                        <div>
                            <x-input-label for="symbol" :value="__('Symbol')" />
                            <x-text-input id="symbol" name="symbol" type="text" class="mt-1 block w-full" required minlength="3" maxlength="10" pattern="[A-Za-z0-9]+" />
                            <x-input-error :messages="$errors->get('symbol')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Enter the stock symbol for this data (e.g., AAPL, MSFT)</p>
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="font-semibold text-lg mb-2">File Format Requirements</h3>
                            <p class="text-sm text-gray-600 mb-2">Your CSV or Excel file should have the following column headers:</p>
                            <div class="bg-gray-50 p-4 rounded-lg overflow-x-auto">
                                <table class="min-w-full text-sm text-gray-500">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 border">Column</th>
                                            <th class="px-4 py-2 border">Required</th>
                                            <th class="px-4 py-2 border">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="px-4 py-2 border font-medium">date</td>
                                            <td class="px-4 py-2 border">Yes</td>
                                            <td class="px-4 py-2 border">Date in YYYY-MM-DD format</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border font-medium">open</td>
                                            <td class="px-4 py-2 border">Yes</td>
                                            <td class="px-4 py-2 border">Opening price (numeric value)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border font-medium">high</td>
                                            <td class="px-4 py-2 border">Yes</td>
                                            <td class="px-4 py-2 border">Highest price (numeric value)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border font-medium">low</td>
                                            <td class="px-4 py-2 border">Yes</td>
                                            <td class="px-4 py-2 border">Lowest price (numeric value)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border font-medium">close</td>
                                            <td class="px-4 py-2 border">Yes</td>
                                            <td class="px-4 py-2 border">Closing price (numeric value)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border font-medium">volume</td>
                                            <td class="px-4 py-2 border">Yes</td>
                                            <td class="px-4 py-2 border">Trading volume (integer)</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border font-medium">symbol</td>
                                            <td class="px-4 py-2 border">Yes</td>
                                            <td class="px-4 py-2 border">Stock/market symbol (max 10 characters)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-sm text-gray-600">Download a sample template:</p>
                                <div class="mt-2">
                                    <a href="{{ route('market-data.downloadCsvTemplate') }}" class="text-blue-600 hover:text-blue-800 underline">CSV Template</a>
                                    <a href="{{ route('market-data.downloadExcelTemplate') }}" class="text-blue-600 hover:text-blue-800 underline">Excel Template</a>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <div class="flex space-x-4">
                                <a href="{{ route('market-data.downloadCsvTemplate') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Download CSV Template') }}
                                </a>
                                <a href="{{ route('market-data.downloadExcelTemplate') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Download Excel Template') }}
                                </a>
                            </div>
                            
                            <div class="flex space-x-4">
                                <a href="{{ route('market-data.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Cancel') }}
                                </a>

                                <x-primary-button class="ml-4">
                                    {{ __('Upload and Import') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 