<?php

namespace Modules\AdBoxes\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class AdBoxButton extends Model
{
    protected $table    = "ad_box_button_translation";
    protected $fillable = ['locale', 'ad_box_type', 'title', 'url', 'external_url', 'visible'];

    public static function getTranslation($adBoxType, $languageCode)
    {
        return self::where('locale', $languageCode)->where('ad_box_type', $adBoxType)->where('visible', true)->first();
    }

    public static function getCreateData($language, $request): array
    {
        $data['ad_box_type'] = $request->ad_box_type;

        return self::getData($language, $request, $data);
    }
    private static function getData($language, $request, $data): array
    {
        $data['locale'] = $language->code;
        $data['title']  = $request['title_' . $language->code];

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

    /**
     * @throws Exception
     */
    public static function cacheUpdate(): void
    {
        cache()->forget('adBoxButtonsAll');
        cache()->forget('adBoxButtonsFrontAll');

        cache()->rememberForever('adBoxButtonsAll', function () {
            return AdBoxButton::where('locale', config('default.app.admin_language.code'))->get();
        });

        cache()->rememberForever('adBoxButtonsFrontAll', function () {
            return self::where('visible', true)->get();
        });
    }
    public function getUpdateData($language, $request): array
    {
        return self::getData($language, $request, []);
    }

    public function getUrl($languageSlug)
    {
        if ($this->external_url) {
            return $this->url;
        }

        return url($languageSlug . '/' . $this->url);
    }
}
