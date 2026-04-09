<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logo = 'LOGO-DUKAFY-removebg-preview.png';
        $articles = [
            [
                'title' => 'Mbinu 5 za Kuongeza Mauzo Kwenye Biashara Yako Ndogo',
                'category' => 'Mauzo na Masoko',
                'age_range' => 'Wote',
                'image' => $logo,
                'published_at' => '2026-04-10',
                'is_featured' => true,
                'content' => '<p>Kuongeza mauzo ni lengo kuu la kila mfanyabiashara. Katika soko la leo lenye ushindani mkubwa, unahitaji mbinu za kisasa ili kuwavutia wateja wapya na kuwafanya wale wa zamani waendelee kurudi.</p>
                <h3>1. Tumia Mitandao ya Kijamii kwa Usahihi</h3>
                <p>Usipost tu picha za bidhaa; elezea jinsi bidhaa zako zinavyotatua matatizo ya wateja wako. Tumia WhatsApp Status na Instagram Stories kuonyesha maisha ya kila siku ya biashara yako.</p>
                <h3>2. Toa Huduma Bora kwa Wateja</h3>
                <p>Mteja mmoja anayehudumiwa vizuri anaweza kuletea wateja wengine watano kupitia neno la mdomo (word of mouth). Hakikisha unawajibu wateja wako haraka na kwa lugha nzuri.</p>
                <ul>
                    <li><strong>Zingatia Ubora:</strong> Kamwe usitoe bidhaa chini ya kiwango.</li>
                    <li><strong>Sikiliza Maoni:</strong> Uliza wateja wako nini unaweza kuboresha.</li>
                </ul>
                <p>Kumbuka, biashara ni watu. Jenga uhusiano mwema na wateja wako na utaona mauzo yakiongezeka.</p>',
            ],
            [
                'title' => 'Umuhimu wa Kutenganisha Fedha za Biashara na Fedha Binafsi',
                'category' => 'Uhasibu na Fedha',
                'age_range' => 'Wote',
                'image' => $logo,
                'published_at' => '2026-04-10',
                'is_featured' => true,
                'content' => '<p>Moja ya makosa makubwa wanayofanya wafanyabiashara wengi ni kuchanganya fedha za matumizi ya nyumbani na fedha za biashara. Hii inafanya iwe vigumu kujua kama biashara inapata faida au hasara.</p>
                <h3>Kwa nini utenganishe?</h3>
                <p>Unapotenganisha fedha hizi, unakuwa na picha halisi ya ukuaji wa biashara yako. Inakusaidia pia kupanga bajeti ya biashara na kujilipa mshahara wewe mwenyewe kama mfanyakazi wa biashara yako.</p>
                <blockquote>"Biashara inayokula mtaji wake haiwezi kudumu. Nidhamu ya fedha ndio siri ya utajiri." — Dukafy Expert Team</blockquote>
                <h3>Hatua za Kuchukua:</h3>
                <ol>
                    <li>Fungua akaunti tofauti ya benki kwa ajili ya biashara pekee.</li>
                    <li>Weka rekodi ya kila senti inayotoka na kuingia.</li>
                    <li>Jilipe mshahara uliopangwa kila mwezi badala ya kuchukua fedha kila unapohitaji.</li>
                </ol>',
            ],
            [
                'title' => 'Jinsi ya Kudhibiti Stoo na Kuzuia Upotevu wa Bidhaa',
                'category' => 'Usimamizi wa Stoo',
                'age_range' => 'Wote',
                'image' => $logo,
                'published_at' => '2026-04-10',
                'is_featured' => true,
                'content' => '<p>Upotevu wa bidhaa stoo ni sawa na kupoteza fedha taslimu. Usimamizi mbovu wa stoo unaweza kuua biashara yoyote ile, iwe ni duka dogo au kampuni kubwa.</p>
                <h3>Tumia Mfumo wa First-In, First-Out (FIFO)</h3>
                <p>Hakikisha bidhaa zilizoingia mapema ndizo zinazotoka kwanza, hasa kwa bidhaa zinazoharibika haraka. Hii inazuia bidhaa kukaa muda mrefu na kuharibika au kupitwa na wakati.</p>
                <h3>Fanya Ukaguzi wa Mara kwa Mara (Stock Take)</h3>
                <p>Usisubiri mpaka mwisho wa mwaka kufanya hesabu. Fanya ukaguzi wa kushtukiza au wa kila wiki ili kuhakikisha idadi ya bidhaa zilizopo stoo inalingana na rekodi zako za mauzo.</p>
                <p>Dukafy inakusaidia kurahisisha kazi hii kwa kuweka rekodi za kidijitali za kila bidhaa inayoingia na kutoka.</p>',
            ],
            [
                'title' => 'Faida za Kutumia Mifumo ya Kidijitali Katika Biashara',
                'category' => 'Teknolojia na Biashara',
                'age_range' => 'Wote',
                'image' => $logo,
                'published_at' => '2026-04-10',
                'is_featured' => true,
                'content' => '<p>Dunia inabadilika, na biashara zinazotumia teknolojia ndizo zinazokua kwa kasi zaidi. Kutumia mfumo wa kidijitali badala ya madaftari ya mkono kuna faida nyingi mno.</p>
                <ul>
                    <li><strong>Usalama wa Kumbukumbu:</strong> Madaftari yanaweza kupotea, kuchanika au kuungua, lakini data za kidijitali ziko salama "online".</li>
                    <li><strong>Ripoti za Papo kwa Papo:</strong> Unaweza kuona faida yako kwa siku, wiki au mwezi kwa kubonyeza kitufe kimoja tu.</li>
                    <li><strong>Usimamizi wa mbali:</strong> Unaweza kuona kinachoendelea kwenye biashara yako hata kama haupo eneo la kazi.</li>
                </ul>
                <p>Kuanza kutumia mfumo kama Dukafy ni uwekezaji mdogo wenye matokeo makubwa kwa mustakabali wa biashara yako.</p>',
            ],
            [
                'title' => 'Siri ya Kupata na Kutunza Wateja Wenye Uaminifu',
                'category' => 'Huduma kwa Wateja',
                'age_range' => 'Wote',
                'image' => $logo,
                'published_at' => '2026-04-10',
                'is_featured' => true,
                'content' => '<p>Kupata mteja mpya ni gharama kubwa kuliko kumtunza mteja uliyenaye tayari. Wateja waaminifu ndio uti wa mgongo wa biashara yoyote imara.</p>
                <h3>Wajue Wateja Wako</h3>
                <p>Jaribu kukumbuka majina ya wateja wako na mapendeleo yao. Hii inawafanya wajihisi kuthaminiwa na kuwa na ukaribu na biashara yako.</p>
                <h3>Toa Zawadi au Punguzo (Loyalty Programs)</h3>
                <p>Wape wateja wako wa kila siku zawadi ndogo ndogo au punguzo la bei baada ya kufikia idadi fulani ya manunuzi. Hii inawapa motisha ya kuendelea kuja kwako badala ya kwenda kwa washindani wako.</p>
                <p>Huduma bora si tu kuhusu kuuza bidhaa, ni kuhusu kutatua matatizo ya mteja kwa tabasamu na uaminifu.</p>',
            ],
        ];

        foreach ($articles as $article) {
            $catId = DB::table('article_categories')->where('name', $article['category'])->value('id');
            
            DB::table('articles')->updateOrInsert(
                ['slug' => Str::slug($article['title'])],
                [
                    'title' => $article['title'],
                    'slug' => Str::slug($article['title']),
                    'category_id' => $catId,
                    'category' => $article['category'],
                    'age_range' => $article['age_range'],
                    'image' => $article['image'],
                    'excerpt' => Str::limit(strip_tags($article['content']), 120),
                    'content' => $article['content'],
                    'published_at' => $article['published_at'],
                    'is_featured' => $article['is_featured'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
