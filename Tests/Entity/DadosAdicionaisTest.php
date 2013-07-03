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

namespace BFOS\PagamentoBundle\Tests\Entity;


use BFOS\PagamentoBundle\Entity\DadosAdicionais;

class DadosAdicionaisTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveIgnoresIfKeyDoesNotExist()
    {
        $data = new DadosAdicionais();

        $this->assertFalse($data->tem('foo'));
        $data->remover('foo');
        $this->assertFalse($data->tem('foo'));
    }

    public function testRemove()
    {
        $data = new DadosAdicionais;

        $this->assertFalse($data->tem('foo'));
        $data->adicionar('foo', 'foo', false);
        $this->assertTrue($data->tem('foo'));
        $data->remover('foo');
        $this->assertFalse($data->tem('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsEncryptionRequiredThrowsExceptionOnNonExistentKey()
    {
        $dadosAdicionais = new DadosAdicionais();
        $dadosAdicionais->ehNecessariaCriptografia('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMayBePersistedThrowsExceptionOnNonExistentKey()
    {
        $dadosAdicionais = new DadosAdicionais();
        $dadosAdicionais->podeSerPersistido('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetThrowsExceptionOnNonExistentKey()
    {
        $dadosAdicionais = new DadosAdicionais();
        $dadosAdicionais->obter('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetThrowsExceptionOnEncriptionOfNonPersistedValue()
    {
        $dadosAdicionais = new DadosAdicionais();
        $dadosAdicionais->adicionar('foo', 'bar', true, false);
    }

    /**
     * @dataProvider getTestData
     */
    public function testWithSomeData($name, $value, $encrypt, $persist)
    {
        $dadosAdicionais = new DadosAdicionais();
        $dadosAdicionais->adicionar($name, $value, $encrypt, $persist);

        $this->assertEquals($value, $dadosAdicionais->obter($name));

        if ($encrypt) {
            $this->assertTrue($dadosAdicionais->ehNecessariaCriptografia($name));
        } else {
            $this->assertFalse($dadosAdicionais->ehNecessariaCriptografia($name));
        }

        if ($persist) {
            $this->assertTrue($dadosAdicionais->podeSerPersistido($name));
        } else {
            $this->assertFalse($dadosAdicionais->podeSerPersistido($name));
        }
    }

    public function getTestData()
    {
        return array(
            array('account_holder', 'fooholder', false, true),
            array('account_number', '1234567890', true, true),
            array('account_cvv', '666', false, false),
        );
    }
}
