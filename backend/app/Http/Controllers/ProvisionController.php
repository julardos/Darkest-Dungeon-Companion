<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProvisionController extends Controller
{
    public function calculate(Request $request): JsonResponse
    {
        $location = $request->query('location', 'ruins');
        $length   = $request->query('length', 'medium');

        if (!in_array($location, ['ruins','warrens','weald','cove','courtyard'])) $location = 'ruins';
        if (!in_array($length,   ['short','medium','long']))                       $length   = 'medium';

        $matrix = $this->matrix();
        $meta   = $this->meta();

        return response()->json([
            'location'   => $location,
            'length'     => $length,
            'provisions' => $matrix[$location][$length],
            'note'       => $meta[$location]['note'],
            'tips'       => $meta[$location]['tips'],
        ]);
    }

    // ── Provision matrix ─────────────────────────────────────────────────────
    // Quantities based on community consensus (Steam guides, Reddit, wiki).
    // General baseline: Short = 8 food/8 torch, Medium = 16/12, Long = 24/16.
    // Hunger fires ~2-3×/short, ~4-5×/medium, ~6-8×/long.

    private function matrix(): array
    {
        return [

            // ── RUINS ───────────────────────────────────────────────────────
            // Undead cannot be Blighted — 0 antivenom.
            // Holy Water is the most valuable curio item here.
            // Many rubble obstacles → shovels matter.
            // Locked Sarcophagi and Display Cabinets reward Skeleton Keys.

            'ruins' => [
                'short'  => $this->pack(8,  8,  2, 2, 0, 1, 2, 2,
                    ['holy_water'=>'Cleanses most Ruins curios safely', 'skeleton_key'=>'Opens Locked Sarcophagi and Cabinets']),
                'medium' => $this->pack(16, 12, 3, 2, 0, 1, 3, 3,
                    ['holy_water'=>'Essential for Altar of Light, Holy Fountain, Confession Booth', 'skeleton_key'=>'Two or three keys pay for themselves in loot']),
                'long'   => $this->pack(24, 16, 4, 3, 0, 2, 4, 3,
                    ['holy_water'=>'Bring the maximum — you will use every bottle', 'skeleton_key'=>'Keep three for locked curios']),
            ],

            // ── WARRENS ─────────────────────────────────────────────────────
            // Swine apply relentless Blight — Antivenom is the #1 priority.
            // Medicinal Herbs unlock huge curio rewards: Moonshine Barrel (+30% DMG!),
            //   Dinner Cart (food), Makeshift Dining Table.
            // Shovels are nearly useless — Warrens has few obstacles.
            // Holy Water useful for Pile of Bones — but NEVER on Occult Scrawlings.

            'warrens' => [
                'short'  => $this->pack(8,  8,  1, 2, 3, 2, 1, 1,
                    ['antivenom'=>'Swine Blight every encounter', 'medicinal_herbs'=>'Moonshine Barrel: +30% DMG buff']),
                'medium' => $this->pack(16, 12, 1, 3, 5, 3, 1, 2,
                    ['antivenom'=>'Top priority — run out and the run suffers badly', 'medicinal_herbs'=>'Dinner Cart + Moonshine Barrel = food and DMG buff']),
                'long'   => $this->pack(24, 16, 2, 4, 7, 4, 2, 2,
                    ['antivenom'=>'Bring the maximum — Blight is relentless on long runs', 'medicinal_herbs'=>'More curios = more herb uses on long expeditions']),
            ],

            // ── WEALD ────────────────────────────────────────────────────────
            // Most obstacles of any dungeon — Shovels are critical.
            // Human Brigands + Spiderweb traps apply heavy Bleed.
            // Troubling Effigy gives free positive quirk with Holy Water.
            // Beast Carcass replenishes food with Medicinal Herbs.
            // Both Blight (fungi, traps) and Bleed (brigands) — bring both cures.

            'weald' => [
                'short'  => $this->pack(8,  8,  3, 3, 2, 2, 1, 1,
                    ['shovel'=>'Weald has more obstacles than any other dungeon', 'bandage'=>'Brigands bleed on every attack']),
                'medium' => $this->pack(16, 12, 4, 4, 3, 2, 1, 2,
                    ['shovel'=>'Bring four minimum — you will use them all', 'bandage'=>'Bleed and Blight both common here']),
                'long'   => $this->pack(24, 16, 5, 5, 4, 3, 2, 2,
                    ['shovel'=>'Five shovels for long — the Weald is packed with rubble', 'bandage'=>'Maximum bandages: Brigands, traps, and spiderwebs all bleed']),
            ],

            // ── COVE ─────────────────────────────────────────────────────────
            // Medicinal Herbs are the MOST important item here:
            //   Eerie Coral (removes negative quirk), Fish Carcass (gems/trinkets),
            //   Brackish Tide Pool (resistance buff), Eerie Spiderweb.
            // Both Blight (Pelagic fish-men, Squiffy Ghasts) AND
            //   Bleed (Drowned Crew pirates) — bring both cures.
            // Holy Water for Fish Idol = guaranteed +18% DMG party buff.
            // Bas-Relief: interact bare-handed (Shovel gives +100 party stress!).

            'cove' => [
                'short'  => $this->pack(8,  8,  1, 2, 3, 3, 1, 1,
                    ['medicinal_herbs'=>'Eerie Coral removes quirks, Fish Carcass gives gems', 'antivenom'=>'Pelagic fish-men blight constantly']),
                'medium' => $this->pack(16, 12, 2, 3, 4, 4, 2, 2,
                    ['medicinal_herbs'=>'Most important curio item in the Cove — never skip herb curios', 'antivenom'=>'Fish-men and eldritch enemies blight every fight']),
                'long'   => $this->pack(24, 16, 2, 4, 5, 5, 2, 2,
                    ['medicinal_herbs'=>'Bring the maximum — four or five herb curios per long run', 'antivenom'=>'Blight is the dominant threat on long Cove runs']),
            ],

            // ── COURTYARD ────────────────────────────────────────────────────
            // Vampire enemies apply Bleed on EVERY hit — Bandages are life-saving.
            // Holy Water significantly weakens Bloodsuckers and Chevaliers.
            // The Blood mechanic: heroes with Crimson Curse need The Blood in camp.
            // Shovels useful for Bloodflowers and Disturbing Diversion curios.
            // Food: bring extra — longer sessions mean more hunger checks and camping.

            'courtyard' => [
                'short'  => $this->pack(12, 12, 2, 4, 2, 2, 3, 1,
                    ['bandage'=>'Every vampire attack applies Bleed', 'holy_water'=>'Weakens Bloodsuckers and Chevaliers significantly']),
                'medium' => $this->pack(16, 12, 2, 6, 2, 3, 4, 2,
                    ['bandage'=>'Maximum bandages — Bleed will kill heroes faster than raw damage', 'holy_water'=>'Absolutely essential against Crimson Court enemies']),
                'long'   => $this->pack(24, 16, 3, 8, 3, 4, 5, 2,
                    ['bandage'=>'Bring eight — one per encounter on a long Courtyard run', 'holy_water'=>'Bring five — every fight benefits from weakened vampires']),
            ],
        ];
    }

    // ── Pack helper ──────────────────────────────────────────────────────────

    private function pack(
        int $food, int $torch, int $shovel, int $bandage,
        int $antivenom, int $herbs, int $holyWater, int $key,
        array $highlights = []
    ): array {
        return [
            ['id'=>'food',            'name'=>'Food',            'icon'=>'🍖', 'quantity'=>$food,       'highlight'=>isset($highlights['food']),            'note'=>$highlights['food']            ?? null],
            ['id'=>'torch',           'name'=>'Torch',           'icon'=>'🔦', 'quantity'=>$torch,      'highlight'=>isset($highlights['torch']),           'note'=>$highlights['torch']           ?? null],
            ['id'=>'shovel',          'name'=>'Shovel',          'icon'=>'⛏️',  'quantity'=>$shovel,     'highlight'=>isset($highlights['shovel']),          'note'=>$highlights['shovel']          ?? null],
            ['id'=>'bandage',         'name'=>'Bandage',         'icon'=>'🩹', 'quantity'=>$bandage,    'highlight'=>isset($highlights['bandage']),         'note'=>$highlights['bandage']         ?? null],
            ['id'=>'antivenom',       'name'=>'Antivenom',       'icon'=>'⚗️',  'quantity'=>$antivenom,  'highlight'=>isset($highlights['antivenom']),       'note'=>$highlights['antivenom']       ?? null],
            ['id'=>'medicinal_herbs', 'name'=>'Medicinal Herbs', 'icon'=>'🌿', 'quantity'=>$herbs,      'highlight'=>isset($highlights['medicinal_herbs']), 'note'=>$highlights['medicinal_herbs'] ?? null],
            ['id'=>'holy_water',      'name'=>'Holy Water',      'icon'=>'✝️',  'quantity'=>$holyWater,  'highlight'=>isset($highlights['holy_water']),      'note'=>$highlights['holy_water']      ?? null],
            ['id'=>'skeleton_key',    'name'=>'Skeleton Key',    'icon'=>'🗝️',  'quantity'=>$key,        'highlight'=>isset($highlights['skeleton_key']),    'note'=>$highlights['skeleton_key']    ?? null],
        ];
    }

    // ── Per-location metadata (overview + community tips) ────────────────────

    private function meta(): array
    {
        return [
            'ruins' => [
                'note' => 'Undead dominate — immune to Blight, so skip Antivenom entirely. Holy Water is your most valuable item for curio interactions and stress.',
                'tips' => [
                    '✝ Holy Water is used on nearly every curio here: Altar of Light gives +30% DMG, Holy Fountain heals the whole party, Confession Booth relieves stress.',
                    '🗝 Skeleton Keys pay for themselves — Locked Sarcophagi and Display Cabinets give gold, gems and heirlooms.',
                    '⛏ Rubble obstacles are very common in the Ruins — bring at least 2–3 Shovels.',
                    '⚠ Suit of Armor is always safe to interact with bare-handed — guaranteed Protection buff or a positive quirk, no item needed.',
                    '⚠ Stress from Bone Courtiers and Gargoyles is the real danger — Crusader and Vestal excel here for holy damage and healing.',
                ],
            ],
            'warrens' => [
                'note' => 'Swine apply relentless Blight every fight. Antivenom is the top priority. Medicinal Herbs unlock massive curio rewards. Skip Shovels — the Warrens has almost no obstacles.',
                'tips' => [
                    '⚗ Antivenom first — Swine Wretch, Swine Skiver, and Spitting Swine all Blight multiple targets each round.',
                    '🌿 Moonshine Barrel with Medicinal Herbs gives your entire party +30% DMG — one of the best buffs in the game. Never skip it.',
                    '⚠ NEVER use Holy Water on Occult Scrawlings — it inflicts a −20 DODGE debuff on a hero instead of helping.',
                    '⚠ Bone Altar is always 100% safe bare-handed: +15% DMG, +10 ACC, +5% CRIT, and cures a status effect. Never skip it.',
                    '⚠ Sacrificial Stone can deal +50 Stress in one hit — skip it if your party is already stressed or near Affliction.',
                ],
            ],
            'weald' => [
                'note' => 'The Weald has more obstacles than any other dungeon — Shovels are mandatory. Human Brigands apply constant Bleed; fungal traps apply Blight. Bring cures for both.',
                'tips' => [
                    '⛏ Pack 3–5 Shovels — fallen trees, barricades, and rubble choke every corridor. Running out means losing time and tiles.',
                    '🩹 Brigand Fusiliers and Cutthroats bleed on almost every attack. Bandages should be your second-highest priority after Shovels.',
                    '🌿 Beast Carcass with Medicinal Herbs gives free food — great for topping up supplies mid-run.',
                    '✝ Troubling Effigy with Holy Water gives a guaranteed positive quirk — save one bottle if you see this curio.',
                    '⚠ Shallow Grave bare-handed gives Blight + Disease simultaneously. Always use a Shovel on it.',
                ],
            ],
            'cove' => [
                'note' => 'Medicinal Herbs are the single most valuable item in the Cove — they unlock quirk removal, gem loot, and resistance buffs from multiple curios. Bring both Antivenom (Pelagic fish-men) and Bandages (Drowned pirates).',
                'tips' => [
                    '🌿 Herbs are used on: Eerie Coral (removes a negative quirk), Fish Carcass (gems/trinkets), Brackish Tide Pool (resistance buff), Beast Carcass. Bring 3–5.',
                    '✝ Fish Idol with Holy Water gives +18% DMG to the whole party — one of the strongest dungeon buffs. Always save a bottle for it.',
                    '⚗ Both Pelagic fish-men AND Squiffy Ghasts apply Blight — Antivenom is essential, not optional.',
                    '🩹 Drowned Crew pirates apply Bleed every round — Bandages needed alongside Antivenom.',
                    '⚠ NEVER use a Shovel on Bas-Relief — it destroys the sculpture and inflicts +100 stress on your entire party. Interact bare-handed for a 67% chance of a positive quirk.',
                    '⚠ Ship\'s Figurehead is always safe bare-handed — stress relief or a DMG/SPD buff every time. Never skip it.',
                ],
            ],
            'courtyard' => [
                'note' => 'Crimson Court DLC. Every vampire enemy applies Bleed on every hit — Bandages are life-saving. Holy Water significantly weakens Bloodsuckers and Chevaliers. Bring Shovels for the unique Courtyard curios.',
                'tips' => [
                    '🩹 Bandages are your top priority — stack more than you think you need. One Bleed each round compounds fast on a 4-hero party.',
                    '✝ Holy Water works differently here: it weakens Crimson Court enemies rather than interacting with curios. Bring 3–5.',
                    '⚠ The Crimson Curse: infected heroes need The Blood at camp or they suffer severe debuffs. Manage this or it spirals.',
                    '⛏ Shovels are used on Bloodflowers (heirloom loot) and Disturbing Diversion — bring 2–3.',
                    '⚠ Damned Fountain is punishing bare-handed: 60% Bleed, 20% Crimson Curse. Use Holy Water for safe stress relief.',
                    '🌿 Forgotten Delicacies with Medicinal Herbs neutralises the food safely — Crimson Curse risk otherwise.',
                ],
            ],
        ];
    }
}
