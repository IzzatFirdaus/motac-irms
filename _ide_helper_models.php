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
 * @property string $approvable_type
 * @property int $approvable_id
 * @property int $officer_id
 * @property string|null $stage e.g., support_review, admin_review, hod_review
 * @property string $status
 * @property string|null $comments
 * @property \Illuminate\Support\Carbon|null $approval_timestamp
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Model|\Eloquent $approvable
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read string|null $stage_translated
 * @property-read string $status_color_class
 * @property-read string $status_translated
 * @property-read \App\Models\User $officer
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\ApprovalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereApprovableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereApprovableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereApprovalTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval withoutTrashed()
 * @mixin \Eloquent
 */
	class Approval extends \Eloquent {}
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
 * @property string|null $description
 * @property string|null $branch_type Corresponds to MOTAC Negeri/Bahagian distinction
 * @property string|null $code Optional department code
 * @property bool $is_active
 * @property int|null $head_of_department_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read string $branch_type_label
 * @property-read \App\Models\User|null $headOfDepartment
 * @property-read \App\Models\User|null $updater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\DepartmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereBranchType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereHeadOfDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withoutTrashed()
 * @mixin \Eloquent
 */
	class Department extends \Eloquent {}
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
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubCategory> $subCategories
 * @property-read int|null $sub_categories_count
 * @property-read \App\Models\User|null $updater
 * @method static Builder<static>|EquipmentCategory active()
 * @method static \Database\Factories\EquipmentCategoryFactory factory($count = null, $state = [])
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
 * @method static Builder<static>|EquipmentCategory whereIsActive($value)
 * @method static Builder<static>|EquipmentCategory whereName($value)
 * @method static Builder<static>|EquipmentCategory whereUpdatedAt($value)
 * @method static Builder<static>|EquipmentCategory whereUpdatedBy($value)
 * @method static Builder<static>|EquipmentCategory withTrashed()
 * @method static Builder<static>|EquipmentCategory withoutTrashed()
 * @mixin \Eloquent
 */
	class EquipmentCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * Grade Model (Gred Perkhidmatan).
 *
 * @property int $id
 * @property string $name e.g., "41", "N19", "JUSA C"
 * @property int|null $level Numeric level for comparison/sorting
 * @property int|null $position_id
 * @property int|null $min_approval_grade_id
 * @property bool $is_approver_grade Can users of this grade approve applications?
 * @property string|null $description Optional description for the grade
 * @property string|null $service_scheme Optional service scheme, e.g., Perkhidmatan Tadbir dan Diplomatik
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read Grade|null $minApprovalGrade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read int|null $positions_count
 * @property-read \App\Models\User|null $updater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\GradeFactory factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereServiceScheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withoutTrashed()
 * @mixin \Eloquent
 */
	class Grade extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $attachable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment query()
 */
	class HelpdeskAttachment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory query()
 */
	class HelpdeskCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\HelpdeskTicket|null $ticket
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment query()
 */
	class HelpdeskComment extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority query()
 */
	class HelpdeskPriority extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read \App\Models\User|null $applicant
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\HelpdeskCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskComment> $comments
 * @property-read int|null $comments_count
 * @property-read bool $is_overdue
 * @property-read string $priority_color
 * @property-read string $status_color
 * @property-read \App\Models\HelpdeskPriority|null $priority
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket query()
 */
	class HelpdeskTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withoutTrashed()
 * @mixin \Eloquent
 */
	class Import extends \Eloquent {}
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
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $currentApprovalOfficer
 * @property-read \App\Models\User|null $deleter
 * @property-read string|null $effective_return_location
 * @property-read \App\Models\LoanTransaction|null $latest_issue_transaction
 * @property-read string $status_color_class
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplicationItem> $loanApplicationItems
 * @property-read int|null $loan_application_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $loanTransactions
 * @property-read int|null $loan_transactions_count
 * @property-read \App\Models\User|null $rejectedBy
 * @property-read \App\Models\User|null $responsibleOfficer
 * @property-read \App\Models\User|null $supportingOfficer
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication dueInDays(int $days)
 * @method static \Database\Factories\LoanApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication overdue()
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
 */
	class LoanApplication extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $loan_application_id
 * @property int|null $equipment_id
 * @property string $equipment_type e.g., Laptop, Projektor, LCD Monitor
 * @property int $quantity_requested
 * @property int|null $quantity_approved
 * @property int $quantity_issued
 * @property int $quantity_returned Added as per System Design
 * @property string $status Status of this specific requested item
 * @property string|null $notes Specific requirements or remarks by applicant
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Equipment|null $equipment
 * @property-read string $equipment_type_label // Changed from equipment_type_name for Blade compatibility
 * @property-read string $status_label
 * @property-read \App\Models\LoanApplication $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
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
	class LoanApplicationItem extends \Eloquent {}
}

namespace App\Models{
/**
 * 
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
 */
	class LoanTransaction extends \Eloquent {}
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
 * @property int $equipment_id
 * @property int|null $loan_application_item_id Link back to the requested item in application
 * @property int $quantity_transacted Typically 1 for serialized items
 * @property string $status Status of this item in this transaction
 * @property string|null $condition_on_return
 * @property array<array-key, mixed>|null $accessories_checklist_issue
 * @property array<array-key, mixed>|null $accessories_checklist_return
 * @property string|null $item_notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Equipment $equipment
 * @property-read string|null $condition_on_return_translated
 * @property-read string $status_label
 * @property-read string $status_translated
 * @property-read \App\Models\LoanApplicationItem|null $loanApplicationItem
 * @property-read \App\Models\LoanTransaction $loanTransaction
 * @property-read LoanTransactionItem|null $returnRecord
 * @property-read \App\Models\User|null $updater
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
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \App\Models\User|null $updater
 * @method static Builder<static>|Location active()
 * @method static Builder<static>|Location byCity(string $city)
 * @method static Builder<static>|Location byCountry(string $country)
 * @method static \Database\Factories\LocationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Location newModelQuery()
 * @method static Builder<static>|Location newQuery()
 * @method static Builder<static>|Location onlyTrashed()
 * @method static Builder<static>|Location query()
 * @method static Builder<static>|Location whereAddress($value)
 * @method static Builder<static>|Location whereCity($value)
 * @method static Builder<static>|Location whereCountry($value)
 * @method static Builder<static>|Location whereCreatedAt($value)
 * @method static Builder<static>|Location whereCreatedBy($value)
 * @method static Builder<static>|Location whereDeletedAt($value)
 * @method static Builder<static>|Location whereDeletedBy($value)
 * @method static Builder<static>|Location whereDescription($value)
 * @method static Builder<static>|Location whereId($value)
 * @method static Builder<static>|Location whereIsActive($value)
 * @method static Builder<static>|Location whereName($value)
 * @method static Builder<static>|Location wherePostalCode($value)
 * @method static Builder<static>|Location whereState($value)
 * @method static Builder<static>|Location whereUpdatedAt($value)
 * @method static Builder<static>|Location whereUpdatedBy($value)
 * @method static Builder<static>|Location withTrashed()
 * @method static Builder<static>|Location withoutTrashed()
 * @mixin \Eloquent
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
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Model $notifiable
 * @property-read \App\Models\User|null $updater
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
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3) - Section 4.1, positions table
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property int|null $grade_id
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\Grade|null $grade
 * @property-read \App\Models\User|null $updater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\PositionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Position newModelQuery()
 * @method static Builder<static>|Position newQuery()
 * @method static Builder<static>|Position onlyTrashed()
 * @method static Builder<static>|Position query()
 * @method static Builder<static>|Position search(?string $term)
 * @method static Builder<static>|Position whereCreatedAt($value)
 * @method static Builder<static>|Position whereCreatedBy($value)
 * @method static Builder<static>|Position whereDeletedAt($value)
 * @method static Builder<static>|Position whereDeletedBy($value)
 * @method static Builder<static>|Position whereDescription($value)
 * @method static Builder<static>|Position whereGradeId($value)
 * @method static Builder<static>|Position whereId($value)
 * @method static Builder<static>|Position whereIsActive($value)
 * @method static Builder<static>|Position whereName($value)
 * @method static Builder<static>|Position whereUpdatedAt($value)
 * @method static Builder<static>|Position whereUpdatedBy($value)
 * @method static Builder<static>|Position withTrashed()
 * @method static Builder<static>|Position withoutTrashed()
 * @mixin \Eloquent
 */
	class Position extends \Eloquent {}
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
	class Role extends \Eloquent {}
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
 * @property string|null $default_notification_email_from
 * @property string|null $default_notification_email_name
 * @property string|null $sms_api_sender
 * @property string|null $sms_api_username
 * @property string|null $sms_api_password
 * @property string|null $terms_and_conditions_loan
 * @property string|null $terms_and_conditions_email
 * @property string $application_name Official name of the application
 * @property string|null $default_system_email Default email for system-originated non-notification emails
 * @property int $default_loan_period_days Default loan period in days
 * @property int $max_loan_items_per_application Max items per single loan application
 * @property string|null $contact_us_email Email for contact us inquiries
 * @property bool $system_maintenance_mode
 * @property string|null $system_maintenance_message
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
	class Setting extends \Eloquent {}
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
 * @property int $equipment_category_id
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
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \App\Models\EquipmentCategory $equipmentCategory
 * @property-read \App\Models\User|null $updater
 * @method static Builder<static>|SubCategory active()
 * @method static Builder<static>|SubCategory byCategory(int $categoryId)
 * @method static Builder<static>|SubCategory byName(string $name)
 * @method static \Database\Factories\SubCategoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|SubCategory newModelQuery()
 * @method static Builder<static>|SubCategory newQuery()
 * @method static Builder<static>|SubCategory onlyTrashed()
 * @method static Builder<static>|SubCategory query()
 * @method static Builder<static>|SubCategory whereCreatedAt($value)
 * @method static Builder<static>|SubCategory whereCreatedBy($value)
 * @method static Builder<static>|SubCategory whereDeletedAt($value)
 * @method static Builder<static>|SubCategory whereDeletedBy($value)
 * @method static Builder<static>|SubCategory whereDescription($value)
 * @method static Builder<static>|SubCategory whereEquipmentCategoryId($value)
 * @method static Builder<static>|SubCategory whereId($value)
 * @method static Builder<static>|SubCategory whereIsActive($value)
 * @method static Builder<static>|SubCategory whereName($value)
 * @method static Builder<static>|SubCategory whereUpdatedAt($value)
 * @method static Builder<static>|SubCategory whereUpdatedBy($value)
 * @method static Builder<static>|SubCategory withTrashed()
 * @method static Builder<static>|SubCategory withoutTrashed()
 * @mixin \Eloquent
 */
	class SubCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * User Model for MOTAC System.
 * 
 * System Design Reference: MOTAC Integrated Resource Management System (Revision 3.5) - Section 4.1
 * Migration context: 2013_01_01_000000_create_users_table.php, 2013_11_01_132200_add_motac_columns_to_users_table.php
 *
 * @property int $id
 * @property string $name
 * @property string|null $title e.g., Encik, Puan, Dr.
 * @property string|null $identification_number NRIC
 * @property string|null $passport_number
 * @property int|null $department_id
 * @property int|null $position_id
 * @property int|null $grade_id
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsApplicant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsResponsibleOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsSupportingOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsMade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read string|null $profile_photo_url
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Position|null $position
 * @property-read \App\Models\Grade|null $grade
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $level For "Aras" or floor level, as string
 * @property string|null $mobile_number
 * @property string|null $personal_email If distinct from login email
 * @property string|null $motac_email
 * @property string|null $user_id_assigned Assigned User ID if different from email
 * @property string|null $service_status Taraf Perkhidmatan. Keys defined in User model.
 * @property string|null $appointment_type Pelantikan. Keys defined in User model.
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property string $status
 * @property int $is_admin Consider using Spatie roles exclusively.
 * @property int $is_bpm_staff Consider using Spatie roles exclusively.
 * @property int|null $employee_id
 * @property-read int|null $approvals_made_count
 * @property-read string $full_name
 * @property-read int|null $loan_applications_as_applicant_count
 * @property-read int|null $loan_applications_as_responsible_officer_count
 * @property-read int|null $loan_applications_as_supporting_officer_count
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

