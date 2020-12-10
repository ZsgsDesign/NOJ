@include('errors.general',[
    'emoji' => ';-&#41;',
    'code' => 503,
    'type' => __('errors.http.503.type'),
    'description' => $exception->getMessage() ?: __('errors.http.503.description', ['name' => config('app.name')]),
    'tips' => __('errors.http.503.tips')
])

