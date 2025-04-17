<?php

namespace Fgtas\Services;

use Fgtas\Entities\Atendimento;
use Fgtas\Entities\FormaAtendimento;
use Fgtas\Entities\Publico;
use Fgtas\Entities\TipoAtendimento;
use Fgtas\Repositories\Interfaces\IAtendimentoRepository;

class AtendimentoService
{
    private IAtendimentoRepository $atendimentoRepository;

    public function __construct(IAtendimentoRepository $atendimentoRepository)
    {
        $this->atendimentoRepository = $atendimentoRepository;
    }

    public function createAtendimento(array $data): void
    {
        $tipoAtendimento = new TipoAtendimento($data['tipoAtendimento'], $data['descricao_tipo_atendimento']);
//        $formaAtendimento = new FormaAtendimento($data['formaAtendimento']);
        $publicoPerfil = new Publico($data['perfilPublico']);

        if ($publicoPerfil == 'empregador' || $publicoPerfil == 'trabalhador'){
            $publicoPerfil->setExtraFields(
                $data['nomePublico'],
                $data['documentoPublico'],
                $data['contatoPublico']
            );
        }

        $atendimento = new Atendimento($data['formaAtendimento'], $tipoAtendimento, $publicoPerfil);

        $this->atendimentoRepository->add($atendimento, $_SESSION['user']['id']);
    }


    /** @return Atendimento[] */
    public function all(): array
    {
        $data = $this->atendimentoRepository->findAll();


        return $data;
    }


    public function delete(int $id)
    {
        $this->atendimentoRepository->delete($id);
    }
}