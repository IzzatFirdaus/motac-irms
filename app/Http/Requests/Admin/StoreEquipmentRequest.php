<?php

namespace App\Http\Requests\Admin;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Location;
use App\Models\SubCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Ensure this model exists and is imported

class StoreEquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assumes EquipmentPolicy@create will be checked, typically via authorizeResource in controller
        return $this->user()->can('create', Equipment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Ensure Equipment model has: getAssetTypeOptions, getInitialStatusOptions,
        // getConditionStatusOptions, getAcquisitionTypeOptions, getClassificationOptions
        return [
            'tag_id'                => ['required', 'string', 'max:255', Rule::unique('equipment', 'tag_id')->whereNull('deleted_at')],
            'item_code'             => ['nullable', 'string', 'max:255', Rule::unique('equipment', 'item_code')->whereNull('deleted_at')],
            'serial_number'         => ['nullable', 'string', 'max:255', Rule::unique('equipment', 'serial_number')->whereNull('deleted_at')],
            'asset_type'            => ['required', 'string', Rule::in(array_keys(Equipment::getAssetTypeOptions()))],
            'brand'                 => ['nullable', 'string', 'max:255'],
            'model'                 => ['nullable', 'string', 'max:255'],
            'description'           => ['nullable', 'string', 'max:2000'],
            'purchase_date'         => ['nullable', 'date_format:Y-m-d'],
            'purchase_price'        => ['nullable', 'numeric', 'min:0', 'regex:/^\d+(\.\d{1,2})?$/'], // Standard decimal format
            'warranty_expiry_date'  => ['nullable', 'date_format:Y-m-d', 'after_or_equal:purchase_date'],
            'status'                => ['required', 'string', Rule::in(array_keys(Equipment::getInitialStatusOptions()))], // Initial status
            'condition_status'      => ['required', 'string', Rule::in(array_keys(Equipment::getConditionStatusOptions()))],
            'current_location'      => ['nullable', 'string', 'max:255'],
            'location_id'           => ['nullable', 'integer', Rule::exists(Location::class, 'id')->whereNull('deleted_at')],
            'department_id'         => ['nullable', 'integer', Rule::exists(Department::class, 'id')->whereNull('deleted_at')],
            'equipment_category_id' => ['nullable', 'integer', Rule::exists(EquipmentCategory::class, 'id')->whereNull('deleted_at')],
            'sub_category_id'       => ['nullable', 'integer', Rule::exists(SubCategory::class, 'id')->whereNull('deleted_at')], // Activated this rule
            'acquisition_type'      => ['nullable', 'string', Rule::in(array_keys(Equipment::getAcquisitionTypeOptions()))],
            'classification'        => ['nullable', 'string', Rule::in(array_keys(Equipment::getClassificationOptions()))],
            'funded_by'             => ['nullable', 'string', 'max:255'],
            'supplier_name'         => ['nullable', 'string', 'max:255'],
            'notes'                 => ['nullable', 'string', 'max:2000'],
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
            'tag_id.required'              => __('No. Tag ID Aset wajib diisi.'),
            'tag_id.unique'                => __('No. Tag ID Aset ini telah wujud.'),
            'item_code.unique'             => __('Kod Item ini telah wujud.'),
            'serial_number.unique'         => __('No. Siri ini telah wujud.'),
            'asset_type.required'          => __('Jenis Aset wajib dipilih.'),
            'asset_type.in'                => __('Jenis Aset yang dipilih tidak sah.'),
            'purchase_price.regex'         => __('Format Harga Belian tidak sah (cth: 1200.00).'),
            'status.required'              => __('Status Operasi wajib dipilih.'),
            'status.in'                    => __('Status Operasi yang dipilih tidak sah.'),
            'condition_status.required'    => __('Status Keadaan wajib dipilih.'),
            'condition_status.in'          => __('Status Keadaan yang dipilih tidak sah.'),
            'location_id.exists'           => __('Lokasi Berstruktur yang dipilih tidak sah.'),
            'department_id.exists'         => __('Jabatan/Bahagian pemilik yang dipilih tidak sah.'),
            'equipment_category_id.exists' => __('Kategori Peralatan yang dipilih tidak sah.'),
            'sub_category_id.exists'       => __('Sub-Kategori Peralatan yang dipilih tidak sah.'),
        ];
    }
}
