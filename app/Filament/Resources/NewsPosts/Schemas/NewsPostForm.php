<?php

namespace App\Filament\Resources\NewsPosts\Schemas;

use App\Models\NewsCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | 1. BASIC NEWS INFORMATION
            |--------------------------------------------------------------------------
            */

            Section::make('News Information')
                ->description(
                    'Select a category and enter news headlines in Bengali and English.'
                )
                ->schema([

                    // Category selector
                    Select::make('news_category_id')
                        ->label('News Category')
                        ->relationship(
                            name: 'category',
                            titleAttribute: 'name_bn'
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (NewsCategory $record): string =>
                                "{$record->name_bn} / {$record->name_en}"
                        )
                        ->searchable([
                            'name_bn',
                            'name_en',
                        ])
                        ->preload()
                        ->native(false)
                        ->required()
                        ->helperText(
                            'Select an existing news category. You can create new categories from News Categories.'
                        )
                        ->columnSpanFull(),

                    // Bengali headline
                    TextInput::make('title_bn')
                        ->label('News Headline - Bengali')
                        ->placeholder(
                            'বাংলা সংবাদের শিরোনাম লিখুন'
                        )
                        ->required()
                        ->maxLength(255),

                    // English headline
                    TextInput::make('title_en')
                        ->label('News Headline - English')
                        ->placeholder(
                            'Enter the English news headline'
                        )
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

                    // SEO-friendly slug
                    TextInput::make('slug')
                        ->label('SEO Friendly URL Slug')
                        ->placeholder(
                            'latest-news-chattogram'
                        )
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->rule(
                            'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
                        )
                        ->helperText(
                            'Unique lowercase English URL slug. Changing a published slug requires a redirect.'
                        )
                        ->columnSpanFull(),

                    // Article author
                    Select::make('author_id')
                        ->label('Article Author / CMS User')
                        ->relationship(
                            name: 'author',
                            titleAttribute: 'name'
                        )
                        ->searchable()
                        ->preload()
                        ->default(
                            fn () => auth()->id()
                        )
                        ->disabled(
                            fn (): bool =>
                                auth()->user()?->role !== 'admin'
                        )
                        ->saved(
                            fn (): bool =>
                                auth()->user()?->role === 'admin'
                        )
                        ->helperText(
                            'Administrators can assign an author. For editors, the existing NewsPost model automatically assigns the logged-in user when creating a post.'
                        )
                        ->columnSpanFull(),

                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | 2. BENGALI ARTICLE CONTENT
            |--------------------------------------------------------------------------
            */

            Section::make('Bengali News Content')
                ->description(
                    'Write the complete Bengali version of the article.'
                )
                ->schema([

                    Textarea::make('excerpt_bn')
                        ->label('News Summary - Bengali')
                        ->placeholder(
                            'সংবাদের সংক্ষিপ্ত সারাংশ লিখুন'
                        )
                        ->rows(4)
                        ->maxLength(2000)
                        ->helperText(
                            'A concise and accurate summary of the article.'
                        )
                        ->columnSpanFull(),

                    RichEditor::make('body_bn')
                        ->label('Full News Article - Bengali')
                        ->required()
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory(
                            'news/articles/bengali'
                        )
                        ->fileAttachmentsVisibility('public')
                        ->fileAttachmentsMaxSize(5120)
                        ->minHeight('22rem')
                        ->toolbarButtons([
                            [
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                            ],
                            [
                                'h2',
                                'h3',
                                'h4',
                            ],
                            [
                                'alignStart',
                                'alignCenter',
                                'alignEnd',
                            ],
                            [
                                'blockquote',
                                'bulletList',
                                'orderedList',
                            ],
                            [
                                'table',
                                'attachFiles',
                            ],
                            [
                                'undo',
                                'redo',
                            ],
                        ])
                        ->helperText(
                            'Use descriptive H2 and H3 subheadings, paragraphs, factual information and relevant internal or external links.'
                        )
                        ->columnSpanFull(),

                ]),

            /*
            |--------------------------------------------------------------------------
            | 3. ENGLISH ARTICLE CONTENT
            |--------------------------------------------------------------------------
            */

            Section::make('English News Content')
                ->description(
                    'Write the complete English version of the article.'
                )
                ->schema([

                    Textarea::make('excerpt_en')
                        ->label('News Summary - English')
                        ->placeholder(
                            'Enter a short summary of the article'
                        )
                        ->rows(4)
                        ->maxLength(2000)
                        ->columnSpanFull(),

                    RichEditor::make('body_en')
                        ->label('Full News Article - English')
                        ->required()
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory(
                            'news/articles/english'
                        )
                        ->fileAttachmentsVisibility('public')
                        ->fileAttachmentsMaxSize(5120)
                        ->minHeight('22rem')
                        ->toolbarButtons([
                            [
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                            ],
                            [
                                'h2',
                                'h3',
                                'h4',
                            ],
                            [
                                'alignStart',
                                'alignCenter',
                                'alignEnd',
                            ],
                            [
                                'blockquote',
                                'bulletList',
                                'orderedList',
                            ],
                            [
                                'table',
                                'attachFiles',
                            ],
                            [
                                'undo',
                                'redo',
                            ],
                        ])
                        ->helperText(
                            'Create original, accurate content with descriptive headings and useful links.'
                        )
                        ->columnSpanFull(),

                ]),

            /*
            |--------------------------------------------------------------------------
            | 4. REPORTER AND NEWS SOURCE
            |--------------------------------------------------------------------------
            */

            Section::make('Reporter & News Source')
                ->description(
                    'Manage reporter bylines and original news sources.'
                )
                ->schema([

                    TextInput::make('reporter_bn')
                        ->label('Reporter Name - Bengali')
                        ->placeholder(
                            'নিজস্ব প্রতিবেদক'
                        )
                        ->maxLength(255),

                    TextInput::make('reporter_en')
                        ->label('Reporter Name - English')
                        ->placeholder(
                            'Staff Reporter'
                        )
                        ->maxLength(255),

                    TextInput::make('source_name')
                        ->label('News Source Name')
                        ->placeholder(
                            'Daily Dariyanagar News Desk'
                        )
                        ->maxLength(255),

                    TextInput::make('source_url')
                        ->label('Original News Source URL')
                        ->url()
                        ->maxLength(2048)
                        ->placeholder(
                            'https://example.com/source'
                        ),

                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | 5. FEATURED IMAGE AND IMAGE SEO
            |--------------------------------------------------------------------------
            */

            Section::make('Featured News Image')
                ->description(
                    'Upload the main news image and provide image SEO information.'
                )
                ->schema([

                    FileUpload::make('featured_image')
                        ->label('Featured News Image')
                        ->image()
                        ->acceptedFileTypes([
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ])
                        ->disk('public')
                        ->visibility('public')
                        ->directory('news/featured')
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->preventFilePathTampering()
                        ->maxSize(5120)
                        ->helperText(
                            'Recommended image size: at least 1200 x 675 pixels. Maximum 5 MB.'
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
                            'Accurately describe the image'
                        )
                        ->maxLength(255),

                    TextInput::make('image_caption_bn')
                        ->label('Image Caption - Bengali')
                        ->placeholder(
                            'ছবির ক্যাপশন লিখুন'
                        )
                        ->maxLength(255),

                    TextInput::make('image_caption_en')
                        ->label('Image Caption - English')
                        ->placeholder(
                            'Enter image caption'
                        )
                        ->maxLength(255),

                    TextInput::make('image_credit')
                        ->label('Image Credit / Photographer')
                        ->placeholder(
                            'Photographer or image source'
                        )
                        ->maxLength(255)
                        ->columnSpanFull(),

                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | 6. PUBLISHING SETTINGS
            |--------------------------------------------------------------------------
            */

            Section::make('Publishing & News Placement')
                ->description(
                    'Control publishing status and where news appears in your CMS.'
                )
                ->schema([

                    Select::make('status')
                        ->label('Publishing Status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false)
                        ->live()
                        ->helperText(
                            'Save as Draft while editing. Select Published when the article is ready.'
                        ),

                    DateTimePicker::make('published_at')
                        ->label('Publication Date & Time')
                        ->native(false)
                        ->seconds(false)
                        ->required(
                            fn (Get $get): bool =>
                                $get('status') === 'published'
                        )
                        ->helperText(
                            'Choose a publication date. A future date can be used for scheduled publication when the frontend is connected.'
                        ),

                    Toggle::make('is_breaking')
                        ->label('Breaking News')
                        ->default(false)
                        ->helperText(
                            'Mark the article as breaking news.'
                        ),

                    Toggle::make('is_featured')
                        ->label('Featured / Lead News')
                        ->default(false)
                        ->helperText(
                            'Mark the article as featured.'
                        ),

                    Toggle::make('is_trending')
                        ->label('Trending / Discussed News')
                        ->default(false)
                        ->helperText(
                            'Mark the article for trending news placement.'
                        ),

                    TextInput::make('views')
                        ->label('Total Views')
                        ->numeric()
                        ->default(0)
                        ->disabled()
                        ->saved(false)
                        ->helperText(
                            'Read-only statistics. Defaults to zero until your website tracks article views.'
                        ),

                ])
                ->columns(2),

            /*
            |--------------------------------------------------------------------------
            | 7. BENGALI SEO SETTINGS
            |--------------------------------------------------------------------------
            */

            Section::make('Bengali SEO Settings')
                ->description(
                    'Configure search engine metadata for the Bengali article.'
                )
                ->schema([

                    TextInput::make('seo_title_bn')
                        ->label('SEO Title - Bengali')
                        ->placeholder(
                            'সংবাদের শিরোনাম | ডেইলি দরিয়ানগর'
                        )
                        ->maxLength(255)
                        ->helperText(
                            'Use a unique title describing the news accurately. Keep it concise.'
                        )
                        ->columnSpanFull(),

                    Textarea::make('seo_description_bn')
                        ->label('SEO Meta Description - Bengali')
                        ->placeholder(
                            'সংবাদটি সম্পর্কে সংক্ষিপ্ত ও আকর্ষণীয় বিবরণ লিখুন'
                        )
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText(
                            'Aim for a useful summary, commonly around 140-160 characters. Google may display a different length.'
                        )
                        ->columnSpanFull(),

                    TextInput::make('focus_keyword_bn')
                        ->label('Focus Keyword - Bengali')
                        ->placeholder(
                            'চট্টগ্রাম সংবাদ'
                        )
                        ->maxLength(255)
                        ->helperText(
                            'The main Bengali search topic or phrase.'
                        )
                        ->columnSpanFull(),

                ])
                ->collapsible(),

            /*
            |--------------------------------------------------------------------------
            | 8. ENGLISH SEO SETTINGS
            |--------------------------------------------------------------------------
            */

            Section::make('English SEO Settings')
                ->description(
                    'Configure search engine metadata for the English article.'
                )
                ->schema([

                    TextInput::make('seo_title_en')
                        ->label('SEO Title - English')
                        ->placeholder(
                            'Latest Chattogram News | Daily Dariyanagar'
                        )
                        ->maxLength(255)
                        ->helperText(
                            'Write a concise and descriptive SEO title.'
                        )
                        ->columnSpanFull(),

                    Textarea::make('seo_description_en')
                        ->label('SEO Meta Description - English')
                        ->placeholder(
                            'Read the latest report, updates and important details.'
                        )
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText(
                            'Write a unique summary for search results.'
                        )
                        ->columnSpanFull(),

                    TextInput::make('focus_keyword_en')
                        ->label('Focus Keyword - English')
                        ->placeholder(
                            'Chattogram News'
                        )
                        ->maxLength(255)
                        ->columnSpanFull(),

                ])
                ->collapsible(),

            /*
            |--------------------------------------------------------------------------
            | 9. ADVANCED TECHNICAL SEO
            |--------------------------------------------------------------------------
            */

            Section::make('Advanced Technical SEO')
                ->description(
                    'Control canonical URLs, search indexing and structured data.'
                )
                ->schema([

                    TextInput::make('canonical_url')
                        ->label('Canonical URL Override')
                        ->url()
                        ->maxLength(2048)
                        ->placeholder(
                            'https://dailydariyanagar.com/news/example-post'
                        )
                        ->helperText(
                            'Optional. Leave blank for the default article URL when frontend SEO is implemented.'
                        )
                        ->columnSpanFull(),

                    Toggle::make('robots_index')
                        ->label('Allow Search Engine Indexing')
                        ->default(true)
                        ->helperText(
                            'Enable to allow indexing. Disable to request noindex.'
                        ),

                    Toggle::make('robots_follow')
                        ->label('Allow Following Links')
                        ->default(true)
                        ->helperText(
                            'Controls the follow/nofollow search directive.'
                        ),

                    Select::make('schema_type')
                        ->label('Structured Data Type')
                        ->options([
                            'NewsArticle' => 'NewsArticle',
                            'Article' => 'Article',
                        ])
                        ->default('NewsArticle')
                        ->native(false)
                        ->required()
                        ->helperText(
                            'Select NewsArticle for news reporting. The JSON-LD will be generated when the frontend is connected.'
                        )
                        ->columnSpanFull(),

                ])
                ->columns(2)
                ->collapsible(),

            /*
            |--------------------------------------------------------------------------
            | 10. OPEN GRAPH / FACEBOOK SEO
            |--------------------------------------------------------------------------
            */

            Section::make('Open Graph / Facebook SEO')
                ->description(
                    'Configure social sharing titles, descriptions and images.'
                )
                ->schema([

                    TextInput::make('og_title_bn')
                        ->label('Open Graph Title - Bengali')
                        ->maxLength(255),

                    TextInput::make('og_title_en')
                        ->label('Open Graph Title - English')
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
                        ->label('Social Sharing Image')
                        ->image()
                        ->acceptedFileTypes([
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ])
                        ->disk('public')
                        ->visibility('public')
                        ->directory('news/social')
                        ->imageEditor()
                        ->imageEditorAspectRatioOptions([
                            null,
                            '1.91:1',
                            '16:9',
                        ])
                        ->preventFilePathTampering()
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
            | 11. TWITTER / X SEO
            |--------------------------------------------------------------------------
            */

            Section::make('Twitter / X SEO')
                ->description(
                    'Set custom Twitter / X sharing information in both languages.'
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

        ]);
    }
}
