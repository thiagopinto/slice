<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Ep747DataLoadService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Log;
use App\Models\Relatorio;

class ProcessEp747Data implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $relatorioId; // Número de tentativas antes de falhar

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @return void
     */
    public function __construct(int $relatorioId)
    {
        $this->relatorioId = $relatorioId;
    }

    /**
     * Execute the job.
     *
     * @param Ep747DataLoadService $dataLoadService
     * @return void
     */
    public function handle(Ep747DataLoadService $dataLoadService)
    {
        try {
            $dataLoadService->loadData($this->relatorioId);
        } catch (\Exception $e) {
            Log::error('Erro ao processar arquivo EP747: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            // Você pode tratar o erro aqui, como enviar uma notificação, etc.
            // Se você quiser que o job tente novamente, não precisa fazer nada aqui (depende da configuração da sua fila).
        }
    }
}
