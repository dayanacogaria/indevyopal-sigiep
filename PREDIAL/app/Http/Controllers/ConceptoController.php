<?php

namespace Predial\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Predial\Concepto;

class ConceptoController extends Controller{

    public $path = "concepto.";

    public function index(){
        if(!empty(session()->get('usuario'))){
            $data = Concepto::all();
            return view($this->path.'index')->with('data', $data);
        }else{
            return redirect("CerrarSesion");
        }
    }

    public function create(){
        if(!empty(session()->get('usuario'))){
            return view($this->path.'create');
        }else{
            return redirect("CerrarSesion");
        }
    }

    public function store(Request $request){
        try {
            $new = new Concepto();
            $new->timestamps = false;
            $new->nombre = $request->txtNombre;
            $new->prescribe = $request->prescribe;
            $new->save();

            flash()->overlay('Información guardada correctamente', 'Información');

            return redirect('ListarConceptoPredial');
        } catch (Exception $e) {
            return "Fatal Error = ".$e->getMessage();
        }
    }

    public function show($id_unico){
        if(!empty(session()->get('usuario'))){
            try {
                $id   = decrypt($id_unico);
                $data = Concepto::find($id);
                return view($this->path.'edit')->with('data', $data);
            } catch (Exception $e) {
                return "Fatal Error = ".$e->getMessage();
            }
        }else{
            return redirect("CerrarSesion");
        }
    }

    public function edit(Request $request){
        try {
            $upd             = Concepto::findOrFail($request->id_unico);
            $upd->timestamps = false;
            $upd->nombre     = $request->txtNombre;
            $upd->prescribe  = $request->prescribe;
            $upd->save();

            flash()->overlay('Información modificada correctamente', 'Información');

            return redirect('ListarConceptoPredial');
        } catch (Exception $e) {
            return "Fatal Error = ".$e->getMessage();
        }
    }

    public function destroy($id_unico){
        try {
            $id  = decrypt($id_unico);
            $del = Concepto::findOrFail($id);
            $del->delete();

            flash()->overlay('Información eliminada correctamente', 'Información');

            return redirect('ListarConceptoPredial');
        } catch (Exception $e) {
            return "Fatal Error = ".$e->getMessage();
        }
    }
}
