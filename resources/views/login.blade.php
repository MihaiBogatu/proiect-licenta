@extends("layouts.layout")
@section("title")

	Login Page

@endsection

@section("content")


	<form method="post" action="/login">
		@csrf
		<div class="form-group">
		<label for="email">Introdu emailul:</label>
		<input type="email" name="email" class="form-control" id="email" placeholder="Introdu email" required>
		</div>
		<div class="form-group">
		<label for="parola">Introdu parola:</label>
		<input type="password" name="parola" id="parola" class="form-control" placeholder="Introdu parola" required>
		</div>
		<input class="btn btn-warning" type="submit" name="buton" value="Login">
	</form>

@endsection