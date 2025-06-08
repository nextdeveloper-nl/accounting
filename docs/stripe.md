# Stripe billing
When we are making a billing with stripe we need to provide the usage amount and the price id. Therefore we need to create a price in stripe first. The price id is then used to create the invoice item and the invoice item is used to create the invoice. The invoice is then paid with the payment method.

On the other hand we need to make sure when we are billing for IaaS services, we should be counting per ram and cpu usage. Therefore we need to create a price for each ram and cpu usage. The price id is then used to create the invoice item and the invoice item is used to create the invoice. The invoice is then paid with the payment method.

For IaaS, each different pool should have different price id in stripe. We should store this information in the database. The price id is then used to create the invoice item and the invoice item is used to create the invoice. The invoice is then paid with the payment method.
