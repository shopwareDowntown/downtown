import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
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
import { HttpClientModule } from '@angular/common/http';
import { LocalDeliveryModule } from './views/local-delivery/local-delivery.module';

import { MerchantAccountModule } from './views/merchant-account/merchant-account.module';

@NgModule({
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    ClarityModule,
    AppRoutingModule,
    CoreModule,
    SharedModule,
    HttpClientModule,

    // Pages
    DashboardModule,
    MerchantDetailsModule,
    MerchantAccountModule,
    MerchantRegisterModule,
    MerchantLoginModule,
    OrganizationDetailsModule,
    OrganizationRegisterModule,
    OrganizationLoginModule,
    LocalDeliveryModule
  ],
  declarations: [
    AppComponent,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule {}
