<?php

namespace Fgtas\Services;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Exception as DBALException;
use Fgtas\Database\Connection;
use Fgtas\Entities\Atendimento;
use Fgtas\Exceptions\DatabaseException;
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


    /**
     * @param array $data
     * @param int $userId
     * @return void
     * @throws DBALException
     * @throws DatabaseException
     */
    public function createAtendimento(array $data, int $userId): void
    {
        $atendimento = Atendimento::make(
            $data['tipoAtendimento'],
            $data['descricao_tipo_atendimento'] ?? null,
            $data['formaAtendimento'],
            $data['perfilPublico'],
            );

        if (in_array($data['perfilPublico'], ['Empregador', 'Trabalhador'])){
            $atendimento->publico->setExtraFields(
                $data['nomePublico'],
                $data['documentoPublico'],
                $data['contatoPublico']
            );
        }

        try {
            $this->conn->beginTransaction();

            $idFormaAtend = $this->formaRepository->findIdByName($atendimento->formaAtendimento->forma);
            $pessoaId = null;

            if ($atendimento->publico->haveExtraFields()) {
                $pessoaId = $this->publicoRepository->findIdByDocumento($atendimento->publico->getExtraFields()->documento);

                if (!$pessoaId) {
                    $pessoaId = $this->publicoRepository->insertExtraFields($atendimento->publico);
                } else {
                    $this->publicoRepository->updateExtraFields($atendimento->publico);
                }
            }

            $idPublico = $this->publicoRepository->add($atendimento->publico, $pessoaId);
            $idTipoAtend = $this->tipoRepository->add($atendimento->tipoAtendimento);

            $this->atendimentoRepository->add($idTipoAtend, $userId, $idPublico, $idFormaAtend);

            $this->conn->commit();
        } catch (DBALException $e) {
            $this->conn->rollBack();

            throw new DatabaseException();
        }
    }

    /**
     * @param array $data
     * @param int $id
     * @return void
     * @throws DBALException
     * @throws DatabaseException
     */
    public function updateAtendimento(array $data, int $id): void
    {
        $atendimento = Atendimento::make(
            $data['tipoAtendimento'],
            $data['descricao_tipo_atendimento'],
            $data['formaAtendimento'],
            $data['perfilPublico'],
            $data['idAtendente']
        );

        $atendimento->setId($id);

        if (in_array($data['perfilPublico'], ['Empregador', 'Trabalhador'])){
            $atendimento->publico->setExtraFields(
                $data['nomePublico'],
                $data['documentoPublico'],
                $data['contatoPublico']
            );
        }

        try {
            $this->conn->beginTransaction();

            $idAtendimento = $this->atendimentoRepository->findForeignKeys($atendimento->getId());

            $idTipoAtend = $this->tipoRepository->update($atendimento->tipoAtendimento, $idAtendimento['tipo_atendimento_id']);
            $idFormaAtend = $this->formaRepository->findIdByName($atendimento->formaAtendimento->forma);
            $pessoaId = null;

            if ($atendimento->publico->haveExtraFields()) {
                $pessoaId = $this->publicoRepository->findIdByDocumento($atendimento->publico->getExtraFields()->documento);

                if (!$pessoaId) {
                    $pessoaId = $this->publicoRepository->insertExtraFields($atendimento->publico);
                } else {
                    $this->publicoRepository->updateExtraFields($atendimento->publico);
                }
            }

            $idPublico = $this->publicoRepository->update($atendimento->publico, $idAtendimento['publico_id'], $pessoaId);
            $this->atendimentoRepository->update(
                $idTipoAtend,
                $idPublico,
                $idFormaAtend,
                $atendimento->getId()
            );

            $this->conn->commit();
        } catch (DBALException $e) {
            $this->conn->rollBack();

            throw new DatabaseException();
        }
    }


    /** @return Atendimento[] */
    public function listAtendimentos(array $filters = []): ?array
    {
        $filter = array_filter($filters, function ($value) {
            return !empty($value) && $value != 'pdf' && $value != 'csv';
        });

        return $this->atendimentoRepository->findAll($filter);
    }

    public function get(int $id): Atendimento
    {
        return $this->atendimentoRepository->findById($id);
    }


    public function delete(int $id): void
    {
        $this->atendimentoRepository->delete($id);
    }
}