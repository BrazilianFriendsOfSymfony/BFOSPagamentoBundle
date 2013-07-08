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

class ObjetoInvalidoException extends \Exception
{

    /**
     * @param ConstraintViolationListInterface $constraints
     * @param object|string                    $objeto
     * @param int                              $code
     * @param \Exception                       $previous
     */
    public function __construct(ConstraintViolationListInterface $constraints, $objeto, $code = 0, \Exception $previous = null)
    {
        $className = '';
        if(is_string($objeto)){
            $className = $objeto;
        } elseif(is_object($objeto)){
            $className = get_class($objeto);
        }
        $message = sprintf("O objeto da classe %s é inválido. Segue a lista de violações.\n", $className);
        /** @var ConstraintViolationInterface $constraint */
        foreach ($constraints as $constraint) {
            $m = $constraint->getMessage();
            $pp = $constraint->getPropertyPath();
            if($m){
                $message .= sprintf("- %s: %s\n", $pp, $m);
            }
        }

        parent::__construct($message, $code, $previous);
    }


}
