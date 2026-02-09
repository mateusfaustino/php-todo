<?php

//namespace Tests;

use PHPUnit\Framework\TestCase;

final class TestSoma extends TestCase
{
    public function testSeOContrutorDeStudentFunciona()
    {
        $aluno = new \App\Entity\Student(
            'Chiquim',
            new DateTime('1991-01-01'),
            '123.456.789-00',
            '85 9 9898-0909',
            'Aluno legal',
            new \App\Entity\Address(),
            new \App\Entity\StudentResponsible()
        );

        $this->assertEquals('Chiquim', $aluno->getName());
        $this->assertEquals('12345678900', $aluno->getCpf());

    }
}
