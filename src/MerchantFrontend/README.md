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


### State Machine

### Api Service

## Add functionalities

### 

### Add a view to the layout


## Clarity Design System
[Clarity](https://clarity.design) is an open source design system that brings together UX guidelines, an HTML/CSS framework, Angular components, and Web Components. It is licensed under the MIT License.

### Clarity packages
Following description can be found at: https://clarity.design/documentation/get-started

* **@clr/core** The library of web components and foundational pieces also used in Angular components. Available starting version 3.0.
* **@clr/icons** The library that provides the custom element icons.
* **@clr/ui** Contains the static styles for building HTML components.
* **@clr/angular** Contains the Angular components. This package depends on **@clr/ui** for styles.
* **@webcomponents/webcomponentsjs** A polyfill for webcomponents for older browsers, which Clarity depends upon.
