@extends('layouts.layout')

@section('title')

@endsection

@section('addons')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
@endsection

@section('content')


  <div class="modal fade" id="editareModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modifica Concediul ({{$user->zile_alocate}} zile ramase)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
          <form method='post' autocomplete="off" action='/concediu-editare/{{ $concediu->id }}'>
            @csrf

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
  						<select class="form-control col-md-9 col-12" name='tip-concediu-modify' id="tip-concediu-modify-select-{{ $concediu->id }}" >
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
  						<select class="form-control col-md-9 col-12" name='inlocuitor-id-modify' id='inlocuitor-id-modify-{{ $concediu->id }}'>
  							<option value='0'>Fara inlocuitor</option>
  						@foreach ($inlocuitori as $value_u)
  							<option value="{{$value_u->id}}">{{$value_u->name}}</option>
  						@endforeach
  					</select>
  					</div>
  					<button type="submit" id="submit" class= "btn btn-success">Modifica</button>
        </form>
        </div>
      </div>
    </div>
  </div>





  <div class='card'>
    <div class='card-header'>
    Concediul lui {{$user->name}}
    </div>
    <div class='card-body'>
      Data Inceput: {{Carbon\Carbon::parse($concediu->data_inceput)->format('d/m/Y')}} -
      Sfarsit: {{Carbon\Carbon::parse($concediu->data_sfarsit)->format('d/m/Y')}}
      @if($concediu->name != null)
      Inlocuitor: {{$concediu->name}}
    @endif
      Tip: {{$concediu->tip_concediu}}
      @if($concediu->motiv != null)
      Motiv: {{$concediu->motiv}}
      @endif
    </div>
    <div class='card-footer'>
      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editareModal">
  Editeaza
</button>
    </div>
  </div>


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

  		var start_date_val = $("#start-date-modify").val();
  		var end_date_val = $("#end-date-modify").val();

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

      $('#tip-concediu-modify-select-{{ $concediu->id }}').val('{{$concediu->tip_concediu}}');
      $('#inlocuitor-id-modify-{{ $concediu->id }}').val('{{$concediu->inlocuitor_id}}');

  	});

  </script>

@endsection
