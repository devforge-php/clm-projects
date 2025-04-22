<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialMediaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or check ownership
    }

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

