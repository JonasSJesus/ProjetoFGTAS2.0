<?php
declare(strict_types=1);

namespace Unit\Controller;

use Exception;
use Fgtas\Controllers\AtendimentoController;
use Fgtas\Entities\Atendimento;
use Fgtas\Entities\FormaAtendimento;
use Fgtas\Entities\Publico;
use Fgtas\Entities\TipoAtendimento;
use Fgtas\Exceptions\DatabaseException;
use Fgtas\Services\AtendimentoService;
use Fgtas\Validations\Validator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class AtendimentoControllerTest extends TestCase
{
    private AtendimentoController $controller;
    private AtendimentoService|MockObject $atendimentoService;
    private Validator|MockObject $validator;
    private Twig|MockObject $twig;
    private Messages|MockObject $flash;
    private ServerRequestInterface|MockObject $request;
    private ResponseInterface|MockObject $response;

    protected function setUp(): void
    {
        $this->atendimentoService = $this->createMock(AtendimentoService::class);
        $this->validator = $this->createMock(Validator::class);
        $this->twig = $this->createMock(Twig::class);
        $this->flash = $this->createMock(Messages::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->controller = new AtendimentoController(
            $this->atendimentoService,
            $this->validator,
            $this->twig,
            $this->flash
        );

        // Simular sessão de usuário
        $_SESSION['user'] = [
            'id' => 1,
            'nome' => 'Usuário Teste'
        ];
    }

    protected function tearDown(): void
    {
        unset($_SESSION['user']);
    }

    public function testStoreAtendimentoComSucesso(): void
    {
        // Dados de entrada válidos
        $requestData = [
            'identificacaoAtendente' => 'Atendente Teste',
            'formaAtendimento' => 'Presencial',
            'perfilPublico' => 'Empregador',
            'tipoAtendimento' => 'Consulta',
            'descricao_tipo_atendimento' => 'Consulta sobre legislação',
            'nomePublico' => 'Empresa Teste LTDA',
            'contatoPublico' => 'empresa@teste.com',
            'documentoPublico' => '12.345.678/0001-90'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do validator - validação bem-sucedida
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($requestData, $this->anything());

        $this->validator
            ->expects($this->once())
            ->method('failed')
            ->willReturn(false);

        // Mock do service - criação bem-sucedida
        $this->atendimentoService
            ->expects($this->once())
            ->method('createAtendimento')
            ->with($requestData, 1);

        // Mock do flash message para sucesso
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with('atendimento-create-success', 'Atendimento criado com sucesso!');

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/home')
            ->willReturnSelf();

        $result = $this->controller->store($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testStoreAtendimentoComValidacaoFalha(): void
    {
        // Dados de entrada inválidos
        $requestData = [
            'identificacaoAtendente' => '',
            'formaAtendimento' => 'Presencial',
            'perfilPublico' => 'Empregador',
            'tipoAtendimento' => ''
        ];

        $validationErrors = [
            'identificacaoAtendente' => 'Campo obrigatório',
            'tipoAtendimento' => 'Campo obrigatório'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do validator - validação falhou
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($requestData, $this->anything());

        $this->validator
            ->expects($this->once())
            ->method('failed')
            ->willReturn(true);

        $this->validator
            ->expects($this->once())
            ->method('getErrors')
            ->willReturn($validationErrors);

        // Mock do flash message para erro de validação
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with('atendimento-validate', $validationErrors);

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/home')
            ->willReturnSelf();

        $result = $this->controller->store($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testStoreAtendimentoComExcecaoDoBanco(): void
    {
        // Dados de entrada válidos
        $requestData = [
            'identificacaoAtendente' => 'Atendente Teste',
            'formaAtendimento' => 'Presencial',
            'perfilPublico' => 'Trabalhador',
            'tipoAtendimento' => 'Consulta',
            'descricao_tipo_atendimento' => 'Consulta sobre direitos',
            'nomePublico' => 'João da Silva',
            'contatoPublico' => 'joao@email.com',
            'documentoPublico' => '123.456.789-00'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do validator - validação bem-sucedida
        $this->validator
            ->expects($this->once())
            ->method('validate');

        $this->validator
            ->expects($this->once())
            ->method('failed')
            ->willReturn(false);

        // Mock do service - exceção do banco
        $this->atendimentoService
            ->expects($this->once())
            ->method('createAtendimento')
            ->with($requestData, 1)
            ->willThrowException(new DatabaseException());

        // Mock do flash message para erro
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with('atendimento-create-error', $this->anything());

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', '/home')
            ->willReturnSelf();

        $result = $this->controller->store($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testUpdateAtendimentoComSucesso(): void
    {
        $atendimentoId = 123;
        $requestData = [
            'identificacaoAtendente' => 'Atendente Atualizado',
            'formaAtendimento' => 'Online',
            'perfilPublico' => 'Empregador',
            'tipoAtendimento' => 'Orientação',
            'descricao_tipo_atendimento' => 'Orientação atualizada',
            'nomePublico' => 'Empresa Atualizada LTDA',
            'contatoPublico' => 'empresa.atualizada@teste.com',
            'documentoPublico' => '98.765.432/0001-10',
            'idAtendente' => 'Atendente Atualizado'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do validator - validação bem-sucedida
        $this->validator
            ->expects($this->once())
            ->method('validate');

        $this->validator
            ->expects($this->once())
            ->method('failed')
            ->willReturn(false);

        // Mock do service - atualização bem-sucedida
        $this->atendimentoService
            ->expects($this->once())
            ->method('updateAtendimento')
            ->with($requestData, $atendimentoId);

        // Mock do flash message para sucesso
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with('atendimento-update-success', 'Atendimento atualizado com sucesso!');

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', "/update-atendimento/{$atendimentoId}")
            ->willReturnSelf();

        $result = $this->controller->update($this->request, $this->response, ['id' => $atendimentoId]);

        $this->assertSame($this->response, $result);
    }

    public function testUpdateAtendimentoComExcecaoDoBanco(): void
    {
        $atendimentoId = 123;
        $requestData = [
            'identificacaoAtendente' => 'Atendente Teste',
            'formaAtendimento' => 'Presencial',
            'perfilPublico' => 'Trabalhador',
            'tipoAtendimento' => 'Consulta',
            'descricao_tipo_atendimento' => 'Consulta teste',
            'nomePublico' => 'Maria Santos',
            'contatoPublico' => 'maria@email.com',
            'documentoPublico' => '987.654.321-00',
            'idAtendente' => 'Atendente Teste'
        ];

        // Mock do request
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($requestData);

        // Mock do validator - validação bem-sucedida
        $this->validator
            ->expects($this->once())
            ->method('validate');

        $this->validator
            ->expects($this->once())
            ->method('failed')
            ->willReturn(false);

        // Mock do service - exceção do banco
        $this->atendimentoService
            ->expects($this->once())
            ->method('updateAtendimento')
            ->with($requestData, $atendimentoId)
            ->willThrowException(new DatabaseException());

        // Mock do flash message para erro
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with('atendimento-update-error', $this->anything());

        // Mock da resposta de redirecionamento
        $this->response
            ->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->response
            ->expects($this->once())
            ->method('withHeader')
            ->with('Location', "/update-atendimento/{$atendimentoId}")
            ->willReturnSelf();

        $result = $this->controller->update($this->request, $this->response, ['id' => $atendimentoId]);

        $this->assertSame($this->response, $result);
    }

    public function testDestroyAtendimentoComSucesso(): void
    {
        $atendimentoId = 123;

        // Mock do service - exclusão bem-sucedida
        $this->atendimentoService
            ->expects($this->once())
            ->method('delete')
            ->with($atendimentoId);

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

        $result = $this->controller->destroy($this->request, $this->response, ['id' => $atendimentoId]);

        $this->assertSame($this->response, $result);
    }

    public function testDestroyAtendimentoComExcecao(): void
    {
        $atendimentoId = 123;
        $errorMessage = 'Erro ao deletar atendimento';

        // Mock do service - exceção na exclusão
        $this->atendimentoService
            ->expects($this->once())
            ->method('delete')
            ->with($atendimentoId)
            ->willThrowException(new Exception($errorMessage));

        // Mock do flash message para erro
        $this->flash
            ->expects($this->once())
            ->method('addMessage')
            ->with('atendimento-destroy', $errorMessage);

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

        $result = $this->controller->destroy($this->request, $this->response, ['id' => $atendimentoId]);

        $this->assertSame($this->response, $result);
    }

    public function testFormsAtendimentoPagina(): void
    {
        $flashMessages = [
            'atendimento-validate' => [],
            'atendimento-create-success' => ['Atendimento criado com sucesso!'],
            'atendimento-create-error' => [],
            'atendimento-destroy' => []
        ];

        // Mock dos flash messages
        $this->flash
            ->expects($this->exactly(4))
            ->method('getMessage')
            ->willReturnMap([
                ['atendimento-validate', $flashMessages['atendimento-validate']],
                ['atendimento-create-success', $flashMessages['atendimento-create-success']],
                ['atendimento-create-error', $flashMessages['atendimento-create-error']],
                ['atendimento-destroy', $flashMessages['atendimento-destroy']]
            ]);

        // Mock da renderização do template
        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                '/views/formulario.html.twig',
                [
                    'userName' => $_SESSION['user'],
                    'validation' => null,
                    'createSuccess' => 'Atendimento criado com sucesso!',
                    'createError' => null,
                    'destroy' => null
                ]
            )
            ->willReturn($this->response);

        $result = $this->controller->formsAtendimentoPage($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }

    public function testUpdateAtendimentoPagina(): void
    {
        $atendimentoId = 123;
        $atendimento = new Atendimento(
            new FormaAtendimento('Presencial'),
            new TipoAtendimento('carteira de trabalho, SD, vagas', 'Informações sobre carteira de trabalho física e digital'),
            new Publico('Empregador'),
            '2023-12-01',
            'Atendente Teste'
        );
        $atendimento->setId($atendimentoId);

        $flashMessages = [
            'atendimento-validate' => ['Erro de validação'],
            'atendimento-update-success' => ['Atualizado com sucesso'],
            'atendimento-update-error' => ['Erro na atualização']
        ];

        // Mock do service - buscar atendimento
        $this->atendimentoService
            ->expects($this->once())
            ->method('get')
            ->with($atendimentoId)
            ->willReturn($atendimento);

        // Mock dos flash messages
        $this->flash
            ->expects($this->exactly(3))
            ->method('getMessage')
            ->willReturnMap([
                ['atendimento-validate', $flashMessages['atendimento-validate']],
                ['atendimento-update-success', $flashMessages['atendimento-update-success']],
                ['atendimento-update-error', $flashMessages['atendimento-update-error']]
            ]);

        // Mock da renderização do template
        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                '/views/editar_atendimento.html.twig',
                [
                    'userName' => $_SESSION['user'],
                    'atendimento' => $atendimento,
                    'validation' => 'Erro de validação',
                    'updateSuccess' => 'Atualizado com sucesso',
                    'updateError' => 'Erro na atualização'
                ]
            )
            ->willReturn($this->response);

        $result = $this->controller->updateAtendimentoPage(
            $this->request, 
            $this->response, 
            ['id' => $atendimentoId]
        );

        $this->assertSame($this->response, $result);
    }

    public function testDashboardPagina(): void
    {
        $atendimentos = [
            new Atendimento(
                new FormaAtendimento('Presencial'),
                new TipoAtendimento('carteira de trabalho, SD, vagas', 'Informações sobre carteira de trabalho física e digital'),
                new Publico('Empregador')
            ),
            new Atendimento(
                new FormaAtendimento('Online'),
                new TipoAtendimento('carteira de trabalho, SD, vagas', 'Informações sobre carteira de trabalho física e digital'),
                new Publico('Trabalhador')
            )
        ];

        // Mock do service - listar atendimentos
        $this->atendimentoService
            ->expects($this->once())
            ->method('listAtendimentos')
            ->willReturn($atendimentos);

        // Mock do flash message para erro de relatório
        $this->flash
            ->expects($this->once())
            ->method('getMessage')
            ->with('report-error')
            ->willReturn([]);

        // Mock da renderização do template
        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                '/views/dashboard.html.twig',
                [
                    'atendimentos' => $atendimentos,
                    'count' => 2,
                    'reportError' => null
                ]
            )
            ->willReturn($this->response);

        $result = $this->controller->dashboardPage($this->request, $this->response);

        $this->assertSame($this->response, $result);
    }
}