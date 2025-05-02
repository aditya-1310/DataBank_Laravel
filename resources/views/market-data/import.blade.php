<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Bulk Import Market Data') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    
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
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-600 rounded-md text-white">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('import_errors'))
                        <div class="mb-4 p-4 bg-red-600 rounded-md text-white">
                            <h3 class="font-bold mb-2">Import Errors:</h3>
                            <ul class="list-disc ml-5">
                                @foreach (session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('market-data.import.process') }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        
                        <div>
                            <x-input-label for="file" :value="__('CSV File')" class="text-white" />
                            <input id="file" type="file" name="file" required class="mt-1 block w-full text-white bg-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500" />
                            <p class="mt-1 text-sm text-gray-400">Upload a CSV file with market data. The file should have headers for date, market_name, product_name, price, quantity, and source.</p>
                        </div>

                        <div class="bg-gray-700 p-4 rounded-md border border-gray-600 mt-4">
                            <h3 class="text-white text-lg font-medium mb-2">CSV Format Guidelines</h3>
                            <p class="text-gray-300 mb-2">Your CSV file should have the following columns:</p>
                            <ul class="list-disc ml-5 text-gray-300 mb-4">
                                <li><span class="text-blue-400">date</span> - Date in YYYY-MM-DD format (required)</li>
                                <li><span class="text-blue-400">market_name</span> - Name of the market (required)</li>
                                <li><span class="text-blue-400">product_name</span> - Name of the product (required)</li>
                                <li><span class="text-blue-400">price</span> - Price as a number (required)</li>
                                <li><span class="text-blue-400">quantity</span> - Quantity as a number (optional)</li>
                                <li><span class="text-blue-400">source</span> - Source of the data (optional)</li>
                            </ul>
                            <p class="text-gray-300">Example CSV format:</p>
                            <pre class="bg-gray-900 p-2 text-white rounded-md font-mono text-sm overflow-auto">date,market_name,product_name,price,quantity,source
2024-05-01,Central Market,Apples,2.99,100,Local Farm
2024-05-01,East Market,Bananas,1.49,150,Import
2024-05-01,West Market,Oranges,3.25,75,Southern Farms</pre>
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <a href="{{ route('market-data.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancel') }}
                            </a>
                            
                            <x-primary-button class="ml-3 bg-blue-600 hover:bg-blue-500">
                                {{ __('Upload and Import') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 