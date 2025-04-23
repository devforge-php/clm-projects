<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialMediaStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'telegram_user_name'   => 'sometimes|nullable|string|max:255',
            'instagram_user_name'  => 'sometimes|nullable|string|max:255',
            'facebook_user_name'   => 'sometimes|nullable|string|max:255',
            'youtube_user_name'    => 'sometimes|nullable|string|max:255',
            'twitter_user_name'    => 'sometimes|nullable|string|max:255',
        ];
    }
}
