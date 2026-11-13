<?php

namespace Database\Seeders;

use App\Enums\FragranceStatus;
use App\Enums\PackshotTone;
use App\Models\Fragrance;
use App\Models\ScentFamily;
use Illuminate\Database\Seeder;

class FragranceCatalogSeeder extends Seeder
{
    /**
     * Uses updateOrCreate throughout, so it can be re-run safely.
     */
    public function run(): void
    {
        $familyIds = [];

        foreach ($this->scentFamilies() as $index => $family) {
            $familyIds[$family['slug']] = ScentFamily::updateOrCreate(
                ['slug' => $family['slug']],
                [...$family, 'position' => $index + 1],
            )->id;
        }

        foreach ($this->fragrances() as $index => $fragrance) {
            [$opening, $heart, $dryDown] = $fragrance['notes'];
            $pickedFamily = $this->finderPicks()[$fragrance['slug']] ?? null;

            Fragrance::updateOrCreate(
                ['slug' => $fragrance['slug']],
                [
                    'scent_family_id' => $familyIds[$fragrance['family']],
                    'house' => $fragrance['house'],
                    'name' => $fragrance['name'],
                    'tone' => $fragrance['tone'],
                    'size' => $fragrance['size'],
                    'concentration' => 'Eau de Parfum',
                    'price' => $fragrance['price'],
                    'caption' => $fragrance['caption'],
                    'description' => $fragrance['description'],
                    'opening_notes' => $opening,
                    'heart_notes' => $heart,
                    'dry_down_notes' => $dryDown,
                    'source_url' => $fragrance['source'],
                    'is_finder_pick' => $pickedFamily === $fragrance['family'],
                    'position' => $index + 1,
                    'status' => FragranceStatus::LISTED,
                ],
            );
        }
    }

    /**
     * @return array<int, array{slug: string, label: string, mood: string, example_notes: string}>
     */
    private function scentFamilies(): array
    {
        return [
            ['slug' => 'sweet', 'label' => 'Sweet & Warm', 'mood' => 'Spiced and sweet', 'example_notes' => 'Vanilla · coffee · cacao'],
            ['slug' => 'fresh', 'label' => 'Fresh & Woody', 'mood' => 'Crisp and bright', 'example_notes' => 'Citrus · mint · cedar'],
            ['slug' => 'soft', 'label' => 'Soft & Creamy', 'mood' => 'Soft and creamy', 'example_notes' => 'Peony · peach · musk'],
            ['slug' => 'dark', 'label' => 'Dark & Spicy', 'mood' => 'Deep and spicy', 'example_notes' => 'Leather · oud · spice'],
        ];
    }

    /**
     * The bottle the scent finder shows first for each family, keyed by fragrance slug.
     *
     * @return array<string, string>
     */
    private function finderPicks(): array
    {
        return ['khamrah' => 'sweet', 'turathi' => 'fresh', 'yara' => 'soft', '9pm' => 'dark'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fragrances(): array
    {
        return [
            [
                'slug' => '9pm', 'house' => 'Afnan', 'name' => '9 PM', 'family' => 'dark', 'tone' => PackshotTone::SMOKE,
                'size' => '100 mL', 'price' => 4900,
                'caption' => 'Cinnamon apple & vanilla',
                'description' => 'Crisp apple and cinnamon open up into orange blossom. As it settles, vanilla and tonka bring out a warmer, sweeter side.',
                'notes' => ['Apple, cinnamon, bergamot, lavandin', 'Orange blossom, lily of the valley', 'Vanilla, tonka bean, amber, patchouli'],
                'source' => 'https://india.afnan.com/products/9-pm',
            ],
            [
                'slug' => 'khamrah', 'house' => 'Lattafa', 'name' => 'Khamrah', 'family' => 'sweet', 'tone' => PackshotTone::HONEY,
                'size' => '100 mL', 'price' => 5200,
                'caption' => 'Spiced dates & praline',
                'description' => 'A dusting of cinnamon and nutmeg gives way to syrupy dates and praline. Vanilla and resin leave a deep, warm finish.',
                'notes' => ['Cinnamon, nutmeg, bergamot', 'Dates, praline, tuberose, lily of the valley', 'Vanilla, tonka, amberwood, benzoin, myrrh, akigalawood'],
                'source' => 'https://lattafa.com/product/khamrah/',
            ],
            [
                'slug' => 'turathi', 'house' => 'Afnan', 'name' => 'Turathi Blue', 'family' => 'fresh', 'tone' => PackshotTone::MINERAL,
                'size' => '90 mL', 'price' => 5800,
                'caption' => 'Bright citrus & amber woods',
                'description' => 'Bergamot and mandarin bring a bright first spray. Amber and woods soften the citrus before a musky, gently spicy finish.',
                'notes' => ['Bergamot, mandarin', 'Amber, woods', 'Musk, patchouli, fresh spice'],
                'source' => 'https://my.afnan.com/products/turathi-blue-homme',
            ],
            [
                'slug' => 'yara', 'house' => 'Lattafa', 'name' => 'Yara', 'family' => 'soft', 'tone' => PackshotTone::PETAL,
                'size' => '100 mL', 'price' => 3900,
                'caption' => 'Tropical fruit & vanilla musk',
                'description' => 'Soft florals and tangerine lead into a creamy tropical sweetness. Vanilla, sandalwood and musk linger underneath.',
                'notes' => ['Tangerine, orchid, heliotrope', 'Tropical fruit, gourmand accord', 'Vanilla, sandalwood, musk'],
                'source' => 'https://lattafa.com/product/yara/',
            ],
            [
                'slug' => 'asad', 'house' => 'Lattafa', 'name' => 'Asad', 'family' => 'dark', 'tone' => PackshotTone::SMOKE,
                'size' => '100 mL', 'price' => 4300,
                'caption' => 'Black pepper & coffee',
                'description' => 'Black pepper and tobacco give way to earthy coffee and iris. The dry woods pick up warmth from amber and vanilla.',
                'notes' => ['Black pepper, pineapple, tobacco', 'Coffee, iris, patchouli', 'Amber, vanilla, dry woods, benzoin, labdanum'],
                'source' => 'https://lattafa.com/product/asad/',
            ],
            [
                'slug' => 'qahwa', 'house' => 'Lattafa', 'name' => 'Khamrah Qahwa', 'family' => 'sweet', 'tone' => PackshotTone::HONEY,
                'size' => '100 mL', 'price' => 5600,
                'caption' => 'Roasted coffee & praline',
                'description' => 'Ginger and cardamom cut through the praline. A roasted coffee note lingers with vanilla and tonka.',
                'notes' => ['Ginger, cardamom, cinnamon', 'Praline, candied fruit, white flowers', 'Coffee, vanilla, tonka, benzoin, musk'],
                'source' => 'https://lattafa.com/product/khamrah-qahwa/',
            ],
            [
                'slug' => 'dive', 'house' => 'Afnan', 'name' => '9 AM Dive', 'family' => 'fresh', 'tone' => PackshotTone::MINERAL,
                'size' => '100 mL', 'price' => 5100,
                'caption' => 'Lemon, mint & cedar',
                'description' => 'Lemon and mint make for a brisk opening. Apple and cedar take over before sandalwood and ginger settle close.',
                'notes' => ['Lemon, pink pepper, mint, blackcurrant', 'Apple, incense, cedar', 'Patchouli, jasmine, ginger, sandalwood'],
                'source' => 'https://india.afnan.com/products/9-am-dive',
            ],
            [
                'slug' => 'bouquet', 'house' => 'Afnan', 'name' => 'Mystique Bouquet', 'family' => 'soft', 'tone' => PackshotTone::PETAL,
                'size' => '80 mL', 'price' => 6500,
                'caption' => 'White peach & peony',
                'description' => 'White peach and lychee open into orange blossom and peony. Musk and oakmoss soften the fruit as it fades.',
                'notes' => ['White peach, mandarin, bergamot, lychee', 'Orange blossom, vetiver, mahonia, peony', 'Ambroxan, musk, vanilla, oakmoss'],
                'source' => 'https://us.afnan.com/products/mystique-bouquet',
            ],
            [
                'slug' => 'nebras', 'house' => 'Lattafa', 'name' => 'Nebras', 'family' => 'sweet', 'tone' => PackshotTone::PETAL,
                'size' => '100 mL', 'price' => 5900,
                'caption' => 'Red berries & cacao',
                'description' => 'Red berries brighten a creamy vanilla and cacao heart. Tonka and sugar keep it warm through the finish.',
                'notes' => ['Red berries, mandarin', 'Vanilla, cacao, rose', 'Tonka bean, amber, musk, sugar'],
                'source' => 'https://lattafa.com/product/nebras/',
            ],
            [
                'slug' => 'fursan', 'house' => 'Lattafa', 'name' => 'Qaed Al Fursan', 'family' => 'fresh', 'tone' => PackshotTone::HONEY,
                'size' => '90 mL', 'price' => 3900,
                'caption' => 'Pineapple & cedarwood',
                'description' => 'Juicy pineapple leads, with fir balsam through the middle and cedar and oud underneath.',
                'notes' => ['Pineapple, saffron', 'Jasmine, fir balsam', 'Oud, cedarwood, amber'],
                'source' => 'https://lattafa.com/product/qaed-al-fursan/',
            ],
            [
                'slug' => 'carbon', 'house' => 'Afnan', 'name' => 'Rare Carbon', 'family' => 'dark', 'tone' => PackshotTone::SMOKE,
                'size' => '100 mL', 'price' => 5700,
                'caption' => 'Leather & violet leaf',
                'description' => 'Leather and violet leaf set the tone. Cedar and oud deepen the heart, and vetiver gives the finish a dry edge.',
                'notes' => ['Leather, violet leaf, cinnamon bark, nutmeg', 'Rose, violet, cedarwood, oud', 'Vetiver, sandalwood, amber'],
                'source' => 'https://india.afnan.com/products/rare-carbon',
            ],
            [
                'slug' => 'glory', 'house' => 'Lattafa', 'name' => 'Oud for Glory', 'family' => 'dark', 'tone' => PackshotTone::SMOKE,
                'size' => '100 mL', 'price' => 4800,
                'caption' => 'Saffron & oud',
                'description' => 'Saffron and nutmeg open into a woody oud heart. Patchouli and musk carry the dry-down.',
                'notes' => ['Saffron, nutmeg, lavender', 'Oud, patchouli', 'Oud, patchouli, musk'],
                'source' => 'https://lattafa.com/product/badee-al-oud-oud-for-glory/',
            ],
        ];
    }
}
