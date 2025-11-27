<?php

namespace Plusinfolab\DodoPayments;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;

class Checkout implements Responsable
{
    /**
     * The subscription instance.
     *
     * @var \Plusinfolab\DodoPayments\Subscription
     */
    public $subscription;

    /**
     * The checkout URL.
     *
     * @var string
     */
    public $checkoutUrl;

    /**
     * Create a new checkout instance.
     *
     * @param  \Plusinfolab\DodoPayments\Subscription  $subscription
     * @param  string  $checkoutUrl
     * @return void
     */
    public function __construct(Subscription $subscription, string $checkoutUrl)
    {
        $this->subscription = $subscription;
        $this->checkoutUrl = $checkoutUrl;
    }

    /**
     * Get the redirect response for the checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toResponse($request): RedirectResponse
    {
        return redirect($this->checkoutUrl);
    }
}
