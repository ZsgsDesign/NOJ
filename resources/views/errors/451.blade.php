@include('errors.general',[
    'emoji'=>':-&#40;',
    'code'=>451,
    'type'=>$type ?? 'Unavailable For Legal Reasons',
    'description'=>$description ?? 'Access to this resource on the server is denied!'
])
