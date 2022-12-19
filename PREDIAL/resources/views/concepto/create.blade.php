@extends('layout')
@section('title', 'Registrar Concepto')
@section('content')
<div class="col-sm-12 col-md-9 col-lg-10 col-xl-10">
    <h3 class="text-center titulo">Registrar Concepto</h3>
    <div class="borde-forma">
        <form id="form" method="post" accept-charset="utf-8" action="{{ route('GuardarConceptoPredial') }}">
            <p class="parrafo-forma">
                Los campos marcados con <strong class="requerido">*</strong> son obligatorios.
            </p>
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="txtNombre" class="col-sm-12 col-form-label col-md-5 col-lg-5 col-xl-5 text-right">
                    <strong class="requerido">*</strong>Nombre:
                </label>
                <div class="col-sm-12 col-md-5 col-lg-5 col-xl-5 validar">
                    <input type="text" name="txtNombre" id="txtNombre" title="Ingrese nombre" value="" class="form-control" placeholder="Nombre" onkeypress="return txtValida(event,'car')" required="">
                </div>
            </div>

            <div class="form-group row">
                <label for="txtNombre" class="col-sm-12 col-form-label col-md-5 col-lg-5 col-xl-5 text-right">
                    <strong class="requerido">*</strong>Prescribe :
                </label>
                <div class="col-sm-12 col-md-5 col-lg-5 col-xl-5 validar">
                    <div class="col-sm-6 col-md-5 col-lg-5 col-xl-5 validar">
                        <input class="form-check-input" type="radio" name="prescribe" id="prescribe" value="1" required="" checked="checked"> SI
                    </div>
                    <div class="col-sm-6 col-md-5 col-lg-5 col-xl-5 validar">
                        <input class="form-check-input" type="radio" name="prescribe" id="prescribe" value="2" required=""> NO
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="btnGuardar" class="col-form-label col-md-5 col-lg-5 col-xl-5"></label>
                <div class="col-sm-2 col-md-1 col-lg-1 col-xl-1">
                    <button type="submit" class="btn btn-primary fa fa-save sombra"></button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop