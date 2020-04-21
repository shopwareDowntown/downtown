import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantLoginComponent } from './merchant-login.component';
import { ClrFormsModule, ClrModalModule, ClrTabsModule } from '@clr/angular';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';
import { OrganizationLoginModule } from '../organization-login/organization-login.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ClrFormsModule,
    ClrModalModule,
    SharedModule,
    ClrTabsModule,
    OrganizationLoginModule
  ],
  declarations: [
    MerchantLoginComponent
  ],
  exports: [
    MerchantLoginComponent
  ],
})
export class MerchantLoginModule {}
