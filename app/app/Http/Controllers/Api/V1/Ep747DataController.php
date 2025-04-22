<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessEp747Data;
use Illuminate\Http\Request;
use App\Models\Relatorio;
use App\Models\ReportSource;
use App\Models\Vss110ResumoLiquidacao;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Http\JsonResponse;

class Ep747DataController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $relatorio = $this->getRelatorio($request);

        if (!$relatorio) {
            return response()->json(['message' => 'Relatório EP747 não encontrado.'], 404);
        }

        // Inicia contadores
        $somas = [
            'COMPRA' => 0.0,
            'SAQUE' => 0.0,
            'REPASSE_LIQUIDO' => 0.0,
        ];

        // Processa transações (COMPRA / SAQUE)
        foreach ($relatorio->vss110ResumosLiquidacao as $linha) {
            $tipo = $this->classify($linha->tipo_transacao);
            if (in_array($tipo, ['COMPRA', 'SAQUE'])) {
                $somas[$tipo] += (float) $linha->valor;
            }
        }

        // Processa repasses (TRANSF.)
        foreach ($relatorio->vss110TransferenciasFundos as $linha) {
            $somas['REPASSE_LIQUIDO'] += (float) $linha->valor;
        }

        return response()->json([
            'relatorio_id' => $relatorio->id,
            'compras_brl' => $somas['COMPRA'],
            'compras_usd' => 0.00,
            'saques_brl' => $somas['SAQUE'],
            'saques_usd' => 0.00,
            'repasse_liquido_brl' => $somas['REPASSE_LIQUIDO'],
            'repasse_liquido_usd' => 0.00,
        ]);
    }

    private function getRelatorio(Request $request): ?Relatorio
    {
        $relatorioId = $request->query('relatorio_id');

        return $relatorioId
            ? Relatorio::where('tipo_relatorio', 'like', 'VSS-%')->find($relatorioId)
            : Relatorio::where('tipo_relatorio', 'like', 'VSS-%')->latest()->first();
    }

    private function classify(?string $descricao): string
    {
        $map = [
            'PURCHASE ORIGINAL SALE' => 'COMPRA',
            'MERCHANDISE CREDIT ORIGINAL SALE' => 'COMPRA',
            'ATM CASH ORIGINAL WITHDRAWAL' => 'SAQUE',
            'PURCHASE DISPUTE RESP FIN' => 'REAPRESENTACAO',
            'PURCHASE DISPUTE RESP FIN REVERSAL' => 'REVERSO-DE-REAPRESENTACAO',
            'PURCHASE DISPUTE FIN' => 'CHARGEBACK-DE-COMPRA',
            'PURCHASE DISPUTE FIN REVERSAL' => 'REVERSO-DE-CHARGEBACK',
            'PURCHASE ORIGINAL SALE REVERSAL' => 'REVERSO-DE-COMPRA',
            'ATM CASH REVERSAL' => 'REVERSO-DE-SAQUE',
        ];

        return $map[$descricao] ?? 'OUTRO';
    }

    /**
     * Armazena um novo recurso no storage (upload do arquivo) e inicia o processamento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        ini_set('memory_limit', '1024M'); // 1GB temporário

        try {
            $request->validate([
                'file' => 'required|file|mimetypes:text/plain|max:10240', // 10MB max
            ]);

            $file = $request->file('file');
            $filename = uniqid('ep747_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('ep747_files', $filename);

            if (!$path) {
                Log::error('Falha ao salvar o arquivo: ' . $file->getClientOriginalName()); // Log do erro
                return response()->json(['message' => 'Falha ao salvar o arquivo.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $reportSource = new ReportSource();
            $reportSource->file_name = $filename;
            $reportSource->file_path = $path;
            $reportSource->imported_at = now();
            $reportSource->save();

            dispatch(new ProcessEp747Data($reportSource->id));

            return response()->json([
                'message' => 'Arquivo enviado com sucesso. Processamento iniciado.',
                'relatorio_id' => $reportSource->id,
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            Log::error('Erro ao processar o upload: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['message' => 'Erro interno do servidor.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Exibe o recurso especificado.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $relatorio = Relatorio::find($id);

        if (!$relatorio) {
            return response()->json(['message' => 'Relatorio não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($relatorio);
    }
}
