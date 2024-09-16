<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Set;
use Filament\Forms\Get;
use App\Filament\Resources\repeaters;
use Illuminate\Support\Number;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ActionGroup;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        Select::make('payment_method')
                        ->options([
                            'stripe' => 'Stripe',
                            'cod' => 'Cash on Delivery'
                        ])
                        ->required(),

                        Select::make('payment_status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->default('pending')
                        ->required(),

                        ToggleButtons::make('status')
                        ->inline() // Display the options inline, horizontally, instead of vertically in a dropdown.
                        ->default('new')
                        ->required()
                        ->options([
                            'new' => 'New',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->colors([ // Assign colors to each option.
                            'new' => 'info',
                            'processing' => 'warning',
                            'shipped' => 'success',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                        ])
                        ->icons([ // Assign icons to each option.
                            'new' => 'heroicon-m-sparkles',
                            'processing' => 'heroicon-m-arrow-path',
                            'shipped' => 'heroicon-m-truck',
                            'delivered' => 'heroicon-m-check-badge',
                            'cancelled' => 'heroicon-m-x-circle',
                        ]),

                        Select::make('currency')
                        ->options([
                            'lkr' => 'LKR',
                            'usd' => 'USD',
                            'eur' => 'EUR',
                            'gbp' => 'GBP',
                        ])
                        ->default('lkr')
                        ->required(),

                        Select::make('shipping_method')
                        ->options([
                            'fedex' => 'FedEx',
                            'ups' => 'UPS',
                            'dhl' => 'DHL',
                            'usps' => 'USPS',
                        ]),
                        
                        Textarea::make('notes')
                        ->columnSpanFull() // Make the notes field take up the full width of the form
                    ])->columns(2), // Divide the group into two columns and keep the notes field take the full width of the form.

                    // This section is for adding order items to the order.
                    Section::make('Order Items')->schema([
                        Repeater::make('items') // A Repeater field is used to allow the user to add/remove items dynamically.
                        ->relationship() // Specify the relationship between the OrderItem and the Product model.
                        ->schema([
                            Select::make('product_id') // The 'product_id' is the foreign key that links the OrderItem to the Product model.
                            ->relationship('product', 'name') // The relationship is specified as 'product' and the label is set to 'name' so that the product name is shown in the select options.
                            ->searchable()
                            ->preload()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems() // If an item is already selected in another repeater, disable it from being selected again.
                            ->columnSpan(4)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                            ->afterStateUpdated(fn ($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0)),


                        TextInput::make('quantity')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required()
                        ->columnSpan(2)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount'))),
                        
                        TextInput::make('unit_amount')
                        ->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->columnSpan(3),

                        TextInput::make('total_amount')
                        ->numeric()
                        ->required()
                        ->dehydrated()
                        ->columnSpan(3),
                        ])->columns(12),

                        Placeholder::make('grand_total_placeholder')
                        ->label('Grand Total')
                        ->content(function (Get $get, Set $set) {
                            $total = 0;
                            if(!$repeaters = $get('items')) {
                                return $total;
                                
                            }

                            foreach($repeaters as $key => $repeater) {
                                $total += $get("items.{$key}.total_amount");
                            }
                            $set('grand_total', $total);
                            return Number::currency($total, 'LKR');
                            
                        }),
 
                        // This is needed because the grand total is not a field in the orders table.
                        Hidden::make('grand_total')
                        ->default(0)
                    ])

                ])->columnSpanFull() // Make the group take up the full width of the form.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                ->label('Customer')
                ->sortable()
                ->searchable(), 

                TextColumn::make('grand_total')
                ->numeric()
                ->sortable()
                ->searchable()
                ->money('LKR'), 

                TextColumn::make('payment_method')
                ->sortable()
                ->searchable(),

                TextColumn::make('payment_status')
                ->sortable()
                ->searchable(),

                TextColumn::make('currency')
                ->sortable()
                ->searchable(),

                TextColumn::make('shipping_method')
                ->sortable()
                ->searchable(),

                SelectColumn::make('status')
                ->options([
                    'new' => 'New',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',                    
                ])
                ->sortable()
                ->searchable(),
                
                TextColumn::make('created_at')
                ->datetime()
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                ->datetime()
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'success' : 'danger';
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
