<?php

    namespace Modules\AdBoxes\Http\Controllers;

    use App\Actions\CommonControllerAction;
    use App\Helpers\AdminHelper;
    use App\Helpers\LanguageHelper;
    use App\Helpers\MainHelper;
    use Exception;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\Cache;
    use Modules\Adboxes\Actions\AdBoxAction;
    use Modules\AdBoxes\Http\Requests\AdBoxStoreRequest;
    use Modules\AdBoxes\Http\Requests\AdBoxUpdateRequest;
    use Modules\AdBoxes\Models\AdBox;
    use Modules\AdBoxes\Models\AdBoxButton;
    use Modules\AdBoxes\Models\AdBoxTranslation;

    class AdBoxesController extends Controller
    {
        /**
         * @throws Exception
         */
        public function index()
        {
            $adBoxesAdminAll = Cache::rememberForever('adBoxesAdminAll', fn() => AdBox::cacheUpdate());
            $adBoxButtons    = Cache::rememberForever('adBoxButtonsAll', fn() => AdBoxButton::cacheUpdate());
            $languages       = LanguageHelper::getActiveLanguages();

            return view('adboxes::admin.index', compact('adBoxButtons', 'adBoxesAdminAll', 'languages'));
        }

        /**
         * @throws Exception
         */
        public function store(AdBoxStoreRequest $request): RedirectResponse
        {
            $data      = AdBox::prepareDataForStorage($request);
            $languages = LanguageHelper::getActiveLanguages();

            foreach ($languages as $language) {
                $data[$language->code] = AdBoxTranslation::getLanguageArray($language, $request);
            }

            $adBox = AdBox::create($data);
            if ($request->has('image')) {
                $adBox->saveFile($request->image);
            }

            AdBox::cacheUpdate();

            return $this->redirectAfterSave($request, 'adboxes::admin.adboxes_actions.successful_create');
        }

        public function create()
        {
            $data = [
                'adBoxesAdminAll' => Cache::get('adBoxesAdminAll'),
                'languages'       => LanguageHelper::getActiveLanguages(),
                'fileRules'       => AdBox::getFileRulesForView()
            ];

            $data = AdminHelper::getInternalLinksUrls($data);

            return view('adboxes::admin.create', $data);
        }

        private function redirectAfterSave($request, $message)
        {
            if ($request->has('submitaddnew')) {
                return redirect()->back()->with('success-message', $message);
            }

            return redirect()->route('admin.ad-boxes.index')->with('success-message', $message);
        }

        public function edit($id)
        {
            $data          = [];
            $data['adBox'] = AdBox::find($id);
            if (is_null($data['adBox'])) {
                return redirect()->back()->withInput()->withErrors(['adboxes::admin.adboxes_actions.record_not_found']);
            }

            $data['languages']       = LanguageHelper::getActiveLanguages();
            $data['adBoxesAdminAll'] = Cache::get('adBoxesAdminAll');
            $data['fileRules']       = AdBox::getFileRulesForView();

            $data = AdminHelper::getInternalLinksUrls($data);

            return view('adboxes::admin.edit', $data);
        }

        public function imgDeleteOneDimension($id)
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);

            if (file_exists($adBox->imagePath())) {
                unlink($adBox->imagePath());
                $adBox->update(['filename' => '']);

                return redirect()->back()->with('success-message', 'administration_messages.successful_delete_image');
            }

            return redirect()->back()->withErrors(['administration_messages.image_not_found']);
        }

        public function update($id, AdBoxUpdateRequest $request): RedirectResponse
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);
            $currentPosition = $adBox->position;

            $data      = AdBox::prepareDataForUpdate($request, $adBox);
            $languages = LanguageHelper::getActiveLanguages();

            foreach ($languages as $language) {
                $data[$language->code] = AdBoxTranslation::getLanguageArray($language, $request);
            }
            $adBox->updatePositions($currentPosition, $data['position']);
            $adBox->update($data);

            if ($request->has('image')) {
                $request->validate(['image' => AdBox::getFileRule($adBox->type)], [AdBox::getFileRuleMessage($adBox->type)]);
                $adBox->saveFile($request->image);
            }

            AdBox::cacheUpdate();

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
        }

        public function deleteMultiple(Request $request, CommonControllerAction $action): RedirectResponse
        {
            if (!is_null($request->ids[0])) {
                $action->deleteMultiple($request, AdBox::class);

                return redirect()->back()->with('success-message', 'admin.common.successful_delete');
            }

            return redirect()->back()->withErrors(['admin.common.no_checked_checkboxes']);
        }

        public function delete($id, AdBoxAction $action): RedirectResponse
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);

            $action->deleteImage($adBox);
            $adBoxesToUpdate = AdBox::whereType($adBox->type)->wherePositionAbove($adBox->position)->get();
            $adBox->delete();
            $action->decrementPosition($adBoxesToUpdate);

            AdBox::cacheUpdate();

            return redirect()->back()->with('success-message', 'adboxes::admin.adboxes_actions.successful_delete');
        }

        public function deleteImage($id): RedirectResponse
        {
            $adBox = AdBox::find($id);
            if (is_null($adBox)) {
                return redirect()->back()->withInput()->withErrors(['adboxes::admin.adboxes_actions.record_not_found']);
            }

            if ($adBox->existsFile($adBox->filename)) {
                $adBox->deleteFile($adBox->filename);
                $adBox->update(['filename' => null]);

                AdBox::cacheUpdate();

                return redirect()->back()->with('success-message', 'admin.common.successful_delete');
            }

            return redirect()->back()->withErrors(['admin.image_not_found']);
        }

        public function activeMultiple($active, Request $request, CommonControllerAction $action): RedirectResponse
        {
            $action->activeMultiple(AdBox::class, $request, $active);

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
        }

        public function positionDown($id): RedirectResponse
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);

            $nextAdBox = AdBox::whereType($adBox->type)->wherePosition($adBox->position + 1)->first();
            if (!is_null($nextAdBox)) {
                $nextAdBox->update(['position' => $nextAdBox->position - 1]);
                $adBox->update(['position' => $adBox->position + 1]);
            }

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
        }

        public function positionUp($id): RedirectResponse
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);

            $prevAdBox = AdBox::whereType($adBox->type)->wherePosition($adBox->position - 1)->first();
            if (!is_null($prevAdBox)) {
                $prevAdBox->update(['position' => $prevAdBox->position + 1]);
                $adBox->update(['position' => $adBox->position - 1]);
            }

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
        }

        public function ajaxUpdatePositions($id, $position)
        {
            $adBox = AdBox::find($id);
            if (is_null($adBox)) {
                return 0;
            }

            $request['position'] = $position;
            $adBox->updatedPosition($request);

            return 1;
        }

        public function returnToWaiting($id): RedirectResponse
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);

            if ($adBox->existsFile($adBox->filename)) {
                $adBox->deleteFile($adBox->filename);
                $adBox->update(['filename' => '']);
            }
            $lastAdBox = AdBox::waitingAction()->get()->max('position');
            $adBox->update([
                               'type'     => 0,
                               'position' => is_null($lastAdBox) ? 1 : $lastAdBox + 1
                           ]);

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
        }

        public function editButton($adboxType)
        {
            $adBoxButton = AdBoxButton::where('ad_box_type', $adboxType)->first();
            if (is_null($adBoxButton)) {
                return redirect()->back()->withInput()->withErrors(['adboxes::admin.adboxes_actions.record_not_found_button']);
            }

            $data = [
                'adBoxButton' => $adBoxButton,
                'languages'   => LanguageHelper::getActiveLanguages(),
            ];
            $data = AdminHelper::getInternalLinksUrls($data);

            return view('adboxes::admin.buttons.edit', $data);
        }

        public function active($id, $active): RedirectResponse
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);

            $adBox->update(['active' => $active]);

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
        }

        public function updateButton($adboxType, Request $request): RedirectResponse
        {
            $adBoxButton = AdBoxButton::where('ad_box_type', $adboxType)->first();
            if (is_null($adBoxButton)) {
                return redirect()->back()->withInput()->withErrors(['adboxes::admin.adboxes_actions.record_not_found']);
            }

            $languages = LanguageHelper::getActiveLanguages();
            foreach ($languages as $language) {
                $adBoxTranslation = $adBoxButton->where('ad_box_type', $adBoxButton->ad_box_type)->where('locale', $language->code)->first();
                if (is_null($adBoxTranslation)) {
                    $request['ad_box_type'] = $adBoxButton->ad_box_type;
                    $adBoxButton->create(AdBoxButton::getCreateData($language, $request));
                } else {
                    $adBoxTranslation->update($adBoxTranslation->getUpdateData($language, $request));
                }
            }

            AdBoxButton::cacheUpdate();

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit_button');
        }
    }
