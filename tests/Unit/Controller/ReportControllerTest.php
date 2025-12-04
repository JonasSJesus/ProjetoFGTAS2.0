<?php
declare(strict_types=1);

namespace Unit\Controller;

use Fgtas\Controllers\ReportController;
use Fgtas\Entities\Atendimento;
use Fgtas\Entities\FormaAtendimento;
use Fgtas\Entities\Publico;
use Fgtas\Entities\TipoAtendimento;
use Fgtas\Services\AtendimentoService;
use Fgtas\Services\Reports\CsvReportService;
use Fgtas\Services\Reports\PdfReportService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ReportControllerTest extends TestCase
{
    private ReportController $controller;
    private AtendimentoService|MockObject $atendimentoService;
    private PdfReportService|MockObject $pdfReportService;
    private CsvReportService|MockObject $csvReportService;
    private Twig|MockObject $twig;
    private Messages|MockObject $flash;
    private ServerRequestInterface|MockObject $request;
    private ResponseInterface|MockObject $response;

    protected function setUp(): void
    {
        $this->atendimentoService = $this->createMock(AtendimentoService::class);
        $this->pdfReportService = $this->createMock(PdfReportService::class);
        $this->csvReportService = $this->createMock(CsvReportService::class);
        $this->twig = $this->createMock(Twig::class);
        $this->flash = $this->createMock(Messages::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->controller = new ReportController(
            $this->atendimentoService,
            $this->pdfReportService,
            $this->csvReportService,
            $this->twig,
            $this->flash
        );
    }

    private function createSampleAtendimentos(): array
    {
        $atendimento1 = new Atendimento(
            new FormaAtendimento('Presencial'),
            new TipoAtendimento('carteira de trabalho, SD, vagas', 'Informações sobre carteira de trabalho física e digital'),
            new Publico('Empregador'),
            '2023-12-01',
            'João Silva'
        );
        $atendimento1->setId(1);

        $atendimento2 = new Atendimento(
            new FormaAtendimento('Online'),
            new TipoAtendimento('Vida Centro Humanistico', 'Orientações para atividades lúdicas e pedagógicas'),
            new Publico('Trabalhador'),
            '2023-12-02',
            'Maria Santos'
        );
        $atendimento2->setId(2);

        return [$atendimento1, $atendimento2];
    }

    public function testGenerateReportPdfComSucesso(): void
    {
        $requestData = [
            'exp' => 'pdf',
            'dataInicio' => '2023-12-01',
            'dataFim' => '2023-12-31'
        ];

        $atendimentos = $this->createSampleAtendimentos();

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service para listar atendimentos
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn($atendimentos);

        // Mock do PDF service
        $this->pdfReportService
            ->expects($this->once())
            ->method('generate')
            ->with($atendimentos);

        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportCsvComSucesso(): void
    {
        $requestData = [
            'exp' => 'csv',
            'tipoAtendimento' => 'Consulta',
            'formaAtendimento' => 'Presencial'
        ];

        $atendimentos = $this->createSampleAtendimentos();
        $filename = 'relatorio_12345_12:30-01_12_2023.csv';

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service para listar atendimentos
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn($atendimentos);

        // Mock do CSV service
        $this->csvReportService
            ->expects($this->once())
            ->method('generate')
            ->with($atendimentos)
            ->willReturn($filename);

        // Mock da resposta com headers para CSV
        $this->response
            ->expects($this->exactly(2))
            ->method('withHeader')
            ->willReturnCallback(function ($header, $value) {
//                dump($value);
                if ($header === 'Content-Type') {
                    $this->assertEquals('application/csv', $value);
                } elseif ($header === 'Content-Disposition') {
                    $this->assertEquals($value, "attachment; filename=relatorio_12345_12:30-01_12_2023.csv");
                }
                return $this->response;
            });

        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportSemTipoExportacao(): void
    {
        $requestData = [
            'dataInicio' => '2023-12-01',
            'dataFim' => '2023-12-31'
            // Sem 'exp'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do flash message para erro
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with(
                'report-error',
                'Erro ao gerar relatório, por favor marque uma opção entre PDF ou CSV'
            );

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/dashboard')
            ->willReturnSelf();

        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportComTipoExportacaoVazio(): void
    {
        $requestData = [
            'exp' => '',
            'dataInicio' => '2023-12-01',
            'dataFim' => '2023-12-31'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do flash message para erro
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with(
                'report-error',
                'Nenhum atendimento encontrado. Tente trocar os filtros. caso o problema persista, entre em contato com um superior'
            );

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/dashboard')
            ->willReturnSelf();

        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportSemAtendimentos(): void
    {
        $requestData = [
            'exp' => 'pdf',
            'dataInicio' => '2023-01-01',
            'dataFim' => '2023-01-31'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service retornando array vazio
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn([]);

        // Mock do flash message para erro
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with(
                'report-error',
                'Nenhum atendimento encontrado. Tente trocar os filtros. caso o problema persista, entre em contato com um superior'
            );

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/dashboard')
            ->willReturnSelf();

        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportComAtendimentosNull(): void
    {
        $requestData = [
            'exp' => 'csv',
            'perfilPublico' => 'Inexistente'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service retornando null
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn(null);

        // Mock do flash message para erro
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with(
                'report-error',
                'Nenhum atendimento encontrado. Tente trocar os filtros. caso o problema persista, entre em contato com um superior'
            );

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/dashboard')
            ->willReturnSelf();

        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportPdfComExcecaoTwig(): void
    {
        $requestData = [
            'exp' => 'pdf'
        ];

        $atendimentos = $this->createSampleAtendimentos();

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service para listar atendimentos
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn($atendimentos);

        // Mock do PDF service lançando exceção
        $this->pdfReportService
            ->expects($this->once())
            ->method('generate')
            ->with($atendimentos)
            ->willThrowException(new LoaderError('Template não encontrado'));

        $this->expectException(LoaderError::class);
        $this->expectExceptionMessage('Template não encontrado');

        $this->controller->generateReport($this->request, $this->response);
    }

    public function testGenerateReportComTipoExportacaoInvalido(): void
    {
        $requestData = [
            'exp' => 'xlsx', // Tipo não suportado
            'dataInicio' => '2023-12-01'
        ];

        $atendimentos = $this->createSampleAtendimentos();

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service para listar atendimentos
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn($atendimentos);

        // Nenhum dos serviços de relatório deve ser chamado para tipo inválido
        $this->pdfReportService
            ->expects($this->never())
            ->method('generate');

        $this->csvReportService
            ->expects($this->never())
            ->method('generate');

        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportComFiltrosComplexos(): void
    {
        $requestData = [
            'exp' => 'pdf',
            'dataInicio' => '2023-12-01',
            'dataFim' => '2023-12-31',
            'tipoAtendimento' => 'Consulta',
            'formaAtendimento' => 'Presencial',
            'perfilPublico' => 'Empregador',
            'nomeAtendente' => 'João Silva'
        ];

        $atendimentos = [$this->createSampleAtendimentos()[0]]; // Apenas um resultado filtrado

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service para listar atendimentos com todos os filtros
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn($atendimentos);

        // Mock do PDF service
        $this->pdfReportService
            ->expects($this->once())
            ->method('generate')
            ->with($atendimentos);

        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportCsvComNomeArquivoPersonalizado(): void
    {
        $requestData = [
            'exp' => 'csv'
        ];

        $atendimentos = $this->createSampleAtendimentos();
        $customFilename = 'relatorio_custom_' . date('Y-m-d') . '.csv';

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service para listar atendimentos
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn($atendimentos);

        // Mock do CSV service com nome personalizado
        $this->csvReportService
            ->expects($this->once())
            ->method('generate')
            ->with($atendimentos)
            ->willReturn($customFilename);

        // Mock da resposta verificando se os headers estão corretos na ordem correta
        $this->response
            ->expects($this->exactly(2))
            ->method('withHeader')
            ->willReturnCallback(function ($header, $value) use ($customFilename) {
                static $callCount = 0;
                $callCount++;

                if ($callCount === 1) {
                    $this->assertEquals('Content-Type', $header);
                    $this->assertEquals('application/csv', $value);
                } elseif ($callCount === 2) {
                    $this->assertEquals('Content-Disposition', $header);
                    $this->assertEquals("attachment; filename=$customFilename", $value);
                }

                return $this->response;
            });
        $result = $this->controller->generateReport($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testGenerateReportPdfComRuntimeError(): void
    {
        $requestData = [
            'exp' => 'pdf'
        ];

        $atendimentos = $this->createSampleAtendimentos();

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service para listar atendimentos
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn($atendimentos);

        // Mock do PDF service lançando RuntimeError
        $this->pdfReportService
            ->expects($this->once())
            ->method('generate')
            ->with($atendimentos)
            ->willThrowException(new RuntimeError('Erro de runtime no template'));

        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessage('Erro de runtime no template');

        $this->controller->generateReport($this->request, $this->response);
    }

    public function testGenerateReportPdfComSyntaxError(): void
    {
        $requestData = [
            'exp' => 'pdf'
        ];

        $atendimentos = $this->createSampleAtendimentos();

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do service para listar atendimentos
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->with($requestData)
            ->willReturn($atendimentos);

        // Mock do PDF service lançando SyntaxError
        $this->pdfReportService
            ->expects($this->once())
            ->method('generate')
            ->with($atendimentos)
            ->willThrowException(new SyntaxError('Erro de sintaxe no template'));

        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage('Erro de sintaxe no template');

        $this->controller->generateReport($this->request, $this->response);
    }
}