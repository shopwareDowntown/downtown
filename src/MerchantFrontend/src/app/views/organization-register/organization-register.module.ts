import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrganizationRegisterComponent } from './organization-register.component';
import { ClrModalModule } from '@clr/angular';

@NgModule({
  imports: [
    CommonModule,
    ClrModalModule
  ],
  declarations: [
    OrganizationRegisterComponent
  ],
  exports: [
    OrganizationRegisterComponent
  ],
})
export class OrganizationRegisterModule {}
