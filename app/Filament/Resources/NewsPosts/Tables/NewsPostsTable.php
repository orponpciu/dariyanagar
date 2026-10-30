<?php

namespace App\Filament\Resources\NewsPosts\Tables;

use App\Models\NewsPost;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NewsPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([

                /*
                |-----------------------------------------
                | MAIN NEWS INFORMATION
                |-----------------------------------------
                */

                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title_bn')
                    ->label('Bengali Headline')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(55)
                    ->wrap(),

                TextColumn::make('title_en')
                    ->label('English Headline')
                    ->searchable()
                    ->sortable()
                    ->limit(55)
                    ->wrap(),

                TextColumn::make('category.name_bn')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('SEO Slug')
                    ->searchable()
                    ->copyable()
                    ->limit(40)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                /*
                |-----------------------------------------
                | PUBLISHING STATUS
                |-----------------------------------------
                */

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (
                            string $state,
                            NewsPost $record
                        ): string => match (true) {
                            $state === 'draft' => 'Draft',
                            $state === 'published'
                                && $record->published_at?->isFuture()
                                    => 'Scheduled',
                            $state === 'published' => 'Published',
                            default => ucfirst($state),
                        }
                    )
                    ->color(
                        fn (
                            string $state,
                            NewsPost $record
                        ): string => match (true) {
                            $state === 'draft' => 'warning',
                            $state === 'published'
                                && $record->published_at?->isFuture()
                                    => 'info',
                            $state === 'published' => 'success',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

                IconColumn::make('is_breaking')
                    ->label('Breaking')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_trending')
                    ->label('Trending')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('views')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),

                /*
                |-----------------------------------------
                | SUMMARIES AND ARTICLE CONTENT
                |-----------------------------------------
                */

                TextColumn::make('excerpt_bn')
                    ->label('Summary - Bengali')
                    ->limit(80)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('excerpt_en')
                    ->label('Summary - English')
                    ->limit(80)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('body_bn')
                    ->label('Article - Bengali')
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            strip_tags($state ?? '')
                    )
                    ->limit(100)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('body_en')
                    ->label('Article - English')
                    ->formatStateUsing(
                        fn (?string $state): string =>
                            strip_tags($state ?? '')
                    )
                    ->limit(100)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |-----------------------------------------
                | REPORTER AND SOURCE
                |-----------------------------------------
                */

                TextColumn::make('reporter_bn')
                    ->label('Reporter - Bengali')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('reporter_en')
                    ->label('Reporter - English')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('source_name')
                    ->label('Source Name')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('source_url')
                    ->label('Source URL')
                    ->limit(50)
                    ->copyable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |-----------------------------------------
                | IMAGE METADATA
                |-----------------------------------------
                */

                TextColumn::make('image_alt_bn')
                    ->label('Image Alt - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('image_alt_en')
                    ->label('Image Alt - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('image_caption_bn')
                    ->label('Image Caption - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('image_caption_en')
                    ->label('Image Caption - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('image_credit')
                    ->label('Image Credit')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |-----------------------------------------
                | BENGALI SEO
                |-----------------------------------------
                */

                TextColumn::make('seo_title_bn')
                    ->label('SEO Title - Bengali')
                    ->searchable()
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('seo_description_bn')
                    ->label('Meta Description - Bengali')
                    ->limit(80)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('focus_keyword_bn')
                    ->label('Focus Keyword - Bengali')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |-----------------------------------------
                | ENGLISH SEO
                |-----------------------------------------
                */

                TextColumn::make('seo_title_en')
                    ->label('SEO Title - English')
                    ->searchable()
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('seo_description_en')
                    ->label('Meta Description - English')
                    ->limit(80)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('focus_keyword_en')
                    ->label('Focus Keyword - English')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |-----------------------------------------
                | TECHNICAL SEO
                |-----------------------------------------
                */

                TextColumn::make('canonical_url')
                    ->label('Canonical URL')
                    ->copyable()
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                IconColumn::make('robots_index')
                    ->label('Index')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('robots_follow')
                    ->label('Follow')
                    ->boolean()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('schema_type')
                    ->label('Schema Type')
                    ->badge()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |-----------------------------------------
                | OPEN GRAPH
                |-----------------------------------------
                */

                TextColumn::make('og_title_bn')
                    ->label('OG Title - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('og_title_en')
                    ->label('OG Title - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('og_description_bn')
                    ->label('OG Description - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('og_description_en')
                    ->label('OG Description - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                ImageColumn::make('og_image')
                    ->label('Social Image')
                    ->disk('public')
                    ->square()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |-----------------------------------------
                | TWITTER / X
                |-----------------------------------------
                */

                TextColumn::make('twitter_title_bn')
                    ->label('Twitter Title - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('twitter_title_en')
                    ->label('Twitter Title - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('twitter_description_bn')
                    ->label('Twitter Description - Bengali')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('twitter_description_en')
                    ->label('Twitter Description - English')
                    ->limit(60)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |-----------------------------------------
                | TIMESTAMPS
                |-----------------------------------------
                */

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            /*
            |-----------------------------------------
            | TABLE FILTERS
            |-----------------------------------------
            */

            ->filters([

                SelectFilter::make('news_category_id')
                    ->label('News Category')
                    ->relationship(
                        'category',
                        'name_bn'
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Publishing Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published / Scheduled',
                    ]),

                TernaryFilter::make('is_breaking')
                    ->label('Breaking News'),

                TernaryFilter::make('is_featured')
                    ->label('Featured News'),

                TernaryFilter::make('is_trending')
                    ->label('Trending News'),

                TernaryFilter::make('robots_index')
                    ->label('Search Engine Indexing'),

            ])

            /*
            |-----------------------------------------
            | RECORD ACTIONS
            |-----------------------------------------
            */

            ->recordActions([

                EditAction::make()
                    ->label('Edit News')
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation(),

            ])

            // Bulk deletion is disabled.
            ->toolbarActions([])

            // Show newest posts first.
            ->defaultSort('created_at', 'desc');
    }
}
