@extends('layouts.app')

@section('template')
<style>
    group-card {
        display: block;
        /* box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px; */
        border-radius: 4px;
        transition: .2s ease-out .0s;
        color: #7a8e97;
        background: #fff;
        /* padding: 1rem; */
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.15);
        margin-bottom: 2rem;
        overflow:hidden;
    }

    a:hover{
        text-decoration: none;
    }

    group-card:hover {
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 30px;
    }

    group-card > div:first-of-type {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 61.8%;
    }

    group-card > div:first-of-type > shadow-div {
        display: block;
        position: absolute;
        overflow: hidden;
        top:0;
        bottom:0;
        right:0;
        left:0;
    }

    group-card > div:first-of-type > shadow-div > img{
        object-fit: cover;
        width:100%;
        height: 100%;
        transition: .2s ease-out .0s;
    }

    group-card > div:first-of-type > shadow-div > img:hover{
        transform: scale(1.2);
    }

    group-card > div:last-of-type{
        padding:1rem;
    }
    .my-card{
        margin-bottom: 100px;
    }
    .avatar-input{
        opacity: 0;
        width: 100%;
        height: 100%;
        transform: translateY(-40px);
        cursor: pointer;
    }
    .avatar-div{
        width: 70px;
        height: 40px;
        background-color: teal;
        text-align: center;
        line-height: 40px;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 200px;
    }

</style>
<div class="container mundb-standard-container">
    <div class="row">
    </div>
    <div class="card my-card">
        <div class="card-body ">
            <h4 class="card-title"><a>Create a New Group</a></h4>
            <div class="paper-card">
                <form id="extra-info-form md-form">
                    <div class="form-group">
                        <label for="contact" class="bmd-label-floating">Group Name</label>
                        <input type="text" name="contact" class="form-control" id="contact" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="school" class="bmd-label-floating">Group Site</label>
                        <input type="text" name="school" class="form-control"  id="school" autocomplete="off" />
                    </div>
                    <div class="form-group" style="display:flex;align-items:flex-end">
                        <label for="avatar" style="color:grey">Group Avatar</label>
                        <div class="avatar-div" id="avatar">
                            Chose
                            <input class="avatar-input" type="file" accept="image/" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="location" class="bmd-label-floating">Is Public</label>
                        <input type="text" name="location" class="form-control"  id="location" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="location" class="bmd-label-floating">Group Description</label>
                        <input type="text" name="location" class="form-control"  id="location" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="location" class="bmd-label-floating">Join Policy</label>
                        <input type="text" name="location" class="form-control"  id="location" autocomplete="off" />
                    </div>

                </form>
            </div>
            <a href="#" class="btn btn-primary">Submit</a>
        </div>
    </div>
</div>



@endsection
