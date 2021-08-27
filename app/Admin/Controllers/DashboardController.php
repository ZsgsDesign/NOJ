<?php

namespace App\Admin\Controllers;

use App\Models\Update\UpdateModel;
use Illuminate\Support\Arr;
use PharIo\Version\Version;

class DashboardController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function general()
    {
        $version=UpdateModel::checkUpdate();

        $status=[
            ['name' => __("admin.home.version"), 'value' => version()],
            ['name' => __("admin.home.latest"), 'value' => is_null($version) ? 'Failed to fetch latest version' : $version["name"]],
            ['name' => __("admin.home.problems"), 'value' => \App\Models\Eloquent\Problem::count()],
            ['name' => __("admin.home.solutions"), 'value' => \App\Models\Eloquent\ProblemSolution::count()],
            ['name' => __("admin.home.submissions"), 'value' => \App\Models\Eloquent\Submission::count()],
            ['name' => __("admin.home.contests"), 'value' => \App\Models\Eloquent\Contest::count()],
            ['name' => __("admin.home.users"), 'value' => \App\Models\Eloquent\User::count()],
            ['name' => __("admin.home.groups"), 'value' => \App\Models\Eloquent\Group::count()],
        ];

        return view('admin::dashboard.general', [
            'status'=>$status,
            'version'=>$version
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function environment()
    {
        $envs=[
            ['name' => __('admin.home.envs.php'), 'value' => 'PHP/'.PHP_VERSION],
            ['name' => __('admin.home.envs.laravel'), 'value' => app()->version()],
            ['name' => __('admin.home.envs.cgi'), 'value' => php_sapi_name()],
            ['name' => __('admin.home.envs.uname'), 'value' => php_uname()],
            ['name' => __('admin.home.envs.server'), 'value' => Arr::get($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => __('admin.home.envs.cache'), 'value' => config('cache.default')],
            ['name' => __('admin.home.envs.session'), 'value' => config('session.driver')],
            ['name' => __('admin.home.envs.queue'), 'value' => config('queue.default')],

            ['name' => __('admin.home.envs.timezone'), 'value' => config('app.timezone')],
            ['name' => __('admin.home.envs.locale'), 'value' => config('app.locale')],
            ['name' => __('admin.home.envs.env'), 'value' => config('app.env')],
            ['name' => __('admin.home.envs.url'), 'value' => config('app.url')],
            ['name' => __('admin.home.envs.babelMirror'), 'value' => config('babel.mirror')],

            ['name' => __('admin.home.envs.tlsv13'), 'value' => ["Not Supported", "Supported"][in_array("tlsv1.3", stream_get_transports())]],
        ];

        foreach ($envs as &$env) {
            $env['icon']="check-circle";
            $env['color']="wemd-teal-text";
        }

        // PHP Version Check
        $installedVersion=new Version(PHP_VERSION);
        $requireVersion=new Version("7.4.0");
        if (!($installedVersion->isGreaterThan($requireVersion) || $installedVersion->getVersionString()===$requireVersion->getVersionString())) {
            $envs[0]['icon']="close-circle";
            $envs[0]['color']="wemd-pink-text";
        }

        // Cache Driver Check
        if (config('cache.default')!="redis") {
            $envs[5]['icon']="close-circle";
            $envs[5]['color']="wemd-pink-text";
        }

        // Session Driver Check
        if (config('session.driver')!="redis") {
            $envs[6]['icon']="close-circle";
            $envs[6]['color']="wemd-pink-text";
        }

        // Queue Driver Check
        if (config('queue.default')!="database") {
            $envs[7]['icon']="close-circle";
            $envs[7]['color']="wemd-pink-text";
        }

        // Locale Check
        if (!in_array(strtolower(config('app.locale')), ['en', 'zh-cn'])) {
            $envs[9]['icon']="close-circle";
            $envs[9]['color']="wemd-pink-text";
        }

        // TLSv1.3 Check
        if ($envs[12]['value']=="Not Supported") {
            $envs[12]['icon']="close-circle";
            $envs[12]['color']="wemd-pink-text";
        }

        return view('admin::dashboard.environment', compact('envs'));
    }
}
