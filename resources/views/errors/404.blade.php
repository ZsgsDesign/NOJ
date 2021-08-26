@include('errors.general',[
    'emoji'=>':-&#40;',
    'code'=>404,
    'type'=>__('errors.http.404.type'),
    'description'=> $exception->getMessage() ?: __('errors.http.404.description'),
    'easter_egg' => true
])
