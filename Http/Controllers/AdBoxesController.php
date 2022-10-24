<?php

namespace Modules\AdBoxes\Http\Controllers;

use App\Helpers\LanguageHelper;
use App\Helpers\MainHelper;
use App\Models\FileDimension;
use App\Models\Language;
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
use Nwidart\Modules\Facades\Module;

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

    public function create()
    {
        $data                    = [];
        $data['languages']       = LanguageHelper::getActiveLanguages();
        $data['adBoxesAdminAll'] = Cache::get('adBoxesAdminAll');
        $data['fileRules']       = AdBox::getFileRulesForView();

        //        if (Module::has('ShopProductBrand')) {
        //            $data['brands'] = ShopProductBrand::active(true)->with('translations')->orderBy('position')->get();
        //        }
        //
        //        if (Module::has('ShopProductBrand')) {
        //            $data['productCategories'] = ShopProductCategory::active(true)->with('translations')->with('products')->orderBy('position')->get();
        //        }

        return view('adboxes::admin.create', $data);
    }

    public function store(AdBoxStoreRequest $request): RedirectResponse
    {
        $data      = AdBox::getRequestData($request);
        $languages = LanguageHelper::getActiveLanguages();

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

        return redirect()->route('ad-boxes')->with('success-message', 'adboxes::admin.adboxes_actions.successful_create');
    }

    public function edit($id)
    {
        $data          = [];
        $data['adBox'] = AdBox::find($id);
        if (is_null($data['adBox'])) {
            return redirect()->back()->withInput()->withErrors(['adboxes::admin.adboxes_actions.record_not_found']);
        }

        $data['languages'] = LanguageHelper::getActiveLanguages();
        $data['adBoxesAdminAll']   = Cache::get('adBoxesAdminAll');
        $data['fileRules']       = AdBox::getFileRulesForView();

        //
        //        $navigations       = Navigation::active(true)->with('translations')->with('content_pages')->orderBy('position')->get();
        //        $brands            = Brand::active(true)->with('translations')->orderBy('position')->get();
        //        $productCategories = ProductCategory::active(true)->with('translations')->with('products')->orderBy('position')->get();

        return view('adboxes::admin.edit', $data);
    }

    public function update($id, AdBoxUpdateRequest $request): RedirectResponse
    {
        $adBox = AdBox::find($id);
        MainHelper::goBackIfNull($adBox);

        $data      = AdBox::getRequestData($request);
        $languages = LanguageHelper::getActiveLanguages();

        foreach ($languages as $language) {
            $data[$language->code] = AdBoxTranslation::getLanguageArray($language, $request);
        }
        $adBox->update($data);
        if ($request->has('image')) {
            $request->validate(['image' => AdBox::getFileRule($adBox->type)], AdBox::getFileRuleMessage($adBox->type));
            $adBox->saveFile($request->image);
        }

        AdBox::cacheUpdate();

        return redirect()->route('ad-boxes')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
    }

    public function delete($id): RedirectResponse
    {
        $adBox = AdBox::find($id);
        MainHelper::goBackIfNull($adBox);

        if (file_exists($adBox->imagePath())) {
            \File::delete($adBox->imagePath());
        }

        $adBoxesToUpdate = AdBox::where('type', $adBox->type)->where('position', '>', $adBox->position)->get();
        $adBox->delete();
        foreach ($adBoxesToUpdate as $adBoxToUpdate) {
            $adBoxToUpdate->update(['position' => $adBoxToUpdate->position - 1]);
        }

        return redirect()->back()->with('success-message', 'adboxes::admin.adboxes_actions.successful_delete');
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

    public function deleteMultiple(Request $request): RedirectResponse
    {
        if (!is_null($request->ids[0])) {
            $ids = array_map('intval', explode(',', $request->ids[0]));
            foreach ($ids as $id) {
                $adBox = AdBox::find($id);
                if (is_null($adBox)) {
                    continue;
                }

                if (file_exists($adBox->imagePath())) {
                    \File::delete($adBox->imagePath());
                }

                $adBoxesToUpdate = AdBox::where('type', $adBox->type)->where('position', '>', $adBox->position)->get();
                $adBox->delete();
                foreach ($adBoxesToUpdate as $adBoxToUpdate) {
                    $adBoxToUpdate->update(['position' => $adBoxToUpdate->position - 1]);
                }
            }

            return redirect()->back()->with('success-message', 'adboxes::admin.adboxes_actions.successful_delete');
        }

        return redirect()->back()->withErrors(['administration_messages.no_checked_checkboxes']);
    }

    public function active($id, $active)
    {
        $adBox = AdBox::find($id);
        MainHelper::goBackIfNull($adBox);

        $adBox->update(['active' => $active]);

        return redirect()->route('ad-boxes')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
    }

    public function activeMultiple($active, Request $request)
    {
        if (!is_null($request->ids[0])) {
            $ids = array_map('intval', explode(',', $request->ids[0]));
            AdBox::whereIn('id', $ids)->update(['active' => $active]);
        }

        return redirect()->route('ad-boxes')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
    }

    public function positionDown($id)
    {
        $adBox = AdBox::find($id);
        MainHelper::goBackIfNull($adBox);

        $nextAdbox = AdBox::where('type', $adBox->type)->where('position', $adBox->position + 1)->first();
        if (!is_null($nextAdbox)) {
            $nextAdbox->update(['position' => $nextAdbox->position - 1]);
            $adBox->update(['position' => $adBox->position + 1]);
        }

        return redirect()->route('ad-boxes')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
    }

    public function positionUp($id)
    {
        $adBox = AdBox::find($id);
        MainHelper::goBackIfNull($adBox);

        $prevAdbox = AdBox::where('type', $adBox->type)->where('position', $adBox->position - 1)->first();
        if (!is_null($prevAdbox)) {
            $prevAdbox->update(['position' => $prevAdbox->position + 1]);
            $adBox->update(['position' => $adBox->position - 1]);
        }

        return redirect()->route('ad-boxes')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
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

    public function returnToWaiting($id)
    {
        $adBox = AdBox::find($id);
        MainHelper::goBackIfNull($adBox);

        if ($adBox->existsFile($adBox->filename)) {
            $adBox->deleteFile($adBox->filename);
            $adBox->update(['filename' => '']);
        }

        $adBox->update(['type' => 0]);

        return redirect()->route('ad-boxes')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
    }

    public function editButton($adboxType)
    {
        $adBoxButton = AdBoxButton::where('adbox_type', $adboxType)->first();
        if (is_null($adBoxButton)) {
            return redirect()->back()->withInput()->withErrors(['adboxes::admin.adboxes_actions.record_not_found']);
        }

        $languages       = LanguageHelper::getActiveLanguages();
        $defaultLanguage = Language::where('code', env('DEF_LANG_CODE'))->first();
        $otherSettings   = OtherSetting::first();

        $navigations       = Navigation::active(true)->with('translations')->with('content_pages')->orderBy('position')->get();
        $brands            = Brand::active(true)->with('translations')->orderBy('position')->get();
        $productCategories = ProductCategory::active(true)->with('translations')->with('products')->orderBy('position')->get();

        return view('admin.adboxes.buttons.edit', compact('languages', 'defaultLanguage', 'otherSettings', 'navigations', 'brands', 'productCategories', 'adBoxButton'));
    }

    public function updateButton($adboxType, Request $request)
    {
        $adBoxButton = AdBoxButton::where('adbox_type', $adboxType)->first();
        if (is_null($adBoxButton)) {
            return redirect()->back()->withInput()->withErrors(['adboxes::admin.adboxes_actions.record_not_found']);
        }

        $languages = LanguageHelper::getActiveLanguages();
        foreach ($languages as $language) {
            $adBoxTranslation = $adBoxButton->where('adbox_type', $adBoxButton->adbox_type)->where('language_id', $language->id)->first();
            if (is_null($adBoxTranslation)) {
                $request['adbox_type'] = $adBoxButton->adbox_type;
                $adBoxButton->create(AdBoxButton::getCreateData($language, $request));
            } else {
                $adBoxTranslation->update($adBoxTranslation->getUpdateData($language, $request));
            }
        }

        return redirect()->route('ad-boxes')->with('success-message', 'adboxes::admin.adboxes_actions.successful_edit');
    }
}
