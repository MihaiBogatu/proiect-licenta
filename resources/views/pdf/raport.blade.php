<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Raport</title>
  <style media="screen">
    * {
      font-family: sans-serif;
    }

    .content-table {
          border-collapse: collapse;
          margin: 25px 0;
          font-size: 0.9em;
          min-width: 400px;
          border-radius: 5px 5px 0 0;
          overflow: hidden;
          box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .content-table thead tr {
          background-color: #009879;
          color: #ffffff;
          text-align: left;
          font-weight: bold;
        }

        .content-table th,
        .content-table td {
          padding: 12px 15px;
        }

        .content-table tbody tr {
          border-bottom: 1px solid #dddddd;
        }

        .content-table tbody tr:nth-of-type(even) {
          background-color: #f3f3f3;
        }

        .content-table tbody tr:last-of-type {
          border-bottom: 2px solid #009879;
        }

        .content-table tbody tr.active-row {
          font-weight: bold;
          color: #009879;
        }
        a:link, a:visited {
        background-color: #f44336;
        color: white;
        padding: 14px 25px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        float:right;
        }

        a:hover, a:active {
        background-color: red;
        }
    </style>
  </head>
  <body>

<center>
<h1>Raport {{$departament->name}}</h1>
</center>
<center>
<table class="content-table">
  <thead>
    <tr>
      <th>Nume</th>
      <th>Rol</th>
      <th>Inlocuitor</th>
      <th>Data inceput</th>
      <th>Data sfarsit</th>
      <th>Perioada</th>
      <th>Zile alocate ramase</th>
      <th>Cerut in data de</th>
    </tr>
  </thead>
  <tbody>

    @foreach ($concedii as $value)

      @php
        $concedii_user = App\Concediu::where('user_id', $value->user_id)->where('data_inceput', '>',
                         Carbon\Carbon::parse('01/01/'.Carbon\Carbon::now()->year))->orderBy('data_inceput')->get();

        $perioada = 1;

        foreach ($concedii_user as $c) {
          if($c->id == $value->id)
            break;
          $perioada++;
        }
      @endphp

      <tr>
        <td>{{$value->name}}</td>
        <td>{{$value->rol}}</td>
        <td>{{$value->inlocuitor}}</td>
        <td>{{Carbon\Carbon::parse($value->data_inceput)->format('d/m/Y')}}</td>
        <td>{{Carbon\Carbon::parse($value->data_sfarsit)->format('d/m/Y')}}</td>
        <td>{{ $perioada }}</td>
        <td>{{$value->zile_alocate}}</td>
        <td>{{Carbon\Carbon::parse($value->created_at)->format('d/m/Y')}}</td>
      </tr>
    @endforeach
  </tbody>
</table>
</center>
<center>
  Generat de {{$user->name}} la data de {{Carbon\Carbon::now()->format('d/m/Y')}}
</center>
<a href='/download/{{$departament->id}}'>Export PDF</a>

  </body>
</html>
