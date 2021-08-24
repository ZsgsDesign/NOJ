<?php

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

Admin::css(mix('/static/css/build/noj_admin.css'));
Admin::css('/static/fonts/mdi-wxss/MDI.css?version=1.0.1');
Admin::js(mix('/static/js/build/noj_admin.js'));
Admin::favicon('/favicon.png');
Encore\Admin\Form::forget(['map', 'editor']);
Encore\Admin\Form::extend('chunk_file', \Encore\ChunkFileUpload\ChunkFileField::class);
app('view')->prependNamespace('admin', resource_path('views/admin'));

