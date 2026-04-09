<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Usimamizi wa Biashara',
                'description' => 'Mbinu bora za kuendesha na kusimamia biashara yako kila siku.',
                'icon' => 'briefcase',
            ],
            [
                'name' => 'Mauzo na Masoko',
                'description' => 'Jinsi ya kupata wateja wapya na kuongeza mauzo ya bidhaa zako.',
                'icon' => 'cart-check',
            ],
            [
                'name' => 'Usimamizi wa Stoo',
                'description' => 'Mbinu za kudhibiti mzigo (stock) na kuzuia upotevu.',
                'icon' => 'box-seam',
            ],
            [
                'name' => 'Uhasibu na Fedha',
                'description' => 'Kuweka rekodi za fedha, faida, na matumizi ya biashara.',
                'icon' => 'cash-stack',
            ],
            [
                'name' => 'Huduma kwa Wateja',
                'description' => 'Jinsi ya kuhudumia wateja ili waendelee kurudi kwenye biashara yako.',
                'icon' => 'people',
            ],
            [
                'name' => 'Teknolojia na Biashara',
                'description' => 'Kutumia mifumo ya kidijitali kukuza biashara yako.',
                'icon' => 'laptop',
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('article_categories')->updateOrInsert(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, [
                    'slug' => Str::slug($cat['name']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
