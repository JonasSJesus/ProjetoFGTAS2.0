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
     * @throws DBALExcetpion
     * @throws DatabaseException
     */
    public function createAtendimento(array $data, int $userId): void
    {
        $atendimento = Atendimento::make(
            $data['tipoAtendimento'],
            $data['descricao_tipo_atendimento'],
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

//            $idPublico = $this->publicoRepository->findIdByName($atendimento->publico->perfilCliente);

            $idFormaAtend = $this->formaRepository->findIdByName($atendimento->formaAtendimento->forma);
            $idPublico = $this->publicoRepository->add($atendimento->publico);

            if ($atendimento->publico->haveExtraFields()) {
                $personalInfoId = $this->publicoRepository->findIdByDocumento($atendimento->publico->getExtraFields()->documento);

                if (!$personalInfoId) {
                    $this->publicoRepository->insertExtraFields($atendimento->publico, $idPublico);
                } else {
                    $this->publicoRepository->updateExtraFields($atendimento->publico, $personalInfoId);
                }
            }

            $idTipoAtend = $this->tipoRepository->add($atendimento->tipoAtendimento);

            $this->atendimentoRepository->add($idTipoAtend, $userId, $idPublico, $idFormaAtend);

            $this->conn->commit();
        } catch (DBALException $e) {
            $this->conn->rollBack();
            throw new DatabaseException();
        }
    }

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
            $idPublico = $this->publicoRepository->update($atendimento->publico, $idAtendimento['publico_id']);

            if ($atendimento->publico->haveExtraFields()) {
                $personalInfoId = $this->publicoRepository->findIdByDocumento($atendimento->publico->getExtraFields()->documento);

                if (!$personalInfoId) {
                    $this->publicoRepository->insertExtraFields($atendimento->publico, $idPublico);
                } else {
                    $this->publicoRepository->updateExtraFields($atendimento->publico, $personalInfoId);
                }
            }

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
    public function all(): array
    {
        return $this->atendimentoRepository->findAll();
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