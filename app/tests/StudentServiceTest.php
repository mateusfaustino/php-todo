<?php


use App\Service\StudentService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class StudentServiceTest extends TestCase
{
    public function testSeOMetodoFindAllTrazTodosOsAlunos(): void
    {
        $service = new StudentService();

        $result = $service->findAll();

        $this->assertIsArray($result);

        $this->assertGreaterThan(0, count($result));

        $aluno1 = $result[0];

        $this->assertEquals('Joao da Silva', $aluno1->getName());
    }

    public function testCreateFromFormPersisteAlunoResponsavelEEndereco(): void
    {
        $service = new StudentService();

        $data = [
            'name' => 'Alex Frazão',
            'birthDate' => '2005-05-10',
            'cpf' => '12345678900',
            'phone' => '11999999999',
            'notes' => 'Aluno funcional',

            'responsibleName' => 'Maria da Silva',
            'responsibleDocument' => '98765432100',
            'responsiblePhone' => '11888888888',
            'responsibleEmail' => 'maria@email.com',

            'street' => 'Rua Teste',
            'number' => '123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zipcode' => '01000-000',
        ];

        // Executa o fluxo real
        $service->createFromForm($data);

        // Busca no banco
        $students = $service->findAll();

        $student = $students[count($students) - 1];

        // === Valida aluno ===
        $this->assertEquals('Alex Frazão', $student->getName());
        $this->assertEquals('12345678900', $student->getCpf());
        $this->assertEquals('11999999999', $student->getPhone());
        $this->assertEquals('Aluno funcional', $student->getNotes());
        $this->assertEquals('2005-05-10', $student->getBirthDate()->format('Y-m-d'));

        // === Valida responsável ===
        $responsible = $student->getStudentResponsible();
        $this->assertEquals('Maria da Silva', $responsible->getName());
        $this->assertEquals('98765432100', $responsible->getDocument());
        $this->assertEquals('11888888888', $responsible->getPhone());
        $this->assertEquals('maria@email.com', $responsible->getEmail());

        // === Valida endereço ===
        $address = $student->getAddress();
        $this->assertEquals('Rua Teste', $address->getStreet());
        $this->assertEquals('123', $address->getNumber());
        $this->assertEquals('São Paulo', $address->getCity());
        $this->assertEquals('SP', $address->getState());
        $this->assertEquals('01000000', $address->getZipCode());
    }

}
