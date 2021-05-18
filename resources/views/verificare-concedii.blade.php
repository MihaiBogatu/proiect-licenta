@extends('layouts.layout')

@section('title')
  Verificare concedii
@endsection

@section('content')

  <div class="row">
    @foreach ($concedii_neacceptate as $value)
      <div class="col-12">

        <div class="modal fade" id="respinge-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Motiv respingere</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form action="/respingere-concediu/{{ $value->id }}" method="post">
                  @csrf

                  <textarea name="motiv" required class="form-control" placeholder="motiv..."></textarea>

                  <button type="submit" class="btn btn-danger">Respingere concediu</button>

                </form>
              </div>
            </div>
          </div>
        </div>
        <div class='card'>
          <div class='card-header'>
       {{$value->name}} = {{Carbon\Carbon::parse($value->data_inceput)->format('d/m/Y')}} - {{Carbon\Carbon::parse($value->data_sfarsit)->format('d/m/Y')}}
       - Tip concediu: {{$value->tip_concediu}}
       <a class="float-right btn btn-primary" href="/concediu-editare/{{$value->id}}">Editeaza</a>
        <a class="float-right btn btn-success" href="/acceptare-concediu/{{$value->id}}">Accepta</a>
        <button class="float-right btn btn-danger"  data-toggle="modal" data-target="#respinge-modal">Respinge</button>

    </div>
    </div>


      </div>
    @endforeach
  </div>

@endsection
