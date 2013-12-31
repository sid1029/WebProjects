@extends('locations.master')

@section('content')
	<h1>Create a Location</h1>

	<!-- if there are creation errors, they will show here -->
	{{ HTML::ul($errors->all()) }}

	{{ Form::open(array('url' => 'locations', 'class' => 'form-horizontal', 'role' => 'form')) }}

		<div class="form-group">
			{{ Form::label('name', 'Name') }}
			{{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
		</div>

		<div class="form-group form-inline">
			{{ Form::label('lat', 'Latitude') }}
			{{ Form::text('lat', Input::old('lat'), array('class' => 'form-control')) }}

			{{ Form::label('lng', 'Longitude') }}
			{{ Form::text('lng', Input::old('lng'), array('class' => 'form-control')) }}
		</div>

		<div class="form-group">
			{{ Form::label('address', 'Address') }}
			{{ Form::textarea('address', Input::old('address'), array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Create the Location!', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop