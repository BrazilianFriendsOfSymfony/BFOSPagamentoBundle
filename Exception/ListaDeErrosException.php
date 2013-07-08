<?php
 /*
 * This file is part of the Duo Criativa software.
 * Este arquivo é parte do software da Duo Criativa.
 *
 * (c) Paulo Ribeiro <paulo@duocriativa.com.br>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BFOS\PagamentoBundle\Exception;


use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ListaDeErrosException extends \Exception
{

    /**
     * @param array      $erros
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = '', $erros = array(), $code = 0, \Exception $previous = null)
    {
        if (strlen($message)) {
            $message .= "\n";
        }
        foreach ($erros as $erro) {
            $message .= sprintf("- %s\n", $erro);
        }

        parent::__construct($message, $code, $previous);
    }

    public function adicionar($mensagemErro)
    {
        $this->message .= sprintf("- %s\n", $mensagemErro);
    }
}
