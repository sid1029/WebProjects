@extends('layouts.master')

@section('content')
    @foreach($locs as $loc)
        <p><span>{{ $loc->name }}</span>--<span>{{ $loc->address }}</span></p>
    @endforeach
@stop