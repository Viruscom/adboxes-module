<?php

namespace Modules\AdBoxes\Models;

use Illuminate\Database\Eloquent\Model;

class AdBoxTranslation extends Model
{
    protected $table    = "ad_box_translation";
    protected $fillable = ['locale', 'ad_box_id', 'title', 'type', 'short_description', 'url', 'external_url', 'visible'];

    public function adbox()
    {
        return $this->belongsTo(AdBox::class, 'ad_box_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public static function getLanguageArray($language, $request)
    {
        $data = [
            'locale' => $language->code,
            'title'       => $request['title_' . $language->code]
        ];

        if ($request->has('type_' . $language->code)) {
            $data['type'] = $request['type_' . $language->code];
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
}
