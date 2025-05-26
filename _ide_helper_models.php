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
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubCategory> $subCategory
 * @property-read int|null $sub_category_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category withoutTrashed()
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $start_work_hour
 * @property string $end_work_hour
 * @property array $weekends
 * @property int|null $holidays_per_year
 * @property int $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Timeline> $timelines
 * @property-read int|null $timelines_count
 * @method static \Illuminate\Database\Eloquent\Builder|Center newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Center newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Center onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Center query()
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereEndWorkHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereHolidaysPerYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereStartWorkHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereWeekends($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Center withoutTrashed()
 */
	class Center extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $version
 * @property string $title
 * @property string $description
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog withoutTrashed()
 */
	class Changelog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $work_rate
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employees
 * @property-read int|null $employees_count
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereWorkRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract withoutTrashed()
 */
	class Contract extends \Eloquent {}
}

namespace App\Models{
/**
 * Department Model.
 *
 * @property int $id
 * @property string $name
 * @property string $branch_type Enum: 'state', 'headquarters'
 * @property string|null $code
 * @property int|null $head_user_id Foreign key for Head of Department User
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \App\Models\User|null $headOfDepartmentUser Accessor for HOD
 * @property-read \App\Models\User|null $creatorInfo
 * @property-read \App\Models\User|null $updaterInfo
 * @property-read \App\Models\User|null $deleterInfo
 * @property string|null $description
 * @property int $is_active
 * @property int|null $head_of_department_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read int|null $positions_count
 * @property-read int|null $users_count
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
 */
	class Department extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $contract_id
 * @property int|null $department_id
 * @property int|null $position_id
 * @property int|null $grade_id
 * @property string $first_name
 * @property string|null $father_name
 * @property string $last_name
 * @property string|null $mother_name
 * @property string|null $birth_and_place
 * @property string $national_number NRIC or equivalent
 * @property string|null $mobile_number
 * @property string|null $degree
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $notes
 * @property int $balance_leave_allowed
 * @property int $max_leave_allowed
 * @property string|null $delay_counter HH:MM:SS or total seconds/minutes
 * @property string|null $hourly_counter HH:MM:SS or total seconds/minutes
 * @property string|null $employee_id_number
 * @property string|null $employment_type
 * @property string|null $service_status
 * @property string|null $date_of_birth
 * @property string|null $date_of_hire
 * @property string|null $contract_end_date
 * @property string|null $office_phone
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 * @property int $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Contract|null $contract
 * @property-read mixed $current_center
 * @property-read mixed $current_department
 * @property-read mixed $current_position
 * @property-read mixed $full_name
 * @property-read mixed $join_at
 * @property-read mixed $join_at_short_form
 * @property-read mixed $short_name
 * @property-read mixed $worked_years
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Timeline> $timelines
 * @property-read int|null $timelines_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transition> $transitions
 * @property-read int|null $transitions_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Employee checkLeave($employee_id, $leave_id, $from_date, $to_date, $start_at, $end_at)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereBalanceLeaveAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereBirthAndPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereContractEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereContractId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDateOfHire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDelayCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmergencyContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmergencyContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmployeeIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereEmploymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFatherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereHourlyCounter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereMaxLeaveAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereMotherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereNationalNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereOfficePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereServiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Employee withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Employee withoutTrashed()
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * Grade Model.
 *
 * @property int $id
 * @property string $name Example: "41", "N19", "JUSA C"
 * @property int|null $level Numeric representation for comparison (e.g., 41, 19, 54)
 * @property int|null $min_approval_grade_id FK to grades table itself (for approval hierarchy)
 * @property bool $is_approver_grade Can this grade generally act as an approver
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Position> $positions
 * @property-read \App\Models\Grade|null $minApprovalGrade
 * @property-read \App\Models\User|null $creatorInfo
 * @property-read \App\Models\User|null $updaterInfo
 * @property-read \App\Models\User|null $deleterInfo
 * @property-read int|null $positions_count
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereIsApproverGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereMinApprovalGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade withoutTrashed()
 */
	class Grade extends \Eloquent {}
}

namespace App\Models{
/**
 * Position Model.
 *
 * @property int $id
 * @property string $name
 * @property int $grade_id
 * @property int|null $department_id (If positions are department-specific, add to design & migration)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Grade $grade
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \App\Models\User|null $creatorInfo
 * @property-read \App\Models\User|null $updaterInfo
 * @property-read \App\Models\User|null $deleterInfo
 * @property string|null $description
 * @property int|null $vacancies_count
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Position newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Position onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Position query()
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position whereVacanciesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Position withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Position withoutTrashed()
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
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDefaultNotificationEmailFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDefaultNotificationEmailName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSiteLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmsApiPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmsApiSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSmsApiUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTermsAndConditionsEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereTermsAndConditionsLoan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting withoutTrashed()
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $equipment_category_id
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Category|null $category
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereEquipmentCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubCategory withoutTrashed()
 */
	class SubCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $employee_id
 * @property int|null $center_id
 * @property int $department_id
 * @property int $position_id
 * @property string $start_date
 * @property string|null $end_date
 * @property int $is_sequential Indicates if this timeline record follows sequentially from a previous one for the same employee.
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Center|null $center
 * @property-read \App\Models\Department $department
 * @property-read \App\Models\Employee $employee
 * @property-read \App\Models\Position $position
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereIsSequential($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline withoutTrashed()
 */
	class Timeline extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $equipment_id
 * @property int $employee_id
 * @property string|null $handed_date
 * @property string|null $return_date
 * @property string|null $center_document_number
 * @property string|null $reason
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Employee $employee
 * @method static \Illuminate\Database\Eloquent\Builder|Transition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Transition query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereCenterDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereEquipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereHandedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereReturnDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transition withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Transition withoutTrashed()
 */
	class Transition extends \Eloquent {}
}

namespace App\Models{
/**
 * User Model for MOTAC System.
 *
 * @property int $id
 * @property string|null $title
 * @property string $name
 * @property string|null $identification_number NRIC
 * @property string|null $passport_number
 * @property string|null $profile_photo_path Used by HasProfilePhoto trait
 * @property int|null $position_id
 * @property int|null $grade_id
 * @property int|null $department_id
 * @property string|null $level Aras/Floor
 * @property string|null $mobile_number
 * @property string $email Unique personal email, used for login
 * @property string|null $motac_email Official MOTAC email
 * @property string|null $user_id_assigned Assigned User ID (e.g., for network access)
 * @property string|null $service_status Enum from SERVICE_STATUS_TYPES // Consider using PHP 8.1 Enums: ServiceStatusEnum::class
 * @property string|null $appointment_type Enum from APPOINTMENT_TYPES // Consider using PHP 8.1 Enums: AppointmentTypeEnum::class
 * @property string|null $previous_department_name
 * @property string|null $previous_department_email
 * @property string $password
 * @property string $status Enum from STATUS_OPTIONS (active, inactive) // Consider using PHP 8.1 Enums: UserStatusEnum::class
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by Foreign key to users table
 * @property int|null $updated_by Foreign key to users table
 * @property int|null $deleted_by Foreign key to users table
 * @property-read string $profile_photo_url Accessor from HasProfilePhoto
 * @property-read string|null $nric Accessor for identification_number
 * @property-read Department|null $department
 * @property-read Grade|null $grade
 * @property-read Position|null $position
 * @property-read \Illuminate\Database\Eloquent\Collection<int, EmailApplication> $emailApplications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsApplicant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsResponsibleOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, LoanApplication> $loanApplicationsAsSupportingOfficer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Approval> $approvalsMade As Approving Officer
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read User|null $creatorInfo
 * @property-read User|null $updaterInfo
 * @property-read User|null $deleterInfo
 * @property int|null $employee_id
 * @property string|null $full_name
 * @property string|null $personal_email
 * @property int $is_admin
 * @property int $is_bpm_staff
 * @property-read int|null $approvals_made_count
 * @property-read int|null $email_applications_count
 * @property-read int|null $loan_applications_as_applicant_count
 * @property-read int|null $loan_applications_as_responsible_officer_count
 * @property-read int|null $loan_applications_as_supporting_officer_count
 * @property-read int|null $notifications_count
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAppointmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsBpmStaff($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMotacEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNric($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereServiceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserIdAssigned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

