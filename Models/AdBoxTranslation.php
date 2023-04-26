<?php

namespace Modules\AdBoxes\Models;

use Illuminate\Database\Eloquent\Model;

class AdBoxTranslation extends Model
{
    protected $table    = "ad_box_translation";
    protected $fillable = ['locale', 'ad_box_id', 'title', 'label', 'short_description', 'url', 'external_url', 'visible'];
    public static function getLanguageArray($language, $request)
    {
        $data = [
            'locale' => $language->code,
            'title'  => $request['title_' . $language->code]
        ];

        if ($request->has('label_' . $language->code)) {
            $data['label'] = $request['label_' . $language->code];
        }

        if ($request->has('short_description_' . $language->code)) {
            $data['short_description'] = $request['short_description_' . $language->code];
        }

        if ($request->has('short_description2_' . $language->code)) {
            $data['short_description2'] = $request['short_description2_' . $language->code];
        }

        if ($request->has('short_description3_' . $language->code)) {
            $data['short_description3'] = $request['short_description3_' . $language->code];
        }

        if ($request->has('url_' . $language->code)) {
            $data['url'] = $request['url_' . $language->code];
        }

        $data['external_url'] = false;
        if ($request->has('external_url_' . $language->code)) {
            $data['external_url'] = filter_var($request['external_url_' . $language->code], FILTER_VALIDATE_BOOLEAN);
        }

        $data['visible'] = false;
        if ($request->has('visible_' . $language->code)) {
            $data['visible'] = filter_var($request['visible_' . $language->code], FILTER_VALIDATE_BOOLEAN);
        }

        return $data;
    }
    public function adbox()
    {
        return $this->belongsTo(AdBox::class, 'ad_box_id');
    }
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
