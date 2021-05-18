@extends('layouts.layout')
@section('title')

  Departamente

@endsection

@section('content')
  <div class='row'>
  <form method="post" action="/departamente" class='col-md-6 col-12'>
    @csrf

    <div class='input-group mb-3 row'>
      <label class="col-md-3 col-12">Nume Departament</label>
      <input type="text" class='form-control col-md-9 col-12' name="nume_departament" placeholder="Introdu numele departamentului" required />
    </div>
    <div class='input-group row mb-3'>
      <label class="col-md-3 col-12">Parinte Departament</label>
      <select class='form-control col-md-9 col-12' name='parinte-departament'>
        <option value='0'>Fara parinte </option>
        @foreach ($departamente as $value)
          <option value='{{$value->id}}'>{{$value->name}}</option>
        @endforeach

      </select>

    </div>
    <button class="btn btn-success" type="submit" name='action' value="add">Submit</button>

  </form>

  <form method="post" action="/departamente" class = 'col-md-6 col-12'>
    @csrf
    <div class='input-group row'>

      <label class="col-md-3 col-12">Selectare Departament</label>

        <select class='form-control col-md-9 col-12' name='id-departament'>
          @foreach ($departamente as $value)
            <option value='{{$value->id}}'>{{$value->name}}</option>
          @endforeach
        </select>


    </div>
    <button class="btn btn-danger float-right" type="submit" name='action' value="delete">Sterge Departament</button>
  </form>
</div>
@endsection
