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
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Group;
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
                    ->dehydrated()
                    ->prefix('Date:')
                    ->columnSpanFull(),
                Group::make()
                ->schema([
                    
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
                                    Placeholder::make('phone')
                                    ->content(fn(Get $get)=>Customer::find($get('customer_id'))?->phone ?? '--'),
                                    Placeholder::make('address')
                                    ->content(fn(Get $get)=>Customer::find($get('customer_id'))?->address ?? '--'),
                            
                                ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make()
                ->description('Order Detail')
                ->schema([
                    Repeater::make('orderDetails')
                    ->relationship()
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        // Calculate total price when repeater items change
                        $items = $get('orderDetails') ?? [];
                        $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                        $set('total_price', $total);
                    })
                    ->schema([
                        Select::make('product_id')
                        ->relationship('product', 'name')
                        ->reactive()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->afterStateUpdated(function($state, Set $set, Get $get){
                            $product = Product::find($state);
                            $price = $product->price ?? 0;
                            $set('price', $price);
                            $qty=$get('qty') ?? 1;
                            $set ('qty', $qty);
                            $subtotal=$price * $qty;
                            $set ('subtotal', $subtotal);

                            // Get parent component to update total
                            $parent = $get('../../');
                            $items = $parent['orderDetails'] ?? [];
                            $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                            $set('../../total_price', $total);

                            $discount = $get('../../discount');
                            $discount_amount = $total * $discount / 100;
                            $set ('../../discount_amount',$discount_amount);
                            $set ('../../total_payment', $total - $discount_amount);
                        }),
                        TextInput::make('price')
                        ->readOnly()
                        ->numeric()
                        ->formatStateUsing(fn($state, Get $get) => $state ?? Product::find($get('product_id')) ?->price ?? 0),
                        TextInput::make('qty')
                        ->numeric()
                        ->default(1)
                        ->reactive()
                        ->afterStateUpdated(function($state, Set $set, Get $get){
                            $price=$get('price') ?? 0;
                            $set('subtotal', $price*$state);

                            // Get parent component to update total
                            $parent = $get('../../');
                            $items = $parent['orderDetails'] ?? [];
                            $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                            $set('../../total_price', $total);

                            $discount = $get('../../discount');
                            $discount_amount = $total * $discount / 100;
                            $set ('../../discount_amount',$discount_amount);
                            $set ('../../total_payment', $total - $discount_amount);
                        }),
                        TextInput::make('subtotal')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(),
                        
                    ])->columns(4),
                    ])->columnSpanFull(),
                ])->columnSpan(2),

                Section::make()
                ->description('Payment Information')
                ->schema([
                    Select::make('status')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ])->default('new')
                    ->columnSpanFull(),
                    TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->dehydrated()
                    ->readOnly()
                    ->dehydrated()
                    ->columnSpanFull(),
                    TextInput::make('discount')
                    ->columnSpan(1)
                    ->reactive()
                    ->afterStateUpdated(function($state, Set $set, Get $get){
                        $discount=floatval($state)?? 0;
                        $total_price=$get('total_price')?? 0;
                        $discount_amount= $total_price * $discount / 100;
                        $set ('discount_amount',$discount_amount);
                        $set ('total_payment',$total_price - $discount_amount);

                    }),
                    TextInput::make('discount_amount')
                    ->columnSpan(3)
                    ->disabled()
                    ->dehydrated(),
                    TextInput::make('total_payment')
                    ->columnSpanFull()
                    ->disabled()
                    ->dehydrated(),
                ])->columnSpan(1)
                ->columns(4),

            ])->columns(3);
    }
}