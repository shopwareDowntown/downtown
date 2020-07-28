# SwagPlatformSecurity

## Add a new Fix

* Create a new class which extends `Swag\Security\Components\AbstractSecurityFix`
* This class needs to be listed in the `\Swag\Security\Components\State::KNOWN_ISSUES`
* Adjust the snippets for the administration `src/Resources/app/administration/src/module/sw-settings-security/snippet/de-DE.json`
* Register the class in the DI with tag `kernel.event_subscriber` when you have overridden `getSubscribedEvents` and add tag `swag.security.fix` with argument `ticket` pointing to the created class

### DI Tag `swag.security.fix`

When the given ticket to the tag is inactive, all services will be removed.

## How can I check is my fix active?

For PHP use `\Swag\Security\Components\State` and call `isActive` method with your Ticket number.

For Admin use `swagSecurityState` service like so

```javascript
let swagSecurityState = Shopware.Service('swagSecurityState');

swagSecurityState.isActive('NEXT-9241')
```
