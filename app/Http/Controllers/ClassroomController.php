<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetUserClassRequest;
use App\Http\Requests\StoreClassRoomRequest;
use App\Http\Requests\UpdateClassRoomRequest;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Get classrooms
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classrooms = Classroom::all();
        return httpSuccess('Classroom', compact('classrooms'));
    }

    public function setUserClassroom(SetUserClassRequest $request)
    {
        $user = $request->user();
        $user->classrooms()->sync($request->name);

        return httpSuccess('Classroom updated', ["classrooms" => $user->classrooms]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClassRoomRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClassRoomRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClassRoom  $classRoom
     * @return \Illuminate\Http\Response
     */
    public function show(ClassRoom $classRoom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClassRoomRequest  $request
     * @param  \App\Models\ClassRoom  $classRoom
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClassRoomRequest $request, ClassRoom $classRoom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClassRoom  $classRoom
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassRoom $classRoom)
    {
        //
    }
}
