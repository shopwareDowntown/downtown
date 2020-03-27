import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DashboardComponent } from './dashboard.component';
import { SharedModule } from '../../shared/shared.module';
import { CoreModule } from '../../core/core.module';
import { ClrIconModule, ClrModalModule } from '@clr/angular';
import { MerchantRegisterModule } from '../merchant-register/merchant-register.module';
import { OrganizationRegisterModule } from '../organization-register/organization-register.module';
import { ReactiveFormsModule } from '@angular/forms';

@NgModule({
  imports: [
    CommonModule,
    SharedModule,
    CoreModule,
    ClrIconModule,
    ClrModalModule,
    MerchantRegisterModule,
    OrganizationRegisterModule,
    ReactiveFormsModule
  ],
  declarations: [
    DashboardComponent
  ],
  exports: [
    DashboardComponent
  ],
})
export class DashboardModule {}
