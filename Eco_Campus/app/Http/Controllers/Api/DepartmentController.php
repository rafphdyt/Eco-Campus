<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;

class DepartmentController extends ApiController
{
    // GET /api/departments  (publik, untuk tahu department_id saat register)
    public function index()
    {
        $data = Department::with('institution')->get()->map(fn ($d) => [
            'department_id' => $d->department_id,
            'name' => $d->name,
            'faculty' => $d->faculty,
            'institution' => $d->institution->name,
        ])->all();

        return $this->success('Data jurusan berhasil diambil', $data);
    }
}