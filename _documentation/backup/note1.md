transform MOTAC_IRMS system into ver 4.0

Here’s a thorough and technically grounded plan based on System Design Rev. 3.6 for transforming the
MOTAC_ICT_LOAN_HRMS system into version 4.0, where: ✅ Email/User ID Provisioning will be fully
removed

✅ Helpdesk Support/Ticketing System will be introduced

✅ ICT Equipment Loan stays intact and becomes the central module

🔧 PHASE 1: PREPARATION ✅ 1.1 Project Version Control Create a new branch:

git checkout -b release/v4.0 Tag the current stable version:

git tag v3.6-final ✅ 1.2 Backup Backup:

email_applications and related data

.env configuration

public/storage/ and storage/app/public/

✂️ PHASE 2: REMOVE EMAIL APPLICATION MODULE 🔍 2.1 Audit Email-Related Files Area Items to Remove
Models EmailApplication.php Controllers EmailApplicationController.php, EmailAccountController.php,
Api\EmailProvisioningController.php Services EmailApplicationService.php,
EmailProvisioningService.php Livewire ResourceManagement\MyApplications\Email\*, EmailAccount\*
Views resources/views/email-applications/,
resources/views/emails/application-submitted-notification.blade.php, etc. Routes Routes using
/email-\* Migrations Tables like email_applications, email_accounts Notifications
Email-application-specific classes Config config/motac.php — remove
approval.min_email_supporting_officer_grade_level Lang Files Translation strings under email or
email_application Tests Tests with email provisioning logic

🔥 2.2 Remove & Clean ❌ Routes In routes/web.php, remove:

Route::resource('email-applications', EmailApplicationController::class);
Route::resource('email-accounts', EmailAccountController::class);

❌ Controllers Delete:

App\Http\Controllers\EmailApplicationController

App\Http\Controllers\EmailAccountController

App\Http\Controllers\Api\EmailProvisioningController

❌ Livewire Components Delete components:

App\Livewire\ResourceManagement\MyApplications\Email\
App\Livewire\EmailAccount\

❌ Models & Migrations Delete:

App\Models\EmailApplication

Associated relationships in User.php (e.g., $this->hasMany(EmailApplication::class))

If keeping history, rename migrations or move to database/migrations/legacy/

❌ Services Remove from App\Services:

EmailApplicationService.php

EmailProvisioningService.php

❌ Notifications Delete:

ApplicationSubmitted.php (if email-related)

EmailApplicationNeedsAction.php, etc.

❌ Views Delete:

resources/views/email-applications/ resources/views/emails/application-\*.blade.php

❌ Config & Language Remove unused keys in:

config/motac.php

resources/lang/en/_.php, resources/lang/ms/_.php

❌ Menu & UI Update resources/menu/verticalMenu.json to remove "Email Provisioning"

➕ PHASE 3: ADD HELPDESK MODULE 📐 3.1 Requirements Entity Fields Ticket id, title, description,
category_id, status, priority, user_id, assigned_to, created_at, updated_at Category e.g., Hardware,
Software, Network Comment For threaded responses on tickets

🏗️ 3.2 File Additions ✅ Models App\Models\HelpdeskTicket.php

App\Models\HelpdeskCategory.php

App\Models\HelpdeskComment.php

✅ Migrations php artisan make:model HelpdeskTicket -m php artisan make:model HelpdeskCategory -m
php artisan make:model HelpdeskComment -m

✅ Livewire Components App\Livewire\Helpdesk\TicketForm.php – New Ticket

App\Livewire\Helpdesk\TicketList.php – My Tickets

App\Livewire\Helpdesk\TicketDetail.php – View & Comment

✅ Services App\Services\HelpdeskService.php Handles ticket creation, assignment, comment logging,
etc.

✅ Notifications TicketCreatedNotification.php

TicketAssignedNotification.php

TicketClosedNotification.php

✅ Routes In routes/web.php: Route::middleware(['auth'])->prefix('helpdesk')->group(function () {
Route::get('/', TicketList::class)->name('helpdesk.index'); Route::get('/create',
TicketForm::class)->name('helpdesk.create'); Route::get('/{ticket}',
TicketDetail::class)->name('helpdesk.view'); });

✅ Views resources/views/livewire/helpdesk/

ticket-form.blade.php

ticket-list.blade.php

ticket-detail.blade.php

🧪 PHASE 4: TESTING & VALIDATION ✅ 4.1 Unit & Feature Tests Create:

tests/Feature/Helpdesk/CreateTicketTest.php

tests/Unit/HelpdeskServiceTest.php

✅ 4.2 Manual Testing Checklist Feature Test Status ICT Loan Application ✅ ICT Loan Approval ✅
Equipment Issuance ✅ Equipment Return ✅ Helpdesk Ticket Creation 🔲 Helpdesk Assigning 🔲 Helpdesk
Commenting 🔲

📦 PHASE 5: FINALIZATION ✅ 5.1 Documentation Update:

README.md: Remove email provisioning, add helpdesk section

User Manual: Add Helpdesk usage flow

System Design v4.0.pdf: New architecture, data models, user flow

✅ 5.2 Staging Deployment Deploy release/v4.0 to staging URL

Final UAT by BPM/Admins

✅ 5.3 Production Rollout Merge to main

Deploy with:

php artisan migrate --force

✅ Summary Task Outcome Email module fully removed ✅ Helpdesk module added ✅ Database & UI cleaned
✅ System refocused to ICT loan & support ✅

Would you like me to generate a Markdown or PDF-formatted migration plan for team circulation?
