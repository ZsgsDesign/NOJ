@extends('layouts.app')

@section('template')
<style>
    h1{
        font-family: Raleway;
        font-weight: 100;
        text-align: center;
    }
    empty-container{
        display:block;
        text-align: center;
        margin-bottom: 2rem;
    }

    empty-container i{
        font-size:5rem;
        color:rgba(0,0,0,0.42);
    }
</style>
<div class="container mundb-standard-container">
    <div>
        <h1><img src="/static/img/icon/icon-imagehosting.png" style="height:5rem;"></h1>
        <h1>{{__('imagehosting.title')}}</h1>
        <p class="text-center" style="margin: 3rem 0;"><a class="btn btn-primary btn-raised" href="{{route('tool.imagehosting.create')}}"><i class="MDI upload"></i> {{__('imagehosting.list.button')}}</a></p>
    </div>
    @if(blank($images))
        <empty-container>
            <i class="MDI package-variant"></i>
            <p>{{__('imagehosting.list.empty')}}</p>
        </empty-container>
    @else
        <div class="table-responsive text-nowrap mb-5">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{__('imagehosting.list.path')}}</th>
                        <th scope="col">{{__('imagehosting.list.time')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($images as $image)
                    <tr>
                        <th scope="row">{{$image->id}}</th>
                        <td><a href="{{route('tool.imagehosting.detail',['id'=>$image->id])}}">{{$image->relative_path}}</a></td>
                        <td>{{$image->created_at}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
      @endif
</div>
@endsection
