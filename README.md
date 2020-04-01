# Project Downtown
With our project, we want to help local cities and communities and the retailers on site to maintain their customer relations even in times of Corona and to be able to quickly offer products without the overhead that a classic shop system of any size entails. The retailer can choose whether to appear in the marketplace exclusively with his name and contact details, or whether he wants to have his products sold directly via the solution. Also the sale of vouchers, with which the own fans / customers can participate in solidarity to keep the shop open, is possible. All this is as minimalistic as possible and requires hardly any technical knowledge or industry expertise. Products can be created via CSV import / table or app. Technically everything is based on Shopware 6 and therefore the project has access to the more than 400 extensions that are already available for Shopware 6. Let's work together with the solution to create pragmatic solutions for retailers who are currently facing the ruins of their existence. 

# More details
You find much more details in the project wiki: https://github.com/shopwareDowntown/portal/wiki

For the german speaking region there is already a free-of-use SaaS offering available, that is using this open source project in the backbone.
http://www.downtowns.io

# Development

## Being part of it
This project is being jointly developed by Shopware, community volunteers and other partners to be adapted for different markets / regions and to provide additional functionality.
If you are interested in participating, please send us a short message or join our Slack Channel

## Requirements

See [https://docs.shopware.com/en/shopware-platform-dev-en/getting-started/requirements](https://docs.shopware.com/en/shopware-platform-dev-en/getting-started/requirements)

NPM and Node are only required during the build process and for development. If you dont have javascript customizations, it's not required at all. Because the storefront and admin are pre-build.

If you are using a separate build server, consider having NPM and Node as build-only requirements. Your operating application server doesn't require any of these to run Shopware 6.

## Setup and install

To setup the environment and install with a basic setup run the following commands:

```bash
# clone newest 6.1 patch version from github 
git clone --branch=6.1 https://github.com/shopware/production shopware
cd shopware

# install shopware and dependencies according to the composer.lock 
composer install

# setup the environment
bin/console system:setup
# or create .env yourself, if you need more control
# create jwt secret: bin/console system:generate-jwt-secret
# create app secret: APP_SECRET=$(bin/console system:generate-app-secret)
# create .env

# create database with a basic setup (admin user and storefront sales channel)
bin/console system:install --create-database --basic-setup

# or use the interactive installer in the browser: /recovery/install/index.php
```

## Update

To update the project just run this:

```bash
# pull newest changes from origin
git pull origin

# the (pre|post)-(install|update)-cmd will execute all steps automatically
composer install
```

