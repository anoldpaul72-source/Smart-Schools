<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch application language between English and Kiswahili.
     */
    public function switch($locale)
    {
        if (in_array($locale, ['en', 'sw'])) {
            session(['locale' => $locale]);
            cookie()->queue('locale', $locale, 60 * 24 * 365);
        }

        return back();
    }
}
