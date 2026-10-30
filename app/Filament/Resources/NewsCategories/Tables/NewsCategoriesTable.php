<?php

namespace App\Filament\Resources\NewsCategories\Tables;

use App\Models\NewsCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NewsCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([

                // CATEGORY IMAGE
                ImageColumn::make('image')
                    ->label('Category Image')
                    ->disk('public')
                    ->visibility('public')
                    ->square(),

                // BENGALI CATEGORY NAME
                TextColumn::make('name_bn')
                    ->label('Bengali Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // ENGLISH CATEGORY NAME
                TextColumn::make('name_en')
                    ->label('English Name')
                    ->searchable()
                    ->sortable(),

                // URL SLUG
                TextColumn::make('slug')
                    ->label('SEO Slug')
                    ->searchable()
                    ->copyable()
                    ->limit(40),

                // NUMBER OF NEWS POSTS
                TextColumn::make('posts_count')
                    ->label('Total News')
                    ->counts('posts')
                    ->numeric()
                    ->sortable(),

                // ACTIVE STATUS
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                // GOOGLE INDEXING STATUS
                IconColumn::make('robots_index')
                    ->label('Index')
                    ->boolean()
                    ->toggleable(),

                // FOLLOW LINKS
                IconColumn::make('robots_follow')
                    ->label('Follow')
                    ->boolean()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // DISPLAY ORDER
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),

                // BENGALI DESCRIPTION
                TextColumn::make('description_bn')
                    ->label('Description - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // ENGLISH DESCRIPTION
                TextColumn::make('description_en')
                    ->label('Description - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // BENGALI IMAGE ALT
                TextColumn::make('image_alt_bn')
                    ->label('Image Alt - Bengali')
                    ->limit(50)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // ENGLISH IMAGE ALT
                TextColumn::make('image_alt_en')
                    ->label('Image Alt - English')
                    ->limit(50)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // BENGALI SEO TITLE
                TextColumn::make('seo_title_bn')
                    ->label('SEO Title - Bengali')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // ENGLISH SEO TITLE
                TextColumn::make('seo_title_en')
                    ->label('SEO Title - English')
                    ->searchable()
                    ->limit(50)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // BENGALI META DESCRIPTION
                TextColumn::make('seo_description_bn')
                    ->label('SEO Description - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // ENGLISH META DESCRIPTION
                TextColumn::make('seo_description_en')
                    ->label('SEO Description - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // BENGALI FOCUS KEYWORD
                TextColumn::make('focus_keyword_bn')
                    ->label('Focus Keyword - Bengali')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // ENGLISH FOCUS KEYWORD
                TextColumn::make('focus_keyword_en')
                    ->label('Focus Keyword - English')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // CANONICAL URL
                TextColumn::make('canonical_url')
                    ->label('Canonical URL')
                    ->limit(50)
                    ->copyable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // OPEN GRAPH BENGALI TITLE
                TextColumn::make('og_title_bn')
                    ->label('OG Title - Bengali')
                    ->limit(50)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // OPEN GRAPH ENGLISH TITLE
                TextColumn::make('og_title_en')
                    ->label('OG Title - English')
                    ->limit(50)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // OPEN GRAPH BENGALI DESCRIPTION
                TextColumn::make('og_description_bn')
                    ->label('OG Description - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // OPEN GRAPH ENGLISH DESCRIPTION
                TextColumn::make('og_description_en')
                    ->label('OG Description - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // OPEN GRAPH IMAGE
                ImageColumn::make('og_image')
                    ->label('Social Image')
                    ->disk('public')
                    ->visibility('public')
                    ->square()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // TWITTER / X BENGALI TITLE
                TextColumn::make('twitter_title_bn')
                    ->label('Twitter Title - Bengali')
                    ->limit(50)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // TWITTER / X ENGLISH TITLE
                TextColumn::make('twitter_title_en')
                    ->label('Twitter Title - English')
                    ->limit(50)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // TWITTER / X BENGALI DESCRIPTION
                TextColumn::make('twitter_description_bn')
                    ->label('Twitter Description - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // TWITTER / X ENGLISH DESCRIPTION
                TextColumn::make('twitter_description_en')
                    ->label('Twitter Description - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // CREATION DATE
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                // LAST UPDATED
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            // CATEGORY FILTERS
            ->filters([

                TernaryFilter::make('is_active')
                    ->label('Category Status')
                    ->placeholder('All Categories')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),

                TernaryFilter::make('robots_index')
                    ->label('Search Engine Indexing')
                    ->placeholder('All')
                    ->trueLabel('Indexable')
                    ->falseLabel('Noindex'),

            ])

            // RECORD ACTIONS
            ->recordActions([

                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()
                    ->label('Delete')
                    ->visible(
                        fn (NewsCategory $record): bool =>
                            ! $record->posts()->exists()
                    ),

            ])

            // BULK DELETE DISABLED FOR SAFETY
            ->toolbarActions([])

            // DEFAULT SORTING
            ->defaultSort('sort_order', 'asc');
    }
}
