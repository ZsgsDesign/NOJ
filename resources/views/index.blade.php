<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" noj-theme="{{config('app.theme')}}">

<head>
    <meta charset="UTF-8">
    <title>{{config('app.name')}}</title>

    <!-- Copyright Information -->
    <meta name="author" content="{{config('version.leader')}}">
    <meta name="organization" content="{{config('version.organization')}}">
    <meta name="developer" content="{{config('version.developers')}}">
    <meta name="version" content="{{version()}} {{config('version.name')}} {{config('version.build')}}">

    <!-- SEO Information -->
    <meta name="keywords" content="NOJ,NJUPT Online Judge,noip,noi,OJ,acm,icpc,onlineJudge,NUPT Online Judge" />
    <meta name="description" itemprop="description" content="NOJ is yet another Online Judge providing you functions like problem solving, discussing, solutions, groups, contests and ranking system." />

    <!-- Share Title -->
    <meta itemprop="name" content="{{config('app.name')}}" />

    <!-- Share Image -->
    <meta itemprop="image" content="{{config('app.logo')}}" />

    <!-- Share Description -->
    <meta itemprop="description" itemprop="description" content="NOJ is yet another Online Judge providing you functions like problem solving, discussing, solutions, groups, contests and ranking system." />

    <!-- Necessarily Declarations -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="alternate icon" type="image/png" href="{{config('app.favicon')}}">

    <!-- OpenSearch -->
    <link rel="search" type="application/opensearchdescription+xml" title="{{config("app.name")}}" href="/opensearch.xml">

    <!-- Mobile Display Declarations -->
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="theme-color" content="{{ getTheme()['primaryColor'] }}">

    <!-- Desktop App Declarations -->
    <meta name="msapplication-TileColor" content="{{ getTheme()['primaryColor'] }}">
    <meta name="msapplication-tap-highlight" content="no">

    <!-- Third-Party Declarations -->
    <meta name="google-site-verification" content="{{ env("GOOGLE_SITE_VERIFICATION") }}" />
    <meta name="baidu-site-verification" content="{{ env("BAIDU_SITE_VERIFICATION") }}" />

    <!-- SPA Resources -->
    @includeIf('spa.resources')
</head>

@includeFirst(['spa.body', 'install'])

</html>
