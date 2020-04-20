# Portal

This document holds only informations about the changes in this project. For the usage and development of Shopware 6. Please checkout the docs at [https://docs.shopware.com/en](https://docs.shopware.com/en)

## API Documentation

* Merchant API Documentation can be found at `/merchant-api/v1/_info/swagger.html`
* Organization API Documentation can be found at `/organization-api/v1/_info/swagger.html`

## General

* The environment variable `MERCHANT_PORTAL` links to the angular application. This variable is used to generate the links
* After the installation following two commands should be executed to ensure the existence of the basic categories and media folders
    *   `./bin/console create:default:categories`
    *   `./bin/console create:default:media:folder`

## Organization

* Each sales channel is an organization. The organization itself is an extension of the sales-channel.
* Using the organization-api, a user can login and change sales channel configuration like: `logo`, `home page`, `name`, `disclaimer` and unlock `merchants`
* The organizations are created manuelly. So you have to create an sales channel and fill the required fields of the organization. After creating the sales channel, the organization gets an email with a generated password. 
* For the api part, we have created a new route scope `organization-api`. In this scope you get an SalesChannelContext from the associated sales channel and a `OrganizationEntity` of the logged in organization.
```php
public function loadOne(OrganizationEntity $organizationEntity, SalesChannelContext $context): JsonResponse
{
    return new JsonResponse($organizationEntity);
}
```

## Merchant

* Merchant is an own new entity in the System. It its assigned to an sales channel (sales-channel => organization) and a category. 
* Categories are used to organize the merchants. The merchants are also only listed in this category
* All products has to be assigned to an merchant
* Following product types are currently supported
  * `product` - Normal product
  * `voucher` - Voucher
  * `service` - Service product
  * `storeWindow` - Product which cannot be ordered
* The merchant api has a new route scope `merchant-api`. In this scope you get an SalesChannelContext from the associated sales channel and a `MerchantEntity` of the logged in merchant.

## Storefront

* A merchant needs to be `active` and `public` to get listed.
    * You should use the filter `Shopware\Production\Merchants\Content\Merchant\MerchantAvailableFilter` always in the storefront, to ensure only the correct entries are listed
* To list the merchants in the category, you have to create a new cms page and add a merchant listing element. This new page has to be assigned to all categories

## Checkout

* Only from one merchant and one product type at once can be bought
* For each order the merchant gets an email with the ordered products
* Shipping methods are only available when its also unlocked from the merchant. 
