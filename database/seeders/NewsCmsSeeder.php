<?php

namespace Database\Seeders;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Database\Seeder;

class NewsCmsSeeder extends Seeder
{
    public function run(): void
    {
        /*
         |-------------------------------------------
         | News Categories
         |-------------------------------------------
         */

        $categories = [
            [
                'slug' => 'national',
                'bn' => 'জাতীয়',
                'en' => 'National',
                'desc_bn' => 'বাংলাদেশের জাতীয় সংবাদ, গুরুত্বপূর্ণ ঘটনা ও সর্বশেষ খবর।',
                'desc_en' => 'Latest national news, developments and updates from Bangladesh.',
            ],
            [
                'slug' => 'chattogram',
                'bn' => 'চট্টগ্রাম',
                'en' => 'Chattogram',
                'desc_bn' => 'চট্টগ্রাম ও আশপাশের এলাকার সর্বশেষ সংবাদ ও প্রতিবেদন।',
                'desc_en' => 'Local news and reports from Chattogram and surrounding areas.',
            ],
            [
                'slug' => 'international',
                'bn' => 'আন্তর্জাতিক',
                'en' => 'International',
                'desc_bn' => 'বিশ্বের বিভিন্ন দেশের সংবাদ ও আন্তর্জাতিক ঘটনাবলি।',
                'desc_en' => 'International headlines and global developments.',
            ],
            [
                'slug' => 'politics',
                'bn' => 'রাজনীতি',
                'en' => 'Politics',
                'desc_bn' => 'বাংলাদেশের রাজনীতি, নীতি ও রাজনৈতিক কর্মকাণ্ডের সংবাদ।',
                'desc_en' => 'Political news, policy and government developments.',
            ],
            [
                'slug' => 'economy',
                'bn' => 'অর্থ-বাণিজ্য',
                'en' => 'Economy',
                'desc_bn' => 'ব্যবসা, অর্থনীতি, বাজার ও বাণিজ্যের সর্বশেষ খবর।',
                'desc_en' => 'Business, economy, markets and trade news.',
            ],
            [
                'slug' => 'sports',
                'bn' => 'খেলাধুলা',
                'en' => 'Sports',
                'desc_bn' => 'ক্রিকেট, ফুটবল ও অন্যান্য খেলার খবর।',
                'desc_en' => 'Cricket, football and other sports news.',
            ],
            [
                'slug' => 'entertainment',
                'bn' => 'বিনোদন',
                'en' => 'Entertainment',
                'desc_bn' => 'চলচ্চিত্র, নাটক, সংগীত ও বিনোদন জগতের সংবাদ।',
                'desc_en' => 'Film, music, television and entertainment news.',
            ],
            [
                'slug' => 'technology',
                'bn' => 'তথ্যপ্রযুক্তি',
                'en' => 'Technology & AI',
                'desc_bn' => 'প্রযুক্তি, কৃত্রিম বুদ্ধিমত্তা ও ডিজিটাল বিশ্বের খবর।',
                'desc_en' => 'Technology, artificial intelligence and digital innovation news.',
            ],
            [
                'slug' => 'video',
                'bn' => 'ভিডিও',
                'en' => 'Video',
                'desc_bn' => 'সংবাদ ভিডিও, প্রতিবেদন ও বিশেষ ভিডিও কনটেন্ট।',
                'desc_en' => 'News videos, reports and special video content.',
            ],
        ];

        foreach ($categories as $index => $category) {
            NewsCategory::firstOrCreate(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'name_bn' => $category['bn'],
                    'name_en' => $category['en'],

                    'description_bn' => $category['desc_bn'],
                    'description_en' => $category['desc_en'],

                    'seo_title_bn' => $category['bn']
                        . ' সংবাদ | ডেইলি দরিয়ানগর',

                    'seo_title_en' => $category['en']
                        . ' News | Daily Dariyanagar',

                    'seo_description_bn' => $category['desc_bn'],
                    'seo_description_en' => $category['desc_en'],

                    'focus_keyword_bn' => $category['bn'] . ' সংবাদ',
                    'focus_keyword_en' => $category['en'] . ' news',

                    'og_title_bn' => $category['bn']
                        . ' | ডেইলি দরিয়ানগর',

                    'og_title_en' => $category['en']
                        . ' | Daily Dariyanagar',

                    'og_description_bn' => $category['desc_bn'],
                    'og_description_en' => $category['desc_en'],

                    'robots_index' => true,
                    'robots_follow' => true,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        /*
         |-------------------------------------------
         | Sample News Posts
         | These are DEMO stories, not real reports.
         | All are saved as unpublished drafts.
         |-------------------------------------------
         */

        $posts = [
            [
                'slug' => 'demo-chattogram-community-project',
                'category' => 'chattogram',
                'bn' => 'নমুনা সংবাদ: চট্টগ্রামে কমিউনিটি প্রকল্প নিয়ে আলোচনা',
                'en' => 'Demo News: Discussion on a Chattogram Community Project',
            ],
            [
                'slug' => 'demo-bangladesh-education-initiative',
                'category' => 'national',
                'bn' => 'নমুনা সংবাদ: বাংলাদেশে শিক্ষা উদ্যোগ নিয়ে বিশেষ প্রতিবেদন',
                'en' => 'Demo News: Special Report on an Education Initiative in Bangladesh',
            ],
            [
                'slug' => 'demo-sports-youth-training',
                'category' => 'sports',
                'bn' => 'নমুনা সংবাদ: তরুণ খেলোয়াড়দের প্রশিক্ষণ নিয়ে আয়োজন',
                'en' => 'Demo News: Youth Athlete Training Programme',
            ],
            [
                'slug' => 'demo-digital-technology-workshop',
                'category' => 'technology',
                'bn' => 'নমুনা সংবাদ: ডিজিটাল প্রযুক্তি বিষয়ে কর্মশালার পরিকল্পনা',
                'en' => 'Demo News: Planning a Digital Technology Workshop',
            ],
            [
                'slug' => 'demo-local-business-discussion',
                'category' => 'economy',
                'bn' => 'নমুনা সংবাদ: স্থানীয় ব্যবসার উন্নয়ন নিয়ে আলোচনা',
                'en' => 'Demo News: Discussion on Local Business Development',
            ],
        ];

        foreach ($posts as $index => $post) {

            $category = NewsCategory::where(
                'slug',
                $post['category']
            )->firstOrFail();

            NewsPost::firstOrCreate(
                [
                    'slug' => $post['slug'],
                ],
                [
                    'news_category_id' => $category->id,

                    'title_bn' => $post['bn'],
                    'title_en' => $post['en'],

                    'excerpt_bn' =>
                        'এটি ডেইলি দরিয়ানগর নিউজ সিএমএস পরীক্ষার জন্য তৈরি নমুনা সংবাদ। এটি কোনো যাচাইকৃত বাস্তব ঘটনার প্রতিবেদন নয়।',

                    'excerpt_en' =>
                        'This is a sample article for testing the Daily Dariyanagar CMS. It does not report a verified real-world event.',

                    'body_bn' =>
                        '<h2>নমুনা সংবাদ</h2>'
                        . '<p>এটি ডেইলি দরিয়ানগর নিউজ সিএমএস-এর একটি পরীক্ষামূলক পোস্ট।</p>'
                        . '<p>এখানে সম্পাদক প্রকৃত সংবাদ, বিস্তারিত তথ্য ও যাচাইকৃত সূত্র যুক্ত করতে পারবেন।</p>'
                        . '<p>এই কনটেন্ট বাস্তব সংবাদ হিসেবে প্রকাশ করবেন না।</p>',

                    'body_en' =>
                        '<h2>Sample News Article</h2>'
                        . '<p>This is a demonstration post for the Daily Dariyanagar content management system.</p>'
                        . '<p>Editors can replace this with verified reporting and appropriate sources.</p>'
                        . '<p>Do not publish this demonstration content as factual news.</p>',

                    'reporter_bn' => 'ডেমো নিউজ ডেস্ক',
                    'reporter_en' => 'Demo News Desk',

                    'status' => 'draft',
                    'published_at' => null,

                    'is_breaking' => false,
                    'is_featured' => $index === 0,
                    'is_trending' => false,

                    'seo_title_bn' => $post['bn']
                        . ' | ডেইলি দরিয়ানগর',

                    'seo_title_en' => $post['en']
                        . ' | Daily Dariyanagar',

                    'seo_description_bn' =>
                        'নিউজ সিএমএস পরীক্ষার নমুনা কনটেন্ট। প্রকাশের আগে প্রকৃত তথ্য ও যাচাইকৃত সংবাদ দিয়ে প্রতিস্থাপন করুন।',

                    'seo_description_en' =>
                        'Demonstration article for CMS testing. Replace with verified editorial content before publishing.',

                    'focus_keyword_bn' => 'নমুনা সংবাদ',
                    'focus_keyword_en' => 'demo news',

                    'robots_index' => false,
                    'robots_follow' => false,

                    'schema_type' => 'NewsArticle',
                ]
            );
        }
    }
}
