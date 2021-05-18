@extends("layouts.layout")
@section("title")

	Profile Page

@endsection

@section("content")

	<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLongTitle">
						Editeaza profilul
					</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
					<form class='form-group' action='/schimbare-profil/{{$user->id}}' enctype="multipart/form-data" method='post'>
						@csrf
						<div class='form-group'>
							<label>Nume:</label>
							<input type='text'class='form-control' name='nume' value='{{$user->name}}' required>
						</div>

						<div class='form-group'>
							<label>Email:</label>
							<input type='text'class='form-control' name='email' value='{{$user->email}}' required>
						</div>

						<div class='form-group'>
							<label>Contact:</label>
							<input type='text'class='form-control' name='contact' value='{{$user->contact}}' required>
						</div>

						<div>
							<label>Poza profil:</label>
							<input type='file' accept=".jpg"  name='profil-img'>
						</div>

						<center>
							<button type="submit" class="btn btn-warning mt-3" name="button">Editeaza</button>
						</center>

					</form>
	      </div>

	    </div>
	  </div>
	</div>


<div class='card'>
	<div class='card-header'>
		<b>{{$user->name}}</b>
	</div>
	<div class='card-body'>
		<p>
		<b>Email: </b>{{$user->email}}
	</p><p>
		<b>Contact: </b>{{$user->contact}}
	</p><p>
		<b>Departament: </b>{{$user->departament_name}}
		</p>
		<p>
		<b>Rol: </b>{{$user->rol}}
		</p>
		<p>
		<b>Zile alocate:</b> {{$user->zile_alocate}}
		</p>
	</div>
	<div class='card-footer'>
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#profileModal">
		  Editeaza
		</button>
	</div>
</div>


@endsection
