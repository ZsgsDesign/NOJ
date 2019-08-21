<div class="box">

    <style>
        .extension-action a{
            cursor: pointer;
            font-size: 0.8em;
        }
        .extension-action a::after{
            content:" | ";
            display: inline;
            color: #ddd;
        }
        .extension-action a:last-of-type:after{
            display:none;
        }
        .extension-title{
            font-weight: 900;
            margin-bottom: 0;
            font-size: 1.1em;
        }
        .extension-title i{
            font-size: 0.9em;
        }
        .extension-description{
            margin-bottom:0.5em;
        }
    </style>

    <!-- /.box-header -->
    <div class="box-body no-padding">
        <table class="table table-striped table-hover">
            <tbody>
                <tr>
                    <th>Extension</th>
                    <th>Description</th>
                </tr>
                @foreach($installedExtensionList as $extension)
                    <tr>
                        <td>
                            <p class="extension-title">@if($extension["details"]["official"])<i class="MDI marker-check wemd-light-blue-text"></i>@endif {{$extension["details"]["name"]}}</p>
                            <p class="extension-action">
                                @if($extension["status"]==1)
                                    <a onclick="alert('php artisan babel:install {{$extension["details"]["code"]}}')">Install</a>
                                    @if($extension["updatable"])
                                        <a onclick="alert('php artisan babel:update {{$extension["details"]["code"]}}')">Update</a>
                                    @endif
                                    @if($extension["details"]["code"]!="noj")
                                        <a onclick="alert('php artisan babel:uninstall {{$extension["details"]["code"]}}')" class="text-danger">Delete</a>
                                    @endif
                                @elseif($extension["status"]==2)
                                    @if($extension["available"])
                                        <a>Disable</a>
                                    @else
                                        <a>Enable</a>
                                    @endif
                                    @if($extension["settings"])
                                        <a>Settings</a>
                                    @endif
                                    @if($extension["updatable"])
                                        <a onclick="alert('php artisan babel:update {{$extension["details"]["code"]}}')">Update</a>
                                    @endif
                                    @if($extension["details"]["code"]!="noj")
                                        <a  onclick="alert('php artisan babel:uninstall {{$extension["details"]["code"]}}')" class="text-danger">Delete</a>
                                    @endif
                                @endif
                            </p>
                        </td>
                        <td>
                            <p class="extension-description">{{$extension["details"]["description"]}}</p>
                            <p class="wemd-grey-text">Version {{$extension["version"]}} - {{$extension["details"]["typeParsed"]}}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
</div>

<script>
    function updateExtension(extension){
        if (!window.XMLHttpRequest){
            console.error("Your browser does not support the native XMLHttpRequest object.");
            return;
        }
        try{
            var xhr = new XMLHttpRequest();
            var $i=0;
            xhr.previous_text = '';
            xhr.onerror = function() { console.warn("[XHR] Fatal Error."); };
            xhr.onreadystatechange = function() {
                try{
                    if (xhr.readyState > 2){
                        var responseArr = xhr.responseText.split("\n");
                        while(1){
                            try{
                                var result = JSON.parse(responseArr[$i]);
                            }catch(e){
                                break;
                            }
                            $i++;
                            console.log(result.data.message);
                            xhr.previous_text = xhr.responseText;
                        }
                    }
                    if (xhr.readyState == 4){
                        console.log('[XHR] Done')
                    }
                }
                catch (e){
                    console.warn("[XHR STATECHANGE] Exception: " + e);
                }
            };
            xhr.open("POST", `/admin/babel/update/${extension}`, true);
            xhr.send();
        }
        catch (e){
            console.warn("[XHR REQUEST] Exception: " + e);
        }
    }

</script>
