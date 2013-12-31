@extends('layouts.master')

@section('content')
        <p>This is the user page for {{ $user->name }}. Contact them at {{ $user->email }}</p>
@stop