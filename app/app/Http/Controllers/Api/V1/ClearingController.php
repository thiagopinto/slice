<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\ClearingFile;
use App\Jobs\ProcessClearingJson;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class ClearingController extends Controller
{

    public function index(): JsonResponse
    {
        return response()->json([
            'compras_brl' => $this->total('COMPRA', 986),
            'compras_usd' => $this->total('COMPRA', 840),
            'saques_brl' => $this->total('SAQUE', 986),
            'saques_usd' => $this->total('SAQUE', 840),
            'repasse_liquido_brl' => $this->repasseLiquido(986),
            'repasse_liquido_usd' => $this->repasseLiquido(840),
        ]);
    }

    private function total(string $tipo, int $moeda): float
    {
        return (float) DB::table('clearing_transactions')
            ->where('classified_type', $tipo)
            ->where('clearing_currency', $moeda)
            ->sum('clearing_value');
    }

    private function repasseLiquido(int $moeda): float
    {
        return (float) DB::table('clearing_transactions')
            ->where('clearing_currency', $moeda)
            ->select(DB::raw('SUM(clearing_value - clearing_commission) as total'))
            ->value('total');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'clearing_file' => 'required|file|mimetypes:application/json,text/plain,text/json|max:10240',
            ]);

            $file = $request->file('clearing_file');
            $filename = uniqid('clearing_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('clearing_files', $filename);

            $clearingFile = ClearingFile::create([
                'arquivo_origem' => $file->getClientOriginalName(),
                'arquivo_path' => $path,
                'data_processamento' => now(),
            ]);

            dispatch(new ProcessClearingJson($clearingFile->id));

            return response()->json([
                'message' => 'Arquivo enviado com sucesso. Processamento iniciado.',
                'clearing_file_id' => $clearingFile->id,
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            Log::error('Erro ao processar o upload: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno do servidor.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
