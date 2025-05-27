<?php

namespace App\Http\Requests;

use App\Models\EmailApplication; //
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProcessEmailProvisioningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var \App\Models\EmailApplication|null $emailApplication */
        // The route parameter name is 'emailApplication' from the web.php definition.
        $emailApplication = $this->route('emailApplication');

        // User must be authenticated and authorized by EmailApplicationPolicy.
        // Assuming the policy ability is 'processByIT' as defined in EmailApplicationPolicy.
        return Auth::check() && $emailApplication instanceof EmailApplication && $this->user()->can('processByIT', $emailApplication);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'final_assigned_email' => 'required|email|max:255',
            'user_id_assigned' => 'nullable|string|max:255',
            // 'admin_notes' => 'nullable|string|max:2000', // Optional: if admins can add notes during this step
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'final_assigned_email.required' => __('E-mel rasmi yang akan diberikan adalah mandatori.'),
            'final_assigned_email.email' => __('Sila masukkan alamat e-mel yang sah.'),
            'final_assigned_email.max' => __('Alamat e-mel tidak boleh melebihi :max aksara.'),
            'user_id_assigned.string' => __('ID Pengguna mestilah dalam format teks.'),
            'user_id_assigned.max' => __('ID Pengguna tidak boleh melebihi :max aksara.'),
        ];
    }
}
