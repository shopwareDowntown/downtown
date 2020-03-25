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

@NgModule({
  imports: [
    BrowserModule,
    AppRoutingModule,
    ClarityModule,
    BrowserAnimationsModule,
    CoreModule,
    SharedModule,
    DashboardModule,
    MerchantDetailsModule,
    MerchantRegisterModule,
    MerchantLoginModule,
    OrganizationDetailsModule,
    OrganizationRegisterModule,
    OrganizationLoginModule
  ],
  declarations: [
    AppComponent,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule {}
