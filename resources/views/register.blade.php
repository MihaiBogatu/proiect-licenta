@extends("layouts.layout")
@section("title")

	Register Page

@endsection

@section("content")



	<form method="post" action="/register">
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
			<select class = 'form-control' name = 'departament_id'>
				<option value='0'>Fara Departament</option>
				@foreach ($departamente as $value)
					<option value="{{$value->id}}">{{$value->name}}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group">
			<label for="nume">Introdu numele:</label>
			<input type="text" name="nume" class="form-control" id="nume" placeholder="Introdu numele" required>
		</div>
		<div class="form-group">
		<label for="email">Introdu emailul:</label>
		<input type="email" name="email" class="form-control" id="email" placeholder="Introdu email" required>
		</div>
		<div class="form-group">
		<label for="parola">Introdu parola:</label>
		<input type="password" name="parola" id="parola" class="form-control" placeholder="Introdu parola" required>
		</div>
		<div class="form-group">
		<label for="repeta-parola">Repeta parola:</label>
		<input type="password" name="repetaparola" class="form-control" id="repeta-parola" placeholder="Repeta parola" required>
		</div>
		<div class="form-check" id='adjunct-check'>
			<label for="checkbox">
				<input type="checkbox" name="este-adjunct" class="form-check-input" id="checkbox">Esti Adjunct?
			</label>
		</div>


		<input class="btn btn-success" type="submit" name="buton" value="Register">
	</form>



@endsection

@section('addons-down')
<script>
$(document).ready(function(){
	$('#rol').val('director');
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
