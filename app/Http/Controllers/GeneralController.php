<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
    /**
     * Save the image on the storage
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!$request->hasFile('image') || !$request->file('image')->isValid()) {
            abort(500, 'Could not upload the image!');
        }

        $validated = $request->validate([
            'name' => 'string|max:40',
            'image' => 'mimes:png,jpg|max:1014',
        ]);

        $extension = $request->image->extension();

        $request->image->storeAs(
            '/public', $validated['name'].'.'.$extension
        );

        $url = Storage::url($validated['name'].'.'.$extension);

        $file = File::create(['name' => $validated['name'],'url' => $url]);

        Session::flash('success', 'Success!');

        return redirect()->back();
    }

    /**
     * Load view_uploads View
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function viewUploads()
    {
        $images = File::all();
        return view('view_uploads')->with('images', $images);
    }
}
