@extends('layouts.app')

@section('template')
<style>
    .image-container{
        text-align: center;
    }

    .input-group-text{
        margin-right: 1rem;
        font-weight: 900;
        color: rgba(0,0,0,0.42);
        font-weight: 500;
        font-family: 'Poppins';
    }
    .input-group-text i{
        margin-right: 0.5rem;
        display: inline-block;
        transform: scale(1.2);
    }

    paper-card {
        display: block;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        padding: 1rem;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
    }

    noj-raised-tabs{
        display: block;
        margin: 2rem 0;
    }

    noj-raised-tabs > paper-card{
        border: none;
        box-shadow: 0 2px 5px 0 rgb(0 0 0 / 16%), 0 2px 10px 0 rgb(0 0 0 / 12%);
    }

    .noj-raised-tabs-nav{
        position: relative;
        z-index: 1;
        padding: .7rem;
        margin-right: 1rem;
        margin-bottom: -20px;
        margin-left: 1rem;
        background-color: #2bbbad;
        border: 0;
        border-radius: .25rem;
        -webkit-box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15);
        box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15);
        justify-content: center;
    }

    .noj-raised-tabs-nav .nav-link {
        display: block;
        padding: .5rem 1rem;
    }

    .noj-raised-tabs-nav .nav-item+.nav-item {
        margin-left: 0
    }

    .noj-raised-tabs-nav .nav-item.disabled {
        pointer-events: none !important
    }

    .noj-raised-tabs-nav .nav-item.disabled .nav-link {
        color: #6c757d
    }

    .noj-raised-tabs-nav .nav-link {
        color: #fff;
        border: 0;
        -webkit-transition: all .4s;
        transition: all .4s
    }

    .noj-raised-tabs-nav .nav-link.active,.noj-raised-tabs-nav .nav-item.open .nav-link {
        color: #fff;
        background-color: rgba(0,0,0,0.2);
        border-radius: .25rem;
        -webkit-transition: all 1s;
        transition: all 1s
    }

    .noj-raised-tabs-nav .nav-item.show .nav-link {
        color: #fff;
        background-color: #2bbbad;
        border-radius: .25rem;
        -webkit-transition: all 1s;
        transition: all 1s
    }

    .noj-raised-tabs-nav .nav-item.show .nav-link.dropdown-toggle {
        background-color: rgba(0,0,0,0.2)
    }
</style>
<div class="container mundb-standard-container">
    <div>
        <div class="img-show-box">
            <div class="image-container">
                <a href="{{$image->relative_path}}" class="fancybox" rel="gallery1">
                    <img class="image" src="{{$image->relative_path}}" alt="{{$image->relative_path}}" title="{{$image->relative_path}}" style="max-width: 100%">
                </a>
            </div>

            <noj-raised-tabs>
                <ul class="nav nav-tabs noj-raised-tabs-nav" id="urlTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="relative-tab" data-toggle="tab" href="#relative" role="tab" aria-controls="relative"
                        aria-selected="true">{{__('imagehosting.detail.relative')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="absolute-tab" data-toggle="tab" href="#absolute" role="tab" aria-controls="absolute"
                        aria-selected="false">{{__('imagehosting.detail.absolute')}}</a>
                    </li>
                </ul>
                <paper-card class="tab-content pt-5" id="urlTabContent">
                    <div class="tab-pane fade show active" id="relative" role="tabpanel" aria-labelledby="relative-tab">
                        <div class="form-group">
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="MDI code-not-equal-variant"></i> {{__('imagehosting.detail.bbcode')}}</div>
                                </div>
                                <input type="text" class="form-control" onclick="this.select();" value="[URL={{$image->relative_path}}][IMG]{{$image->relative_path}}[/IMG][/URL]">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="MDI language-html"></i> {{__('imagehosting.detail.html')}}</div>
                                </div>
                                <input type="text" class="form-control" id="sharegigya" onclick="this.select();" value="<a href=&quot;{{$image->relative_path}}&quot; target=&quot;_blank&quot;><img src=&quot;{{$image->relative_path}}&quot; alt=&quot;{{$image->relative_path}}&quot;></a>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="MDI markdown"></i> {{__('imagehosting.detail.markdown')}}</div>
                                </div>
                                <input type="text" class="form-control" onclick="this.select();" value="![{{$image->relative_path}}]({{$image->relative_path}})">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="MDI link-variant"></i> {{__('imagehosting.detail.imageurl')}}</div>
                                </div>
                                <input type="text" class="form-control" onclick="this.select();" value="{{$image->relative_path}}">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="absolute" role="tabpanel" aria-labelledby="absolute-tab">
                        <div class="form-group">
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="MDI code-not-equal-variant"></i> {{__('imagehosting.detail.bbcode')}}</div>
                                </div>
                                <input type="text" class="form-control" onclick="this.select();" value="[URL={{$image->absolute_path}}][IMG]{{$image->absolute_path}}[/IMG][/URL]">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="MDI language-html"></i> {{__('imagehosting.detail.html')}}</div>
                                </div>
                                <input type="text" class="form-control" id="sharegigya" onclick="this.select();" value="<a href=&quot;{{$image->absolute_path}}&quot; target=&quot;_blank&quot;><img src=&quot;{{$image->absolute_path}}&quot; alt=&quot;{{$image->absolute_path}}&quot;></a>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="MDI markdown"></i> {{__('imagehosting.detail.markdown')}}</div>
                                </div>
                                <input type="text" class="form-control" onclick="this.select();" value="![{{$image->absolute_path}}]({{$image->absolute_path}})">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="MDI link-variant"></i> {{__('imagehosting.detail.imageurl')}}</div>
                                </div>
                                <input type="text" class="form-control" onclick="this.select();" value="{{$image->absolute_path}}">
                            </div>
                        </div>
                    </div>
                </paper-card>
            </noj-raised-tabs>
        </div>
    </div>
</div>
@endsection
