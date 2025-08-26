<?php

namespace App\Actions\Fortify;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Update the given user's profile information.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            // Add any additional rules as needed
            'profile_photo_path' => ['nullable', 'image', 'max:2048'],
        ])->validateWithBag('updateProfileInformation');

        // Only update the profile photo if it's an uploaded file
        if (isset($input['profile_photo_path']) && $input['profile_photo_path'] instanceof UploadedFile) {
            $user->updateProfilePhoto($input['profile_photo_path']);
        }

        // Update other profile fields
        $user->forceFill([
            'name'  => $input['name'],
            'email' => $input['email'],
            // Add other fields as needed
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
