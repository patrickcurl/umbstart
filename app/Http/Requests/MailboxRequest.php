<?php

namespace App\Http\Requests;

use App\Models\Mailbox;
use Illuminate\Foundation\Http\FormRequest;

class MailboxRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'host'       => 'required',
            'port'       => 'required|numeric',
            'encryption' => 'required|in:false,ssl,tls,notls,starttls',
            'username'   => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (Mailbox::where(['username' => $value, 'user_id' => auth()->user()->id])->count() > 0) {
                        $fail('That mailbox is already setup for user. Please add a different one.');
                    }
                }
            ],
            'password' => 'required|min:8'
        ];
    }
}
