<?php

namespace Plusinfolab\DodoCashier;

use Plusinfolab\DodoCashier\Concerns\ManageSubscriptions;
use Plusinfolab\DodoCashier\Concerns\ManageTransactions;

trait Billable
{
    use ManageSubscriptions;
    use ManageTransactions;
}
