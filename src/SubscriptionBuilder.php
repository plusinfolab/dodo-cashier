<?php

namespace Plusinfolab\DodoPayments;

use Plusinfolab\DodoPayments\Enum\PaymentStatusEnum;
use Plusinfolab\DodoPayments\Exceptions\DodoPaymentsException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

use function Laravel\Prompts\info;

/**
 *
 */
class SubscriptionBuilder
{

    protected array $data = [];
    protected $enableDebug = false;
    protected $returnCheckoutUrl = false;
    /**
     *
     * @param string $type
     * @param string $productId
     * @param Model|null $user
     */
    public function __construct(protected string $type, protected string $productId, protected ?Model $user = null)
    {

        if ($user instanceof DodoPayments::$customerModel) {
            $this->setCustomer($this->user->name, $this->user->email);
        }
        $this->setProduct($productId)->setPaymentLink();
    }

    /**
     * @param mixed ...$args
     * @return SubscriptionBuilder
     * @throws DodoPaymentsException
     */
    public function setBilling(...$args): self
    {
        // Check if the first argument is an array
        if (is_array($args[0])) {
            // Use the array directly
            $billing = $args[0];
        } else {
            // Assume arguments are passed as separate strings
            $billing = [
                'street' => $args[0] ?? null,
                'city' => $args[1] ?? null,
                'state' => $args[2] ?? null,
                'country' => $args[3] ?? null,
                'zipcode' => $args[4] ?? null,
            ];
        }

        $this->validateBilling($billing);

        $this->data['billing'] = [
            'street' => $billing['street'],
            'city' => $billing['city'],
            'state' => $billing['state'],
            'country' => strtoupper($billing['country']),
            'zipcode' => (int)$billing['zipcode']
        ];

        return $this;
    }

    /**
     * @param array $billing
     * @return void
     * @throws DodoPaymentsException
     */
    private function validateBilling(array $billing): void
    {
        if ($billing['country'] !== null && strlen($billing['country']) !== 2) {
            throw new DodoPaymentsException('Country code must be exactly 2 characters long.');
        }

        if ($billing['zipcode'] !== null && !preg_match('/^\d*$/', (string)$billing['zipcode'])) {
            throw new DodoPaymentsException('Zipcode must be numeric or null.');
        }

        // Additional validation for other fields can be added here if needed.
    }

    /**
     * @param string $name
     * @param string $email
     * @param bool $
     * @return $this
     */
    public function setCustomer(string $name, string $email, bool $isNewCustomer = false): self
    {
        $this->data['customer'] = [
            'name' => $name,
            'email' => $email,
            'create_new_customer' => $isNewCustomer
        ];
        return $this;
    }

    /**
     * @param bool $paymentLink
     * @return $this
     */
    public function setPaymentLink(bool $paymentLink = true): self
    {
        $this->data['payment_link'] = $paymentLink;
        return $this;
    }

    /**
     * @param string $productId
     * @param int $quantity
     * @return $this
     */
    public function setProduct(string $productId, int $quantity = 1): self
    {
        $this->data['product_id'] = $productId;
        $this->data['quantity'] = $quantity;
        return $this;
    }

    /**
     * @param string $returnUrl
     * @return $this
     */
    public function setReturnUrl(string $returnUrl): self
    {
        $this->data['return_url'] = $returnUrl;
        return $this;
    }

    /**
     * @param int $trialDays
     * @return $this
     */
    public function setTrialPeriodDays(int $trialDays): self
    {
        $this->data['trial_period_days'] = $trialDays;
        return $this;
    }

    /**
     * @param array $metadata
     * @return $this
     */
    public function setMetadata(array $metadata): self
    {
        $this->data['metadata'] = (object) array_map(fn($value) => (string) $value, $metadata);
        return $this;
    }

    /**
     * @param bool $enableDebug
     * @return $this
     */
    public function enableDebug(bool $enableDebug = true): self
    {
        $this->enableDebug = $enableDebug;
        return $this;
    }

    public function returnCheckoutUrl(bool $returnCheckoutUrl): self
    {
        $this->returnCheckoutUrl = $returnCheckoutUrl;
        return $this;
    }

    /**
     * @throws DodoPaymentsException
     */
    public function create()
    {
        $response = DodoPayments::api('post', 'subscriptions', $this->data);
        if ($response->failed()) {
            throw new DodoPaymentsException('Failed to create subscription: ' . $response->body());
        }

        $subscription = $this->user->subscriptions()->create([
            'type' => $this->type,
            'subscription_id' => $response->json('subscription_id'),
            'product_id' => $this->data['product_id'],
            'status' => PaymentStatusEnum::PENDING
        ]);

        return new Checkout($subscription, $response->json('payment_link'));
    }
}
