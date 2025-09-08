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
 * Represents an approval task for a polymorphic "approvable" (e.g., LoanApplication).
 * This model is aligned with the updated approvals table which supports richer workflow:
 * - status as string (pending, approved, rejected, canceled, forwarded)
 * - dedicated decision timestamps (approved_at, rejected_at, canceled_at, resubmitted_at)
 * - notes field
 *
 * @property int                             $id
 * @property string                          $approvable_type
 * @property int                             $approvable_id
 * @property int|null                        $loan_application_id
 * @property int|null                        $approver_id
 * @property int|string|null                 $level
 * @property string|null                     $stage
 * @property int                             $officer_id
 * @property string                          $status
 * @property string|null                     $notes
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property \Illuminate\Support\Carbon|null $resubmitted_at
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $approvable
 * @property-read \App\Models\User|null $officer
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @method static \Database\Factories\ApprovalFactory factory($count = null, $state = [])
 * @property-read string $stage_label
 * @property-read string $status_color
 * @property-read string $status_label
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval byOfficer(int $officerId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval canceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval forwarded()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval stage(string $stage)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereApprovableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereApprovableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereOfficerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereRejectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereResubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Approval withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApproval {}
}

namespace App\Models{
/**
 * Department Model.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property string|null                     $branch_type
 * @property string|null                     $code
 * @property bool                            $is_active
 * @property int|null                        $head_of_department_id
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Database\Factories\DepartmentFactory                    factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department search(?string $term)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepartment {}
}

namespace App\Models{
/**
 * Minimal EmailApplication model used for static analysis compatibility.
 * 
 * Real implementation may be more feature rich.
 *
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailApplication query()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailApplication {}
}

namespace App\Models{
/**
 * Equipment model for ICT inventory.
 * 
 * Represents an equipment asset, including all core details and relationships.
 *
 * @property int                             $id
 * @property int|null                        $equipment_category_id
 * @property int|null                        $sub_category_id
 * @property string|null                     $item_code
 * @property string|null                     $tag_id
 * @property string|null                     $serial_number
 * @property string                          $asset_type
 * @property string|null                     $brand
 * @property string|null                     $model
 * @property string|null                     $description
 * @property float|null                      $purchase_price
 * @property \Illuminate\Support\Carbon|null $purchase_date
 * @property \Illuminate\Support\Carbon|null $warranty_expiry_date
 * @property string                          $status
 * @property string|null                     $condition_status
 * @property int|null                        $location_id
 * @property string|null                     $current_location
 * @property string|null                     $notes
 * @property string|null                     $classification
 * @property string|null                     $acquisition_type
 * @property string|null                     $funded_by
 * @property string|null                     $supplier_name
 * @property int|null                        $department_id
 * @property int|null                        $defined_location_id
 * @property int|null                        $current_loan_id
 * @property int|null                        $defined_location_id
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\EquipmentCategory|null $category
 * @property-read \Illuminate\Support\Carbon|null $warranty_end_date
 * @property-read string|null $specifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $currentLoanItem
 * @property-read int|null $current_loan_item_count
 * @property-read \App\Models\Location|null $definedLocation
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\EquipmentCategory|null $equipmentCategory
 * @property-read string $asset_type_label
 * @property-read string $condition_status_label
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read int|null $loan_transaction_items_count
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\SubCategory|null $subCategory
 * @method static \Database\Factories\EquipmentFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment search(string $term)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment filterAssetType(?string $assetType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment filterDepartment(?int $departmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipment filterStatus(?string $status)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEquipment {}
}

namespace App\Models{
/**
 * EquipmentCategory Model.
 * 
 * Represents a type/category of ICT equipment. Used for organizing equipment and subcategories.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubCategory> $subCategories
 * @property-read int|null $sub_categories_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory active()
 * @method static \Database\Factories\EquipmentCategoryFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EquipmentCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEquipmentCategory {}
}

namespace App\Models{
/**
 * Grade Model (Gred Perkhidmatan).
 * 
 * Represents job grades in the system. Used for user profiles, positions, and approval levels.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $level
 * @property int|null                        $position_id
 * @property int|null                        $min_approval_grade_id
 * @property bool                            $is_approver_grade
 * @property string|null                     $description
 * @property string|null                     $service_scheme
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Grade|null $minApprovalGrade
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read int|null $positions_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @method static \Database\Factories\GradeFactory                    factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Grade withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperGrade {}
}

namespace App\Models{
/**
 * HelpdeskAttachment Model.
 * 
 * Stores files attached to helpdesk tickets or comments (polymorphic).
 *
 * @property int                             $id
 * @property string                          $attachable_type
 * @property int                             $attachable_id
 * @property string                          $file_path
 * @property string                          $file_name
 * @property int                             $file_size
 * @property string                          $file_type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Model|\Eloquent $attachable
 * @property-read string $file_url
 * @property-read string $readable_file_size
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment withoutTrashed()
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereAttachableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereAttachableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskAttachment whereUpdatedBy($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHelpdeskAttachment {}
}

namespace App\Models{
/**
 * HelpdeskCategory Model.
 * 
 * Represents categories for helpdesk tickets.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory active()
 * @method static \Database\Factories\HelpdeskCategoryFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHelpdeskCategory {}
}

namespace App\Models{
/**
 * HelpdeskComment Model.
 * 
 * Stores comments on HelpdeskTicket, can be internal or external.
 *
 * @property int    $id
 * @property int    $ticket_id
 * @property int    $helpdesk_ticket_id
 * @property int    $user_id
 * @property string $comment
 * @property bool   $is_internal
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read string $preview
 * @property-read \App\Models\HelpdeskTicket|null $ticket
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment withoutTrashed()
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereIsInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskComment whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHelpdeskComment {}
}

namespace App\Models{
/**
 * HelpdeskPriority Model.
 * 
 * Represents priority levels for helpdesk tickets.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int                             $level
 * @property string|null                     $color_code
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $display_color_code
 * @property-read string $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskTicket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Database\Factories\HelpdeskPriorityFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskPriority withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHelpdeskPriority {}
}

namespace App\Models{
/**
 * HelpdeskTicket Model.
 * 
 * Main ticket model for the Helpdesk system.
 *
 * @property int                             $id
 * @property string                          $title
 * @property string                          $description
 * @property int                             $category_id
 * @property string                          $status
 * @property int                             $priority_id
 * @property int                             $user_id
 * @property int|null                        $assigned_to_user_id
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property string|null                     $resolution_notes
 * @property \Illuminate\Support\Carbon|null $sla_due_at
 * @property int|null                        $closed_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property-read \App\Models\User $applicant
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\HelpdeskCategory $category
 * @property-read \App\Models\User|null $closedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskComment> $comments
 * @property-read int|null $comments_count
 * @property-read bool $is_overdue
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskComment> $latestComment
 * @property-read int|null $latest_comment_count
 * @property-read \App\Models\HelpdeskPriority $priority
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket closed()
 * @method static \Database\Factories\HelpdeskTicketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereAssignedToUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereClosedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereSlaDueAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HelpdeskTicket withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHelpdeskTicket {}
}

namespace App\Models{
/**
 * Import Model.
 * 
 * Handles import job/file tracking for the system.
 *
 * @property int                             $id
 * @property string                          $file_name
 * @property int|null                        $file_size
 * @property string|null                     $file_ext
 * @property string|null                     $file_type
 * @property string|null                     $status
 * @property string|null                     $details
 * @property int|null                        $current
 * @property int|null                        $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperImport {}
}

namespace App\Models{
/**
 * LoanApplication Model.
 * 
 * Represents a loan application for ICT equipment.
 *
 * @property int                             $id
 * @property int                             $user_id
 * @property int|null                        $responsible_officer_id
 * @property int|null                        $supporting_officer_id
 * @property string                          $purpose
 * @property string|null                     $location
 * @property string|null                     $return_location
 * @property \Illuminate\Support\Carbon|null $loan_start_date
 * @property \Illuminate\Support\Carbon|null $loan_end_date
 * @property string                          $status
 * @property string|null                     $rejection_reason
 * @property \Illuminate\Support\Carbon|null $applicant_confirmation_timestamp
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property int|null                        $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $issued_at
 * @property int|null                        $rejected_by
 * @property \Illuminate\Support\Carbon|null $rejected_at
 * @property int|null                        $cancelled_by
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property string|null                     $admin_notes
 * @property int|null                        $current_approval_officer_id
 * @property string|null                     $current_approval_stage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $currentApprovalOfficer
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read string|null $effective_return_location
 * @property-read \App\Models\LoanTransaction|null $latest_issue_transaction
 * @property-read string $status_color_class
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransaction> $transactions
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
 * @method static \Database\Factories\LoanApplicationFactory                    factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication filterCreatedBetween(?string $dateFrom, ?string $dateTo)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication filterDepartment(?int $departmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication filterStatus(?string $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplication search(?string $term)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoanApplication {}
}

namespace App\Models{
/**
 * LoanApplicationItem Model.
 * 
 * Represents a requested equipment type/quantity in a loan application.
 * Each item records the type of equipment, amount requested/approved/issued/returned,
 * and status within the application's approval and issuance workflow.
 *
 * @property int                             $id
 * @property int                             $loan_application_id
 * @property int|null                        $equipment_id
 * @property string                          $equipment_type
 * @property int                             $quantity_requested
 * @property int|null                        $quantity_approved
 * @property int                             $quantity_issued
 * @property int                             $quantity_returned
 * @property string                          $status
 * @property string|null                     $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property-read \App\Models\Equipment|null $equipment
 * @property-read string $equipment_type_label
 * @property-read string $status_label
 * @property-read \App\Models\LoanApplication|null $loanApplication
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanTransactionItem> $loanTransactionItems
 * @property-read int|null $loan_transaction_items_count
 * @method static \Database\Factories\LoanApplicationItemFactory                    factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApplicationItem withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoanApplicationItem {}
}

namespace App\Models{
/**
 * LoanTransaction Model.
 * 
 * Represents an equipment issue or return record for a loan application.
 *
 * @property int                             $id
 * @property int                             $loan_application_id
 * @property string                          $type
 * @property \Illuminate\Support\Carbon|null $transaction_date
 * @property int|null                        $issuing_officer_id
 * @property int|null                        $receiving_officer_id
 * @property array|null                      $accessories_checklist_on_issue
 * @property string|null                     $issue_notes
 * @property \Illuminate\Support\Carbon|null $issue_timestamp
 * @property int|null                        $returning_officer_id
 * @property int|null                        $return_accepting_officer_id
 * @property array|null                      $accessories_checklist_on_return
 * @property string|null                     $return_notes
 * @property \Illuminate\Support\Carbon|null $return_timestamp
 * @property int|null                        $related_transaction_id
 * @property string                          $status
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction filterDateBetween(?string $dateFrom, ?string $dateTo)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction filterType(?string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction filterUser(?int $userId)
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransaction withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoanTransaction {}
}

namespace App\Models{
/**
 * LoanTransactionItem Model.
 * 
 * Represents a specific equipment item within a loan transaction (either an issue or a return).
 * Each record links a transaction (issue/return) with a specific equipment asset,
 * and may be associated with a LoanApplicationItem for workflow tracking.
 *
 * @property int                             $id
 * @property int                             $loan_transaction_id
 * @property int                             $equipment_id
 * @property int|null                        $loan_application_item_id
 * @property int                             $quantity_transacted
 * @property string                          $status
 * @property string|null                     $condition_on_return
 * @property array|string|null               $accessories_checklist_issue
 * @property array|string|null               $accessories_checklist_return
 * @property string|null                     $item_notes
 * @property string|null                     $notes
 * @property int|null                        $quantity_returned
 * @property string|null                     $return_status
 * @property array|string|null               $accessories_checklist_on_return
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\LoanTransaction $loanTransaction
 * @property-read \App\Models\Equipment $equipment
 * @property-read \App\Models\LoanApplicationItem|null $loanApplicationItem
 * @property-read string|null $condition_on_return_translated
 * @property-read string|null $condition_on_transaction
 * @property-read string $status_label
 * @property-read string $status_translated
 * @method static \Database\Factories\LoanTransactionItemFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanTransactionItem withoutTrashed()
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLoanTransactionItem {}
}

namespace App\Models{
/**
 * Location Model.
 * 
 * Represents a physical location or branch for assets/equipment.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property string|null                     $address
 * @property string|null                     $city
 * @property string|null                     $state
 * @property string|null                     $country
 * @property string|null                     $postal_code
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \App\Models\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location byCity(string $city)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location byCountry(string $country)
 * @method static \Database\Factories\LocationFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereUpdatedBy($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLocation {}
}

namespace App\Models{
/**
 * Notification Model.
 * 
 * Stores notification records for notifiable entities (users, etc).
 *
 * @property string                          $id
 * @property string                          $type
 * @property string                          $notifiable_type
 * @property int                             $notifiable_id
 * @property array                           $data
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $notifiable
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @method static Builder<static>|Notification            byNotifiable(\Illuminate\Database\Eloquent\Model $notifiableModel)
 * @method static Builder<static>|Notification            byType(array|string $type)
 * @method static \Database\Factories\NotificationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Notification            newModelQuery()
 * @method static Builder<static>|Notification            newQuery()
 * @method static Builder<static>|Notification            onlyTrashed()
 * @method static Builder<static>|Notification            query()
 * @method static Builder<static>|Notification            read()
 * @method static Builder<static>|Notification            unread()
 * @method static Builder<static>|Notification            withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Notification            withoutTrashed()
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
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperNotification {}
}

namespace App\Models{
/**
 * Position Model (Jawatan).
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property bool                            $is_active
 * @property int|null                        $grade_id
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Grade|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \App\Models\User|null $updater
 * @method static \Database\Factories\PositionFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position search(?string $term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Position withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPosition {}
}

namespace App\Models{
/**
 * Role Model.
 * 
 * Extends Spatie Role for the system, adds custom logic for user relationships.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $guard_name
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
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
 *
 * @property int                             $id
 * @property string                          $site_name
 * @property string|null                     $site_logo_path
 * @property string|null                     $default_notification_email_from
 * @property string|null                     $default_notification_email_name
 * @property string|null                     $sms_api_sender
 * @property string|null                     $sms_api_username
 * @property string|null                     $sms_api_password
 * @property string|null                     $terms_and_conditions_loan
 * @property string|null                     $terms_and_conditions_email
 * @property string                          $application_name
 * @property string|null                     $default_system_email
 * @property int                             $default_loan_period_days
 * @property int                             $max_loan_items_per_application
 * @property string|null                     $contact_us_email
 * @property bool                            $system_maintenance_mode
 * @property string|null                     $system_maintenance_message
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $updater
 * @property-read \App\Models\User|null $deleter
 * @method static \Database\Factories\SettingFactory                    factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting withTrashed(bool $withTrashed = true)
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
 *
 * @property int                             $id
 * @property int                             $equipment_category_id
 * @property string                          $name
 * @property string|null                     $description
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $deleter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Equipment> $equipment
 * @property-read int|null $equipment_count
 * @property-read \App\Models\EquipmentCategory $equipmentCategory
 * @property-read \App\Models\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory byCategory(int $categoryId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory byName(string $name)
 * @method static \Database\Factories\SubCategoryFactory                    factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory query()
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSubCategory {}
}

namespace App\Models{
/**
 * Ticket Model (Helpdesk Ticket).
 * 
 * Represents a helpdesk ticket submitted by users for ICT support.
 *
 * @property int                             $id
 * @property string                          $subject
 * @property string                          $description
 * @property int                             $user_id
 * @property int                             $department_id
 * @property int                             $category_id
 * @property int                             $priority_id
 * @property string                          $status
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property int|null                        $resolved_by
 * @property int|null                        $assigned_to
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Department $department
 * @property-read \App\Models\TicketCategory $category
 * @property-read \App\Models\TicketPriority $priority
 * @property-read \App\Models\User|null $assignee
 * @property-read \App\Models\User|null $resolver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketComment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read int|null $comments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket withoutTrashed()
 * @property string|null $sla_due_at SLA due date
 * @property string|null $resolution_notes
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereResolutionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereResolvedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereSlaDueAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTicket {}
}

namespace App\Models{
/**
 * TicketAttachment Model (Helpdesk Ticket Attachment).
 * 
 * Represents file attachments for helpdesk tickets and comments.
 *
 * @property int                             $id
 * @property int                             $ticket_id
 * @property int|null                        $comment_id
 * @property int                             $user_id
 * @property string                          $filename
 * @property string                          $filepath
 * @property string|null                     $mime_type
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Ticket $ticket
 * @property-read \App\Models\TicketComment|null $comment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment withoutTrashed()
 * @property int|null $size File size in bytes
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereFilepath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketAttachment whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTicketAttachment {}
}

namespace App\Models{
/**
 * TicketCategory Model (Helpdesk Ticket Category).
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketCategory whereUpdatedBy($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTicketCategory {}
}

namespace App\Models{
/**
 * TicketComment Model (Helpdesk Ticket Comment).
 * 
 * Represents comments left on helpdesk tickets.
 *
 * @property int                             $id
 * @property int                             $ticket_id
 * @property int                             $user_id
 * @property string                          $comment
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Ticket $ticket
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TicketAttachment[] $attachments
 * @property-read int|null $attachments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment withoutTrashed()
 * @property int $is_internal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereIsInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketComment whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTicketComment {}
}

namespace App\Models{
/**
 * TicketPriority Model (Helpdesk Ticket Priority).
 * 
 * Represents priority levels for helpdesk tickets (e.g., Low, Medium, High, Critical).
 *
 * @property int                             $id
 * @property string                          $name
 * @property string|null                     $description
 * @property int                             $level
 * @property bool                            $is_active
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority withoutTrashed()
 * @property string|null $color_code UI color (hex)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereColorCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TicketPriority whereUpdatedBy($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTicketPriority {}
}

namespace App\Models{
/**
 * User Model for MOTAC System.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $email
 * @property string|null                     $title
 * @property string|null                     $identification_number
 * @property string|null                     $passport_number
 * @property int|null                        $department_id
 * @property int|null                        $position_id
 * @property int|null                        $grade_id
 * @property string|null                     $phone_number
 * @property string                          $status
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string                          $password
 * @property string|null                     $remember_token
 * @property string|null                     $two_factor_secret
 * @property string|null                     $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $deactivated_at
 * @property string|null                     $mobile_number
 * @property string|null                     $motac_email
 * @property string|null                     $jawatan_gred
 * @property string|null                     $bahagian_unit
 * @property string|null                     $previous_department_name
 * @property string|null                     $previous_department_email
 * @property int|null                        $created_by
 * @property int|null                        $updated_by
 * @property int|null                        $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $profile_photo_url
 * @property-read string $full_name
 * @property string|null                                                                                               $preferred_locale
 * @property string|null                                                                                               $motac_email
 * @property \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @method static \Illuminate\Database\Eloquent\Builder whereHasRole(string $role)
 * @property string|null $level                   For "Aras" or floor level, as string
 * @property string|null $personal_email          If distinct from login email
 * @property string|null $user_id_assigned        Assigned User ID if different from email
 * @property string|null $service_status          Taraf Perkhidmatan. Keys defined in User model.
 * @property string|null $appointment_type        Pelantikan. Keys defined in User model.
 * @property int         $is_admin                Consider using Spatie roles exclusively.
 * @property int         $is_bpm_staff            Consider using Spatie roles exclusively.
 * @property string|null $profile_photo_path
 * @property int|null    $employee_id
 * @property string|null $two_factor_confirmed_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsAsApprover
 * @property-read int|null $approvals_as_approver_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvalsAssigned
 * @property-read int|null $approvals_assigned_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $assignedTickets
 * @property-read int|null $assigned_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $createdLoans
 * @property-read int|null $created_loans_count
 * @property-read User|null $creator
 * @property-read User|null $deleter
 * @property-read \App\Models\Department|null $department
 * @property-read \App\Models\Grade|null $grade
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $loanApplicationsAsApplicant
 * @property-read int|null $loan_applications_as_applicant_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $responsibleForLoans
 * @property-read int|null $responsible_for_loans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketAttachment> $ticketAttachments
 * @property-read int|null $ticket_attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TicketComment> $ticketComments
 * @property-read int|null $ticket_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read User|null $updater
 * 
 * Instance helpers from Spatie\Permission HasRoles/HasPermissions
 * @method bool                                               hasRole(string|array $roles, string|null $guard = null)
 * @method bool                                               hasAnyRole(string|array $roles, string|null $guard = null)
 * @method bool                                               hasAllRoles(string|array $roles, string|null $guard = null)
 * @method bool                                               hasPermissionTo(string|\Spatie\Permission\Contracts\Permission $permission, string|null $guard = null)
 * @method \Illuminate\Support\Collection                     getRoleNames()
 * @method static \Database\Factories\UserFactory                    factory($count = null, $state = [])
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filterDepartment(?int $departmentId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filterGrade(?int $gradeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User filterPosition(?int $positionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User orderByName()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User search(?string $term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

