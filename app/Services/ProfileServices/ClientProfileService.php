<?php

namespace App\Services\ProfileServices;

use App\Contracts\ProfileDataBuilderInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;

class ClientProfileService implements ProfileDataBuilderInterface
{
    public function build($user): array
    {
        return [
            'full_name'   => $user->full_name,
            'email'       => $user->email,
            'phone'       => $user->phone,
            'avatar'      => $user->avatar_path,
            'address'     => $user->address,
            'city'        => $user->city,
            'location'    => ['lat' => $user->lat, 'lng' => $user->lng],
        ];
    }
    public function update($user, $request)
    {
        // $avatarPath = null;
        // if ($request->hasFile('avatar')) {
        //     // delete last avatar
        //     if ($user->avatar_path) {
        //         Storage::disk('public')->delete($user->avatar_path);
        //     }
        //     $path = $request->file('avatar')->store('avatars', 'public');
        //     $avatarPath = Storage::disk('public')->url($path);
        //     $user->update(['avatar_path' => $avatarPath]);
        // }
        if ($request->hasFile('avatar')) {
            $result = app(Cloudinary::class)
                ->uploadApi()
                ->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'lawx/avatars',
                ]);
            $user->update(['avatar_path' => $result['secure_url']]);
        }
        $user->update([
            'email' => $request->input('email') ?? $user->email,
            'phone' => $request->input('phone') ??  $user->phone,
            'address' => $request->input('address') ?? $user->address,
            'city' => $request->input('city') ?? $user->city,
            'lat' => $request->input('lat') ?? $user->lat,
            'lng' => $request->input('lng') ?? $user->lng,
            'password' => $request->input('password') ? Hash::make($request->input('password')) : $user->password
        ]);
    }
}
