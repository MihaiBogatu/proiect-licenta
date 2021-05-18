@extends('layouts.layout')

@section('title')
	Statistici
@endsection

@section('addons')
<link rel="stylesheet" href="/fullcalendar-js/main.css">
@endsection

@section('content')

	<div class="row">
	<canvas id="chart-1" class="col-md-6 col-12"></canvas>

	<div class="col-md-6 col-12" id = "calendar">
	</div>
	</div>

	<div class="row mt-3">
		<div class="col-12 col-md-6">

			@foreach ($evenimente_luna as $key => $value)
				<div class="row p-3 card">
					{{ $evenimente_luna[$key]->name  }} = {{ $evenimente_luna[$key]->data_inceput  }} -> {{ $evenimente_luna[$key]->data_sfarsit }}
				</div>
			@endforeach
		</div>

	</div>

@endsection

@section('addons-down')

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<script src="/fullcalendar-js/main.js"></script>
<script>

	$(document).ready(function(){
		var ctx = document.getElementById('chart-1').getContext('2d');
		var chart = new Chart(ctx, {

			type: 'line',

			data: {
				labels: ['Luna trecuta', 'Luna aceasta'],
				datasets: [{
					label: 'Concedii',
					backgroundColor: 'rgb(255, 99, 105)',
					borderColor: 'rgb(255,0,0)',
					data: [{{ count($evenimente_2luni) }}, {{ count($evenimente_luna) }}],
				}]
			}

		});

		var element = $('#calendar')[0];
		calendar = new FullCalendar.Calendar(element, {
			initialView: 'dayGridMonth',
			displayEventTime: false,
			events: [
				@foreach($evenimente as $eveniment)

				{
					id: '{{ $eveniment->id }}',
					title: '{{ $eveniment->name }} Concediu',
					start: '{{ $eveniment->data_inceput }}',
					end: '{{ Carbon\Carbon::parse($eveniment->data_sfarsit)->addDays(1)->format('Y-m-d') }}',
				},

				@endforeach
			],
		});
		calendar.render();
	});

</script>
@endsection
