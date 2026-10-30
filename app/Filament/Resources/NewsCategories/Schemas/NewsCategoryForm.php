<?php

namespace App\Filament\Resources\NewsCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | 1. BASIC CATEGORY INFORMATION
            |--------------------------------------------------------------------------
            */

            Section::make('Category Information')
                ->description(
                    'Enter Bengali and English category details.'
                )
                ->schema([

                    TextInput::make('name_bn')
                        ->label('Category Name - Bengali')
                        ->placeholder('জাতীয়')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('name_en')
                        ->label('Category Name - English')
                        ->placeholder('National')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(
                            function (
                                Set $set,
                                Get $get,
                                ?string $state
                            ): void {
                                if (blank($get('slug'))) {
                                    $set(
                                        'slug',
                                        Str::slug($state ?? '')
                                    );
                                }
                            }
                        ),

                    TextInput::make('slug')
                        ->label('SEO Friendly URL Slug')
                        ->placeholder('national')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->rule(
                            'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
                        )
                        ->helperText(
                            'Use lowercase English letters, numbers and hyphens. Changing an existing slug requires a URL redirect.'
                        )
                        ->columnSpanFull(),

                    Textarea::make('description_bn')
                        ->label('Category Description - Bengali')
                        ->placeholder(
                            'এই বিভাগের বিস্তারিত বিবরণ লিখুন...'
                        )
                        ->rows(4)
                        ->columnSpanFull(),

                    Textarea::make('description_en')
                        ->label('Category Description - English')
                        ->placeholder(
                            'Write a description of this news category...'
                        )
                        ->rows(4)
                        ->columnSpanFull(),

                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | 2. CATEGORY IMAGE AND ALT TEXT
            |--------------------------------------------------------------------------
            */

            Section::make('Category Image & Image SEO')
                ->description(
                    'Upload a category image and add descriptive alt text in both languages.'
                )
                ->schema([

                    FileUpload::make('image')
                        ->label('Category Featured Image')
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->directory('news/categories')
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->maxSize(5120)
                        ->helperText(
                            'Maximum image size: 5 MB. Use a clear, relevant image.'
                        )
                        ->columnSpanFull(),

                    TextInput::make('image_alt_bn')
                        ->label('Image Alt Text - Bengali')
                        ->placeholder(
                            'ছবিটির অর্থপূর্ণ বাংলা বিবরণ'
                        )
                        ->maxLength(255),

                    TextInput::make('image_alt_en')
                        ->label('Image Alt Text - English')
                        ->placeholder(
                            'Describe the image accurately in English'
                        )
                        ->maxLength(255),

                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | 3. BENGALI SEO SETTINGS
            |--------------------------------------------------------------------------
            */

            Section::make('Bengali SEO Settings')
                ->description(
                    'Search engine optimization metadata for Bengali content.'
                )
                ->schema([

                    TextInput::make('seo_title_bn')
                        ->label('SEO Title - Bengali')
                        ->placeholder(
                            'জাতীয় সংবাদ | ডেইলি দরিয়ানগর'
                        )
                        ->maxLength(255)
                        ->helperText(
                            'Use a clear, unique search title containing the main topic naturally.'
                        )
                        ->columnSpanFull(),

                    Textarea::make('seo_description_bn')
                        ->label('Meta Description - Bengali')
                        ->placeholder(
                            'এই বিভাগের গুরুত্বপূর্ণ ও সর্বশেষ সংবাদ সম্পর্কে সংক্ষিপ্ত বিবরণ লিখুন।'
                        )
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText(
                            'Suggested length: around 140-160 characters. Search engines may display a different length.'
                        )
                        ->columnSpanFull(),

                    TextInput::make('focus_keyword_bn')
                        ->label('Focus Keyword - Bengali')
                        ->placeholder('জাতীয় সংবাদ')
                        ->maxLength(255)
                        ->helperText(
                            'Primary Bengali search phrase for this category.'
                        )
                        ->columnSpanFull(),

                ])
                ->collapsible(),

            /*
            |--------------------------------------------------------------------------
            | 4. ENGLISH SEO SETTINGS
            |--------------------------------------------------------------------------
            */

            Section::make('English SEO Settings')
                ->description(
                    'Search engine optimization metadata for English content.'
                )
                ->schema([

                    TextInput::make('seo_title_en')
                        ->label('SEO Title - English')
                        ->placeholder(
                            'National News | Daily Dariyanagar'
                        )
                        ->maxLength(255)
                        ->helperText(
                            'Write a natural title that accurately describes the category.'
                        )
                        ->columnSpanFull(),

                    Textarea::make('seo_description_en')
                        ->label('Meta Description - English')
                        ->placeholder(
                            'Read the latest national news, reports and updates from Bangladesh.'
                        )
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText(
                            'Aim for a useful, unique search summary.'
                        )
                        ->columnSpanFull(),

                    TextInput::make('focus_keyword_en')
                        ->label('Focus Keyword - English')
                        ->placeholder('Bangladesh National News')
                        ->maxLength(255)
                        ->columnSpanFull(),

                ])
                ->collapsible(),

            /*
            |--------------------------------------------------------------------------
            | 5. ADVANCED TECHNICAL SEO
            |--------------------------------------------------------------------------
            */

            Section::make('Advanced Technical SEO')
                ->description(
                    'Configure canonical URLs and search engine crawling directives.'
                )
                ->schema([

                    TextInput::make('canonical_url')
                        ->label('Canonical URL Override')
                        ->url()
                        ->maxLength(2048)
                        ->placeholder(
                            'https://dailydariyanagar.com/category/national'
                        )
                        ->helperText(
                            'Optional. Leave blank to use the default category URL when frontend SEO is connected.'
                        )
                        ->columnSpanFull(),

                    Toggle::make('robots_index')
                        ->label('Allow Search Engine Indexing')
                        ->default(true)
                        ->helperText(
                            'Enable to permit indexing. Disable to request noindex.'
                        ),

                    Toggle::make('robots_follow')
                        ->label('Allow Following Links')
                        ->default(true)
                        ->helperText(
                            'Enable to allow search engines to follow links on the category page.'
                        ),

                ])
                ->columns(2)
                ->collapsible(),

            /*
            |--------------------------------------------------------------------------
            | 6. OPEN GRAPH / FACEBOOK SEO
            |--------------------------------------------------------------------------
            */

            Section::make('Open Graph & Facebook SEO')
                ->description(
                    'Control the category title, description and image used when sharing links on supported social platforms.'
                )
                ->schema([

                    TextInput::make('og_title_bn')
                        ->label('Open Graph Title - Bengali')
                        ->placeholder(
                            'জাতীয় সংবাদ | ডেইলি দরিয়ানগর'
                        )
                        ->maxLength(255),

                    TextInput::make('og_title_en')
                        ->label('Open Graph Title - English')
                        ->placeholder(
                            'National News | Daily Dariyanagar'
                        )
                        ->maxLength(255),

                    Textarea::make('og_description_bn')
                        ->label('Open Graph Description - Bengali')
                        ->rows(3)
                        ->maxLength(1000),

                    Textarea::make('og_description_en')
                        ->label('Open Graph Description - English')
                        ->rows(3)
                        ->maxLength(1000),

                    FileUpload::make('og_image')
                        ->label('Open Graph / Social Sharing Image')
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->directory('news/categories/social')
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions([
                            null,
                            '1.91:1',
                            '16:9',
                        ])
                        ->maxSize(5120)
                        ->helperText(
                            'Recommended size: 1200 x 630 pixels. Maximum 5 MB.'
                        )
                        ->columnSpanFull(),

                ])
                ->columns(2)
                ->collapsible(),

            /*
            |--------------------------------------------------------------------------
            | 7. TWITTER / X SEO
            |--------------------------------------------------------------------------
            */

            Section::make('Twitter / X SEO Settings')
                ->description(
                    'Set custom sharing titles and descriptions for Twitter / X.'
                )
                ->schema([

                    TextInput::make('twitter_title_bn')
                        ->label('Twitter Title - Bengali')
                        ->maxLength(255),

                    TextInput::make('twitter_title_en')
                        ->label('Twitter Title - English')
                        ->maxLength(255),

                    Textarea::make('twitter_description_bn')
                        ->label('Twitter Description - Bengali')
                        ->rows(3)
                        ->maxLength(1000),

                    Textarea::make('twitter_description_en')
                        ->label('Twitter Description - English')
                        ->rows(3)
                        ->maxLength(1000),

                ])
                ->columns(2)
                ->collapsible(),

            /*
            |--------------------------------------------------------------------------
            | 8. CATEGORY STATUS & DISPLAY SETTINGS
            |--------------------------------------------------------------------------
            */

            Section::make('Category Display Settings')
                ->description(
                    'Control whether this category is active and its ordering in the news CMS.'
                )
                ->schema([

                    Toggle::make('is_active')
                        ->label('Active Category')
                        ->default(true)
                        ->helperText(
                            'Enable this category for use in the news publishing system.'
                        ),

                    TextInput::make('sort_order')
                        ->label('Category Display Order')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required()
                        ->helperText(
                            'Lower numbers appear first when ordered by sort_order.'
                        ),

                ])
                ->columns(2),

        ]);
    }
}
