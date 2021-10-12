<?php

use Encore\Admin\Facades\Admin;

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Admin::css(mix('/static/css/build/app.admin.css'));
Admin::css('/static/fonts/mdi-wxss/MDI.css?version=1.0.1');
Admin::css('/static/fonts/poppins/poppins.css?version=1.0.0');
Admin::style(".main-sidebar, .main-footer, .main-header .logo .logo-lg, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {font-family:'Poppins';}");
Admin::js(mix('/static/js/build/app.admin.js'));
Admin::favicon(config('app.favicon'));
Encore\Admin\Form::forget(['map', 'editor']);
Encore\Admin\Form::extend('chunk_file', \Encore\ChunkFileUpload\ChunkFileField::class);
app('view')->prependNamespace('admin', resource_path('views/admin'));

