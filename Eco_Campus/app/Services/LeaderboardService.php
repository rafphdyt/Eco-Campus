<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Collection;

class LeaderboardService
{
    public static function valid(?string $board): string
    {
        return in_array($board, ['orang', 'jurusan', 'fakultas']) ? $board : 'orang';
    }

    public static function get(string $type): Collection
    {
        $students = User::where('role', 'mahasiswa')->with('department')->get();
        $row = fn ($name, $sub, $pts) => (object) ['name' => $name, 'sub' => $sub, 'pts' => (int) $pts];

        if ($type === 'jurusan') {
            $rows = Department::all()->map(fn ($d) => $row($d->name, null,
                $students->where('department_id', $d->department_id)->sum('total_points')));
        } elseif ($type === 'fakultas') {
            $rows = Department::pluck('faculty')->unique()->map(fn ($f) => $row($f, null,
                $students->filter(fn ($u) => $u->department->faculty === $f)->sum('total_points')));
        } else {
            $rows = $students->map(fn ($u) => $row($u->name, $u->department->name, $u->total_points));
        }

        return $rows->sortByDesc('pts')->values();
    }
}