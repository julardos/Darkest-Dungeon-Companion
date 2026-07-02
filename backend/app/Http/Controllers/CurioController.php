<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CurioController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json($this->curios());
    }

    private function curios(): array
    {
        return [

            // ================================================================
            // ALL DUNGEONS — corridor curios found anywhere
            // ================================================================

            ['id'=>'crate','name'=>'Crate','locations'=>['ruins','warrens','weald','cove'],'tags'=>['all','loot'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'75% chance of heirloom loot. No item needed — always interact.'],
             'risk'=>['effects'=>['25% Nothing']]],

            ['id'=>'discarded_pack','name'=>'Discarded Pack','locations'=>['ruins','warrens','weald','cove'],'tags'=>['all','loot'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'60% loot + supplies, 20% dungeon scouting, 20% nothing. Always safe to open.'],
             'risk'=>['effects'=>['20% Nothing — no downside risk']]],

            ['id'=>'sack','name'=>'Sack','locations'=>['ruins','warrens','weald','cove'],'tags'=>['all','loot'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'75% gold loot. Always safe — no negative outcomes.'],
             'risk'=>['effects'=>['25% Nothing']]],

            ['id'=>'sconce','name'=>'Sconce','locations'=>['ruins','warrens','weald','cove'],'tags'=>['all','torch'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'Always recovers a Torch. Never skip this — free light.'],
             'risk'=>['effects'=>['No negative outcomes — always take it']]],

            ['id'=>'shamblers_altar','name'=>"Shambler's Altar",'locations'=>['ruins','warrens','weald','cove'],'tags'=>['all','danger','boss'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'Walk past — bare-hand interaction yields nothing. Only use a Torch if you intentionally want to fight the Shambler (secret boss, Champion-tier rewards).'],
             'risk'=>['effects'=>['Using a Torch summons the Shambler — a brutal secret boss with 3 spawning Tentacles. Only for prepared parties seeking his unique trinkets.']]],

            ['id'=>'stack_of_books','name'=>'Stack of Books','locations'=>['ruins','warrens','weald','cove'],'tags'=>['all','quirk','stress'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'22% positive quirk or journal page. No item improves the odds — just interact.'],
             'risk'=>['effects'=>['22% Stress +25 to a hero','17% Journal page (good)','22% Positive quirk (good)','11% Negative quirk','11% Torch loss','17% Nothing']]],

            ['id'=>'throbbing_cocoons_infestation','name'=>'Throbbing Cocoons (Infestation)','locations'=>['ruins','warrens','weald','cove'],'tags'=>['all','danger','spawn'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'Do NOT interact. These cocoons always spawn the Gatekeeper and minion wave when touched.'],
             'risk'=>['effects'=>['100% Spawns Gatekeeper boss + minions — unavoidable ambush if interacted with']]],

            // ================================================================
            // RUINS
            // ================================================================

            ['id'=>'alchemy_table','name'=>'Alchemy Table','locations'=>['ruins'],'tags'=>['ruins','loot','blight'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'Gold synthesis — guaranteed gold loot from the alchemical process.'],
             'risk'=>['effects'=>['50% Blight on a hero (acid splash)','25% Gold loot anyway','25% Nothing']]],

            ['id'=>'altar_of_light','name'=>'Altar of Light','locations'=>['ruins'],'tags'=>['ruins','buff'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'+30% DMG buff for the party. Very strong — always bring Holy Water here.'],
             'risk'=>['effects'=>['+20% DMG buff still possible (lesser blessing)','But no guarantee without Holy Water']]],

            ['id'=>'bookshelf','name'=>'Bookshelf','locations'=>['ruins'],'tags'=>['ruins','quirk','stress'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'20% scouting, 13% positive quirk, 20% journal page. No item improves this — just interact.'],
             'risk'=>['effects'=>['20% Stress +15 to hero','7% Negative quirk','20% Nothing']]],

            ['id'=>'confession_booth','name'=>'Confession Booth','locations'=>['ruins'],'tags'=>['ruins','stress','quirk'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'Guaranteed party stress heal — relieves the burden of sin.'],
             'risk'=>['effects'=>['50% Stress +15 to a hero','25% Gold treasure','25% Removes a negative quirk (lucky outcome)']]],

            ['id'=>'decorative_urn','name'=>'Decorative Urn','locations'=>['ruins'],'tags'=>['ruins','loot','blight','disease'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'Reveals valuables inside — guaranteed gem/gold loot.'],
             'risk'=>['effects'=>['44% Gem loot (lucky)','22% Blight on hero','11% Disease (Cough or random)','22% Nothing — WARNING: using Shovel gives Guilt quirk']]],

            ['id'=>'holy_fountain','name'=>'Holy Fountain','locations'=>['ruins'],'tags'=>['ruins','stress','heal','buff'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'Full party stress heal, HP restoration, and status cure. One of the best curio interactions in the Ruins.'],
             'risk'=>['effects'=>['50% Partial stress relief + gold loot','50% Gold only — no healing']]],

            ['id'=>'iron_maiden','name'=>'Iron Maiden','locations'=>['ruins'],'tags'=>['ruins','disease','loot'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'Safely extracts loot from inside — guaranteed gold or trinket reward.'],
             'risk'=>['effects'=>['40% Loot anyway (lucky)','20% Claustrophobia quirk on hero','13% Tetanus disease','7% Random disease','20% Nothing']]],

            ['id'=>'locked_display_cabinet','name'=>'Locked Display Cabinet','locations'=>['ruins'],'tags'=>['ruins','loot','bleed','blight'],
             'safe'=>['item'=>'skeleton_key','itemName'=>'Skeleton Key','effect'=>'Guaranteed gold and heirloom loot. Worth the key.'],
             'risk'=>['effects'=>['50% Bleed trap on hero','50% Blight trap on hero — bare-hand interaction is pure downside']]],

            ['id'=>'locked_sarcophagus','name'=>'Locked Sarcophagus','locations'=>['ruins'],'tags'=>['ruins','loot','bleed','blight'],
             'safe'=>['item'=>'skeleton_key','itemName'=>'Skeleton Key','effect'=>'Safe loot extraction — gold, gems, and Ruins heirlooms.'],
             'risk'=>['effects'=>['50% Bleed trap on a hero','50% Blight trap on a hero — never open bare-handed']]],

            ['id'=>'sarcophagus','name'=>'Sarcophagus','locations'=>['ruins'],'tags'=>['ruins','loot','quirk'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'60% chance of gold and heirloom loot. Worth interacting — majority outcome is positive.'],
             'risk'=>['effects'=>['20% Death-fear quirk on a hero','20% Nothing']]],

            ['id'=>'suit_of_armor','name'=>'Suit of Armor','locations'=>['ruins'],'tags'=>['ruins','buff','quirk'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'75% party Protection buff for the dungeon. Always interact — no negative outcomes possible.'],
             'risk'=>['effects'=>['12.5% Ruins Adventurer quirk (positive)','12.5% Ruins Tactician quirk (positive)','No negative outcomes']]],

            // ================================================================
            // WARRENS
            // ================================================================

            ['id'=>'dinner_cart','name'=>'Dinner Cart','locations'=>['warrens'],'tags'=>['warrens','food','blight','disease'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'Guaranteed food loot — cleanses the meat for safe consumption.'],
             'risk'=>['effects'=>['38% Food (lucky)','25% Blight on hero','13% Black Plague disease','25% Nothing']]],

            ['id'=>'moonshine_barrel','name'=>'Moonshine Barrel','locations'=>['warrens'],'tags'=>['warrens','buff','blight','food'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'+30% DMG buff for the party. Excellent value — rivals the Altar of Light.'],
             'risk'=>['effects'=>['33% Food/supply loot (lucky)','33% Blight on hero','11% Tippler quirk (negative)','22% Nothing']]],

            ['id'=>'occult_scrawlings','name'=>'Occult Scrawlings','locations'=>['warrens'],'tags'=>['warrens','quirk','stress'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'33% positive quirk. Interact bare-handed only — DO NOT use Holy Water (it causes a -20 DODGE debuff).'],
             'risk'=>['effects'=>['WARNING: Holy Water gives -20 DODGE debuff to hero!','17% Negative quirk','25% Stress +25 to a hero','25% Nothing']]],

            ['id'=>'pile_of_bones','name'=>'Pile of Bones','locations'=>['warrens'],'tags'=>['warrens','loot','disease','quirk'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'Purifies the remains — guaranteed gold and heirloom loot.'],
             'risk'=>['effects'=>['25% Loot (lucky)','25% Bloodthirsty quirk on hero','25% Random disease','25% Nothing']]],

            ['id'=>'pile_of_scrolls','name'=>'Pile of Scrolls','locations'=>['warrens'],'tags'=>['warrens','quirk','stress','map'],
             'safe'=>['item'=>'torch','itemName'=>'Torch','effect'=>'Burns away one negative quirk from a hero. Excellent use of a spare torch.'],
             'risk'=>['effects'=>['29% Dungeon scouting (lucky)','14% Stress +15','14% Journal page','10% Positive quirk','5% Negative quirk','29% Nothing']]],

            ['id'=>'rack_of_blades','name'=>'Rack of Blades','locations'=>['warrens'],'tags'=>['warrens','loot','bleed'],
             'safe'=>['item'=>'bandage','itemName'=>'Bandage','effect'=>'Protected hand search — guaranteed gold and gem loot without injury.'],
             'risk'=>['effects'=>['40% Gold/gem loot (lucky)','40% Bleed on hero','20% Nothing']]],

            ['id'=>'bone_altar','name'=>'Bone Altar','locations'=>['warrens'],'tags'=>['warrens','buff'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'Always: +15% DMG, +10 ACC, +5% CRT buff for party + cures a status effect. One of the best curios in the game — never skip it.'],
             'risk'=>['effects'=>['No negative outcomes — 100% party buff']]],

            ['id'=>'makeshift_dining_table','name'=>'Makeshift Dining Table','locations'=>['warrens'],'tags'=>['warrens','food','blight','disease'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'Purifies the meal — guaranteed food and supply loot.'],
             'risk'=>['effects'=>['25% Food/supply loot (lucky)','25% Blight on hero','25% Tapeworm disease','25% Nothing']]],

            ['id'=>'sacrificial_stone','name'=>'Sacrificial Stone','locations'=>['warrens'],'tags'=>['warrens','stress','quirk'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'25% removes a negative quirk, 25% grants a Warrens-themed quirk. Worth risking — no item helps.'],
             'risk'=>['effects'=>['50% Stress +50 to a hero (severe — can trigger an affliction)','25% Negative quirk removal (good)','25% Warrens Explorer / Scrounger quirk']]],

            // ================================================================
            // WEALD
            // ================================================================

            ['id'=>'beast_carcass','name'=>'Beast Carcass','locations'=>['weald'],'tags'=>['weald','food','disease'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'Safe butchering — guaranteed food loot, no disease risk.'],
             'risk'=>['effects'=>['43% Food (lucky)','19% Random disease','10% Rabies disease','14% Zoophobia quirk','14% Nothing']]],

            ['id'=>'eerie_spiderweb','name'=>'Eerie Spiderweb','locations'=>['weald'],'tags'=>['weald','loot','quirk'],
             'safe'=>['item'=>'bandage','itemName'=>'Bandage','effect'=>'Protected search through the web — guaranteed loot from the spider\'s cache.'],
             'risk'=>['effects'=>['40% Loot (lucky)','10% Slow Reflexes quirk','10% Slowdraw quirk','40% Nothing']]],

            ['id'=>'left_luggage','name'=>'Left Luggage','locations'=>['weald'],'tags'=>['weald','loot','blight'],
             'safe'=>['item'=>'skeleton_key','itemName'=>'Skeleton Key','effect'=>'Opens the hidden compartment — gold and heirloom loot guaranteed. Antivenom also works.'],
             'risk'=>['effects'=>['50% Multi-item loot (lucky)','50% Blight trap on hero — never open bare-handed']]],

            ['id'=>'mummified_remains','name'=>'Mummified Remains','locations'=>['weald'],'tags'=>['weald','loot','blight'],
             'safe'=>['item'=>'bandage','itemName'=>'Bandage','effect'=>'Safely searches the remains — gold or trinket loot with no blight exposure.'],
             'risk'=>['effects'=>['40% Gold/trinket loot (lucky)','40% Blight on hero','20% Nothing']]],

            ['id'=>'old_tree','name'=>'Old Tree','locations'=>['weald'],'tags'=>['weald','loot','blight'],
             'safe'=>['item'=>'antivenom','itemName'=>'Antivenom','effect'=>'Neutralises the sap — safe loot extraction from the hollow.'],
             'risk'=>['effects'=>['50% Loot (lucky)','25% Blight on hero (toxic sap)','25% Nothing']]],

            ['id'=>'shallow_grave','name'=>'Shallow Grave','locations'=>['weald'],'tags'=>['weald','loot','blight','disease'],
             'safe'=>['item'=>'shovel','itemName'=>'Shovel','effect'=>'Proper excavation — gold, gems, and Weald heirlooms. Always dig when you have a shovel.'],
             'risk'=>['effects'=>['50% Blight on hero (corpse gas)','50% Disease on hero — very punishing without a shovel']]],

            ['id'=>'troubling_effigy','name'=>'Troubling Effigy','locations'=>['weald'],'tags'=>['weald','quirk','bleed','blight'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'Cleanses the effigy — guaranteed positive quirk for one hero.'],
             'risk'=>['effects'=>['19% Positive quirk (lucky)','19% Negative quirk','19% Bleed on hero','9% Blight on hero','9% Stress +15','25% Nothing']]],

            ['id'=>'ancient_coffin','name'=>'Ancient Coffin','locations'=>['weald'],'tags'=>['weald','loot','quirk'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'50% gold and heirloom loot. Majority positive — interact freely, no item needed.'],
             'risk'=>['effects'=>['8% Weald Adventurer quirk (positive)','8% Weald Explorer quirk (positive)','33% Nothing']]],

            ['id'=>'pristine_fountain','name'=>'Pristine Fountain','locations'=>['weald'],'tags'=>['weald','stress'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'Full party stress relief. Holy Water significantly increases the amount healed.'],
             'risk'=>['effects'=>['Stress relief still happens bare-handed, but less effective']]],

            ['id'=>'travelers_tent','name'=>"Traveler's Tent",'locations'=>['weald'],'tags'=>['weald','loot','stress','map'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'38% gold/multi-loot, 38% dungeon scouting. No item needed — mostly positive outcomes.'],
             'risk'=>['effects'=>['13% Stress +25 to a hero','13% Nothing']]],

            // ================================================================
            // COVE
            // ================================================================

            ['id'=>'barnacle_crusted_chest','name'=>'Barnacle-Crusted Chest','locations'=>['cove'],'tags'=>['cove','loot','bleed'],
             'safe'=>['item'=>'shovel','itemName'=>'Shovel','effect'=>'Pries the barnacles open cleanly — gold, gems, and Cove heirlooms.'],
             'risk'=>['effects'=>['50% Loot (lucky)','25% Bleed on hero (barnacle cuts)','25% Nothing']]],

            ['id'=>'bas_relief','name'=>'Bas-Relief','locations'=>['cove'],'tags'=>['cove','quirk','disease'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'67% positive quirk, 22% negative quirk. Interact bare-handed — DO NOT use a Shovel (destroys it, +100 party stress).'],
             'risk'=>['effects'=>['WARNING: Using a Shovel destroys the relief and inflicts +100 stress on the entire party!','11% Random disease (minor risk from bare hand)']]],

            ['id'=>'brackish_tide_pool','name'=>'Brackish Tide Pool','locations'=>['cove'],'tags'=>['cove','buff','disease'],
             'safe'=>['item'=>'antivenom','itemName'=>'Antivenom','effect'=>'Party resistance buff + heal/stress relief. Antivenom neutralises the toxic brine.'],
             'risk'=>['effects'=>['75% Resistance buff still happens (lucky)','25% Random disease on hero']]],

            ['id'=>'eerie_coral','name'=>'Eerie Coral','locations'=>['cove'],'tags'=>['cove','quirk','stress'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'Removes one negative quirk from a hero — the herbs bind with the coral\'s properties.'],
             'risk'=>['effects'=>['50% Party stress relief (lucky)','25% Stress +25 on hero','25% Nothing']]],

            ['id'=>'fish_carcass','name'=>'Fish Carcass','locations'=>['cove'],'tags'=>['cove','loot','disease','blight'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'Safe gutting — gem or trinket loot guaranteed, no disease exposure.'],
             'risk'=>['effects'=>['17% Trinket or gem loot (lucky)','17% Red Plague disease','11% Blight on hero','6% Bleed on hero','50% Nothing — very likely nothing without herbs']]],

            ['id'=>'fish_idol','name'=>'Fish Idol','locations'=>['cove'],'tags'=>['cove','buff','debuff'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'+18% DMG buff for the party. Strong offensive boost — always use Holy Water on this.'],
             'risk'=>['effects'=>['50% Severe debuff: -25% DMG and -10 ACC on a hero','50% Debuff: -12 DODGE on a hero — always a punishment without holy water']]],

            ['id'=>'giant_oyster','name'=>'Giant Oyster','locations'=>['cove'],'tags'=>['cove','loot','buff','bleed'],
             'safe'=>['item'=>'shovel','itemName'=>'Shovel','effect'=>'Pries the shell safely — gold or trinket loot. Dog Treats also work for a +25 DODGE buff.'],
             'risk'=>['effects'=>['40% Gold or trinket loot (lucky)','40% Bleed on hero (shell clamp)','20% Nothing']]],

            ['id'=>'ships_figurehead','name'=>"Ship's Figurehead",'locations'=>['cove'],'tags'=>['cove','stress','buff'],
             'safe'=>['item'=>null,'itemName'=>null,'effect'=>'67% party stress relief, 33% DMG and SPD buff. Always positive — never skip this curio.'],
             'risk'=>['effects'=>['No negative outcomes — 100% beneficial interaction']]],

            // ================================================================
            // COURTYARD (Crimson Court DLC)
            // ================================================================

            ['id'=>'bloodflowers','name'=>'Bloodflowers','locations'=>['courtyard'],'tags'=>['courtyard','loot','disease','stress'],
             'safe'=>['item'=>'shovel','itemName'=>'Shovel','effect'=>'Digs up heirlooms and loot buried beneath — no blood exposure.'],
             'risk'=>['effects'=>['38% Stress +15 to a hero','23% Dungeon scouting','15% Loot + The Blood','5% Tetanus disease','3% Random disease','15% Nothing — WARNING: Holy Water causes Stress +15']]],

            ['id'=>'damned_fountain','name'=>'Damned Fountain','locations'=>['courtyard'],'tags'=>['courtyard','stress','bleed','curse'],
             'safe'=>['item'=>'holy_water','itemName'=>'Holy Water','effect'=>'Purifies the fountain — party stress relief.'],
             'risk'=>['effects'=>['60% Bleed on a hero','20% Crimson Curse on a hero','20% Loot + The Blood — WARNING: Torch causes Stress +5']]],

            ['id'=>'disturbing_diversion','name'=>'Disturbing Diversion','locations'=>['courtyard'],'tags'=>['courtyard','loot','quirk','stress'],
             'safe'=>['item'=>'shovel','itemName'=>'Shovel','effect'=>'Excavates the hidden compartment — heirloom and loot reward.'],
             'risk'=>['effects'=>['40% Stress +25 to a hero','15% Positive quirk','15% Negative quirk','10% Loot + The Blood','20% Nothing']]],

            ['id'=>'forgotten_delicacies','name'=>'Forgotten Delicacies','locations'=>['courtyard'],'tags'=>['courtyard','food','blight','disease','curse'],
             'safe'=>['item'=>'medicinal_herbs','itemName'=>'Medicinal Herbs','effect'=>'Purifies the food — safe loot and supply recovery.'],
             'risk'=>['effects'=>['20% Loot + The Blood','20% Stress Eater quirk','20% Blight on hero','13% Crimson Curse on hero','7% Random disease','20% Nothing']]],

            ['id'=>'hooded_shrew','name'=>'Hooded Shrew','locations'=>['courtyard'],'tags'=>['courtyard','loot','stress','disease'],
             'safe'=>['item'=>'the_blood','itemName'=>'The Blood','effect'=>'Appeases the shrew with blood — guaranteed trinket reward.'],
             'risk'=>['effects'=>['43% Stress +15 to a hero','29% Loot (lucky)','8% Random disease','21% Nothing']]],

            ['id'=>'pile_of_strange_bones','name'=>'Pile of Strange Bones','locations'=>['courtyard'],'tags'=>['courtyard','bleed','loot','curse'],
             'safe'=>['item'=>'bandage','itemName'=>'Bandage','effect'=>'Loot and The Blood safely — protected search through the remains.'],
             'risk'=>['effects'=>['50% Bleed on a hero','20% Loot + The Blood','10% Crimson Curse on hero','20% Nothing']]],

            ['id'=>'throbbing_cocoons_courtyard','name'=>'Throbbing Cocoons','locations'=>['courtyard'],'tags'=>['courtyard','spawn','stress'],
             'safe'=>['item'=>'torch','itemName'=>'Torch','effect'=>'Burns the cocoons — party stress relief (the larvae die before hatching).'],
             'risk'=>['effects'=>['75% Summons Courtyard enemies — do NOT interact bare-handed','12% Loot + The Blood','12% Nothing']]],

            ['id'=>'thronging_hive','name'=>'Thronging Hive','locations'=>['courtyard'],'tags'=>['courtyard','loot','bleed'],
             'safe'=>['item'=>'torch','itemName'=>'Torch','effect'=>'Burns the hive — guaranteed loot and The Blood (the fire scatters the contents).'],
             'risk'=>['effects'=>['75% Loot + The Blood (lucky — same result without torch but unreliable)','25% Bleed on a hero (bee stings)']]],

            ['id'=>'wine_crate','name'=>'Wine Crate','locations'=>['courtyard'],'tags'=>['courtyard','loot','stress','curse'],
             'safe'=>['item'=>'antivenom','itemName'=>'Antivenom','effect'=>'Neutralises the tainted wine — party stress relief.'],
             'risk'=>['effects'=>['42% Loot + The Blood (lucky)','17% Crimson Curse on hero','17% Tippler quirk','8% Dungeon scouting','8% Bleed on hero','8% Nothing — NOTE: Shovel also works to reveal extra treasure']]],

            ['id'=>'wizened_shrew','name'=>'Wizened Shrew','locations'=>['courtyard'],'tags'=>['courtyard','loot','disease'],
             'safe'=>['item'=>'the_blood','itemName'=>'The Blood','effect'=>'Appeases the elder shrew — guaranteed trinket reward.'],
             'risk'=>['effects'=>['33% Dungeon scouting','17% Random disease','33% Nothing (two possible "nothing" outcomes)']]],

        ];
    }
}
