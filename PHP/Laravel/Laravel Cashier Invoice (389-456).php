        });
    }

    /**
     * Get the taxes applied to the invoice.
     *
     * @return \Laravel\Cashier\Tax[]
     */
    public function taxes()
    {
        if (! is_null($this->taxes)) {
            return $this->taxes;
        }

        $this->refreshWithExpandedData();

        return $this->taxes = Collection::make($this->invoice->total_tax_amounts)
            ->sortByDesc('inclusive')
            ->map(function (object $taxAmount) {
                return new Tax($taxAmount->amount, $this->invoice->currency, $taxAmount->tax_rate);
            })
            ->all();
    }

    /**
     * Determine if the customer is not exempted from taxes.
     *
     * @return bool
     */
    public function isNotTaxExempt()
    {
        return $this->invoice->customer_tax_exempt === StripeCustomer::TAX_EXEMPT_NONE;
    }

    /**
     * Determine if the customer is exempted from taxes.
     *
     * @return bool
     */
    public function isTaxExempt()
    {
        return $this->invoice->customer_tax_exempt === StripeCustomer::TAX_EXEMPT_EXEMPT;
    }

    /**
     * Determine if reverse charge applies to the customer.
     *
     * @return bool
     */
    public function reverseChargeApplies()
    {
        return $this->invoice->customer_tax_exempt === StripeCustomer::TAX_EXEMPT_REVERSE;
    }

    /**
     * Determine if the invoice will charge the customer automatically.
     *
     * @return bool
     */
    public function chargesAutomatically()
    {
        return $this->invoice->collection_method === StripeInvoice::COLLECTION_METHOD_CHARGE_AUTOMATICALLY;
    }

    /**
     * Determine if the invoice will send an invoice to the customer.
     *
     * @return bool