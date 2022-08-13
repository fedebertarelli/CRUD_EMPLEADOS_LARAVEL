<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $datos['empleados'] = Empleado::paginate(1);
        return view('empleado.index', $datos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('empleado.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $campos=[
            'Nombre' => 'required|string|max:100',
            'ApellidoPaterno' => 'required|string|max:100',
            'ApellidoMaterno' => 'required|string|max:100',
            'Correo' => 'required|email',
            'Foto' => 'required|max:10000|mimes:jpeg,png,jpg',
        ];

        $mensaje=[
            'required' => 'El :attribute es requerido',
            'Foto.required' => 'La Foto es requerida'
        ];

        $this-> validate($request, $campos, $mensaje);

        //$datosEmpledos = $request->all();
        $datosEmpleados = $request->except('_token');
        
        if($request->hasfile('Foto')){
            $datosEmpleados['Foto'] = $request->file('Foto')->store('uploads','public');
        }

        Empleado::insert($datosEmpleados);

        //return response()->json($datosEmpleados);
        return redirect('empleado')->with('mensaje', 'Empleado agregado con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado $empleado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $empleado = Empleado::findorfail($id);
        return view('empleado.edit', compact('empleado'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $campos=[
            'Nombre' => 'required|string|max:100',
            'ApellidoPaterno' => 'required|string|max:100',
            'ApellidoMaterno' => 'required|string|max:100',
            'Correo' => 'required|email',
            
        ];

        $mensaje=[
            'required' => 'El :attribute es requerido',
            
        ];

        if($request->hasfile('Foto')){
            $campos=['Foto' => 'required|max:10000|mimes:jpeg,png,jpg'];
            $mensaje=['Foto.required' => 'La Foto es requerida' ];
        }
        $this-> validate($request, $campos, $mensaje);


        $datosEmpleado= $request->except(['_token','_method']);

        if($request->hasfile('Foto')){
            $empleado = Empleado::findorfail($id); 

            Storage::delete('public/'.$empleado->Foto); 

            $datosEmpleado['Foto']=$request->file('Foto')->store('uploads','public'); 
        }

        Empleado::where('id','=', $id)->update($datosEmpleado);

        $empleado = Empleado::findorfail($id);
        //return view('empleado.edit', compact('empleado'));

        return redirect('empleado')->with('mensaje', 'Empleado Modificado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $empleado = Empleado::findorfail($id); 
        if(Storage::delete('public/'.$empleado->Foto)){
            Empleado::destroy($id);
        }
        //
        
        return redirect('empleado')->with('mensaje', 'Empleado Borrado');
    }
}
