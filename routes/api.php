<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserController,
    JobController,
    ApplicationController,
    ProjectController,
    ProjectDemoController,
    BlogPostController,
    CommentController, // أضفنا كنترولر التعليقات الجديد
    SkillController,
    ServiceController,
    ExperienceController,
    CourseController,
    ContactController,
    SiteInfoController,
    SocialLinkController,
    TeamMemberController,
    LinkController,
    PackageController,
    PaymentController,
    MailController
};

/*
|--------------------------------------------------------------------------
| 🟢 Public Routes (متاحة للجميع)
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Blog & Content
Route::get('/blog', [BlogPostController::class, 'index']);      // عرض كل المقالات
Route::get('/blog/{blog_post}', [BlogPostController::class, 'show']); // عرض مقال واحد بجميع تعليقاته وردوده
Route::get('/site-info', [SiteInfoController::class, 'index']);
Route::get('/skills', [SkillController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job}', [JobController::class, 'show']);

// Infrastructure
Route::get('/links', [LinkController::class, 'index']);
Route::get('/links/{id}', [LinkController::class, 'show']);
Route::get('/team-members', [TeamMemberController::class, 'index']);
Route::get('/packages', [PackageController::class, 'index']);
Route::get('/projects_demo', [ProjectDemoController::class, 'index']);

// Forms
Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store']);
Route::post('/contact', [ContactController::class, 'store']);
Route::post('/send-mail', [MailController::class, 'sendMail']);


/*
|--------------------------------------------------------------------------
| 🟠 Authenticated Routes (للمسجلين فقط - يوزر وأدمن)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // --- نظام التعليقات والردود ---
    // إضافة تعليق (لو parent_id موجود في الطلب يتحول لرد تلقائياً)
    Route::post('/blog/{blog_post}/comments', [CommentController::class, 'store']); 
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    /*
    |----------------------------------------------------------------------
    | 🔴 Admin Only Routes (الأدمن فقط ✨)
    |----------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {

        // 1. User & Payments
        Route::apiResource('users', UserController::class);
        Route::get('payments', [PaymentController::class, 'index']);
        Route::get('payments/{id}', [PaymentController::class, 'show']);
        Route::post('payments', [PaymentController::class, 'makePayment']);

        // 2. Jobs & Applications
        Route::apiResource('jobs', JobController::class)->except(['index', 'show']);
        Route::get('/jobs/{job}/applications', [ApplicationController::class, 'index']);
        Route::post('/applications/{application}/status', [ApplicationController::class, 'updateStatus']);

        // 3. Content Management (CMS) & Blog Post Creation
        $resources = [
            'site-info'    => SiteInfoController::class,
            'social-links' => SocialLinkController::class,
            'skills'       => SkillController::class,
            'services'     => ServiceController::class,
            'experiences'  => ExperienceController::class,
            'projects'     => ProjectController::class,
            'courses'      => CourseController::class,
            'blog-posts'   => BlogPostController::class, // إدارة المقالات (إنشاء/حذف)
            'team-members' => TeamMemberController::class,
            'packages'     => PackageController::class,
        ];

        foreach ($resources as $uri => $controller) {
            Route::apiResource($uri, $controller)->except(['index', 'show']);
            Route::post("$uri/{{$uri}}", [$controller, 'update']); // لدعم الصور في التعديل
        }

        // 4. Links & Demos
        Route::post('/links', [LinkController::class, 'store']);
        Route::post('/links/{id}', [LinkController::class, 'update']);
        Route::delete('/links/{id}', [LinkController::class, 'destroy']);

        Route::prefix('projects_demo')->group(function () {
            Route::post('/', [ProjectDemoController::class, 'store']);
            Route::post('/{id}', [ProjectDemoController::class, 'update']);
            Route::delete('/{id}', [ProjectDemoController::class, 'destroy']);
        });

        // 5. Contact Management
        Route::get('/contact', [ContactController::class, 'index']);
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);
    });
});