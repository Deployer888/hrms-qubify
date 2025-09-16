<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;

class SocialController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param  string  $provider
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = User::where('email', $socialUser->getEmail())->first();
        if ($user) {
            Auth::login($user);
        } else {
            $default_language = \DB::table('settings')->select('value')->where('name', 'default_language')->first();
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(uniqid()), // Generate a random password
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'type' => 'company',
                'lang' => !empty($default_language) ? $default_language->value : '',
                'plan' => 0,
                'created_by' => 1,
            ]);

            $role_r = Role::findByName('company');

            $user->assignRole($role_r);

            event(new Registered($user));

                Auth::login($user);
            }

        return redirect()->intended('/home');
    }
}
