# Contracts
In contracts we have two tables;

- contracts
- contract_items

The contracts can be either; fixed price contract or fixed discount rate contract. At the moment we dont have third version of a contract.

# Contract Items
When an item in the database is related to this item, while calculating the price, the related module should check if the model has a contract item or not. If the object/model has a contract item, then the module maintainer should take that contract into consideration while calculating the price.

The set item price function of invoice item, has 2 more parameters while saving the invoice item, one is the details of the calculation, where you can save how you calculated the price for more transparency, the second it the contract item that you used while calculating the invoice. The contract item can be nullable, therefore you dont need to apply any discount.
