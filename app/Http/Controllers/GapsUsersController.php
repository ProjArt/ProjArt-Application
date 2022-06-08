<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\GapsUser;
use App\Models\User;
use Illuminate\Http\Request;

class GapsUsersController extends Controller
{
    /**
     * Get profs of my section
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfessorsMySection(Request $request)
    {
        $factulties = $request->user()->gaps_user->courses()->pluck('faculty')->unique();

        $courses = Course::whereIn('faculty', $factulties)->get();

        $professors = [];
        foreach ($courses as $course) {
            foreach ($course->professors as $professor) {
                $professor->lessons = $professor->courses()->pluck('name')->unique();
                $professor->faculty = $course->faculty;
                $professors[] = $professor;
            }
        }

        $professors = collect($professors)->unique('username')->groupBy('faculty')->toArray();

        usort($professors, function ($a, $b) {
            return $a["name"] <=> $b["name"];
        });

        return httpSuccess('Liste des profs', $professors);
    }

    public function getStudentsMyCourses(Request $request)
    {
        $courses = $request->user()->gaps_user->courses;
        return httpSuccess('Liste des étudiants', $courses);
    }
}
