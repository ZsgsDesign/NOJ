@extends('layouts.app')

@section('template')
<style>
.error-container{
    min-height: 40vh;
    min-width: 500px;
    margin: 0 auto;
    display: flex;
    align-items: center;
}
.error-container-left{
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding-right: 0;
}
footer {
    bottom: 0;
    position: absolute;
    width: 100%;
}
.error-container-right {
    margin-left: 30px;
}
.error-container-right > img {
    width: 100px;
    height: 100px;
    border-radius: 10px;
}
.error-title{
    color:gray;
    margin-bottom: 30px;
}
@media screen and (max-width: 1160px){
    footer{
        position: inherit;
    }
    .error-container{
        flex-direction:column-reverse;
    }
    .error-container{
        min-width: 0;
        width: 80%;
    }
    .error-container-right{
        margin-bottom: 80px;
        margin-top: 30px;
    }
    .error-container-left{
        margin-bottom: 300px;
    }
}
</style>

<div class="error-container">
    <div class="error-container-left">
        <div class="error-title"><span style="font-weight:500;color:black">404. </span>That’s an error.</div>
        <div class="error-description">The requested URL was not found on this server.</div>
        <div class="error-description" style="color:gray">That’s all we know.</div>
    </div>
    <div class="error-container-right">
        <img src="/static/img/avatar/noj.png" alt="">
    </div>
</div>

@endsection
