import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardComponent } from './dashboard.component';
import { SharedModule } from '../../shared/shared.module';
import { CoreModule } from '../../core/core.module';
import { ClrIconModule, ClrModalModule } from '@clr/angular';
import { MerchantRegisterModule } from '../merchant-register/merchant-register.module';
import { OrganizationRegisterModule } from '../organization-register/organization-register.module';
import { ReactiveFormsModule } from '@angular/forms';
import { MerchantLoginModule } from '../merchant-login/merchant-login.module';

@NgModule({
  imports: [
    CommonModule,
    SharedModule,
    CoreModule,
    ClrIconModule,
    ClrModalModule,
    MerchantRegisterModule,
    OrganizationRegisterModule,
    ReactiveFormsModule,
    MerchantLoginModule
  ],
  declarations: [
    DashboardComponent
  ],
  exports: [
    DashboardComponent
  ],
})
export class DashboardModule {}
