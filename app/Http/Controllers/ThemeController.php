<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThemeRequest;
use App\Http\Requests\ThemeRequest;
use App\Http\Requests\UpdateThemeRequest;
use App\Models\Theme;

/**
 * 
 * @group Themes
 * 
 * APIs pour gérer les thèmes
 * 
 */
class ThemeController extends Controller
{
    /**
     * Get themes
     * 
     * Pour obtenir tout les thèmes disponibles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $themes = Theme::all();
        return httpSuccess("Les thèmes", $themes);
    }

    /**
     * 
     * Set theme
     * 
     * Pour mettre à jour le thème d'un utilisateur
     * 
     */
    public function setTheme(ThemeRequest $request)
    {
        $user = $request->user();
        $user->update([
            'theme_id' => $request->theme_id
        ]);
        return httpSuccess("Le thème a été mis à jour", $user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreThemeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreThemeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Theme  $theme
     * @return \Illuminate\Http\Response
     */
    public function show(Theme $theme)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateThemeRequest  $request
     * @param  \App\Models\Theme  $theme
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateThemeRequest $request, Theme $theme)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Theme  $theme
     * @return \Illuminate\Http\Response
     */
    public function destroy(Theme $theme)
    {
        //
    }
}
