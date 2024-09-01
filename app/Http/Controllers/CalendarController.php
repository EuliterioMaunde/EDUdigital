<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Caminho para o arquivo JSON
        $path = storage_path('app/notes.json');

        // Verifica se o arquivo existe antes de tentar recuperar o conteúdo
        if (file_exists($path)) {
            // Recupera o conteúdo do arquivo JSON
            $notesContent = file_get_contents($path);

            // Decodifica o conteúdo JSON em um array associativo
            $notes = json_decode($notesContent, true); // Use `true` para decodificar como um array associativo
        } else {
            // Se o arquivo não existir, inicializa a variável como um array vazio
            $notes = [];
        }

        // Retorna a view com o conteúdo do JSON
        return view('calendar.home', compact('notes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validação dos Feilds
        $rules = [
            'notes' => 'required|JSON',
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['data' => $validate->errors(), 'message' => 'Houve um erro durante a validação dos campus', 'status' => 412], 412);
        }


        // Caminho para o arquivo JSON
        $path = storage_path('app/notes.json');

        // Salvar os dados no arquivo JSON
        file_put_contents($path, json_encode(json_decode($request->notes), JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Notas salvas com sucesso.','status'=>201,'data'=>array()]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
