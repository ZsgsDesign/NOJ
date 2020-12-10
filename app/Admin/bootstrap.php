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

Admin::css('/static/css/wemd-color-scheme.css');
Admin::css('/static/fonts/mdi-wxss/MDI.css');
Admin::favicon('/favicon.png');
Admin::css('/static/library/highlightjs/styles/atom-one-light.css');
Admin::js('/static/library/highlightjs/highlight.pack.min.js');
Encore\Admin\Form::forget(['map', 'editor']);
app('view')->prependNamespace('admin', resource_path('views/admin'));

