            'return_url' => $returnUrl ?? route('home'),
        ], $options))['url'];
    }

    /**
     * Generate a redirect response to the customer's Stripe billing portal.
     *
     * @param  string|null  $returnUrl
     * @param  array  $options
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToBillingPortal($returnUrl = null, array $options = [])
    {
        return new RedirectResponse(
            $this->billingPortalUrl($returnUrl, $options)
        );
    }

    /**
     * Get a collection of the customer's TaxID's.
     *
     * @param  array  $options
     * @return \Illuminate\Support\Collection|\Stripe\TaxId[]
     */
    public function taxIds(array $options = [])
    {
        $this->assertCustomerExists();

        return new Collection(
            static::stripe()->customers->allTaxIds($this->stripe_id, $options)->data
        );
    }

    /**
     * Find a TaxID by ID.
     *
     * @param  string  $id
     * @return \Stripe\TaxId|null
     */
    public function findTaxId($id)
    {
        $this->assertCustomerExists();

        try {
            return static::stripe()->customers->retrieveTaxId(
                $this->stripe_id, $id, []
            );
        } catch (StripeInvalidRequestException $exception) {
            //
        }
    }

    /**
     * Create a TaxID for the customer.
     *
     * @param  string  $type
     * @param  string  $value
     * @return \Stripe\TaxId
     */
    public function createTaxId($type, $value)
    {
        $this->assertCustomerExists();

        return static::stripe()->customers->createTaxId($this->stripe_id, [
            'type' => $type,
            'value' => $value,
        ]);
    }