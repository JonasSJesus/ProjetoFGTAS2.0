<?php

namespace Fgtas\Services;

use DI\Container;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception;
use Fgtas\Database\Connection;
use Fgtas\Entities\Atendimento;
use Fgtas\Repositories\Atendimentos\AtendimentoRepository;
use Fgtas\Repositories\Interfaces\IAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IFormaAtendimentoRepository;
use Fgtas\Repositories\Interfaces\IPublicoRepository;
use Fgtas\Repositories\Interfaces\ITipoAtendimentoRepository;

class AtendimentoService
{
    private IAtendimentoRepository $atendimentoRepository;
    private ITipoAtendimentoRepository $tipoRepository;
    private IPublicoRepository $publicoRepository;
    private IFormaAtendimentoRepository $formaRepository;
    private DBALConnection $conn;

    public function __construct(
        IAtendimentoRepository $atendimentoRepository,
        ITipoAtendimentoRepository $tipoRepository,
        IPublicoRepository $publicoRepository,
        IFormaAtendimentoRepository $formaRepository,
        Connection $conn
    )
    {
        $this->atendimentoRepository = $atendimentoRepository;
        $this->tipoRepository = $tipoRepository;
        $this->publicoRepository = $publicoRepository;
        $this->formaRepository = $formaRepository;
        $this->conn = $conn->getConnection();
    }


    public function createAtendimento(array $data, int $userId): void
    {
        $atendimento = Atendimento::make(
            $data['tipoAtendimento'],
            $data['descricao_tipo_atendimento'],
            $data['formaAtendimento'],
            $data['perfilPublico'],
            );

        if (in_array($data['perfilPublico'], ['empregador', 'trabalhador'])){
            $atendimento->publico->setExtraFields(
                $data['nomePublico'],
                $data['documentoPublico'],
                $data['contatoPublico']
            );
        }

        $this->conn->beginTransaction();
        try {
            // TODO: Em vez de salvar os itens, isso poderia apenas buscar o ID de itens salvos previamente no banco de dados lookup talvez?
            $idPublico = $this->publicoRepository->add($atendimento->publico);
            $idTipoAtend = $this->tipoRepository->add($atendimento->tipoAtendimento);
            $idFormaAtend = $this->formaRepository->add($atendimento->formaAtendimento);

            $this->atendimentoRepository->add($idTipoAtend, $userId, $idPublico, $idFormaAtend);

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }


    /** @return Atendimento[] */
    public function all(): array
    {
        return $this->atendimentoRepository->findAll();
    }


    public function delete(int $id)
    {
        $this->atendimentoRepository->delete($id);
    }
}