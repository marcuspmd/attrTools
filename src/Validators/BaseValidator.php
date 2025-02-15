<?php

namespace Marcuspmd\AttrTools\Validators;

use Closure;
use DateTime;

/**
 * Classe abstrata BaseValidator
 *
 * Esta classe fornece a base para validadores de campos. Ela define propriedades
 * comuns, bem como métodos utilitários para manipulação de valores, verificação
 * condicional de validação e formatação de mensagens de erro.
 *
 * **Propriedades:**
 * - $field: Nome do campo a ser validado (string).
 * - $message: Mensagem de erro personalizada (string).
 * - $nullable: Indica se o campo pode ser nulo (bool).
 * - $emptyToNull: Indica se valores vazios devem ser convertidos para nulo (bool).
 * - $errorCode: Código de erro opcional associado à validação (int).
 * - $when: Método ou condição que determina se a validação deve ser aplicada.
 * - $context: Contexto no qual a validação está sendo executada, podendo ser uma
 *   instância de uma classe com métodos condicionais de validação.
 */
abstract class BaseValidator
{
    /**
     * @var mixed $context Contexto ou objeto relacionado à validação, podendo ser usado para condições.
     */
    public $context;

    /**
     * Construtor do validador base.
     *
     * @param string|null $field Nome do campo a ser validado.
     * @param string|null $message Mensagem de erro personalizada.
     * @param bool|null $nullable Indica se o campo pode ser nulo.
     * @param bool|null $emptyToNull Se true, valores vazios serão convertidos em nulos.
     * @param int|null $errorCode Código de erro associado à validação.
     * @param string|null $when Nome de um método no contexto ($this->context) que determina se deve validar, por padrão será feita a validação.
     */
    public function __construct(
        public ?string $field = null,
        public ?string $message = null,
        public ?bool $nullable = false,
        public ?bool $emptyToNull = false,
        public ?int $errorCode = null,
        public ?string $when = null,
        public mixed $min = null,
        public mixed $max = null,
        public mixed $callback = null,
        public ?DateTime $minDate = null,
        public ?DateTime $maxDate = null,
        public ?int $scale = null,
        public ?int $precision = null,
        public mixed $pattern = null,
        public mixed $enum = null,
        public mixed $valueToCompare = null,
        public mixed $fieldToCompare = null,
        public mixed $type = null,
        public mixed $allowedValues = [],
        public mixed $instance = null,
    ) {
    }

    /**
     * Método mágico para chamadas dinâmicas.
     *
     * Aqui é interceptada a chamada ao método 'isValid', verificando se existe uma condição 'when'.
     * Caso haja uma condição e essa condição (um método do contexto) retorne false, a validação é
     * considerada verdadeira sem a necessidade de execução (i.e., a validação é ignorada).
     *
     * @param string $method Nome do método chamado.
     * @param array $arguments Argumentos passados ao método.
     * @return mixed Retorno do método solicitado ou true se a condição 'when' não permitir validação.
     */
    public function __call($method, $arguments)
    {
        if ($method === 'isValid' && $this->when !== null) {
            if (method_exists($this->context, $this->when) && $this->context->{$this->when}() === false) {
                return true;
            }
        }
        return $this->{$method}(...$arguments);
    }

    /**
     * Obtém o valor do campo a partir do input fornecido, tratando espaços,
     * valores nulos e conversões entre array/objeto e strings.
     *
     * Este método lida com:
     * - Conversão de valores vazios em nulos se $emptyToNull for true.
     * - Trim de strings.
     * - Acesso a campos aninhados (como "endereco.rua") em arrays ou objetos.
     *
     * @param mixed $value O valor original do campo.
     * @return mixed O valor tratado do campo.
     */
    public function getValue($value)
    {
        // Converte valor vazio em null se $emptyToNull for habilitado
        if ($this->emptyToNull && empty($value)) {
            $this->nullable = true;
            return null;
        }

        // Retorna null se o valor for explicitamente nulo
        if ($value === null) {
            return null;
        }

        // Trim de strings
        if (is_string($value)) {
            return trim($value);
        }

        // Valores numéricos retornados como estão
        if (is_numeric($value)) {
            return $value;
        }

        // Valores booleanos retornados como estão
        if (is_bool($value)) {
            return $value;
        }

        // Se não houver campo definido, apenas retorna o valor
        if ($this->field === null) {
            return $value;
        }

        // Verifica se o campo é composto (aninhado) utilizando ponto (.)
        if (strstr($this->field, '.') === false) {
            return $value;
        }

        // Se o valor não for array nem objeto, não há como navegar pelos campos aninhados
        if (!is_array($value) && !is_object($value)) {
            return $value;
        }

        // Se for objeto, converte para array antes de navegar
        if (is_object($value)) {
            $value = json_decode(json_encode($value), true);
        }

        // Navega pelos subcampos definidos pelo "field" (ex: "endereco.rua")
        $multArray = explode('.', $this->field);

        foreach ($multArray as $key) {
            if (is_object($value)) {
                $value = (array) $value;
            }
            if (is_array($value)) {
                $value = $value[$key] ?? null;
            }
        }

        // Aplica trim caso o valor final seja string
        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    /**
     * Obtém a mensagem de erro formatada.
     *
     * Caso a propriedade $message não esteja definida, chama $this->setMessage() para
     * tentar obter uma mensagem padrão. Se ainda assim não houver mensagem, retorna uma
     * mensagem genérica. Antes de retornar, aplica a substituição de placeholders com
     * $this->parseMessage().
     *
     * @return string Mensagem de erro formatada.
     */
    public function getError(): string
    {
        if ($this->message === null) {
            $this->message = $this->setMessage();
        }

        if ($this->message !== null) {
            return $this->parseMessage($this->message);
        }

        return 'Field " ' . $this->field . ' " inválido.';
    }

    /**
     * Retorna o array de erros, neste caso apenas um array contendo a única mensagem de erro.
     *
     * @return array Array contendo a(s) mensagem(ns) de erro.
     */
    public function getErrors(): array
    {
        return [$this->getError()];
    }

    /**
     * Define a mensagem padrão de erro caso $message esteja nula.
     *
     * Pode ser sobrescrito por classes filhas para retornar mensagens específicas.
     *
     * @return string|null Mensagem padrão ou null se não houver nenhuma.
     */
    protected function setMessage(): ?string
    {
        return null;
    }

    /**
     * Faz a substituição de placeholders na mensagem de erro.
     *
     * O padrão de placeholder é {{nome_da_propriedade}},
     * e a substituição será feita com o valor da propriedade correspondente.
     *
     * @param string $message Mensagem contendo placeholders.
     * @return string Mensagem com placeholders substituídos.
     */
    private function parseMessage($message): string
    {
        $pattern = '/{{(.*?)}}/';
        if (preg_match_all($pattern, $message, $matches) === false) {
            return $message;
        }

        foreach ($matches[1] as $match) {
            $auxMatch = '{{' . $match . '}}';
            $auxValue = $this->{$match} ?? '';
            $message = str_replace($auxMatch, $auxValue, $message);
        }

        return $message;
    }
}
