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

namespace BFOS\PagamentoBundle\Entity;


use BFOS\PagamentoBundle\Model\DadosAdicionaisInterface;

class DadosAdicionais implements DadosAdicionaisInterface
{
    private $data;
    private $listeners;

    public function __construct()
    {
        $this->data = array();
    }

    public function remover($name)
    {
        unset($this->data[$name]);
    }

    public function ehNecessariaCriptografia($name)
    {
        if (!isset($this->data[$name])) {
            throw new \InvalidArgumentException(sprintf('There is no data with key "%s".', $name));
        }

        return $this->data[$name][1];
    }

    public function podeSerPersistido($name)
    {
        if (!isset($this->data[$name])) {
            throw new \InvalidArgumentException(sprintf('There is no data with key "%s".', $name));
        }

        return $this->data[$name][2];
    }

    public function adicionar($name, $value, $encrypt = true, $persist = true)
    {
        if ($encrypt && !$persist) {
            throw new \InvalidArgumentException(sprintf('Non persisted field cannot be encrypted "%s".', $name));
        }

        $this->data[$name] = array($value, $encrypt, $persist);
    }

    public function obter($name)
    {
        if (!isset($this->data[$name])) {
            return null;
//            throw new \InvalidArgumentException(sprintf('There is no data with key "%s".', $name));
        }

        return $this->data[$name][0];
    }

    public function tem($name)
    {
        return isset($this->data[$name]);
    }

    public function todos()
    {
        return $this->data;
    }

    public function igual(DadosAdicionaisInterface $data)
    {
        $data = $data->todos();
        ksort($data);

        $cData = $this->data;
        ksort($cData);

        return $data === $cData;
    }
}