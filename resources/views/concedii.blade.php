@extends("layouts.minimal-layout")

@section('addons')
<link rel="stylesheet" href="/fullcalendar-js/main.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
@endsection


@section("content")
		<div class="modal fade" id="concedii-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">Cere un concediu</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">

					<form method="post" autocomplete="off" action="/concedii">
						@csrf


						<h3>Selecteaza intervalul: <span id="start-date"></span> - <span id="end-date"></span> </h3>

						<div class="form-group row mb-3">
							<label class="col-md-3 col-12">Data Inceput:</label>
							<input class="col-md-9 col-12 form-control datepicker" type="text" onchange="verify_input();" name="start-date" required id="start-date-input">
						</div>

						<div class="form-group row mb-3">
							<label class="col-md-3 col-12">Data Sfarsit:</label>
							<input type="text" name="end-date" class="col-md-9 col-12 form-control datepicker" onchange="verify_input();" required id="end-date-input">
						</div>

						<div class='form-group row mb-3'>
							<label class="col-md-3 col-12">Selecteaza tipul:</label>
							<select class="form-control col-md-9 col-12" name='tip-concediu'>
								<option value='odihna'>Odihna</option>
								<option value='parental'>Parental</option>
								<option value='maternitate'>De maternitate</option>
								<option value='medical'>Medical</option>
								<option value='fara-plata'>Fara plata</option>
								<option value='formare-profesionala'>Formare profesionala</option>
								<option value='carantina'>Pentru Carantina</option>
								<option value='alt-motiv'>Alt Motiv</option>
						</select>
						</div>

						<div class='form-group row mb-3'>
							<label class="col-md-3 col-12">Selecteaza inlocuitor</label>
							<select class="form-control col-md-9 col-12" name='inlocuitor-id'>
								<option value='0'>Fara inlocuitor</option>
							@foreach ($users as $value)
								<option value="{{$value->id}}">{{$value->name}}</option>
							@endforeach
						</select>
						</div>
						<p> Mai ai {{$zile_alocate}}@if($user->zile_alocate > 20) de @endif zile alocate  </p>
						<button type="submit" id="submitbtn" class= "btn btn-success">Submit</button>


					</form>
	      </div>

	    </div>
	  </div>
	</div>

	<div class="modal fade" id="asteptare-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Cererile tale in asteptare</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
				@if(count($concedii_asteptare) > 0)
				@foreach ($concedii_asteptare as $value)

				<div class="card col-12">
					<div class="card-header">
						<div class="panel-group">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" href="#collapse1">{{\Carbon\Carbon::parse($value->data_inceput)->format('d/m/y')}} -> {{\Carbon\Carbon::parse($value->data_sfarsit)->format('d/m/y')}}
				- Tip concediu {{$value->tip_concediu}}
</a>
      </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse">

      <div class="panel-footer">
				<form method="post" autocomplete="off" action="/editeaza-concediu">
					@csrf


					<h3>Modifica Concediul: <span id="start-date"></span> - <span id="end-date"></span> </h3>

					<div class="form-group row mb-3">
						<label class="col-md-3 col-12">Data Inceput:</label>
						<input class="col-md-9 col-12 form-control datepicker" type="text" onchange="verify_input();" name="start-date-modify" required id="start-date-modify">
					</div>

					<div class="form-group row mb-3">
						<label class="col-md-3 col-12">Data Sfarsit:</label>
						<input type="text" name="end-date-modify" class="col-md-9 col-12 form-control datepicker" onchange="verify_input();" required id="end-date-modify">
					</div>

					<div class='form-group row mb-3'>
						<label class="col-md-3 col-12">Selecteaza tipul:</label>
						<select class="form-control col-md-9 col-12" name='tip-concediu-modify' id="tip-concediu-modify-select-{{ $value->id }}" >
							<option value='odihna'>Odihna</option>
							<option value='parental'>Parental</option>
							<option value='maternitate'>De maternitate</option>
							<option value='medical'>Medical</option>
							<option value='fara-plata'>Fara plata</option>
							<option value='formare-profesionala'>Formare profesionala</option>
							<option value='carantina'>Pentru Carantina</option>
							<option value='alt-motiv'>Alt Motiv</option>
					</select>
					</div>

					<div class='form-group row mb-3'>
						<label class="col-md-3 col-12">Selecteaza inlocuitor</label>
						<select class="form-control col-md-9 col-12" name='inlocuitor-id-modify' id='inlocuitor-id-modify-{{ $value->id }}'>
							<option value='0'>Fara inlocuitor</option>
						@foreach ($users as $value_u)
							<option value="{{$value_u->id}}">{{$value_u->name}}</option>
						@endforeach
					</select>
					</div>
					<button type="submit" id="submit" class= "btn btn-success">Modifica</button>
					<a href="/sterge-concediu/{{$concedii_asteptare[0]->id}}" class="btn btn-danger">Sterge concediu</a>
				</form>

				<?php
					$concediu_id = $value->id;
					$concediu_schimb = App\Concediu::where('schimb_concediu_id', $concediu_id)->get();
				?>


				@if($value->schimb_concediu_id==null)
				@if(count($concedii_schimb) > 0 && count($concediu_schimb)==0)
				<form method="post" autocomplete="off" class="mt-4" action="/cere-schimb-concediu">
					@csrf
					<input type="hidden" name="concediu-curent" value="{{ $value->id }}">
				<div class='form-group row mb-3'>
					<label class="col-md-3 col-12">Concedii:</label>

					<select class='form-control col-md-9 col-12' name='schimb' id='schimb'>

						@foreach($concedii_schimb as $value)
							<option value='{{$value->id}}'>{{Carbon\Carbon::parse($value->data_inceput)->format('d/m/Y')}}-
								{{Carbon\Carbon::parse($value->data_sfarsit)->format('d/m/Y')}}-{{$value->name}}
							</option>
						@endforeach

					</select>
				</div>
				<button type="submit" class= "btn btn-success">Schimba</button>
			</form>
			@endif
			@else

				<form action="/schimb-concediu" class="mt-4" method="post">
					@csrf
					<?php
					$concediu_schimb = App\Concediu::where('id', $value->schimb_concediu_id)->first();
					$coleg = App\User::where('id', $concediu_schimb->id)->first();
					?>
					Schimb de concediu:{{$coleg->name}}(
					{{Carbon\Carbon::parse($concediu_schimb->data_inceput)->format('d/m/Y')}}-
					{{Carbon\Carbon::parse($concediu_schimb->data_sfarsit)->format('d/m/Y')}})
					<input type="hidden" name="concediu-id" value="{{ $value->id }}">
					<button type="submit" name="action" value="accept" class= "btn btn-success">Accepta</button>
					<button type="submit" name="action" value="refuz" class= "btn btn-danger">Refuza</button>
				</form>

			@endif

			</div>
    </div>
  </div>
</div>
					</div>


				</div>
				@endforeach
			@else
				<p class='text-danger'>Nu ai nici un concediu in asteptare!</p>
			@endif
      </div>
    </div>
  </div>
</div>

	<div class="row">


		<div class="col-12 col-md-1 px-0">
			<button type="button" class="w-100 btn btn-warning float-right mx-3" data-toggle="modal" data-target="#asteptare-modal">
				Concedii in asteptare
			</button>
			<a class="btn btn-danger w-100 float-right mx-3" href="/logout">Logout</a>
		</div>



		<div class="col-12 col-md-10">
			<div id="calendar"></div>
		</div>
		<div class="col-12 col-md-1 px-0">

			<button type="button" class="btn btn-success w-100" data-toggle="modal" data-target="#concedii-modal">
			  Cere un concediu
			</button>

			<a href='/profile' type="button" class="btn btn-primary w-100">
			  Pagina Profil
			</a>
		</div>
	</div>

@endsection


@section('addons-down')
<script src="/fullcalendar-js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>

<script>

	var evenimente_concedii = [];

	function date_diff(start, end){
		return Math.round((end-start)/(1000*60*60*24))
	}

	function addDays(date, days){
		var result = new Date(date);
		result.setDate(result.getDate() + days);
		return result;
	}

	function verify_input(){

		var start_date_val = $("#start-date-input").val();
		var end_date_val = $("#end-date-input").val();

		if(start_date_val==null || start_date_val=='' || end_date_val==null || end_date_val==''){
			return;
		}

		var start_date = new Date(start_date_val);
		var end_date = new Date(end_date_val);

		var now = new Date();

		console.log(start_date);
		console.log(now);

		if(start_date<now){
			alert("Ai selectat o data din trecut!");
			$("#end-date-input").val('');
			$("#start-date-input").val('');
			return;
		}


		if(end_date < start_date){
			alert("Ai selectat o data de inceput mai mare!");
			$("#end-date-input").val('');
			$("#start-date-input").val('');
			return;
		}

		if(date_diff(start_date,end_date) > 21){

			$("#end-date-input").val('');

			alert("Concediul tau depaseste 21 de zile!");
			return;

		}

	}

	$(document).ready(function(){

		$( ".datepicker" ).datepicker({
			autoclose: true,

		});

		evenimente_concedii = [

			@foreach ($concedii as $concediu)
			{
				id: '{{ $concediu->id }}',
				displayEventTime: false,
				title:'Propriul concediu',
				start:"{{ $concediu->data_inceput }}",
				end:"{{ \Carbon\Carbon::parse($concediu->data_sfarsit)->addDay(1) }}",
			},
			@endforeach

			@foreach($concedii_colegi as $value)
			{
				id: '{{ $value->id }}',
				displayEventTime: false,
				title:'{{ $value->name }} @if($value->nume_inlocuitor), Inlocuitor - {{ $value->nume_inlocuitor }} @endif, Tip: {{$value->tip_concediu}}',
				start:"{{ $value->data_inceput }}",
				end:"{{ \Carbon\Carbon::parse($value->data_sfarsit)->addDay(1) }}",
				@if($value->acceptat!=null)
				color:'#038024',
				@else
				color:'#d49100',
				@endif
			},

			@endforeach

		];

		var element = $('#calendar')[0];
		calendar = new FullCalendar.Calendar(element, {
			initialView: 'dayGridMonth',
			events:evenimente_concedii,
			displayEventTime: false,
		});
		calendar.render();

		@foreach ($concedii_asteptare as $value)
		$('#tip-concediu-modify-select-{{ $value->id }}').val('{{ $value->tip_concediu }}')
		$('#inlocuitor-id-modify-{{ $value->id }}').val('{{$value->inlocuitor_id}}')
		@endforeach

	});

</script>

@endsection
