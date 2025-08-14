<?php

namespace App\Http\Requests;

use App\Models\EmailApplication;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ProcessEmailProvisioningRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        if (! $user) {
            return false;
        }

        /** @var EmailApplication|null $emailApplication */
        $emailApplication = $this->route('emailApplication'); // Matches definition in EmailAccountController

        // System Design: EmailApplicationPolicy to authorize IT Admin actions.
        return $emailApplication instanceof EmailApplication && $user->can('processByIT', $emailApplication);
    }

    public function rules(): array
    {
        return [
            // final_assigned_email and final_assigned_user_id are fields in 'email_applications' table
            'final_assigned_email' => ['required', 'email:rfc,dns', 'max:255'],
            'user_id_assigned' => ['nullable', 'string', 'max:255', 'alpha_dash:ascii'],
        ];
    }

    public function messages(): array
    {
        return [
            'final_assigned_email.required' => __('E-mel rasmi yang akan diberikan adalah mandatori.'),
            'final_assigned_email.email' => __('Sila masukkan alamat e-mel yang sah.'),
            'final_assigned_email.max' => __('Alamat e-mel tidak boleh melebihi :max aksara.'),
            'user_id_assigned.string' => __('ID Pengguna mestilah dalam format teks.'),
            'user_id_assigned.max' => __('ID Pengguna tidak boleh melebihi :max aksara.'),
            'user_id_assigned.alpha_dash' => __('ID Pengguna hanya boleh mengandungi aksara alpha-numerik, sengkang, dan garis bawah.'),
        ];
    }
}
