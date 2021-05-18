@extends("layouts.layout")
@section("title")
	Concedii Totale
@endsection

@section('content')

@foreach ($concedii_tot as $value)
	<div class="modal fade" id="editModal-{{$value->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
				<form method="post" autocomplete="off" action="/concediu-editare">
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
						@foreach ($colegi as $value_u)
							<option value="{{$value_u->id}}">{{$value_u->name}}</option>
						@endforeach
					</select>
					</div>
					<button type="submit" id="submit" class= "btn btn-success">Modifica</button>

				</form>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<div id="concediu-{{ $value->id }}" class='card @if($value->acceptat) bg-success @else bg-danger @endif'>
  <div class='card-header'>
    {{$value->name}}
  </div>
    <div class='card-body'>
      {{Carbon\Carbon::parse($value->data_inceput)->format('d/m/Y')}} - {{Carbon\Carbon::parse($value->data_sfarsit)->format('d/m/Y')}}
      @if($value->motiv != null)
        Motiv : {{$value->motiv}}
      @endif

			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal-{{$value->id}}">
  Editeaza
</button>
      <a href='/concediu-edit/{{$value->id}}' class='btn btn-warning float-right'>Permite Editare</a>
  </div>

</div>


@endforeach


@endsection
