<?php

namespace App\Policies;

use App\Models\CaseModel;
use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    public function upload_Document(User $user, CaseModel $case)
    {
        return $user->id === $case->lead_lawyer_id || $case->team->contains('id', $user->id);
    }
    public function get_documents_for_case(User $user, CaseModel $case)
    {
        return $user->id === $case->lead_lawyer_id || $case->team->contains('id', $user->id);
    }
    public function delete_document(User $user, Document $document)
    {
        // state that the user can delete the document if he is the lead lawyer of the case
        $isLeadLawyer = $user->id === $document->case->lead_lawyer_id;
        // state that the user can delete the document if he is the uploader of the document
        $isUploader = $user->id === $document->uploaded_by;

        return $isLeadLawyer || $isUploader;
    }
}
