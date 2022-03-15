<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware=[
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups=[
        'web' => [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Feature\Check::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],

        'problem.valid:pcode' => [
            'problem.exists:pcode',
            'problem.not_hidden',
            'problem.not_blockaded',
        ],

        'problem.valid:pid' => [
            'problem.exists:pid',
            'problem.not_hidden',
            'problem.not_blockaded',
        ],

    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware=[
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'contest_account' => \App\Http\Middleware\ContestAccount::class,
        'privileged' => \App\Http\Middleware\Privileged::class,

        'group.exist' => \App\Http\Middleware\Group\Exists::class,
        'group.banned' => \App\Http\Middleware\Group\Banned::class,

        'problem.exists' => \App\Http\Middleware\Problem\Exists::class,
        'problem.not_hidden' => \App\Http\Middleware\Problem\NotHidden::class,
        'problem.not_blockaded' => \App\Http\Middleware\Problem\NotBlockaded::class,

        'contest.exists' => \App\Http\Middleware\Contest\Exists::class,
        'contest.desktop' => \App\Http\Middleware\Contest\IsDesktop::class,
        'contest.board.admin.pdfview.clearance' => \App\Http\Middleware\Contest\Board\Admin\PDFView\Clearance::class,
        'contest.challenge.exists' => \App\Http\Middleware\Contest\Challenge\Exists::class,
        'contest.challenge.problem.exists' => \App\Http\Middleware\Contest\Challenge\Problem\Exists::class,

        'user.banned' => \App\Http\Middleware\User\Banned::class,

        'api.contest.clearance' => \App\Http\Middleware\Api\Contest\Clearance::class,
        'api.contest.hasProblem' => \App\Http\Middleware\Api\Contest\HasProblem::class,
        'api.contest.hasCompiler' => \App\Http\Middleware\Api\Contest\HasCompiler::class,

        'api.submission.exist' => \App\Http\Middleware\Api\Submission\Exist::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority=[
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,

        \App\Http\Middleware\Group\Exists::class,
        \App\Http\Middleware\Group\Banned::class,

        \App\Http\Middleware\Api\Contest\Clearance::class,
        \App\Http\Middleware\Api\Contest\HasProblem::class,
        \App\Http\Middleware\Api\Contest\HasCompiler::class,
    ];
}
