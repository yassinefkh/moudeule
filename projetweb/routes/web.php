<?php

use Illuminate\Support\Facades\Route;

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

// Route qui verifie si l'utilisateur est actif avant de lui permettre d'accéder à la page principale
Route::get('/', function () {
    return view('main');
})->middleware('ensureUserIsActive')->name('main');

Route::get('/about', function () {
    return view('about');
})->name('about');

// Route pour la page de connexion
Route::get('/login', [App\Http\Controllers\AuthenticatedSessionController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('throttle');

// Route pour la soumission du formulaire de connexion
Route::post('/login', [App\Http\Controllers\AuthenticatedSessionController::class, 'login']);

// Route pour la déconnexion
Route::post('/logout', [App\Http\Controllers\AuthenticatedSessionController::class, 'logout'])
    ->name('logout');

Route::get('/register', [App\Http\Controllers\RegisterController::class, 'showRegistrationForm'])
    ->name('register');
Route::post('/register', [App\Http\Controllers\RegisterController::class, 'register']);

// Groupe de routes pour les utilisateurs connectés
Route::group(['middleware' => ['auth']], function () {

    Route::get('/logout', [App\Http\Controllers\AuthenticatedSessionController::class, 'logout'])->name('logout');
    Route::get('/profil', [App\Http\Controllers\UserController::class, 'profil'])->name('profil');
    Route::get('/changepassword', [App\Http\Controllers\UserController::class, 'showChangePasswordForm'])->name('user.changePassword');
    Route::post('/changepassword', [App\Http\Controllers\UserController::class, 'changePassword']);
    Route::get('/etudiant/planning/{week?}', [App\Http\Controllers\SessionController::class, 'studentPlanning'])->name('planning.student_planning');
    Route::put('/user/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');
    Route::get('/student/courses', [App\Http\Controllers\CourseController::class, 'studentCourses'])->name('student.courses');
    Route::post('/student/courses/{id}/enroll', [App\Http\Controllers\CourseController::class, 'enroll'])->name('student.enroll');
    Route::post('/student/courses/{id}/unenroll', [App\Http\Controllers\CourseController::class, 'unenroll'])->name('student.unenroll');
    Route::get('/planning', [App\Http\Controllers\SessionController::class, 'index'])->name('planning.index');
    Route::get('/student-planning', 'App\Http\Controllers\SessionController@studentPlanning')->name('planning.student_sessions');
    Route::get('/student-planning-table', 'App\Http\Controllers\SessionController@studentPlanningTable')->name('planning.student_sessionsTable');
    Route::get('/forum', [App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
    Route::get('/forum/create', [App\Http\Controllers\PostController::class, 'create'])->name('posts.create');
    Route::post('/forum', [App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
    Route::post('/reactions', [App\Http\Controllers\ReactionController::class, 'react'])->middleware('auth')->name('reactions.react');
    Route::post('/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::get('/forum/{id}', [App\Http\Controllers\PostController::class, 'show'])->name('posts.show');
    Route::get('/formations/{id}', [App\Http\Controllers\FormationController::class, 'show'])->name('formations.show');
    Route::get('/documents/{id}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
    Route::get('/cours/{id}', [App\Http\Controllers\CourseController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('cours.show');


});

// Groupe de routes pour les utilisateurs administrateurs
Route::middleware(['auth', 'is_admin'])->group(function () {

    Route::view('/admin', 'admin.home')->name('admin.home');
    Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.users.index');
    Route::resource('formations', App\Http\Controllers\FormationController::class);
    Route::resource('admin/formations', App\Http\Controllers\FormationController::class)->except(['show']);
    Route::get('admin/formations/create', [App\Http\Controllers\FormationController::class, 'create'])->name('admin.formations.create');
    Route::post('admin/formations', [App\Http\Controllers\FormationController::class, 'store'])->name('admin.formations.store');
    Route::get('admin/formations', [App\Http\Controllers\FormationController::class, 'index'])->name('admin.formations.index');
    Route::get('/admin/formations/{formation}/edit', [App\Http\Controllers\FormationController::class, 'edit'])->name('admin.formations.edit');
    Route::delete('/admin/formations/{formation}', [App\Http\Controllers\FormationController::class, 'destroy'])->name('admin.formations.destroy');
    Route::put('/admin/formations/{formation}', [App\Http\Controllers\FormationController::class, 'update'])->name('admin.formations.update');
    Route::post('/admin/users/{user}/approve', 'App\Http\Controllers\AdminController@approveUser')->name('admin.users.approve');
    Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/admin/users/{user}/refuse', [App\Http\Controllers\AdminController::class, 'refuseUser'])->name('admin.users.refuse');
    Route::get('/cours', [App\Http\Controllers\CourseController::class, 'index'])->name('cours.index');
    Route::get('/cours/create', [App\Http\Controllers\CourseController::class, 'create'])->name('cours.create');
    Route::post('/cours', [App\Http\Controllers\CourseController::class, 'store'])->name('cours.store');
    Route::get('/cours/{id}/edit', [App\Http\Controllers\CourseController::class, 'edit'])->name('cours.edit');
    Route::put('/cours/{id}', [App\Http\Controllers\CourseController::class, 'update'])->name('cours.update');
    Route::delete('/cours/{id}', [App\Http\Controllers\CourseController::class, 'destroy'])->name('cours.destroy');
    Route::put('/cours/{id}', [App\Http\Controllers\CourseController::class, 'update'])->name('cours.update');
    Route::get('/admin/users/{user}/changepassword', [App\Http\Controllers\AdminController::class, 'showChangePasswordUserForm'])->name('admin.users.changePasswordForm');
    Route::post('/admin/users/{user}/changepassword', [App\Http\Controllers\AdminController::class, 'changePasswordUser'])->name('admin.users.changepassword');
    Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'showCreateUserForm'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::put('/admin/users/{user}/type', [App\Http\Controllers\AdminController::class, 'updateType'])->name('admin.users.update.type');
    Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/admin/formations/{formation}/confirm-destroy', [
        'uses' => 'App\Http\Controllers\FormationController@confirmDestroy',
        'as' => 'admin.formations.confirm-destroy'
    ]);
});

// Groupe de routes pour les utilisateurs administrateur ou enseignant 
Route::middleware(['auth', 'is_admin_ou_enseignant'])->group(function () {

    Route::get('/planning/{session}/edit', [App\Http\Controllers\SessionController::class, 'edit'])->name('planning.edit');
    Route::put('/planning/{session}', [App\Http\Controllers\SessionController::class, 'update'])->name('planning.update');
    Route::delete('/planning/{session}', [App\Http\Controllers\SessionController::class, 'destroy'])->name('planning.destroy');
    Route::get('/planning', [App\Http\Controllers\SessionController::class, 'index'])->name('planning.index');
    Route::get('/planning/create', [App\Http\Controllers\SessionController::class, 'create'])->name('planning.create');
    Route::post('/planning', [App\Http\Controllers\SessionController::class, 'store'])->name('planning.store');
    Route::delete('/planning/{session}', [App\Http\Controllers\SessionController::class, 'destroy'])->name('planning.destroy');
    Route::get('/planning', [App\Http\Controllers\SessionController::class, 'index'])->name('planning.index');


    Route::prefix('cours/{cours}')->middleware(['auth', 'is_admin_ou_enseignant'])->group(function () {
        Route::get('/documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');

        Route::get('/annonces', [App\Http\Controllers\AnnonceController::class, 'index'])->name('annonces.index');
        Route::post('/annonces', [App\Http\Controllers\AnnonceController::class, 'store'])->name('annonces.store');
        Route::delete('/annonces/{annonce}', [App\Http\Controllers\AnnonceController::class, 'destroy'])->name('annonces.destroy');

        Route::get('/controles/create', [App\Http\Controllers\ControleController::class, 'create'])->name('controles.create');
        Route::post('/controles', [App\Http\Controllers\ControleController::class, 'store'])->name('controles.store');
        Route::get('/controles/{controle}/edit', [App\Http\Controllers\ControleController::class, 'edit'])->name('controles.edit');
        Route::put('/controles/{controle}', [App\Http\Controllers\ControleController::class, 'update'])->name('controles.update');
        Route::delete('/controles/{controle}', [App\Http\Controllers\ControleController::class, 'destroy'])->name('controles.destroy');
        Route::get('/controles', [App\Http\Controllers\ControleController::class, 'index'])->name('controles.index');
        Route::get('/controles/{controle}', [App\Http\Controllers\ControleController::class, 'show'])->name('controles.show');

        Route::get('/controles/{controle}/notes/create', [App\Http\Controllers\NoteController::class, 'create'])->name('notes.create');
        Route::post('/controles/{controle}/notes', [App\Http\Controllers\NoteController::class, 'store'])->name('notes.store');
    });

    Route::post('/sections', [App\Http\Controllers\SectionController::class, 'store'])->name('sections.store')->middleware('auth', 'is_admin_ou_enseignant');
    Route::put('/sections/{section}', [App\Http\Controllers\SectionController::class, 'update'])->name('sections.update')->middleware('auth', 'is_admin_ou_enseignant');
    Route::delete('/sections/{section}', [App\Http\Controllers\SectionController::class, 'destroy'])->name('sections.destroy')->middleware('auth', 'is_admin_ou_enseignant');


});