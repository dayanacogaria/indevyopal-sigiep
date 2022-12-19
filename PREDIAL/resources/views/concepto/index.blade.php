@extends('layout')
@section('title', 'Concepto')
@section('content')
<div class="col-sm-12 col-md-9 col-lg-10 col-xl-10">
    <h3 class="text-center titulo">Concepto</h3>
    <div class="well bg-light col-12 text-right inferior borde-well">
        <a href="RegistrarConceptoPredial" class="btn btn-primary fa fa-plus sombra superior inferior" title="Registrar Nuevo"></a>
    </div>
    <table id="table" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table table-hover table-bordered table-striped table-condensed" data-toggle="dataTable" data-form="deleteForm">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Prescribe</th>
                <th width="7%"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row->nombre }}</td>
                    <td><?php if($row->prescribe=="2"){echo 'No'; } else {echo 'Si';}?></td>
                    <td>
                        <a class="fa fa-pencil icons" href="{{ route('ModificarConceptoPredial', encrypt($row->id_unico)) }}"></a>
                        {!! Form::model($row, ['method' => 'post', 'route' => ['EliminarConceptoPredial', encrypt($row->id_unico)], 'class' => 'form-tabla form-delete']) !!}
                        {!! Form::button(trans(''), ['class' => 'fa fa-trash icons btn-sin cursor', 'type' => 'submit', 'id' => 'btnD']) !!}
                        {!! Form::hidden('id_unico', encrypt($row->id_unico)) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop
@include('modal-confirm')
@section('script')
<script>
    $('table[data-form="deleteForm"]').on('click', '.form-delete', function(e){
        e.preventDefault();
        var $form=$(this);
        $('#confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#delete-btn', function(){
                $form.submit();
            });
    });
</script>
@stop