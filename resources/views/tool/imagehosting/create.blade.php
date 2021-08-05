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

    empty-container p{
        font-size: 1rem;
        color:rgba(0,0,0,0.54);
    }
    .mundb-standard-container{
        display: flex;
        flex-direction: column;
    }
    .mundb-standard-container > div:last-of-type{
        flex-grow: 1;
        flex-shrink: 1;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        position: relative;
    }
    image-choser{
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: block;
        border-color: rgba(0,0,0,0.25);
        border-width: 4px;
        border-style: dashed;
        background: #fff;
        border-radius: 12px;
        text-align: center;
        vertical-align: middle;
        margin-top: 3rem;
        margin-left: 2rem;
        margin-right: 2rem;
        margin-bottom: 1rem;
        box-shadow: rgb(0 0 0 / 10%) 0px 0px 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>
<div class="container mundb-standard-container">
    <div>
        <h1><img src="/static/img/icon/icon-imagehosting.png" style="height:5rem;"></h1>
        <h1>{{__('imagehosting.title')}}</h1>
    </div>
    <div>
        @if($permission)
            <image-choser  class="animated jackInTheBox" ondragenter="drag(this, event);" ondragover="drag(this, event);" ondrop="dragUpload(this, event)" onclick="uploadFile()">
                <empty-container>
                    <i class="MDI cloud-upload wemd-light-blue-text"></i>
                    <p>Drag & drop images here</p>
                </empty-container>
            </image-choser>
            <input type="file" style="display:none" id="uploadFile" accept=".jpg,.png,.jpeg,.gif" onchange="fileChange(this, event);">
        @else
            <empty-container>
                <i class="MDI key-remove"></i>
                <p>{{__("imagehosting.denied")}}</p>
            </empty-container>
        @endif
    </div>
</div>
@endsection

@if($permission)
    @section('additionJS')
        <script>
            var generate_processing=false;

            function drag(that, event) {
                var e = event || window.event;
                e.preventDefault();
                e.stopPropagation();
            }

            function dragUpload(that, event) {
                var e = event || window.event;
                e.preventDefault();
                e.stopPropagation();
                const files = e.dataTransfer.files;
                prepUpload(files);
            }

            function prepUpload(files){
                if(generate_processing) return alert('Already Processing');
                else generate_processing=true;
                console.log(files);
                if (files.length == 0) {
                    return;
                }
                if (files.length > 1) {
                    alert('Please upload one image at a time.');
                    $("#uploadFile").val('');
                    return;
                }
                if (Array.prototype.some.call(files, function(file) {return !['image/jpeg', 'image/jpg', 'image/png', 'image/gif'].includes(file.type)})) {
                    alert('Image format not supported.');
                    $("#uploadFile").val('');
                    return;
                }
                file=files[0];
                upload(file);
            }

            function uploadFile() {
                $("#uploadFile").click();
            }

            function fileChange(that, event) {
                const files = event.target.files;
                prepUpload(files);
            }

            function upload(file){
                if(file == undefined){
                    alert('Unknown File Selector Error.');
                    generate_processing=false;
                    return;
                }

                if(file.size/1024 > 2048){ // 2mb max
                    alert('File Size Limit Exceed.');
                    generate_processing=false;
                    return;
                }

                var image_data = new FormData();
                image_data.append('image',file);

                $.ajax({
                    url : '{{route("tool.ajax.imagehosting.generate")}}',
                    type : 'POST',
                    data : image_data,
                    processData : false,
                    contentType : false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success : function(result){
                        console.log(result);
                        if(result.ret == 200){
                            location.href=result.data.redirect_url;
                        }else{
                            alert(result.desc, "Oops!");
                        }
                        generate_processing=false;
                    },
                    error: function(xhr, type){
                        console.log('Ajax error!');
                        switch(xhr.status) {
                            case 422:
                                alert(xhr.responseJSON.errors[Object.keys(xhr.responseJSON.errors)[0]][0], xhr.responseJSON.message);
                                break;
                            default:
                                alert("Something went wrong","Oops!");
                        }
                        generate_processing = false;
                    }
                });
            }
        </script>
    @endsection
@endif
