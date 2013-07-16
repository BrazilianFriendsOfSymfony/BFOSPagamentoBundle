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

use BFOS\PagamentoBundle\Criptografia\ServicoDeCriptografiaInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\ObjectType;

class DadosAdicionaisPagamentoType extends ObjectType
{
    const NAME = 'dados_adicionais_pagamento_type';

    /** @var ServicoDeCriptografiaInterface $servicoDeCriptografia */
    private static $servicoDeCriptografia;

    public static function setServicoDeCriptografia(ServicoDeCriptografiaInterface $service)
    {
        self::$servicoDeCriptografia = $service;
    }

    public static function getServicoDeCriptografia()
    {
        return self::$servicoDeCriptografia;
    }

    public function convertToDatabaseValue($dadosAdicionais, AbstractPlatform $platform)
    {
        if (null === $dadosAdicionais) {
            return null;
        }

        $reflection = new \ReflectionProperty($dadosAdicionais, 'data');
        $reflection->setAccessible(true);
        $data = clone $reflection->getValue($dadosAdicionais);
        $reflection->setAccessible(false);

        foreach ($data as $name => $value) {
            if (false === $value[2]) {
                unset($data[$name]);
                continue;
            }
            if(is_callable($value[0])){
                $data[$name][0] = call_user_func($value[0]);
            }
            if (true === $value[1]) {
                $data[$name][0] = self::$servicoDeCriptografia->criptografar(serialize($data[$name][0]));
            }
        }

        return parent::convertToDatabaseValue($data, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $data = parent::convertToPHPValue($value, $platform);

        if (null === $data) {
            return null;
        } else if (is_array($data)) {
            foreach ($data as $name => $value) {
                if (true === $value[1]) {
                    $data[$name][0] = unserialize(self::$servicoDeCriptografia->descriptografar($value[0]));
                }
            }

            $dadosAdicionais = new DadosAdicionais();
            $reflection = new \ReflectionProperty($dadosAdicionais, 'data');
            $reflection->setAccessible(true);
            $reflection->setValue($dadosAdicionais, $data);
            $reflection->setAccessible(false);

            return $dadosAdicionais;
        } else {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
    }

    public function getName()
    {
        return self::NAME;
    }
}
