<?php

namespace Fgtas\Entities;

class FormaAtendimento
{
    private ?int $id;
    public readonly string $forma; // apenas atributos padrao (presencial, whatsapp, ligação telefônica, e-mail, redes socias, teams, outra(personalizado))

    /**
     * @param string $forma
     */
    public function __construct(string $forma)
    {
        $this->forma = $forma;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public static function fromArray(array $data): FormaAtendimento
    {
        $formaAtendimento = new FormaAtendimento($data['forma']);
        $formaAtendimento->setId($data['id']);

//        if ($formaAtendimento instanceof FormaAtendimento) {
//            exit('Erro ao criar instancia da classe');
//        }

        return $formaAtendimento;
    }
}