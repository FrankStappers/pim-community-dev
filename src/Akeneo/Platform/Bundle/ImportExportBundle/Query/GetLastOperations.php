<?php
declare(strict_types=1);

namespace Akeneo\Platform\Bundle\ImportExportBundle\Query;

use Akeneo\UserManagement\Component\Model\UserInterface;
use Doctrine\DBAL\Connection;

/**
 * @author    Willy Mesnage <willy.mesnage@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GetLastOperations implements GetLastOperationsInterface
{
    /** @var Connection */
    private $connection;

    /** @var array */
    private $jobInstanceBlackList;

    public function __construct(Connection $connection, array $jobInstanceBlackList)
    {
        $this->connection = $connection;
        $this->jobInstanceBlackList = $jobInstanceBlackList;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(UserInterface $user): array
    {
        $query = <<< SQL
    SELECT
        execution.id,
        execution.start_time as date,
        instance.id as job_instance_id,
        instance.type,
        instance.label,
        execution.status,
        COUNT(warning.id) as warningCount
    FROM akeneo_batch_job_execution as execution
    INNER JOIN akeneo_batch_job_instance as instance
        ON instance.id = execution.job_instance_id
    LEFT JOIN akeneo_batch_step_execution as step
        ON step.job_execution_id = execution.id
    LEFT JOIN akeneo_batch_warning as warning
        ON warning.step_execution_id = step.id
    WHERE execution.user = :username AND instance.code NOT IN (:blackList)
    GROUP BY execution.id
    ORDER BY execution.start_time DESC
    LIMIT 10
SQL;

        $statement = $this->connection->executeQuery(
            $query,
            ['username' => $user->getUsername(), 'blackList' => $this->jobInstanceBlackList],
            ['username' => \PDO::PARAM_STR, 'blackList' => Connection::PARAM_STR_ARRAY]
        );

        return $statement->fetchAll();
    }
}
