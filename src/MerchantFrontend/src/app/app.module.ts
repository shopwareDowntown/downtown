import { BrowserModule } from '@angular/platform-browser';
import { LOCALE_ID, NgModule } from '@angular/core';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { ClarityModule } from '@clr/angular';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { CoreModule } from './core/core.module';
import { SharedModule } from './shared/shared.module';
import { DashboardModule } from './views/dashboard/dashboard.module';
import { MerchantDetailsModule } from './views/merchant-details/merchant-details.module';
import { MerchantLoginModule } from './views/merchant-login/merchant-login.module';
import { MerchantRegisterModule } from './views/merchant-register/merchant-register.module';
import { OrganizationDetailsModule } from './views/organization-details/organization-details.module';
import { OrganizationRegisterModule } from './views/organization-register/organization-register.module';
import { OrganizationLoginModule } from './views/organization-login/organization-login.module';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { LocalDeliveryModule } from './views/local-delivery/local-delivery.module';
import localeDe from '@angular/common/locales/de';
registerLocaleData(localeDe);

import { MerchantAccountModule } from './views/merchant-account/merchant-account.module';
import { MerchantHomeModule } from './views/merchant-home/merchant-home.module';
import { MerchantOrdersModule } from './views/merchant-orders/merchant-orders.module';
import { registerLocaleData } from '@angular/common';
import { TranslateLoader, TranslateModule } from '@ngx-translate/core';
import { TranslateHttpLoader } from '@ngx-translate/http-loader';

export function createTranslateLoader(http: HttpClient) {
  return new TranslateHttpLoader(http, './assets/i18n/', '.json');
}

@NgModule({
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    ClarityModule,
    AppRoutingModule,
    CoreModule,
    SharedModule,
    HttpClientModule,
    TranslateModule.forRoot({
      loader: {
        provide: TranslateLoader,
        useFactory: (createTranslateLoader),
        deps: [HttpClient]
      },
      defaultLanguage: 'de'
    }),

    // Pages
    DashboardModule,
    MerchantDetailsModule,
    MerchantAccountModule,
    MerchantRegisterModule,
    MerchantLoginModule,
    MerchantHomeModule,
    OrganizationDetailsModule,
    OrganizationRegisterModule,
    OrganizationLoginModule,
    LocalDeliveryModule,
    MerchantOrdersModule
  ],
  declarations: [
    AppComponent,
  ],
  providers: [
    { provide: LOCALE_ID, useValue: 'de-DE' }
  ],
  bootstrap: [AppComponent]
})
export class AppModule {}
