<?php

namespace Modules\AdBoxes\Models;

use Illuminate\Database\Eloquent\Model;

class AdBoxButton extends Model
{
    protected $table    = "ad_box_button_translation";
    protected $fillable = ['language_id', 'adbox_type', 'title', 'url', 'external_url', 'visible'];

    public static function getTranslation($adboxType, $languageId)
    {
        return self::where('language_id', $languageId)->where('adbox_type', $adboxType)->where('visible', true)->first();
    }

    public static function getCreateData($language, $request): array
    {
        $data['adbox_type'] = $request->adbox_type;

        return self::getData($language, $request, $data);
    }
    private static function getData($language, $request, $data): array
    {
        $data['language_id'] = $language->id;
        $data['title']       = $request['title_' . $language->code];

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
    public function getUpdateData($language, $request): array
    {
        return self::getData($language, $request, []);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::created(static function () {
            self::cacheUpdate();
        });

        static::updated(static function () {
            self::cacheUpdate();
        });

        static::deleted(static function () {
            self::cacheUpdate();
        });
    }
    /**
     * @throws \Exception
     */
    public static function cacheUpdate(): void
    {
        cache()->forget('adBoxButtonsAll');
        cache()->remember('adBoxButtonsAll', config('default.app.cache.ttl_seconds'), function () {
            return AdBoxButton::where('locale', config('default.app.admin_language.code'))->get();
        });
    }
}
