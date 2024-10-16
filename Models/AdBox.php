<?php

    namespace Modules\AdBoxes\Models;

    use App\Helpers\AdminHelper;
    use App\Helpers\FileDimensionHelper;
    use App\Traits\CommonActions;
    use App\Traits\Scopes;
    use App\Traits\StorageActions;
    use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
    use Astrotomic\Translatable\Translatable;
    use Carbon\Carbon;
    use Exception;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    class AdBox extends Model implements TranslatableContract
    {
        use Translatable, StorageActions, Scopes;

        public const PREVIEW_PERMISSION          = "ad_boxes_preview";
        public const PREVIEW_AND_EDIT_PERMISSION = "ad_boxes_preview_and_edit";
        public const FILES_PATH                  = "ad_boxes";

        public const CURRENCY_DECIMALS            = 2;
        public const CURRENCY_SEPARATOR           = ',';
        public const CURRENCY_THOUSANDS_SEPARATOR = '';
        public static int    $WAITING_ACTION         = 0;
        public static int    $FIRST_TYPE             = 1;
        public static int    $SECOND_TYPE            = 2;
        public static int    $THIRD_TYPE             = 3;
        public static int    $FOURTH_TYPE            = 4;
        public static string $AD_BOX_1_SYSTEM_IMAGE  = 'adboxes_1_image.png';
        public static string $AD_BOX_2_SYSTEM_IMAGE  = 'adboxes_2_image.png';
        public static string $AD_BOX_3_SYSTEM_IMAGE  = 'adboxes_3_image.png';
        public static string $AD_BOX_4_SYSTEM_IMAGE  = 'adboxes_4_image.png';
        public static string $AD_BOX_1_MIMES         = "jpg,jpeg,png,gif";
        public static string $AD_BOX_2_MIMES         = "jpg,jpeg,png,gif";
        public static string $AD_BOX_3_MIMES         = "jpg,jpeg,png,gif";
        public static string $AD_BOX_4_MIMES         = "jpg,jpeg,png,gif";
        public static string $AD_BOX_1_RATIO         = "3/2";
        public static string $AD_BOX_2_RATIO         = "3/2";
        public static string $AD_BOX_3_RATIO         = "1/1";
        public static string $AD_BOX_4_RATIO         = "1/1";
        public static string $AD_BOX_1_MAX_FILE_SIZE = "3000";
        public static string $AD_BOX_2_MAX_FILE_SIZE = "3000";
        public static string $AD_BOX_3_MAX_FILE_SIZE = "3000";
        public static string $AD_BOX_4_MAX_FILE_SIZE = "3000";
        public               $translatedAttributes   = ['title', 'label', 'short_description', 'visible', 'url', 'external_url'];
        protected            $fillable               = ['type', 'page_id', 'product_id', 'active', 'position', 'created_by', 'updated_bg', 'filename', 'date', 'from_date', 'to_date', 'price', 'from_price', 'new_price', 'from_new_price', 'type_color_class'];

        public static function getTypes(): array
        {
            return [self::$FIRST_TYPE, self::$SECOND_TYPE, self::$THIRD_TYPE, self::$FOURTH_TYPE];
        }

        public static function getFileRule($AdBoxType): string
        {
            return FileDimensionHelper::getRules('AdBoxes', self::getFileDimensionKey($AdBoxType));
        }

        public static function getFileDimensionKey($AdBoxType): string
        {
            switch ($AdBoxType) {
                case 1:
                    return self::$FIRST_TYPE;
                case 2:
                    return self::$SECOND_TYPE;
                case 3:
                    return self::$THIRD_TYPE;
                case 4:
                    return self::$FOURTH_TYPE;
            }
        }

        public static function getFileRulesForView(): array
        {
            return [
                self::$FIRST_TYPE  => FileDimensionHelper::getUserInfoMessage('AdBoxes', self::$FIRST_TYPE),
                self::$SECOND_TYPE => FileDimensionHelper::getUserInfoMessage('AdBoxes', self::$SECOND_TYPE),
                self::$THIRD_TYPE  => FileDimensionHelper::getUserInfoMessage('AdBoxes', self::$THIRD_TYPE),
                self::$FOURTH_TYPE => FileDimensionHelper::getUserInfoMessage('AdBoxes', self::$FOURTH_TYPE),
            ];
        }

        public static function getFileRuleMessage($AdBoxType): string
        {
            return FileDimensionHelper::getUserInfoMessage('AdBoxes', self::getFileDimensionKey($AdBoxType));
        }

        protected static function boot(): void
        {
            parent::boot();

            static::created(static function (AdBox $adBox) {
                self::cacheUpdate();
                activity()->causedBy(\auth()->user())->log('[Рекламни карета] Добавено е ново каре "' . $adBox->title . '" от потребител:  ' . \auth()->user()->name);
            });

            static::updated(static function (AdBox $adBox) {
                activity()->causedBy(\auth()->user())->log('[Рекламни карета] Редактирано е каре "' . $adBox->title . '" от потребител:  ' . \auth()->user()->name);
                self::cacheUpdate();
            });

            static::deleted(static function (AdBox $adBox) {
                self::cacheUpdate();
                activity()->causedBy(\auth()->user())->log('[Рекламни карета] Изтрито е каре "' . $adBox->title . '" от потребител:  ' . \auth()->user()->name);
            });
        }

        /**
         * @throws Exception
         */
        public static function cacheUpdate(): void
        {
            cache()->forget('adBoxesAdminAll');
            cache()->forget('adBoxesFrontAll');
            cache()->rememberForever('adBoxesAdminAll', function () {
                $adBoxes                  = [];
                $adBoxes['waitingAction'] = AdBox::waitingAction()->orderByPosition('asc')->with('translations')->get();

                return self::setAdBoxesTypes($adBoxes);
            });

            cache()->rememberForever('adBoxesFrontAll', function () {
                $adBoxes                     = [];
                $adBoxes[self::$FIRST_TYPE]  = self::firstType()->active(true)->orderByPosition('asc')->with('translations')->get();
                $adBoxes[self::$SECOND_TYPE] = self::secondType()->active(true)->orderByPosition('asc')->with('translations')->get();
                $adBoxes[self::$THIRD_TYPE]  = self::thirdType()->active(true)->orderByPosition('asc')->with('translations')->get();
                $adBoxes[self::$FOURTH_TYPE] = self::fourthType()->active(true)->orderByPosition('asc')->with('translations')->get();

                return $adBoxes;
            });
        }

        private static function setAdBoxesTypes($array)
        {
            $array[self::$FIRST_TYPE]  = self::firstType()->orderByPosition('asc')->with('translations')->get();
            $array[self::$SECOND_TYPE] = self::secondType()->orderByPosition('asc')->with('translations')->get();
            $array[self::$THIRD_TYPE]  = self::thirdType()->orderByPosition('asc')->with('translations')->get();
            $array[self::$FOURTH_TYPE] = self::fourthType()->orderByPosition('asc')->with('translations')->get();

            return $array;
        }

        public function directoryPath()
        {
            return public_path(self::$IMAGES_PATH . '/' . $this->id);
        }

        public function setKeys($array): array
        {
            $array[1]['sys_image_name'] = trans('adboxes::admin.adboxes.index') . ': ' . trans('adboxes::admin.ad_boxes_type_1');
            $array[1]['sys_image']      = self::$AD_BOX_1_SYSTEM_IMAGE;
            $array[1]['sys_image_path'] = AdminHelper::getSystemImage(self::$AD_BOX_1_SYSTEM_IMAGE);
            $array[1]['ratio']          = self::$AD_BOX_1_RATIO;
            $array[1]['mimes']          = self::$AD_BOX_1_MIMES;
            $array[1]['max_file_size']  = self::$AD_BOX_1_MAX_FILE_SIZE;
            $array[1]['file_rules']     = 'mimes:' . self::$AD_BOX_1_MIMES . '|size:' . self::$AD_BOX_1_MAX_FILE_SIZE . '|dimensions:ratio=' . self::$AD_BOX_1_RATIO;

            $array[2]['sys_image_name'] = trans('adboxes::admin.adboxes.index') . ': ' . trans('adboxes::admin.ad_boxes_type_2');
            $array[2]['sys_image']      = self::$AD_BOX_2_SYSTEM_IMAGE;
            $array[2]['sys_image_path'] = AdminHelper::getSystemImage(self::$AD_BOX_2_SYSTEM_IMAGE);
            $array[2]['ratio']          = self::$AD_BOX_2_RATIO;
            $array[2]['mimes']          = self::$AD_BOX_2_MIMES;
            $array[2]['max_file_size']  = self::$AD_BOX_2_MAX_FILE_SIZE;
            $array[2]['file_rules']     = 'mimes:' . self::$AD_BOX_2_MIMES . '|size:' . self::$AD_BOX_2_MAX_FILE_SIZE . '|dimensions:ratio=' . self::$AD_BOX_2_RATIO;

            $array[3]['sys_image_name'] = trans('adboxes::admin.adboxes.index') . ': ' . trans('adboxes::admin.ad_boxes_type_3');
            $array[3]['sys_image']      = self::$AD_BOX_3_SYSTEM_IMAGE;
            $array[3]['sys_image_path'] = AdminHelper::getSystemImage(self::$AD_BOX_3_SYSTEM_IMAGE);
            $array[3]['ratio']          = self::$AD_BOX_3_RATIO;
            $array[3]['mimes']          = self::$AD_BOX_3_MIMES;
            $array[3]['max_file_size']  = self::$AD_BOX_3_MAX_FILE_SIZE;
            $array[3]['file_rules']     = 'mimes:' . self::$AD_BOX_3_MIMES . '|size:' . self::$AD_BOX_3_MAX_FILE_SIZE . '|dimensions:ratio=' . self::$AD_BOX_3_RATIO;

            $array[4]['sys_image_name'] = trans('adboxes::admin.adboxes.index') . ': ' . trans('adboxes::admin.ad_boxes_type_4');
            $array[4]['sys_image']      = self::$AD_BOX_4_SYSTEM_IMAGE;
            $array[4]['sys_image_path'] = AdminHelper::getSystemImage(self::$AD_BOX_4_SYSTEM_IMAGE);
            $array[4]['ratio']          = self::$AD_BOX_4_RATIO;
            $array[4]['mimes']          = self::$AD_BOX_4_MIMES;
            $array[4]['max_file_size']  = self::$AD_BOX_4_MAX_FILE_SIZE;
            $array[4]['file_rules']     = 'mimes:' . self::$AD_BOX_4_MIMES . '|size:' . self::$AD_BOX_4_MAX_FILE_SIZE . '|dimensions:ratio=' . self::$AD_BOX_4_RATIO;

            return $array;
        }

        public function getSystemImage(): string
        {
            return AdminHelper::getSystemImage(self::${'AD_BOX_' . $this->type . '_SYSTEM_IMAGE'});
        }

        public function getUrl($languageSlug)
        {
            if (!is_null($this->url)) {
                if ($this->external_url) {
                    return $this->url;
                }

                return url($languageSlug . '/' . $this->url);
            }

            return '';
        }

        public function updatedPosition($request, $adBox, $AdBoxType): int
        {
            if ($adBox->type == 0) {
                $lastPosition        = self::where('type', $AdBoxType)->max('position');
                $request['position'] = $lastPosition + 1;

                return $request['position'];
            }

            if (!$request->has('position') || is_null($request->position) || $request->position == $this->position) {
                return $this->position;
            }

            $adboxes = self::where('type', $AdBoxType)->orderBy('position', 'desc')->get();
            if (count($adboxes) == 1) {
                $request['position'] = 1;

                return $request['position'];
            }

            if ($request['position'] >= $this->position) {
                $adboxesToUpdate = self::where('type', $AdBoxType)->where('id', '<>', $this->id)->where('position', '>', $this->position)->where('position', '<=', $request['position'])->get();
                self::updateAdBoxPosition($adboxesToUpdate, false);

                return $request['position'];
            }

            $adboxesToUpdate = self::where('type', $AdBoxType)->where('id', '<>', $this->id)->where('position', '<', $this->position)->where('position', '>=', $request['position'])->get();
            self::updateAdBoxPosition($adboxesToUpdate, true);

            return $request['position'];
        }

        private static function updateAdBoxPosition($adboxes, $increment = true): void
        {
            foreach ($adboxes as $AdBoxUpdate) {
                $position = ($increment) ? $AdBoxUpdate->position + 1 : $AdBoxUpdate->position - 1;
                $AdBoxUpdate->update(['position' => $position]);
            }
        }

        public function getUpdateData($request)
        {
            $data                    = self::getRequestData($request);
            $data['creator_user_id'] = $this->creator_user_id;
            if ($request->has('type')) {
                $data['type'] = $request['type'];
            }

            return $data;
        }

        public static function getRequestData($request, $adBox = null): array
        {
            $data = [];

            if (is_null($adBox)) {
                // Creating a new adBox
                if ($request->has('position') && !is_null($request->position)) {
                    $data['position'] = $request->position;
                } else {
                    $data['position'] = self::getNextAvailablePosition($request->type);
                }
            } else {
                // Updating an existing adBox
                if ($adBox->type == self::$WAITING_ACTION) {
                    $data['position'] = self::getNextAvailablePosition($request->type);
                } else {
                    $data['position'] = $adBox->position;
                    
                    if ($request->has('position') && $request->position != $adBox->position) {
                        $data['position'] = $request->position;
                    }
                }
            }

            $data['creator_user_id'] = Auth::user()->id;
            $data['type']            = self::$WAITING_ACTION;

            if ($request->has('type')) {
                $data['type'] = $request['type'];
            }

            $data['active'] = false;
            if ($request->has('active')) {
                $data['active'] = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            }

            if ($request->has('type_color_class')) {
                $data['type_color_class'] = $request->type_color_class;
            }
            if ($request->has('filename')) {
                $data['filename'] = $request->filename;
            }

            $data['from_price'] = false;
            if ($request->has('from_price')) {
                $data['from_price'] = filter_var($request->from_price, FILTER_VALIDATE_BOOLEAN);
            }

            $data['price'] = 0;
            if ($request->has('price') && $request->price != '') {
                $data['price'] = $request->price;
            }

            $data['from_new_price'] = false;
            if ($request->has('from_new_price')) {
                $data['from_new_price'] = filter_var($request->from_new_price, FILTER_VALIDATE_BOOLEAN);
            }

            $data['new_price'] = 0;
            if ($request->has('new_price') && $request->new_price != '') {
                $data['new_price'] = $request->new_price;
            }

            $data['from_date'] = null;
            $data['to_date']   = null;
            if ($request->has('date') && $request->date != "") {
                $data['date'] = Carbon::parse($request->date)->format('Y-m-d');
            } else {
                if ($request->has('from_date') && $request->from_date != "") {
                    $data['from_date'] = Carbon::parse($request->from_date)->format('Y-m-d');
                }

                if ($request->has('to_date') && $request->to_date != "") {
                    $data['to_date'] = Carbon::parse($request->to_date)->format('Y-m-d');
                }

                $data['date'] = null;
            }

            if ($request->hasFile('image')) {
                $data['filename'] = pathinfo(CommonActions::getValidFilenameStatic($request->image->getClientOriginalName()), PATHINFO_FILENAME) . '.' . $request->image->getClientOriginalExtension();
            }

            return $data;
        }

        public static function getNextAvailablePosition($type): int
        {
            $maxPosition = self::where('type', $type)->max('position');

            return is_null($maxPosition) ? 1 : $maxPosition + 1;
        }

        public function adjustPositions($oldPosition, $newPosition)
        {
            if ($oldPosition == $newPosition) {
                return;
            }

            if ($newPosition > $oldPosition) {
                self::where('type', $this->type)
                    ->where('position', '>', $oldPosition)
                    ->where('position', '<=', $newPosition)
                    ->decrement('position');
            } else {
                self::where('type', $this->type)
                    ->where('position', '>=', $newPosition)
                    ->where('position', '<', $oldPosition)
                    ->increment('position');
            }
        }


        public static function adjustPositionsOnCreate($type, $position)
        {
            self::where('type', $type)
                ->where('position', '>=', $position)
                ->increment('position');
        }

        public static function prepareDataForUpdate($request, $adBox): array
        {
            $data = self::getRequestData($request, $adBox);

            // No need to adjust 'position' here

            return $data;
        }

        public function scopeWaitingAction($query)
        {
            return $query->where('type', self::$WAITING_ACTION);
        }

        public function scopeActive($query, $active)
        {
            return $query->where('active', $active);
        }

        public function scopeFirstType($query)
        {
            return $query->where('type', self::$FIRST_TYPE);
        }

        public function scopeSecondType($query)
        {
            return $query->where('type', self::$SECOND_TYPE);
        }

        public function scopeThirdType($query)
        {
            return $query->where('type', self::$THIRD_TYPE);
        }

        public function scopeFourthType($query)
        {
            return $query->where('type', self::$FOURTH_TYPE);
        }

        public function isWaitingAction(): bool
        {
            return $this->type === self::$WAITING_ACTION;
        }

        public function getFilepath($filename): string
        {
            return $this->getFilesPath() . $filename;
        }

        public function getFilesPath(): string
        {
            return self::FILES_PATH . '/' . $this->id . '/';
        }

        public function getAnnounce(): string
        {
            if (is_null($this->short_description)) {
                return '';
            }

            return Str::limit($this->short_description, 255, ' ...');
        }

        public function getFromDate($format): string
        {
            if (is_null($this->from_date)) {
                return '';
            }

            return Carbon::parse($this->from_date)->format($format);
        }

        public function getToDate($format): string
        {
            if (is_null($this->to_date)) {
                return '';
            }

            return Carbon::parse($this->to_date)->format($format);
        }

        public function getPrice(): string
        {
            if (is_null($this->price) || $this->price == '0.00' || $this->price == '0,00') {
                return '';
            }

            return number_format($this->price, self::CURRENCY_DECIMALS, self::CURRENCY_SEPARATOR, self::CURRENCY_THOUSANDS_SEPARATOR);
        }

        public function getNewPrice(): string
        {
            if (is_null($this->new_price) || $this->new_price == '0.00' || $this->new_price == '0,00') {
                return '';
            }

            return number_format($this->new_price, self::CURRENCY_DECIMALS, self::CURRENCY_SEPARATOR, self::CURRENCY_THOUSANDS_SEPARATOR);
        }

        public function getLabelColor()
        {
            return $this->type_color_class;
        }

        public static function generatePositionForWaitingAdBox($AdBoxType = null): int
        {
            if (is_null($AdBoxType)) {
                $AdBoxType = self::$WAITING_ACTION;
            }

            $adBoxes = self::whereType($AdBoxType)->orderByPosition('asc')->get();

            if ($adBoxes->isEmpty()) {
                return 1;
            }

            return $adBoxes->last()->position + 1;
        }
    }
