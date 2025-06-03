<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Approval Model.
 * 
 * Represents an approval task for various approvable items (e.g., EmailApplication, LoanApplication).
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.4
 *
 * @property int $id
 * @property string $approvable_type Model class name (e.g., EmailApplication::class, LoanApplication::class)
 * @property int $approvable_id ID of the model instance being approved
 * @property int $officer_id User ID of the approving/rejecting officer
 * @property string|null $stage Approval stage identifier (e.g., 'email_support_review', 'loan_hod_review')
 * @property string $status Enum: 'pending', 'approved', 'rejected'
 * @property string|null $comments Officer's comments regarding the decision
 * @property \Illuminate\Support\Carbon|null $approval_timestamp Timestamp of when the approval/rejection decision was made
 * @property int|null $created_by User ID of the creator of this approval record
 * @property int|null $updated_by User ID of the last updater of this approval record
 * @property int|null $deleted_by User ID of the deleter (for soft deletes)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Model|\Eloquent $approvable Polymorphic relation to the item being approved.
 * @property-read \App\Models\User $officer The User who is assigned to make the approval decision.
 * @property-read \App\Models\User|null $creator User who created this approval record.
 * @property-read \App\Models\User|null $updater User who last updated this approval record.
 * @property-read \App\Models\User|null $deleter User who soft-deleted this approval record.
 * @property-read string $statusTranslated Accessor for a human-readable, translated status.
 * @property-read string|null $stageTranslated Accessor for a human-readable, translated stage name.
 * @method static ApprovalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval query()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereApprovableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereApprovableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereApprovalTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval withoutTrashed()
 * @mixin \Eloquent
 * @property-read string|null $stage_translated
 * @property-read string $status_translated
 */
	class Approval extends \Eloquent {}
}

namespace App\Models{
/**
 * Department Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.2 [cite: 72]
 *
 * @property int $id
 * @property string $name
 * @property string $branch_type Enum: 'state', 'headquarters'
 * @property string|null $code
 * @property string|null $description
 * @property bool $is_active Default true
 * @property int|null $head_user_id Foreign key for Head of Department User (Note: System Design DB shows head_of_department_id [cite: 72])
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \App\Models\User|null $headOfDepartment User relationship for HOD
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $branch_type_label
 * @method static \Database\Factories\DepartmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereBranchType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereHeadUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Department withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $head_of_department_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereHeadOfDepartmentId($value)
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * Email Application Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.2
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $service_status From User model's service_status enum options [cite: 70]
 * @property string|null $appointment_type From User model's appointment_type enum options [cite: 70]
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property \Illuminate\Support\Carbon|null $service_start_date
 * @property \Illuminate\Support\Carbon|null $service_end_date
 * @property string|null $application_reason_notes For "Tujuan/Catatan"
 * @property string|null $proposed_email For "Cadangan E-mel ID"
 * @property string|null $group_email For Group Email address if requested
 * @property string|null $contact_person_name For "Nama Admin/EO/CC" of Group Email
 * @property string|null $contact_person_email For "E-mel Admin/EO/CC" of Group Email
 * @property int|null $supporting_officer_id
 * @property string|null $supporting_officer_name Manual entry if not from system list
 * @property string|null $supporting_officer_grade Manual entry if not from system list [cite: 284]
 * @property string|null $supporting_officer_email Manual entry if not from system list
 * @property string $status Enum: 'draft', 'pending_support', 'pending_admin', 'approved', 'rejected', 'processing', 'provision_failed', 'completed' [cite: 78]
 * @property bool $cert_info_is_true
 * @property bool $cert_data_usage_agreed
 * @property bool $cert_email_responsibility_agreed
 * @property \Illuminate\Support\Carbon|null $certification_timestamp
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property string|null $rejection_reason
 * @property string|null $final_assigned_email
 * @property string|null $final_assigned_user_id
 * @property int|null $processed_by User ID of IT Admin who processed
 * @property \Illuminate\Support\Carbon|null $processed_at Timestamp of processing
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user Applicant
 * @property-read \App\Models\User|null $supportingOfficer Selected from system or details for manual entry
 * @property-read \App\Models\User|null $processor User who processed the application (IT Admin)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_translated
 * @method static EmailApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailApplication withoutTrashed()
 * @mixin \Eloquent
 */
	class EmailApplication extends \Eloquent {}
}

namespace App\Models{
/**
 * Equipment Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 *
 * @property int $id
 * @property string $asset_type (Enum from ASSET_TYPE_CONSTANTS)
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $serial_number (Unique)
 * @property string|null $tag_id (Unique MOTAC Tag ID)
 * @property string|null $item_code (Unique item code)
 * @property string|null $description (Detailed description)
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property float|null $purchase_price
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string $status (Enum from STATUS_CONSTANTS, default: 'available')
 * @property string $condition_status (Enum from CONDITION_STATUS_CONSTANTS, default: 'good')
 * @property string|null $current_location
 * @property string|null $acquisition_type (Enum from ACQUISITION_TYPE_CONSTANTS)
 * @property string|null $classification (Enum from CLASSIFICATION_CONSTANTS)
 * @property string|null $funded_by
 * @property string|null $supplier_name
 * @property string|null $notes
 * @property array|null $specifications (JSON for detailed specs)
 * @property int|null $department_id (FK to departments.id)
 * @property int|null $equipment_category_id (FK to equipment_categories.id)
 * @property int|null $sub_category_id (FK to sub_categories.id)
 * @property int|null $location_id (FK to locations.id for structured location)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanTransactionItem|null $activeLoanTransactionItem
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Location|null $definedLocation
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\EquipmentCategory|null $equipmentCategory
 * @property-read string $asset_type_label
 * @property-read string $condition_status_label
 * @property-read string $status_label
 * @property-read string|null $acquisition_type_label
 * @property-read string|null $classification_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read int|null $loan_transaction_items_count
 * @property-read \App\Models\SubCategory|null $subCategory
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\EquipmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereAcquisitionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereAssetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereConditionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereCurrentLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereEquipmentCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereFundedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereItemCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereSubCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment whereWarrantyExpiryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment withoutTrashed()
 */
	class Equipment extends \Eloquent {}
}

namespace App\Models{
/**
 * Equipment Category Model.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubCategory> $subCategories
 * @property-read int|null $sub_categories_count
 * @method static Builder<static>|EquipmentCategory active()
 * @method static EquipmentCategoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|EquipmentCategory newModelQuery()
 * @method static Builder<static>|EquipmentCategory newQuery()
 * @method static Builder<static>|EquipmentCategory onlyTrashed()
 * @method static Builder<static>|EquipmentCategory query()
 * @method static Builder<static>|EquipmentCategory whereCreatedAt($value)
 * @method static Builder<static>|EquipmentCategory whereCreatedBy($value)
 * @method static Builder<static>|EquipmentCategory whereDeletedAt($value)
 * @method static Builder<static>|EquipmentCategory whereDeletedBy($value)
 * @method static Builder<static>|EquipmentCategory whereDescription($value)
 * @method static Builder<static>|EquipmentCategory whereId($value)
 * @method static Builder<static>|EquipmentCategory whereName($value)
 * @method static Builder<static>|EquipmentCategory whereUpdatedAt($value)
 * @method static Builder<static>|EquipmentCategory whereUpdatedBy($value)
 * @method static Builder<static>|EquipmentCategory withTrashed()
 * @method static Builder<static>|EquipmentCategory withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereIsActive($value)
 */
	class EquipmentCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * Grade Model (Gred Perkhidmatan).
 *
 * @property int $id
 * @property string $name (e.g., "F41", "N19", "JUSA C")
 * @property int|null $level Numeric level for comparison/sorting (as per System Design & Livewire component)
 * @property int|null $min_approval_grade_id (FK to grades.id)
 * @property bool $is_approver_grade Can users of this grade approve applications? (System Design default: false)
 * @property string|null $description (Optional, kept from your file)
 * @property string|null $service_scheme (Optional, kept from your file, might be legacy)
 * @property int|null $created_by (FK to users.id, typically handled by BlameableObserver)
 * @property int|null $updated_by (FK to users.id, typically handled by BlameableObserver)
 * @property int|null $deleted_by (FK to users.id, typically handled by BlameableObserver)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Grade|null $minApprovalGrade Relationship for min_approval_grade_id
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereIsApproverGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereMinApprovalGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereServiceScheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read int|null $positions_count
 */
	class Grade extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $file_name Original client-side filename
 * @property string|null $original_file_name Stored if different from file_name or for reference
 * @property string|null $file_path Storage path of the imported file
 * @property int|null $file_size Size in bytes
 * @property string|null $file_ext
 * @property string|null $file_type Type of data being imported
 * @property string $status
 * @property string|null $notes User-provided notes or comments about the import
 * @property string|null $details JSON containing import results, errors, parameters, etc.
 * @property int $total_rows Total rows detected or expected in the file
 * @property int $processed_rows Number of rows successfully processed
 * @property int|null $failed_rows Number of rows that failed during processing
 * @property string|null $completed_at Timestamp when import process finished (completed or failed)
 * @property int|null $user_id User who initiated the import
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFailedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereOriginalFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereProcessedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withoutTrashed()
 * @mixin \Eloquent
 */
	class Import extends \Eloquent {}
}

namespace App\Models{
/**
 * Loan Application Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3 [cite: 368]
 *
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property int|null $responsible_officer_id
 * @property int|null $supporting_officer_id
 * @property string $purpose
 * @property string $location Usage location
 * @property string|null $return_location
 * @property \Illuminate\Support\Carbon $loan_start_date
 * @property \Illuminate\Support\Carbon $loan_end_date
 * @property string $status
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $applicant_confirmation_timestamp
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property int|null $approved_by User ID of approver
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $rejected_by User ID of rejector
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $cancelled_by User ID of canceller
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $admin_notes
 * @property int|null $current_approval_officer_id User ID of current approver
 * @property string|null $current_approval_stage
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User|null $responsibleOfficer
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $rejectedBy
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\User|null $currentApprovalOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $applicationItems
 * @property-read int|null $application_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $items alias for applicationItems
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read int|null $loan_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_translated
 * @property-read bool $is_draft
 * @method static \Database\Factories\LoanApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereApplicantConfirmationTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCancelledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCurrentApprovalOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereCurrentApprovalStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereLoanEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereLoanStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereRejectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereResponsibleOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereReturnLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereSupportingOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanApplication withoutTrashed()
 * @mixin \Eloquent
 */
	class LoanApplication extends \Eloquent {}
}

namespace App\Models{
/**
 * Loan Application Item Model.
 * 
 * Represents a type of equipment and quantity requested in a loan application.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $equipment_type Type of equipment requested (e.g., 'laptop', 'projector')
 * @property int $quantity_requested
 * @property int|null $quantity_approved
 * @property int $quantity_issued Default: 0
 * @property int $quantity_returned Default: 0
 * @property string|null $notes Notes for this specific item request
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $equipmentTypeLabel Accessor for equipment_type label
 * @property string $status Status of this specific requested item
 * @property-read string $equipment_type_label
 * @property-read int|null $loan_transaction_items_count
 * @method static \Database\Factories\LoanApplicationItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereEquipmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityIssued($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem withoutTrashed()
 * @mixin \Eloquent
 */
	class LoanApplicationItem extends \Eloquent {}
}

namespace App\Models{
/**
 * Loan Transaction Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $type Enum: 'issue', 'return'
 * @property \Illuminate\Support\Carbon $transaction_date General date of the transaction event
 * @property int|null $issuing_officer_id Pegawai Pengeluar (BPM Staff)
 * @property int|null $receiving_officer_id Pegawai Penerima (Applicant/Delegate)
 * @property array|null $accessories_checklist_on_issue JSON
 * @property string|null $issue_notes
 * @property \Illuminate\Support\Carbon|null $issue_timestamp Actual moment of physical issuance
 * @property int|null $returning_officer_id Pegawai Yang Memulangkan
 * @property int|null $return_accepting_officer_id Pegawai Terima Pulangan (BPM Staff)
 * @property array|null $accessories_checklist_on_return JSON
 * @property string|null $return_notes Catatan semasa pemulangan
 * @property \Illuminate\Support\Carbon|null $return_timestamp Actual moment of physical return
 * @property int|null $related_transaction_id For linking return to issue
 * @property string $status Enum from STATUSES_LABELS keys
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $items Alias for loanTransactionItems
 * @property-read int|null $loan_transaction_items_count
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $issuingOfficer
 * @property-read \App\Models\User|null $receivingOfficer
 * @property-read \App\Models\User|null $returningOfficer
 * @property-read \App\Models\User|null $returnAcceptingOfficer
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read LoanTransaction|null $relatedIssueTransaction If this is a return transaction, this points to the original issue transaction
 * @property-read string $type_label Translated type
 * @property-read string $status_label Translated status
 * @method static LoanTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LoanTransaction withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $due_date Applicable for issue transactions
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereAccessoriesChecklistOnIssue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereAccessoriesChecklistOnReturn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereIssueNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereIssueTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereIssuingOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereReceivingOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereRelatedTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereReturnAcceptingOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereReturnNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereReturnTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereReturningOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction whereUpdatedBy($value)
 */
	class LoanTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * Loan Transaction Item Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 *
 * @property int $id
 * @property int $loan_transaction_id
 * @property int $equipment_id Specific physical equipment item
 * @property int|null $loan_application_item_id Link to original request line item
 * @property int $quantity_transacted Typically 1 for serialized items
 * @property string $status Status of this item within THIS transaction (e.g., 'issued', 'returned_good')
 * @property string|null $condition_on_return Matches Equipment model's condition_status enum keys (e.g., Equipment::CONDITION_GOOD)
 * @property array|null $accessories_checklist_issue Item-specific accessories issued (JSON)
 * @property array|null $accessories_checklist_return Item-specific accessories returned (JSON)
 * @property string|null $item_notes Notes specific to this item in this transaction
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanTransaction $loanTransaction
 * @property-read \App\Models\Equipment $equipment
 * @property-read \App\Models\LoanApplicationItem|null $loanApplicationItem
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_translated
 * @property-read string|null $condition_on_return_translated
 * @property-read string $status_label
 * @method static \Database\Factories\LoanTransactionItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereAccessoriesChecklistIssue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereAccessoriesChecklistReturn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereConditionOnReturn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereEquipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereItemNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereLoanApplicationItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereLoanTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereQuantityTransacted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem withoutTrashed()
 */
	class LoanTransactionItem extends \Eloquent {}
}

namespace App\Models{
/**
 * Location Model.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property string|null $postal_code
 * @property bool $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * // Removed: @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Device> $devices
 * // Removed: @property-read int|null $devices_count
 * @method static Builder<static>|Location active()
 * @method static Builder<static>|Location byCity(string $city)
 * @method static Builder<static>|Location byCountry(string $country)
 * @method static LocationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Location newModelQuery()
 * @method static Builder<static>|Location newQuery()
 * @method static Builder<static>|Location onlyTrashed()
 * @method static Builder<static>|Location query()
 * @method static Builder<static>|Location whereCreatedAt($value)
 * @method static Builder<static>|Location whereCreatedBy($value)
 * @method static Builder<static>|Location whereDeletedAt($value)
 * @method static Builder<static>|Location whereDeletedBy($value)
 * @method static Builder<static>|Location whereDescription($value)
 * @method static Builder<static>|Location whereId($value)
 * @method static Builder<static>|Location whereName($value)
 * @method static Builder<static>|Location whereUpdatedAt($value)
 * @method static Builder<static>|Location whereUpdatedBy($value)
 * @method static Builder<static>|Location withTrashed()
 * @method static Builder<static>|Location withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereState($value)
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property array<array-key, mixed> $data
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Model $notifiable
 * @method static Builder<static>|Notification byNotifiable(\Illuminate\Database\Eloquent\Model $notifiableModel)
 * @method static Builder<static>|Notification byType(array|string $type)
 * @method static \Database\Factories\NotificationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Notification newModelQuery()
 * @method static Builder<static>|Notification newQuery()
 * @method static Builder<static>|Notification onlyTrashed()
 * @method static Builder<static>|Notification query()
 * @method static Builder<static>|Notification read()
 * @method static Builder<static>|Notification unread()
 * @method static Builder<static>|Notification whereCreatedAt($value)
 * @method static Builder<static>|Notification whereCreatedBy($value)
 * @method static Builder<static>|Notification whereData($value)
 * @method static Builder<static>|Notification whereDeletedAt($value)
 * @method static Builder<static>|Notification whereDeletedBy($value)
 * @method static Builder<static>|Notification whereId($value)
 * @method static Builder<static>|Notification whereNotifiableId($value)
 * @method static Builder<static>|Notification whereNotifiableType($value)
 * @method static Builder<static>|Notification whereReadAt($value)
 * @method static Builder<static>|Notification whereType($value)
 * @method static Builder<static>|Notification whereUpdatedAt($value)
 * @method static Builder<static>|Notification whereUpdatedBy($value)
 * @method static Builder<static>|Notification withTrashed()
 * @method static Builder<static>|Notification withoutTrashed()
 * @mixin \Eloquent
 */
	final class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * Position Model (Jawatan).
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1, positions table [cite: 314, 357]
 *
 * @property int $id
 * @property string $name (e.g., "Pegawai Teknologi Maklumat", "Pembantu Tadbir")
 * @property string|null $description
 * @property int|null $grade_id (FK to grades.id)
 * @property bool $is_active (default: true)
 * @property int|null $created_by (FK to users.id)
 * @property int|null $updated_by (FK to users.id)
 * @property int|null $deleted_by (FK to users.id)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Grade|null $grade The grade associated with this position.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users Users who hold this position.
 * @property-read int|null $users_count
 * @property-read \App\Models\User|null $creator User who created this record.
 * @property-read \App\Models\User|null $updater User who last updated this record.
 * @property-read \App\Models\User|null $deleter User who soft deleted this record.
 * @method static \Database\Factories\PositionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Position withoutTrashed()
 * @mixin \Eloquent
 */
	class Position extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $site_name
 * @property string|null $site_logo_path
 * @property string|null $default_notification_email_from
 * @property string|null $default_notification_email_name
 * @property string|null $sms_api_sender
 * @property string|null $sms_api_username
 * @property string|null $sms_api_password
 * @property string|null $terms_and_conditions_loan
 * @property string|null $terms_and_conditions_email
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\SettingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefaultNotificationEmailFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefaultNotificationEmailName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSiteLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTermsAndConditionsEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTermsAndConditionsLoan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withoutTrashed()
 * @mixin \Eloquent
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * SubCategory Model.
 * 
 * Defines sub-categories for ICT equipment, linked to EquipmentCategory.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 *
 * @property int $id
 * @property int $equipment_category_id Foreign key to equipment_categories.id
 * @property string $name Name of the sub-category
 * @property string|null $description Optional description
 * @property bool $is_active Whether the sub-category is active (default: true)
 * @property int|null $created_by User ID of the creator
 * @property int|null $updated_by User ID of the last updater
 * @property int|null $deleted_by User ID of the deleter (for soft deletes)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\EquipmentCategory $equipmentCategory The parent equipment category.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment Equipment items belonging to this sub-category.
 * @property-read int|null $equipment_count
 * @property-read \App\Models\User|null $creator User who created this record.
 * @property-read \App\Models\User|null $updater User who last updated this record.
 * @property-read \App\Models\User|null $deleter User who soft-deleted this record.
 * @method static SubCategoryFactory factory($count = null, $state = [])
 * @method static Builder|SubCategory newModelQuery()
 * @method static Builder|SubCategory newQuery()
 * @method static Builder|SubCategory onlyTrashed()
 * @method static Builder|SubCategory query()
 * @method static Builder|SubCategory active()
 * @method static Builder|SubCategory byCategory(int $categoryId)
 * @method static Builder|SubCategory byName(string $name)
 * @method static Builder|SubCategory whereCreatedAt($value)
 * @method static Builder|SubCategory whereCreatedBy($value)
 * @method static Builder|SubCategory whereDeletedAt($value)
 * @method static Builder|SubCategory whereDeletedBy($value)
 * @method static Builder|SubCategory whereDescription($value)
 * @method static Builder|SubCategory whereEquipmentCategoryId($value)
 * @method static Builder|SubCategory whereId($value)
 * @method static Builder|SubCategory whereIsActive($value)
 * @method static Builder|SubCategory whereName($value)
 * @method static Builder|SubCategory whereUpdatedAt($value)
 * @method static Builder|SubCategory whereUpdatedBy($value)
 * @method static Builder|SubCategory withTrashed()
 * @method static Builder|SubCategory withoutTrashed()
 * @mixin \Eloquent
 */
	class SubCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * User Model for MOTAC System.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1
 * Migration context: 2014_10_12_000000_create_users_table.php, 2013_11_01_132200_add_motac_columns_to_users_table.php
 *
 * @property int $id
 * @property string|null $title (e.g., "Encik", "Puan", "Dr.")
 * @property string $name
 * @property string|null $identification_number (NRIC)
 * @property string|null $passport_number
 * @property string|null $profile_photo_path
 * @property int|null $position_id (FK to positions.id)
 * @property int|null $grade_id (FK to grades.id)
 * @property int|null $department_id (FK to departments.id)
 * @property string|null $level (Aras/Floor)
 * @property string|null $mobile_number
 * @property string $email (Login email)
 * @property string|null $personal_email (From motac_columns migration)
 * @property string|null $motac_email (Official MOTAC email)
 * @property string|null $user_id_assigned (e.g., network ID)
 * @property string|null $service_status (Enum '1','2','3','4' etc. from migration - IMPORTANT: Migration must be updated)
 * @property string|null $appointment_type (Enum '1','2','3' from migration)
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property string $password
 * @property string $status (Enum: 'active', 'inactive', default: 'active')
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\Grade|null $grade
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmailApplication> $emailApplications
 * @property-read int|null $email_applications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsApplicant
 * @property-read int|null $loan_applications_as_applicant_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsResponsibleOfficer
 * @property-read int|null $loan_applications_as_responsible_officer_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsSupportingOfficer
 * @property-read int|null $loan_applications_as_supporting_officer_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsMade
 * @property-read int|null $approvals_made_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<User> newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<User> newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<User> onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<User> query()
 * @method static \Illuminate\Database\Eloquent\Builder<User> role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<User> permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<User> withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<User> withoutTrashed()
 * @mixin \Eloquent
 * @property int $is_admin
 * @property int $is_bpm_staff Belongs to Bahagian Pengurusan Maklumat
 * @property int|null $employee_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAppointmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdentificationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBpmStaff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMotacEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassportNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePreviousDepartmentEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePreviousDepartmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereServiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserIdAssigned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

