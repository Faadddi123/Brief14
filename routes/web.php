<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

 
Route::get('/auth/redirect/{provider}', function (Request $request, $provider) {
    
    if (!in_array($provider, ['github', 'google', 'facebook', 'etc'])) {
        abort(404); // Provider not found
    }

    return Socialite::driver($provider)->redirect();
});

Route::get('/auth/callback', function (Request $request) {
    $provider = $request->query('provider');
    $providerUser = Socialite::driver($provider)->user();
    $user = User::updateOrCreate([
        $provider . '_id' => $providerUser->id,
    ], [
        'name' => $providerUser->name,
        'email' => $providerUser->email,
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});










Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
