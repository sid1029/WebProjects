@extends('locations.master')

@section('content')
	<h1>All the locations</h1>

	<!-- will be used to show any messages -->
	@if (Session::has('message'))
		<div class="alert alert-info">{{ Session::get('message') }}</div>
	@endif

	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<td>Name</td>
				<td>Lat</td>
				<td>Lng</td>
				<td>Address</td>
				<td>Actions</td>
			</tr>
		</thead>
		<tbody>
		@foreach($locs as $key => $value)
			<tr>
				<td>{{ $value->name }}</td>
				<td>{{ $value->lat }}</td>
				<td>{{ $value->lng }}</td>
				<td>{{ $value->address }}</td>

				<!-- we will also add show, edit, and delete buttons -->
				<td>

					<!-- delete the nerd (uses the destroy method DESTROY /locations/{id} -->
					<!-- we will add this later since its a little more complicated than the other two buttons -->

					<!-- show the nerd (uses the show method found at GET /locations/{id} -->
					<a class="btn btn-small btn-success" href="{{ URL::to('locations/' . $value->id) }}">Show this Location</a>

					<!-- edit this nerd (uses the edit method found at GET /locations/{id}/edit -->
					<a class="btn btn-small btn-info" href="{{ URL::to('locations/' . $value->id . '/edit') }}">Edit this Location</a>

				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@stop