<?php

namespace Fgtas\Validations;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Factory;
use Respect\Validation\Validator as RespectValidator;

class Validator
{
    private array $errors = [];

    /**
     * Valida um array com base nas regras de Respect\Validation\Validator
     * ```
     * $validator->validate($dataFromForm, [
     *     'nome' => v::notEmpty(),
     *     'email' => v::notEmpty()->email(),
     *     'senha' => v::notEmpty()->min(5)
     * ]);
     * ```
     * @param array $data o array a ser validado
     * @param array $rules as regras de validação
     * @return void
     */
    public function validate(array $data, array $rules): void
    {
        Factory::setDefaultInstance(
            (new Factory())->withTranslator(function (string $message): string {
                    return $this->translateErrorMessage($message);
                })
        );

        /** @var RespectValidator $rule */
        foreach ($rules as $field => $rule) {
            try {
                $rule->setName($field)->check($data[$field] ?? null);
            } catch (ValidationException $e) {
                $this->errors[$field] = $e->getMessage();
            }
        }
    }

    public function failed(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * Tradução de todas as mensagens de erro usadas na aplicação
     *
     * @param string $message
     * @return string
     */
    private function translateErrorMessage(string $message): string
    {
        $translations = [
            "{{name}} must not be empty" => "O campo {{name}} não pode estar vazio.",
            "{{name}} must be a string" => "O campo {{name}} deve ser uma string.",
            "{{name}} must be valid email" => "O campo {{name}} deve ser um e-mail válido.",
            "{{name}} must be a valid CNPJ number" => "O campo {{name}} deve ser um CNPJ válido.",
            "{{name}} must be a valid CPF number" => "O campo {{name}} deve ser um CPF válido.",
        ];

        return $translations[$message] ?? "Erro desconhecido, contate o administrador do sistema para obter ajuda.";
    }
}