<?php

namespace App\Http\Requests\Api;

use App\Models\EmailApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProvisionEmailRequest extends FormRequest
{
  public function authorize(): bool
  {
    // Assuming Sanctum route middleware handles primary auth.
    // Add $this->user()->tokenCan('email:provision') if specific ability is needed.
    return true;
  }

  public function rules(): array
  {
    return [
      'application_id' => [
        'required',
        'integer',
        Rule::exists('email_applications', 'id')
            // ->where('status', EmailApplication::STATUS_APPROVED) // Controller handles for better response
      ],
      'final_assigned_email' => ['required', 'email:rfc,dns', 'max:255'],
      'user_id_assigned' => ['nullable', 'string', 'max:255', 'alpha_dash:ascii'],
    ];
  }

  public function messages(): array
  {
    return [
      'application_id.required' => __('ID Permohonan E-mel diperlukan.'),
      'application_id.integer' => __('ID Permohonan E-mel mestilah nombor bulat.'),
      'application_id.exists' => __('ID Permohonan E-mel yang dipilih tidak sah atau tidak wujud.'),
      'final_assigned_email.required' => __('Alamat e-mel akhir yang akan diberikan adalah mandatori.'),
      'final_assigned_email.email' => __('Alamat e-mel akhir mestilah alamat e-mel yang sah.'),
      'final_assigned_email.max' => __('Alamat e-mel akhir tidak boleh melebihi :max aksara.'),
      'user_id_assigned.string' => __('ID Pengguna yang diberikan mestilah dalam format teks.'),
      'user_id_assigned.max' => __('ID Pengguna yang diberikan tidak boleh melebihi :max aksara.'),
      'user_id_assigned.alpha_dash' => __('ID Pengguna hanya boleh mengandungi aksara alpha-numerik, sengkang, dan garis bawah.'),
    ];
  }
}
