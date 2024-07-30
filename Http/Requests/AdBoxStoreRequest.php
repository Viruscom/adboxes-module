<?php

namespace Modules\AdBoxes\Http\Requests;


use App\Helpers\LanguageHelper;
use Illuminate\Foundation\Http\FormRequest;
use Modules\AdBoxes\Models\AdBox;

class AdBoxStoreRequest extends FormRequest
{
    protected $LANGUAGES;

    public function __construct()
    {
        $this->LANGUAGES = LanguageHelper::getActiveLanguages();
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $this->trimInput();
        $array = [
            // 'code' => 'required'
        ];

        foreach ($this->LANGUAGES as $language) {
            $array['title_' . $language->code] = 'required';
//            $array['url_' . $language->code]   = 'required';
        }
        if ($this->hasFile('image')) {
            $array['image'] = AdBox::getFileRule($this->type);
        }

        return $array;
    }
    public function trimInput(): void
    {
        $trim_if_string = static function ($var) {
            return is_string($var) ? trim($var) : $var;
        };
        $this->merge(array_map($trim_if_string, $this->all()));
    }
    public function messages(): array
    {
        $messages = [];

        foreach ($this->LANGUAGES as $language) {
            $messages['title_' . $language->code . '.required'] = 'Полето за заглавие (' . $language->code . ') е задължително';
            $messages['url_' . $language->code . '.required']   = 'Полето за линк (' . $language->code . ') е задължително';
        }

        return $messages;
    }
}
