<script>
function notify(title,body,icon="{{config('app.logo')}}",tag="default"){
    if (window.Notification) {
        showMess(title,body,icon,tag);
    } else {
        console.log("Doesn't Support Web Notifications API");
    }
}

function showMess(title,body,icon,tag){
    if(window.Notification && Notification.permission !== "denied") {
        Notification.requestPermission(function(status) {
            if (status === "granted") {
                var m = new Notification(title, {
                    body: body,
                    tag: tag,
                    icon: icon
                });
                m.onclick = function () {
                    window.focus();
                }
            } else{
                console.log("Doesn't Support Web Notification Entity");
            }
        });
    }
}
</script>
