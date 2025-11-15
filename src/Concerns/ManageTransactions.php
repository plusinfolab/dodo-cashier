<?php

namespace Plusinfolab\DodoCashier\Concerns;

use Plusinfolab\DodoCashier\DodoPayments;

trait ManageTransactions
{
    /**
     * Get all of the transactions for the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function transactions()
    {
        return $this->hasMany(DodoPayments::$transactionModel, $this->getForeignKey())->orderBy('billed_at', 'desc');
    }
}
