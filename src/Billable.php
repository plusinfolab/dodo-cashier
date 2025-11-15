<?php

namespace Plusinfolab\DodoPayments;

use Plusinfolab\DodoPayments\Concerns\ManageSubscriptions;
use Plusinfolab\DodoPayments\Concerns\ManageTransactions;

trait Billable
{
    use ManageSubscriptions;
    use ManageTransactions;
}
