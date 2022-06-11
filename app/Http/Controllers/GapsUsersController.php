<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\GapsUser;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * 
 * @group Liste Gaps Users
 */
class GapsUsersController extends Controller
{
    /**
     * Get profs of my section
     * 
     * Récupère les profs de ma section
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfessorsMySection(Request $request)
    {
        $factulties = $request->user()->gaps_user->courses()->pluck('faculty')->unique()->toArray();

        $courses = Course::whereIn('faculty', $factulties)->get();

        $professors = [];
        foreach ($courses as $course) {
            foreach ($course->professors as $professor) {
                $lessons = $professor->courses()->pluck('name')->unique()->toArray();
                $professor->lessons = [...$lessons];
                $professor->faculty = $course->faculty;
                $professor->faculties = $professor->courses()->pluck('faculty')->unique()->toArray();
                $professors[] = $professor;
            }
        }

        usort($professors, function ($a, $b) {
            return $a["name"] <=> $b["name"];
        });

        $professors = collect($professors)->unique('username')->groupBy('faculty');



        return httpSuccess('Liste des profs', $professors);
    }

    /**
     * 
     * Get students of my courses
     * 
     * Récupère les étudiants de ma section
     * 
     */
    public function getStudentsMyCourses(Request $request)
    {
        $courses = $request->user()->gaps_user->courses;
        return httpSuccess('Liste des étudiants', $courses);
    }
}
