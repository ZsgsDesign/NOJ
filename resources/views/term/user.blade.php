@extends('layouts.app')

@section('template')
    <style>
        term-container{
            display: block;
            font-family: 'Roboto Slab';
            line-height: 1.5;
        }

        term-container h2{
            margin-top: 3rem;
            margin-bottom: 1rem;
        }

        term-container hr{
            margin-top: 3rem;
            margin-bottom: 1rem;
        }
    </style>
    <div class="container mundb-standard-container">
        <term-container>
            @include('term.content.user')
        </term-container>
    </div>
@endsection
