<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Show available templates for download from public/template/laporan
     */
    public function index(Request $request)
    {
        $templateDir = public_path('template/laporan');
        $files = [];

        if (is_dir($templateDir)) {
            foreach (scandir($templateDir) as $f) {
                if ($f === '.' || $f === '..') continue;
                if (is_file($templateDir . DIRECTORY_SEPARATOR . $f)) {
                    $files[] = $f;
                }
            }
        }

        return view('laporan.templateDownload', ['files' => $files]);
    }
}
