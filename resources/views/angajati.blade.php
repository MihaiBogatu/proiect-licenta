@extends("layouts.layout")
@section("title")
	Angajati
@endsection

@section('content')
	<form method="get" class="row" action="/angajati">
		<div class="form-group col-4 offset-1 mb-0">
			<div class="row">
				<label class="col-5">Cauta Angajat:</label>
				<input type="text" name='search' class='form-control col-7'>
			</div>
		</div>
		<div class="col-5">
			<div class="row">
				<select class="form-control col-5" name="order">
					<option value="cresc">Crescator (nume)</option>
					<option value="descresc">Descrescator (nume)</option>
				</select>
				<select class="form-control col-5" name="departament" id='departament'>
					<option value='0'>Toate departamentele</option>

					@foreach ($departamente as $value)
						<option value='{{$value->id}}'>{{$value->name}}</option>
					@endforeach
				</select>

				<div class="col-2 px-2">
					<button type="submit" class='btn btn-success w-100' name="button"> <i class="fas fa-search"></i> </button>
				</div>
			</div>
		</div>
	</form>

	<div class="row">


		@php
		$user_id = \Session::get('user_id');
		$user = \App\User::where('id', $user_id)->get()[0];
		@endphp
		@foreach ($angajati as $angajat)
			<div class="col-12 card">


				<div class="card-header">
					<div class="panel-group">
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="panel-title">
				<a data-toggle="collapse" class="row" href="#angajat-{{$angajat->id}}">

				<div class='col-4'>
					{{ $angajat->name }}

				</div>
			<div class='col-4'>
				@if($angajat->nume_departament)
					{{ $angajat->nume_departament }}
				@endif
			</div>
			<div class='col-4'>
				{{ $angajat->rol }}
			</div>

				</a>
      </div>
    </div>
    <div id="angajat-{{$angajat->id}}" class="panel-collapse collapse">
      <div class="panel-body">
				<form action='/editare-zile/{{$angajat->id}}' method='post'>
					@csrf
					<div class='form-group'>
						<label>Editeaza zile alocate</label>
						<input type='text' class='form-control' name='editeaza-zile' value='{{$angajat->zile_alocate}}'>
					</div>
					<button type='submit' class='btn btn-success'>Editeaza</button>
				</form>

				<a class="btn btn-success float-right" href="/profile/{{ $angajat->id }}">Arata profil</a>

				@if ($user->rol =='director')

				<a class="btn btn-success float-right mr-2" href="/gestionare-profil/{{ $angajat->id }}">Getioneaza profil</a>
				<a class="btn btn-danger float-right mr-2" href="/delete-profile/{{ $angajat->id }}">Sterge profil</a>

			@endif</div>
    </div>
  </div>
</div>





				</div>
			</div>
		@endforeach

		<div class="col-12">

		@if($pagina_curenta > 1)
			<a href="/angajati?page={{ $pagina_curenta-1 }}">Previous</a>
		@endif
		@if ($pagina_curenta < $pagina_max)
			<a class="float-right" href="/angajati?page={{ $pagina_curenta+1 }}">Next</a>
		@endif

		</div>


	</div>

@endsection

@section('addons-down')
<script>
$(document).ready(function(){

$('#departament').val({{$dep_selectat}});

});



</script>
@endsection
