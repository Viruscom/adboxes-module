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
            if (is_null(Cache::get('adBoxesAdminAll'))) {
                AdBox::cacheUpdate();
            }
            if (is_null(Cache::get('adBoxButtonsAll'))) {
                AdBoxButton::cacheUpdate();
            }
            $adBoxesAdminAll = Cache::get('adBoxesAdminAll');
            $languages       = LanguageHelper::getActiveLanguages();
            $adBoxButtons    = Cache::get('adBoxButtonsAll');

            return view('adboxes::admin.index', compact('adBoxButtons', 'adBoxesAdminAll', 'languages'));
        }

        public function store(AdBoxStoreRequest $request): RedirectResponse
        {
            $data      = AdBox::getRequestData($request);
            $languages = LanguageHelper::getActiveLanguages();

            if ($request->has('position') && !is_null($request->position)) {
                AdBox::adjustPositionsOnCreate($data['type'], $data['position']);
            }

            foreach ($languages as $language) {
                $data[$language->code] = AdBoxTranslation::getLanguageArray($language, $request);
            }

            $adBox = AdBox::create($data);
            if ($request->has('image')) {
                $adBox->saveFile($request->image);
            }

            AdBox::cacheUpdate();

            if ($request->has('submitaddnew')) {
                return redirect()->back()->with('success-message', 'adboxes::admin.adboxes_actions.successful_create');
            }

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_create');
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

            if ($data['position'] != $currentPosition) {
                $adBox->adjustPositions($currentPosition, $data['position']);
            }

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

        public function delete($id): RedirectResponse
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);

            if ($adBox->existsFile($adBox->filename)) {
                $adBox->deleteFile($adBox->filename);
            }

            $adBoxesToUpdate = AdBox::where('type', $adBox->type)->where('position', '>', $adBox->position)->get();
            $adBox->delete();
            foreach ($adBoxesToUpdate as $adBoxToUpdate) {
                $adBoxToUpdate->update(['position' => $adBoxToUpdate->position - 1]);
            }

            AdBox::cacheUpdate();

            return redirect()->back()->with('success-message', 'adboxes::admin.adboxes_actions.successful_delete');
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

            $nextAdbox = AdBox::where('type', $adBox->type)->where('position', $adBox->position + 1)->first();
            if (!is_null($nextAdbox)) {
                $nextAdbox->update(['position' => $nextAdbox->position - 1]);
                $adBox->update(['position' => $adBox->position + 1]);
            }

            return redirect()->route('admin.ad-boxes.index')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
        }

        public function positionUp($id): RedirectResponse
        {
            $adBox = AdBox::find($id);
            MainHelper::goBackIfNull($adBox);

            $prevAdbox = AdBox::where('type', $adBox->type)->where('position', $adBox->position - 1)->first();
            if (!is_null($prevAdbox)) {
                $prevAdbox->update(['position' => $prevAdbox->position + 1]);
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

            $currentPosition = $adBox->position;
            if ($position != $currentPosition) {
                $adBox->adjustPositions($currentPosition, $position);
                $adBox->update(['position' => $position]);
            }

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
            $lastAdBox      = AdBox::where('type', AdBox::$WAITING_ACTION)->max('position');
            $neededPosition = is_null($lastAdBox) ? 1 : $lastAdBox + 1;
            $adBox->update(['type' => 0, 'position' => $neededPosition]);

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
    }
