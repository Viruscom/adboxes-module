<?php

namespace Modules\AdBoxes\Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Modules\AdBoxes\Models\AdBox;
use Modules\AdBoxes\Models\AdBoxButton;

class AdboxButtonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $languages  = Language::all();
        $adBoxTypes = AdBox::getTypes();

        foreach ($languages as $language) {
            foreach ($adBoxTypes as $adBoxType) {
                AdBoxButton::create([
                                        'locale'       => $language->code,
                                        'ad_box_type'  => $adBoxType,
                                        'title'        => trans('adboxes::admin.button_after_ad_boxes') . ' ' . $adBoxType,
                                        'url'          => '/',
                                        'external_url' => 0,
                                        'visible'      => 1]
                );
            }
        }
    }
}
