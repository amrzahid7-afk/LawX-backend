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
 * @property string $id
 * @property string $case_number
 * @property string $title
 * @property float $price
 * @property string $case_category
 * @property string|null $description
 * @property string $result
 * @property int $importance_stars
 * @property string $billing_type
 * @property numeric|null $collection_percentage
 * @property string $status
 * @property string $financial_status
 * @property int $progress_percentage
 * @property string|null $court
 * @property string|null $lead_lawyer_id
 * @property string|null $office_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\User|null $leadLawyer
 * @property-read \App\Models\Office|null $office
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Opponent> $opponents
 * @property-read int|null $opponents_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Case_payment_milestone> $paymentMilestones
 * @property-read int|null $payment_milestones_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Session> $sessions
 * @property-read int|null $sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $team
 * @property-read int|null $team_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereBillingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereCaseCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereCaseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereCollectionPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereCourt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereFinancialStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereImportanceStars($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereLeadLawyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereProgressPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseModel whereUpdatedAt($value)
 */
	class CaseModel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Office|null $office
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CaseType query()
 */
	class CaseType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $case_id
 * @property string $user_id
 * @property int $is_primary
 * @property-read \App\Models\CaseModel $case
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_client whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_client whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_client whereUserId($value)
 */
	class Case_client extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $title
 * @property numeric $amount
 * @property \Illuminate\Support\Carbon $due_date
 * @property string $status
 * @property string $case_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CaseModel $case
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_payment_milestone whereUpdatedAt($value)
 */
	class Case_payment_milestone extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $case_id
 * @property string $user_id
 * @property string $role_in_case
 * @property-read \App\Models\CaseModel $case
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_team whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_team whereRoleInCase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Case_team whereUserId($value)
 */
	class Case_team extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\CaseModel|null $case
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Casemillstone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Casemillstone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Casemillstone query()
 */
	class Casemillstone extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 */
	class Client extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientRating query()
 */
	class ClientRating extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $file_path
 * @property string|null $signed_file_path
 * @property string $type
 * @property int|null $file_size
 * @property string|null $case_id
 * @property string|null $invoice_id
 * @property string|null $legal_request_id
 * @property string|null $proposal_id
 * @property string $uploaded_by
 * @property string|null $extracted_text
 * @property array<array-key, mixed>|null $ai_tags
 * @property string|null $ai_summary
 * @property bool $has_summary
 * @property bool $has_versions
 * @property int $version_number
 * @property string|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CaseModel|null $case
 * @property-read \App\Models\Invoice|null $invoice
 * @property-read \App\Models\Legal_request|null $legal_request
 * @property-read Document|null $parent
 * @property-read \App\Models\Proposal|null $proposal
 * @property-read \App\Models\User $uploadedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Document> $versions
 * @property-read int|null $versions_count
 * @method static \Database\Factories\DocumentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereAiSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereAiTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereExtractedText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereHasSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereHasVersions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereLegalRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereProposalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereSignedFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUploadedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereVersionNumber($value)
 */
	class Document extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TemplateVariable> $variables
 * @property-read int|null $variables_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentTemplate query()
 */
	class DocumentTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ESign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ESign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ESign query()
 */
	class ESign extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ESignRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ESignRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ESignRequest query()
 */
	class ESignRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee query()
 */
	class Employee extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EvaluationSetting query()
 */
	class EvaluationSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $task_id
 * @property numeric $amount
 * @property string $description
 * @property string $status
 * @property \Illuminate\Support\Carbon $expense_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Task $task
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereExpenseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Expense whereUpdatedAt($value)
 */
	class Expense extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $template_id
 * @property string $file_id
 * @property string|null $case_id
 * @property string $generated_by
 * @property array<array-key, mixed>|null $used_values
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CaseModel|null $case
 * @property-read \App\Models\Document $file
 * @property-read \App\Models\User $generatedBy
 * @property-read \App\Models\SmartTemplate $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate whereGeneratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GeneratedTemplate whereUsedValues($value)
 */
	class GeneratedTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hearing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hearing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hearing query()
 */
	class Hearing extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string|null $case_id
 * @property string $lawyer_id
 * @property string|null $client_id
 * @property numeric $total_amount
 * @property string $status
 * @property \Illuminate\Support\Carbon $due_date
 * @property string $type
 * @property string|null $stripe_checkout_session_id
 * @property string $payment_gateway
 * @property string|null $items
 * @property string|null $pdf_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CaseModel|null $case
 * @property-read \App\Models\User|null $client
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\User $lawyer
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Salary|null $salary
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereLawyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePdfUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStripeCheckoutSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string|null $user_id
 * @property string|null $supervised_by_lawyer_id
 * @property string|null $employed_in_office_id
 * @property string $national_id
 * @property string|null $full_name
 * @property string|null $birth_date
 * @property string|null $birth_place
 * @property string|null $father_name
 * @property string|null $mother_name
 * @property string|null $mother_family_name
 * @property string|null $civil_registry_number
 * @property string $syndicate_card_number
 * @property string $syndicate_branch
 * @property string $lawyer_state
 * @property \Illuminate\Support\Carbon|null $trainee_approved_independent_at
 * @property bool $is_claimed
 * @property \Illuminate\Support\Carbon|null $claimed_at
 * @property bool $is_suspended
 * @property string|null $created_by
 * @property numeric|null $base_salary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Office|null $employedOffice
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Office> $employedOffices
 * @property-read int|null $employed_offices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OfficeInvitation> $officeInvitations
 * @property-read int|null $office_invitations_count
 * @property-read \App\Models\Office|null $ownedOffice
 * @property-read LawerProfile|null $supervisor
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereBaseSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereBirthPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereCivilRegistryNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereClaimedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereEmployedInOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereFatherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereIsClaimed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereIsSuspended($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereLawyerState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereMotherFamilyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereMotherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereSupervisedByLawyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereSyndicateBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereSyndicateCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereTraineeApprovedIndependentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LawerProfile whereUserId($value)
 */
	class LawerProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LegalEmbedding> $embeddings
 * @property-read int|null $embeddings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegalArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegalArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegalArticle query()
 */
	class LegalArticle extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegalEmbedding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegalEmbedding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LegalEmbedding query()
 */
	class LegalEmbedding extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_category whereUpdatedAt($value)
 */
	class Legal_category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $title_request
 * @property string $description_request
 * @property string $location_request
 * @property numeric|null $budget_min
 * @property numeric|null $budget_max
 * @property string $type
 * @property string $status
 * @property string $client_id
 * @property string|null $assigned_lawyer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $client
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\User|null $lawyer
 * @method static \Database\Factories\LegalRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereAssignedLawyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereBudgetMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereBudgetMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereDescriptionRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereLocationRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereTitleRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legal_request whereUpdatedAt($value)
 */
	class Legal_request extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $title
 * @property string $content
 * @property string|null $law_number
 * @property string|null $category_id
 * @property string $status
 * @property string $source
 * @property string $country
 * @property \Illuminate\Support\Carbon|null $effective_date
 * @property array<array-key, mixed>|null $embedding
 * @property array<array-key, mixed>|null $related_legislation_ids
 * @property array<array-key, mixed>|null $articles
 * @property string|null $book
 * @property string|null $part
 * @property string|null $chapter
 * @property string|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Legal_category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Legislation> $children
 * @property-read int|null $children_count
 * @property-read Legislation|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereArticles($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereBook($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereChapter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereEffectiveDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereLawNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation wherePart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereRelatedLegislationIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Legislation whereUpdatedAt($value)
 */
	class Legislation extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $type
 * @property string $notifiable_type
 * @property string $notifiable_id
 * @property string $data
 * @property string|null $read_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereNotifiableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereNotifiableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $owner_lawyer_profile_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LawerProfile> $employees
 * @property-read int|null $employees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OfficeInvitation> $invitations
 * @property-read int|null $invitations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CaseModel> $office_cases
 * @property-read int|null $office_cases_count
 * @property-read \App\Models\LawerProfile $owner
 * @property-read \App\Models\User|null $reviewedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereOwnerLawyerProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereUpdatedAt($value)
 */
	class Office extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeAddOn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeAddOn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeAddOn query()
 */
	class OfficeAddOn extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $office_id
 * @property string $invited_lawyer_profile_id
 * @property string|null $invited_by
 * @property numeric|null $salary_expected
 * @property string $status
 * @property string|null $responded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LawerProfile $invitedLawyer
 * @property-read \App\Models\Office $office
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereInvitedLawyerProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereRespondedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereSalaryExpected($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OfficeInvitation whereUpdatedAt($value)
 */
	class OfficeInvitation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string|null $national_id
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CaseModel> $cases
 * @property-read int|null $cases_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opponent whereUpdatedAt($value)
 */
	class Opponent extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubscriptionPayment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Subscription|null $subscription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OwnerInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OwnerInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OwnerInvoice query()
 */
	class OwnerInvoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $email
 * @property string $token
 * @property string|null $expired_at
 * @property string $type
 * @property string|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PasswordResetToken whereType($value)
 */
	class PasswordResetToken extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $invoice_id
 * @property string|null $transaction_id
 * @property string $gateway
 * @property numeric $amount_paid
 * @property string $status
 * @property array<array-key, mixed>|null $gateway_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereGatewayResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Invoice|null $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentInstallment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentInstallment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentInstallment query()
 */
	class PaymentInstallment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $tokenable_type
 * @property string $tokenable_id
 * @property string $name
 * @property string $token
 * @property array<array-key, mixed>|null $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $tokenable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereAbilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereTokenableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereTokenableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereUpdatedAt($value)
 */
	class PersonalAccessToken extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SystemFeature> $features
 * @property-read int|null $features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 */
	class Plan extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature query()
 */
	class PlanFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog query()
 */
	class PointsLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property numeric $price
 * @property string $estimated_days
 * @property string $message_for_client
 * @property \Illuminate\Support\Carbon $valid_until
 * @property string $payment_terms
 * @property string $status
 * @property string $price_currency
 * @property string $request_id
 * @property string $lawer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\User $lawyer
 * @property-read \App\Models\Legal_request $request
 * @method static \Database\Factories\ProposalFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereEstimatedDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereLawerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereMessageForClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal wherePriceCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Proposal whereValidUntil($value)
 */
	class Proposal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $lawyer_id
 * @property string $client_id
 * @property string $case_id
 * @property int $professionalism_rating
 * @property int $communication_rating
 * @property int $result_rating
 * @property float $rating
 * @property string|null $comment
 * @property int $is_hidden
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CaseModel $case
 * @property-read \App\Models\User $client
 * @property-read \App\Models\User $lawyer
 * @method static \Database\Factories\ReviewFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCommunicationRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereIsHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereLawyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereProfessionalismRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereResultRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $slug
 * @property string $label_ar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereLabelAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $lawyer_id
 * @property int $month
 * @property int $year
 * @property numeric $base_salary
 * @property numeric $total_approved_expenses
 * @property numeric $net_salary
 * @property string $status
 * @property string|null $invoice_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice|null $invoice
 * @property-read \App\Models\User $lawyer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereBaseSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereLawyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereNetSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereTotalApprovedExpenses($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Salary whereYear($value)
 */
	class Salary extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $case_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $location
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property string $nots
 * @property string|null $result
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CaseModel $case
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereNots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereUpdatedAt($value)
 */
	class Session extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $lawyer_id
 * @property string $document_id
 * @property string|null $envelope_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Document $document
 * @property-read \App\Models\User $lawyer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Signer> $signers
 * @property-read int|null $signers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest whereEnvelopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest whereLawyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SignatureRequest whereUpdatedAt($value)
 */
	class SignatureRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $signature_request_id
 * @property string $user_id
 * @property string $role
 * @property bool $is_seal
 * @property int $x_position
 * @property int $y_position
 * @property int $width
 * @property int $height
 * @property string $sign_type
 * @property string|null $anchor_text
 * @property int $page_number
 * @property string|null $docusign_recipient_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $signed_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\SignatureRequest $request
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereAnchorText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereDocusignRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereIsSeal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer wherePageNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereSignType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereSignatureRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereSignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereXPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Signer whereYPosition($value)
 */
	class Signer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $title
 * @property string $template_desc
 * @property string $file_id
 * @property array<array-key, mixed>|null $variables_json
 * @property string|null $owner_id
 * @property string $category
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Document $file
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GeneratedTemplate> $generatedFiles
 * @property-read int|null $generated_files_count
 * @property-read \App\Models\User|null $owner
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate accessibleBy($ownerId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate privateOf($ownerId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereTemplateDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmartTemplate whereVariablesJson($value)
 */
	class SmartTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OwnerInvoice> $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\Office|null $office
 * @property-read \App\Models\Plan|null $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\OwnerInvoice|null $invoice
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPayment query()
 */
	class SubscriptionPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuperAdmin query()
 */
	class SuperAdmin extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Plan> $plans
 * @property-read int|null $plans_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemFeature query()
 */
	class SystemFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $case_id
 * @property string $title
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string $assigned_to
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $assignedTo
 * @property-read \App\Models\CaseModel $case
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expenses
 * @property-read int|null $expenses_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 */
	class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateVariable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateVariable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateVariable query()
 */
	class TemplateVariable extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $full_name
 * @property string $email
 * @property string $password
 * @property int $role_id
 * @property bool $is_active
 * @property string|null $address
 * @property string|null $city
 * @property string|null $lat
 * @property string|null $lng
 * @property int|null $experience_years
 * @property string|null $bio
 * @property string|null $phone
 * @property string|null $national_id
 * @property string|null $avatar_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CaseModel> $cases
 * @property-read int|null $cases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CaseModel> $clientCases
 * @property-read int|null $client_cases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $client_invoices
 * @property-read int|null $client_invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \App\Models\LawerProfile|null $lawyerProfile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $lawyer_invoices
 * @property-read int|null $lawyer_invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Legal_category> $legalCategories
 * @property-read int|null $legal_categories_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviewsGiven
 * @property-read int|null $reviews_given_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviewsReceived
 * @property-read int|null $reviews_received_count
 * @property-read \App\Models\Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Salary> $salaryRecords
 * @property-read int|null $salary_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereExperienceYears($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNationalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string $permission_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission wherePermissionKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPermission whereUserId($value)
 */
	class UserPermission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string $code
 * @property string $type
 * @property string $expires_at
 * @property int $is_used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereIsUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VerificationCode whereUserId($value)
 */
	class VerificationCode extends \Eloquent {}
}

