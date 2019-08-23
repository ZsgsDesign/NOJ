<style>
pre{
    background: #1e1e1e;
    border:none;
    color:#fff;
    font-size: 1.5rem;
}
.d-none{
    display: none;
}
</style>
<div class="box box-info">
    <!-- /.box-header -->
    <div class="box-header with-border">
        <i class="fa fa-terminal"></i>
    </div>
    <div class="box-body">
        <div id="successful-box" class="d-none">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>Finished</h3>

                    <p>{{$extension}} Operation Successful.</p>
                </div>
                <div class="icon">
                    <i class="MDI bookmark-check"></i>
                </div>
            </div>
        </div>
        <div id="error-box" class="d-none">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>Error</h3>

                    <p></p>
                </div>
                <div class="icon">
                    <i class="MDI alert-circle"></i>
                </div>
            </div>
        </div>
        <pre id="main-section"></pre>
    </div>
    <!-- /.box-body -->
</div>

<script>
    function execute() {
        if (!window.XMLHttpRequest){
            $("#error-box").removeClass('d-none');
            $("#error-box .inner p").text("Your browser does not support the native XMLHttpRequest object.");
            return;
        }
        try{
            var xhr = new XMLHttpRequest();
            var $i=0;
            xhr.previous_text = '';
            xhr.onerror = function() {
                $("#error-box").removeClass('d-none');
                $("#error-box .inner p").text("[XHR] Fatal Error.");
            };
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
                            $('#main-section').append(`[${new Date().toLocaleString('en')}] ${result.data.message}`);
                            xhr.previous_text = xhr.responseText;
                        }
                    }
                    if (xhr.readyState == 4){
                        $("#successful-box").removeClass('d-none');
                    }
                }
                catch (e){
                    $("#error-box").removeClass('d-none');
                    $("#error-box .inner p").text("[XHR STATECHANGE] Exception: " + e);
                }
            };
            xhr.open("POST", location.href, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
            xhr.send();
        }
        catch (e){
            $("#error-box").removeClass('d-none');
            $("#error-box .inner p").text("[XHR REQUEST] Exception: " + e);
        }
    }

    if (typeof jQuery == 'undefined') {
        window.addEventListener('load', function(){ execute(); });
    }else{
        execute();
    }

</script>
