<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Mail\CustomMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('profile.edit');
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        auth()->user()->update($request->all());

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {

        try {
            auth()->user()->update(['password' => Hash::make($request->get('password'))]);

            // Mail::to([Auth::user()->email])->send(new CustomMail("Password Update Notification",'update-password',Auth::user()->name,null,null));
            return back()->withPasswordStatus(__('Password successfully updated.'));
        } catch (\Exception $e) {
            // Log::error('Error Sending Email: ' . $e->getMessage());
            return back()->withPasswordStatus(__('Failed to update password'));
        }
        
    }
}
