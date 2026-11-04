<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoProfilController extends Controller
{
    public function update(Request $request)
    {
        $request->validateWithBag('foto', [
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $path = $request->file('foto')->store('foto-profil', 'public');

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->forceFill(['foto' => $path])->save();

        return back()->with('success', __('Foto profil diperbarui.'));
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
            $user->forceFill(['foto' => null])->save();
        }

        return back()->with('success', __('Foto profil dihapus.'));
    }
}