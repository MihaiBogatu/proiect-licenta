@extends("layouts.layout")
@section("title")
  Istoric Departament
@endsection

@section('content')


  <div class="row mt-3">
  	<div class='col-12'>
  	 <div class="col-12">
  		 @foreach($concedii_tot as $value)
  		 <div class='card row'>
  			 <div class="card-header
  			 @if($value->acceptat)
  				 bg-success
  			 @else
  				 bg-danger
  			 @endif
  			 ">
  				 {{Carbon\Carbon::parse($value->data_inceput)->format("d/m/Y")}} - {{Carbon\Carbon::parse($value->data_sfarsit)->format("d/m/Y")}} - @if($value->acceptat) Concediu acceptat @else Concediu respins @endif
           Tip concediu: {{$value->tip_concediu}}
          <a href='/concediu-editare/{{$value->id}}' class='btn btn-warning float-right'>Editeaza</a>
  			 </div>
  		 </div>
  	 @endforeach
  	 </div>
   </div>
  </div>


@endsection
