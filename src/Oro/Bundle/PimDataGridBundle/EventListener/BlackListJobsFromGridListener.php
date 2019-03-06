<?php
declare(strict_types=1);

namespace Oro\Bundle\PimDataGridBundle\EventListener;

use Oro\Bundle\DataGridBundle\Event\BuildAfter;

/**
 * Black list job codes from process tracker grid.
 *
 * @author    Willy Mesnage <willy.mesnage@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class BlackListJobsFromGridListener
{
    /** @var array */
    private $blackListedJobCodes;

    public function __construct(array $blackListedJobCodes)
    {
        $this->blackListedJobCodes = $blackListedJobCodes;
    }

    public function onBuildAfter(BuildAfter $event): void
    {
        $dataSource = $event->getDatagrid()->getDatasource();

        $parameters = $dataSource->getParameters();
        $parameters['blackListedJobCodes'] = $this->blackListedJobCodes;
        $dataSource->setParameters($parameters);

        $qb = $dataSource->getQueryBuilder();
        $qb->andWhere($qb->expr()->notIn('j.code', ':blackListedJobCodes'));
    }
}
