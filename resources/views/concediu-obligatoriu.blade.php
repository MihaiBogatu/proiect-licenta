@extends('layouts.layout')

@section('title')

  <h3>Concediu pentru {{ $user->name }}</h3>

@endsection


@section('addons')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
@endsection

@section('content')

  <div class="container tect-center">
  </div>

  <form method="post" autocomplete="off" action="/concediu-obligatoriu/{{ $user_id }}">
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
    <p> Mai are {{$user->zile_alocate}} @if($user->zile_alocate > 20) de @endif zi(le) alocate</p>
    <button type="submit" id="submitbtn" class= "btn btn-success">Submit</button>


  </form>


@endsection


@section('addons-down')

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



	});

</script>

@endsection
