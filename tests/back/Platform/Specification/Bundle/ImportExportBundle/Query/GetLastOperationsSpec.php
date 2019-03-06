<?php

namespace Specification\Akeneo\Platform\Bundle\ImportExportBundle\Query;

use Akeneo\Platform\Bundle\ImportExportBundle\Query\GetLastOperationsInterface;
use Akeneo\UserManagement\Component\Model\UserInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GetLastOperationsSpec extends ObjectBehavior
{
    function let(Connection $connection)
    {
        $this->beConstructedWith($connection, ['first_job', 'second_job']);
    }

    function it_is_a_last_operations_query()
    {
        $this->shouldImplement(GetLastOperationsInterface::class);
    }

    function it_returns_last_operations(
        $connection,
        Statement $statement,
        UserInterface $user
    ) {
        $operations = [
            [
                'id' => 42,
                'date' => '2019-12-05',
                'type' => 'import',
                'label' => 'My import',
                'status' => 1,
            ],
            [
                'id' => 5,
                'date' => '2019-12-05',
                'type' => 'import',
                'label' => 'My import',
                'status' => 1,
            ]
        ];
        $user->getUsername()->willReturn('marty');
        $connection->executeQuery(Argument::cetera())->willReturn($statement);
        $statement->fetchAll()->willReturn($operations);

        $this->execute($user)->shouldReturn($operations);
    }
}
