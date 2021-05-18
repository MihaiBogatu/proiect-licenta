@extends('layouts.layout')

@section('title')
  Gestioneaza Profil
@endsection

@section('content')

  <form method="post" action="/gestionare-profil/{{$user->id}}">
  	@csrf

  	<div class="form-group">
  		<label for="rol">Selecteaza Rolul:</label>
  			<select name="rol" onchange="change_input();" class="form-control" id="rol" required>
  			<option value="angajat">Angajat/Executant</option>
  			<option value="sef-departament">Sef Departament</option>
  			<option value="director" checked>Director</option>
  			<option value="administrator">Administrator</option>
  			</select>
  	</div>

  	<div class="form-group" style="display: none;" id="departament-input-group">
  		<label for="departament_id">Nume departament:</label>
  		<select id="departamente-select" class = 'form-control' name = 'departament_id'>
  			<option value='0'>Fara Departament</option>
  			@foreach ($departamente as $value)

  				<option
          @if($value->id == $user->departament_id)
            checked
          @endif
          value="{{$value->id}}">{{$value->name}}</option>
  			@endforeach
  		</select>
  	</div>

  	<div class="form-check" id='adjunct-check'>
  		<label for="checkbox">
  			<input type="checkbox" @if($user->adjunct == true) checked @endif  name="este-adjunct" class="form-check-input" id="checkbox">Esti Adjunct?
  		</label>
  	</div>


  	<input class="btn btn-success" type="submit" name="buton" value="Register">
  </form>

@endsection

@section('addons-down')
<script>
$(document).ready(function(){
	$('#rol').val('{{ $user->rol }}');
  $('#departamente-select').val({{ $user->departament_id }});
  change_input();
});

function change_input(){
	var opt = $('#rol').val();
	if(opt == 'sef-departament'){
		$('#adjunct-check').hide(250);
		$('#adjunct-check').attr('checked', false);
	}
	else{
		$('#adjunct-check').show(250);
	}
	if(opt=='angajat' || opt=='sef-departament'){
		$('#departament-input-group').show(250);
	}
	else{
		$('#departament-input-group').hide(250);
	}
}

</script>
@endsection
