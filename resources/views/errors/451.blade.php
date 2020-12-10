@include('errors.general',[
    'emoji'=>':-&#40;',
    'code'=>451,
    'type'=>$type ?? __('errors.http.451.type'),
    'description'=>$description ?? __('errors.http.451.description')
])
