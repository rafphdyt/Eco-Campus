<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use App\Models\Institution;
use Illuminate\Http\Request;

class CatalogController extends ApiController
{
    public function institutions()
    {
        $institutions = Institution::query()
            ->orderBy('name')
            ->get(['institution_id', 'name', 'type', 'location']);

        return $this->success('Daftar institusi berhasil diambil', $institutions);
    }

    public function departments(Request $request)
    {
        $data = $this->check($request, [
            'institution_id' => ['sometimes', 'integer', 'exists:institutions,institution_id'],
        ]);

        $departments = Department::query()
            ->when(isset($data['institution_id']), fn ($query) => $query->where('institution_id', $data['institution_id']))
            ->orderBy('faculty')
            ->orderBy('name')
            ->get(['department_id', 'institution_id', 'name', 'faculty']);

        return $this->success('Daftar jurusan berhasil diambil', $departments);
    }
}