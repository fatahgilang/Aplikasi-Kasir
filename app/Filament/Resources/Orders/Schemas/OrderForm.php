<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Product;
use App\Models\Customer;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DateTimePicker::make('date')
                    ->default(now())
                    ->required()
                    ->disabled()
                    ->hiddenLabel()
                    ->prefix('Date:'),
                Section::make()
                    ->description('Customer Information')
                    ->schema([
                                Select::make('customer_id')
                                    ->relationship('customer','name')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function($state, Set $set){
                                        $customer=Customer::find($state);
                                        $set('phone', $customer->phone ?? null);
                                        $set('address', $customer->address ?? null);
                                    }),
                                    TextInput::make('phone')
                                    ->disabled(),
                                    TextInput::make('address')
                                    ->disabled()
                            
                                ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make()
                ->description('Order Detail')
                ->schema([
                    Repeater::make('orderDetails')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                        ->relationship('product', 'name')
                        ->reactive()
                        ->afterStateUpdated(function($state, Set $set, Get $get){
                            $product=Product::find($state);
                            $price=$product->price ?? 0;
                            $set('price', $price);
                            $qty=$get('qty') ?? 1;
                            $set ('qty', $qty);
                            $subtotal=$price*$qty;
                            $set ('subtotal', $subtotal);

                        }),
                        TextInput::make('price'),
                        TextInput::make('qty'),
                        TextInput::make('subtotal'),
                        
                    ])->columns(4),
                ])
                    ->columnSpanFull(),



                TextInput::make('total_price')
                    ->required()
                    ->numeric(),
            ]);
    }
}
