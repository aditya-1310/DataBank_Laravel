<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 ">
            {{ __('Submit New Market Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-600 rounded-md">
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
                            <x-input-label for="date" :value="__('Date')" class="text-gray-200"/>
                            <x-text-input id="date" class="block mt-1 w-full bg-gray-700 text-white border-gray-600" 
                                type="date" name="date" :value="old('date')" required autofocus />
                        </div>
                        
                        <div>
                            <x-input-label for="market_name" :value="__('Market Name')" class="text-gray-200" />
                            <x-text-input id="market_name" class="block mt-1 w-full bg-gray-700 text-white border-gray-600" 
                                type="text" name="market_name" :value="old('market_name')" required />
                        </div>
                        
                        <div>
                            <x-input-label for="product_name" :value="__('Product Name')" class="text-gray-200" />
                            <x-text-input id="product_name" class="block mt-1 w-full bg-gray-700 text-white border-gray-600" 
                                type="text" name="product_name" :value="old('product_name')" required />
                        </div>
                        
                        <div>
                            <x-input-label for="price" :value="__('Price')" class="text-gray-200" />
                            <x-text-input id="price" class="block mt-1 w-full bg-gray-700 text-white border-gray-600" 
                                type="number" name="price" step="0.01" min="0" :value="old('price')" required />
                        </div>
                        
                        <div>
                            <x-input-label for="quantity" :value="__('Quantity')" class="text-gray-200" />
                            <x-text-input id="quantity" class="block mt-1 w-full bg-gray-700 text-white border-gray-600" 
                                type="number" name="quantity" step="0.01" min="0" :value="old('quantity')" />
                        </div>
                        
                        <div>
                            <x-input-label for="source" :value="__('Source')" class="text-gray-200" />
                            <x-text-input id="source" class="block mt-1 w-full bg-gray-700 text-white border-gray-600" 
                                type="text" name="source" :value="old('source')" />
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
</x-app-layout> 