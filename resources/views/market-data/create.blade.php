<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Submit New Market Data') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-800 text-black rounded-md">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('market-data.store') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="date" :value="__('Date')" class="text-white"/>
                            <x-text-input id="date" class="block mt-1 w-full bg-gray-700 text-black border-gray-600 placeholder-gray-400" 
                                type="date" name="date" :value="old('date')" required autofocus placeholder="Select a date" />
                        </div>
                        
                        <div>
                            <x-input-label for="market_name" :value="__('Market Name')" class="text-white" />
                            <x-text-input id="market_name" class="block mt-1 w-full bg-gray-700 text-black border-gray-600 placeholder-gray-400" 
                                type="text" name="market_name" :value="old('market_name')" required placeholder="Enter market name" />
                        </div>
                        
                        <div>
                            <x-input-label for="product_name" :value="__('Product Name')" class="text-white" />
                            <x-text-input id="product_name" class="block mt-1 w-full bg-gray-700 text-black border-gray-600 placeholder-gray-400" 
                                type="text" name="product_name" :value="old('product_name')" required placeholder="Enter product name" />
                        </div>
                        
                        <div>
                            <x-input-label for="price" :value="__('Price')" class="text-white" />
                            <x-text-input id="price" class="block mt-1 w-full  text-black border-gray-600 placeholder-gray-400" 
                                type="number" name="price" step="0.01" min="0" :value="old('price')" required placeholder="0.00" />
                        </div>
                        
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity')" class="text-white" />
                            <x-text-input id="quantity" class="block mt-1 w-full bg-gray-700 text-black border-gray-600 placeholder-gray-400" 
                                type="number" name="quantity" step="0.01" min="0" :value="old('quantity')" placeholder="0.00 (optional)" />
                        </div>
                        
                        <div>
                            <x-input-label for="source" :value="__('Source')" class="text-white" />
                            <x-text-input id="source" class="block mt-1 w-full bg-gray-700 text-black border-gray-600 placeholder-gray-400" 
                                type="text" name="source" :value="old('source')" placeholder="Enter data source (optional)" />
                        </div>
                        
                        <div class="flex items-center justify-end">
                            <a href="{{ route('market-data.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition ease-in-out duration-150 mr-3">
                                {{ __('Cancel') }}
                            </a>
                            
                            <x-primary-button class="ml-3 bg-blue-600 hover:bg-blue-500">
                                {{ __('Submit Data') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Add custom styles to ensure placeholders are visible */
    ::placeholder {
        color: rgba(156, 163, 175, 0.8) !important; /* gray-400 with higher opacity */
        opacity: 1 !important;
    }
    </style>
</x-app-layout> 