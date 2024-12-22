# Invoices
Invoices are basicly an object that holds constantly updated bills until they are sealed. When the invoice is sealed, it is converted to be a virtual invoice with number. However the invoices are not paid the invoice will never be converted to a physical invoices.

# definitions
- term_year: represents which year is the invoice is about
- term_month: represents which month is the invoice is about.

# Rules for invoicing

- The common currency ID of the invoice is determined with the customers providers currency. If the provider is using USD, then the invoice is created as USD.
- If the invoice is sealed, which means that we cannot alter the invoice and/or add a new item to the invoice.
- When the invoice is sealed, the helper will create a new invoice to be able to altered.
- The invoice is sealed under various conditions;
- - The customer may have made early payment.
- - The customer may be using pre-paid services
- Due date will be created after we seal the invoice
- Invoices will be converted to an actual invoice when they are paid. Until that time invoices will be left as unpaid orders.
