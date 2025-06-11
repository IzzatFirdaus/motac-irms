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
 * @property string|null $stage Approval stage identifier (e.g., 'email_support_review', 'loan_approver_review') // Updated example
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
 * @property-read string $status_color_class Accessor for the Bootstrap badge color class.
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
 * @property-read string|null $stage_translated
 * @property-read string $status_translated
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApproval {}
}

namespace App\Models{
/**
 * Department Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1 (Database Schema for departments implies head_of_department_id)
 * Migration context: 2013_11_01_131800_create_departments_table.php uses head_of_department_id
 *
 * @property int $id
 * @property string $name
 * @property string $branch_type Enum: 'state', 'headquarters'
 * @property string|null $code
 * @property string|null $description
 * @property bool $is_active Default true
 * @property int|null $head_of_department_id Foreign key for Head of Department User
 * @property int|null $created_by (Handled by BlameableObserver)
 * @property int|null $updated_by (Handled by BlameableObserver)
 * @property int|null $deleted_by (Handled by BlameableObserver)
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
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereHeadOfDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Department withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepartment {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property string|null $applicant_title Snapshot: Applicant's title (e.g., Encik, Puan)
 * @property string|null $applicant_name Snapshot: Applicant's full name
 * @property string|null $applicant_identification_number Snapshot: Applicant's NRIC
 * @property string|null $applicant_passport_number Snapshot: Applicant's Passport No
 * @property string|null $applicant_jawatan_gred Snapshot: Applicant's Jawatan & Gred text
 * @property string|null $applicant_bahagian_unit Snapshot: Applicant's Bahagian/Unit text
 * @property string|null $applicant_level_aras Snapshot: Applicant's Aras (Level) text
 * @property string|null $applicant_mobile_number Snapshot: Applicant's mobile number
 * @property string|null $applicant_personal_email Snapshot: Applicant's personal email
 * @property string|null $service_status Key for Taraf Perkhidmatan, from User model options
 * @property string|null $appointment_type Key for Pelantikan, from User model options
 * @property string|null $previous_department_name For Kenaikan Pangkat/Pertukaran
 * @property string|null $previous_department_email For Kenaikan Pangkat/Pertukaran
 * @property \Illuminate\Support\Carbon|null $service_start_date For contract/intern
 * @property \Illuminate\Support\Carbon|null $service_end_date For contract/intern
 * @property string|null $purpose Purpose of application / Notes (Tujuan/Catatan)
 * @property string|null $proposed_email Applicant's proposed email or user ID
 * @property string|null $group_email Requested group email address
 * @property string|null $group_admin_name Name of Admin/EO/CC for group email
 * @property string|null $group_admin_email Email of Admin/EO/CC for group email
 * @property int|null $supporting_officer_id FK to users table if system user
 * @property string|null $supporting_officer_name Manually entered supporting officer name
 * @property string|null $supporting_officer_grade Manually entered supporting officer grade
 * @property string|null $supporting_officer_email Manually entered supporting officer email
 * @property string $status
 * @property bool $cert_info_is_true Semua maklumat adalah BENAR
 * @property bool $cert_data_usage_agreed BERSETUJU maklumat diguna pakai oleh BPM
 * @property bool $cert_email_responsibility_agreed BERSETUJU bertanggungjawab ke atas e-mel
 * @property \Illuminate\Support\Carbon|null $certification_timestamp
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property string|null $rejection_reason
 * @property string|null $final_assigned_email
 * @property string|null $final_assigned_user_id
 * @property int|null $processed_by FK to users, IT Admin who processed
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read string $application_type_label
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \App\Models\User|null $processor
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\EmailApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantBahagianUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantIdentificationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantJawatanGred($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantLevelAras($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantPassportNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantPersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereApplicantTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereAppointmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCertDataUsageAgreed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCertEmailResponsibilityAgreed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCertInfoIsTrue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCertificationTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereFinalAssignedEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereFinalAssignedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereGroupAdminEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereGroupAdminName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereGroupEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication wherePreviousDepartmentEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication wherePreviousDepartmentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereProcessedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereProposedEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereServiceEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereServiceStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereServiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSupportingOfficerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSupportingOfficerGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSupportingOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereSupportingOfficerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailApplication {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $equipment_category_id
 * @property int|null $sub_category_id
 * @property string|null $item_code Unique internal identifier (from HRMS template)
 * @property string|null $tag_id MOTAC asset tag / No. Aset (from MOTAC Design)
 * @property string|null $serial_number Manufacturer Serial Number
 * @property string $asset_type Specific type of asset (e.g., laptop, projector - from MOTAC Design)
 * @property string|null $brand
 * @property string|null $model
 * @property string|null $description Detailed description of the equipment
 * @property numeric|null $purchase_price
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string $status Operational status (e.g., available, on_loan - from MOTAC Design)
 * @property string $condition_status Physical condition (e.g., good, fair - from MOTAC Design)
 * @property int|null $location_id
 * @property string|null $current_location Free-text current location details (from MOTAC Design)
 * @property string|null $notes
 * @property string|null $classification Broad classification (from HRMS template)
 * @property string|null $acquisition_type How the equipment was acquired (from HRMS template)
 * @property string|null $funded_by e.g., Project Name, Grant ID (from HRMS template)
 * @property string|null $supplier_name Supplier name (from HRMS template)
 * @property int|null $department_id
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
 * @property-read string $acquisition_type_label
 * @property-read string $asset_type_label
 * @property-read string $brand_model_serial
 * @property-read string $classification_label
 * @property-read string $condition_color_class
 * @property-read string $condition_status_label
 * @property-read string $name
 * @property-read string $status_color_class
 * @property-read string $status_label
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEquipment {}
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereIsActive($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEquipmentCategory {}
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
 * @property int|null $position_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read int|null $positions_count
 * @method static \Database\Factories\GradeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade wherePositionId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGrade {}
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
	#[\AllowDynamicProperties]
	class IdeHelperImport {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id Applicant User ID
 * @property int|null $responsible_officer_id User ID of the officer responsible, if not applicant
 * @property int|null $supporting_officer_id Supporting officer for the application
 * @property string $purpose
 * @property string $location Location where equipment will be used
 * @property string|null $return_location Location where equipment will be returned
 * @property \Illuminate\Support\Carbon $loan_start_date
 * @property \Illuminate\Support\Carbon $loan_end_date
 * @property string $status
 * @property string|null $rejection_reason
 * @property \Illuminate\Support\Carbon|null $applicant_confirmation_timestamp Timestamp for applicant PART 4 confirmation
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $rejected_by
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null $cancelled_by
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null $admin_notes
 * @property int|null $current_approval_officer_id
 * @property string|null $current_approval_stage
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read string $status_color_class
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $loanApplicationItems
 * @property-read int|null $loan_application_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read int|null $loan_transactions_count
 * @property-read \App\Models\User|null $responsibleOfficer
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\LoanApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApplicantConfirmationTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereCancelledBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereCurrentApprovalOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereCurrentApprovalStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereLoanEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereLoanStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereRejectedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereResponsibleOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereReturnLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereSupportingOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoanApplication {}
}

namespace App\Models{
/**
 * Loan Application Item Model.
 * 
 * Represents a type of equipment and quantity requested in a loan application.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $equipment_type
 * @property int $quantity_requested
 * @property int|null $quantity_approved
 * @property int $quantity_issued
 * @property int $quantity_returned
 * @property string $status
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection|LoanTransactionItem[] $loanTransactionItems
 * @property-read string $status_label
 * @property int|null $equipment_id
 * @property-read \App\Models\Equipment|null $equipment
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereEquipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereEquipmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityIssued($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityRequested($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereQuantityReturned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoanApplicationItem {}
}

namespace App\Models{
/**
 * Loan Transaction Model.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.3
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $type
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property int|null $issuing_officer_id
 * @property int|null $receiving_officer_id
 * @property array<array-key, mixed>|null $accessories_checklist_on_issue
 * @property string|null $issue_notes
 * @property \Illuminate\Support\Carbon|null $issue_timestamp Actual moment of physical issue
 * @property int|null $returning_officer_id
 * @property int|null $return_accepting_officer_id
 * @property array<array-key, mixed>|null $accessories_checklist_on_return
 * @property string|null $return_notes
 * @property \Illuminate\Support\Carbon|null $return_timestamp Actual moment of physical return
 * @property int|null $related_transaction_id
 * @property string|null $due_date Applicable for issue transactions
 * @property string $status Status of the transaction itself
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read string $item_name
 * @property-read int $quantity
 * @property-read string $status_color_class
 * @property-read string $status_label
 * @property-read string $type_color_class
 * @property-read string $type_label
 * @property-read \App\Models\User|null $issuingOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read int|null $loan_transaction_items_count
 * @property-read \App\Models\User|null $receivingOfficer
 * @property-read LoanTransaction|null $relatedIssueTransaction
 * @property-read \App\Models\User|null $returnAcceptingOfficer
 * @property-read \App\Models\User|null $returningOfficer
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\LoanTransactionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction query()
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoanTransaction {}
}

namespace App\Models{
/**
 * Loan Transaction Item Model.
 * 
 * Represents a specific equipment item within a loan transaction (either an issue or a return).
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.3
 *
 * @property int $id
 * @property int $loan_transaction_id
 * @property int $equipment_id Specific physical equipment item
 * @property int|null $loan_application_item_id Link to original request line item
 * @property int $quantity_transacted Typically 1 for serialized items
 * @property string $status Status of this item within THIS transaction (e.g., 'issued', 'returned_good')
 * @property string|null $condition_on_return Physical condition of the equipment upon return
 * @property array|null $accessories_checklist_issue Item-specific accessories checklist at the point of issue (JSON)
 * @property array|null $accessories_checklist_return Item-specific accessories checklist at the point of return (JSON)
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
 * @property-read LoanTransactionItem|null $returnRecord
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoanTransactionItem {}
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
 * // Removed Device PHPDoc properties
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
 * @method static Builder<static>|Location whereIsActive($value)
 * @method static Builder<static>|Location whereName($value)
 * @method static Builder<static>|Location whereUpdatedAt($value)
 * @method static Builder<static>|Location whereUpdatedBy($value)
 * @method static Builder<static>|Location withTrashed()
 * @method static Builder<static>|Location withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereState($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLocation {}
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
	#[\AllowDynamicProperties]
	final class IdeHelperNotification {}
}

namespace App\Models{
/**
 * Position Model (Jawatan).
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1, positions table
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
 * @method static \Illuminate\Database\Eloquent\Builder|Position search(string $term) Scope for searching.
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPosition {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRole {}
}

namespace App\Models{
/**
 * Setting Model.
 * 
 * Manages application-wide settings, typically as a single row in the database.
 * Assumes a global BlameableObserver handles created_by, updated_by, deleted_by.
 *
 * @property int $id
 * @property string $site_name
 * @property string|null $site_logo_path
 * @property string $application_name Name of the application
 * @property string|null $default_notification_email_from
 * @property string|null $default_notification_email_name
 * @property string|null $default_system_email Default email for system (non-notification)
 * @property int $default_loan_period_days
 * @property int $max_loan_items_per_application
 * @property string|null $contact_us_email
 * @property bool $system_maintenance_mode
 * @property string|null $system_maintenance_message
 * @property string|null $sms_api_sender
 * @property string|null $sms_api_username
 * @property string|null $sms_api_password
 * @property string|null $terms_and_conditions_loan
 * @property string|null $terms_and_conditions_email
 * @property int|null $created_by (Handled by BlameableObserver)
 * @property int|null $updated_by (Handled by BlameableObserver)
 * @property int|null $deleted_by (Handled by BlameableObserver)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereApplicationName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereContactUsEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefaultLoanPeriodDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefaultNotificationEmailFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefaultNotificationEmailName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDefaultSystemEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereMaxLoanItemsPerApplication($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSiteLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSmsApiUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSystemMaintenanceMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSystemMaintenanceMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTermsAndConditionsEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereTermsAndConditionsLoan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSetting {}
}

namespace App\Models{
/**
 * SubCategory Model.
 * 
 * Defines sub-categories for ICT equipment, linked to EquipmentCategory.
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.3
 * Assumes a global BlameableObserver handles created_by, updated_by, deleted_by.
 *
 * @property int $id
 * @property int $equipment_category_id Foreign key to equipment_categories.id
 * @property string $name Name of the sub-category
 * @property string|null $description Optional description
 * @property bool $is_active Whether the sub-category is active (default: true)
 * @property int|null $created_by (Handled by BlameableObserver)
 * @property int|null $updated_by (Handled by BlameableObserver)
 * @property int|null $deleted_by (Handled by BlameableObserver)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereEquipmentCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSubCategory {}
}

namespace App\Models{
/**
 * User Model for MOTAC System.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.1
 * Migration context: 2013_01_01_000000_create_users_table.php, 2013_11_01_132200_add_motac_columns_to_users_table.php
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
 * @property string|null $service_status (Enum '1','2','3','4' - CRITICAL: Migration's ENUM definition must match all defined constants)
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
 * @property int|null $created_by (Handled by BlameableObserver)
 * @property int|null $updated_by (Handled by BlameableObserver)
 * @property int|null $deleted_by (Handled by BlameableObserver)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\Grade|null $grade
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EmailApplication> $emailApplications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsApplicant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsResponsibleOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsSupportingOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsMade
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property int $is_admin Consider using Spatie roles exclusively.
 * @property int $is_bpm_staff Consider using Spatie roles exclusively.
 * @property int|null $employee_id
 * @property-read int|null $approvals_made_count
 * @property-read User|null $creator
 * @property-read User|null $deleter
 * @property-read int|null $email_applications_count
 * @property-read int|null $loan_applications_as_applicant_count
 * @property-read int|null $loan_applications_as_responsible_officer_count
 * @property-read int|null $loan_applications_as_supporting_officer_count
 * @property-read int|null $notifications_count
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read int|null $tokens_count
 * @property-read User|null $updater
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserIdAssigned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

