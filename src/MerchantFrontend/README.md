# Merchant and Organization Administration

The portal is a web application based on Angular. 
It offers the possibility for merchants and organizations to manage their affairs in connection to the Downtown store front.
It uses the Clarity Design System.

## Requirements

To run the portal the following requirements must be fulfilled. 
* A current version of Node https://nodejs.org/en/
* The Angluar CLI https://cli.angular.io/

Before starting the application for the first time the command `npm install` must be executed to get all required node packages.

## Start local

To start the app on your local system use the command `ng serve`. The app will be available at http://localhost:4200.

In the file `environment.ts` different parameters can be configured. 
They are needed for the communication to the Api of the Downtown backend or the Hubspot integration for the manual organization registration.

## Deployment

You can simply build and deploy the app a web sever. See https://angular.io/guide/deployment

Be sure that all parameters in the `environment.prod.ts` are well configured for your production mode.

## Structure

In the next chapter a few special parts of the app architecture will be mentioned. 

### Merchants / Organizations

This application manages two different user groups. The merchants and the organizations. The groups are clearly divided.
That means every user can be logged in as an organization or as a merchant. 
Every group has its own sidebar from which the various functionalities for the group are reachable.


### State Service

The `StateService` manages the login state. It differentiates between three different statuses:
* Logged in as merchant
* Logged in as organization
* Not logged in

The service additionally provides the current logged in merchant/ organization. 
After changes the object in the state service also needs to be updated. The service provides methods for this.

### Api Service

The `MerchantApiService` provides every method for the communication with the Downtown backend.
After extensions of the api further methods can be added here.

## Add functionalities

This part shows the common way to add new functionalities to the merchant side, or the organization side of the administration

### Create modules

Every new functionality should be encapsulated in a separate module. 
These modules should have `organization-` or `merchant-` prefixed in the name and should be positioned in the `views` folder of the source code.

The Angular Cli helps to generate modules:

`ng generate module views/organization-name-of-the-functionality`

Then a component belonging to the module can be created:

`ng generate component views/organization-name-of-the-functionality`

Of course several components with individual routing can be added to the module.

### Add a view to the layout

To add a functionality to the sidebar of the administration area (merchant or organization) two steps are necessary:
* Add a route to the base component of your functionality. This route must be added in the `app-routing.module.ts`.
It should be defined as a child route of the merchant or the organization path.
* Add a new entry to the `NAVIGATION_ADMIN_MERCHANT` or the `NAVIGATION_ADMIN_ORGANIZATION` array in the `navigation.ts`.
The previously defined route is needed here.

## Clarity Design System
[Clarity](https://clarity.design) is an open source design system that brings together UX guidelines, an HTML/CSS framework, Angular components, and Web Components. It is licensed under the MIT License.

### Clarity packages
Following description can be found at: https://clarity.design/documentation/get-started

* **@clr/core** The library of web components and foundational pieces also used in Angular components. Available starting version 3.0.
* **@clr/icons** The library that provides the custom element icons.
* **@clr/ui** Contains the static styles for building HTML components.
* **@clr/angular** Contains the Angular components. This package depends on **@clr/ui** for styles.
* **@webcomponents/webcomponentsjs** A polyfill for webcomponents for older browsers, which Clarity depends upon.
