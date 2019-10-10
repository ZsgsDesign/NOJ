@include('errors.general',[
    'emoji'=>';-&#41;',
    'code'=>503,
    'type'=>'Maintenance Mode',
    'description'=>$exception->getMessage() ?: 'NOJ is now updating or maintaining',
    'tips'=>'Please visit later'
])

