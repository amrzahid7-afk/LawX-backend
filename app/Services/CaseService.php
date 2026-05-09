<?php

namespace App\Services;

use App\Models\CaseModel;

use function Laravel\Prompts\select;

class CaseService
{
    public function createCase($data)
    {
        $user = auth('api')->user();
        CaseModel::create([
            'client_id' => $data['client_id'],
            'lawer_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => 'open'
        ]);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'create case successfully'
            ],
            'code' => '200'
        ];
    }
    public function updateCase($data)
    {
        $updated_case = CaseModel::where("id", $data['case_id'])->first();;
        $updated_case['status'] = $data['status'] ?? $updated_case['status'];
        $updated_case['title'] = $data['title'] ?? $updated_case['title'];
        $updated_case['description'] = $data['description'] ?? $updated_case['description'];
        $updated_case->save();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'update case successfully'
            ],
            'code' => '200'
        ];
    }
    public function get_cases()
    {
        $user = auth('api')->user();
        $column = '';
        if ($user->role->name == 'admin') {
            $cases = CaseModel::select(['status', 'title', 'description', 'id'])->get();
        } else {
            switch ($user->role->name) {
                case 'client':
                    $column = 'client_id';
                    break;
                case 'lawer':
                    $column = 'lawer_id';
                    break;
            }
            $cases = CaseModel::where($column, $user->id)
                // ->where('client_id', $user->id)
                ->select(['status', 'title', 'description', 'id'])
                ->get();
        }

        return [
            'status' => 'success',
            'data' => [
                'message' => 'get your cases successfully',
                'cases' => $cases
            ],
            'code' => '200'
        ];
    }
}
