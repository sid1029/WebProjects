@extends('layouts.master')

@section('content')
    @foreach($users as $user)
        <p><span>{{ $user->name }}</span>--<span>{{ $user->email }}</span></p>
    @endforeach
@stop