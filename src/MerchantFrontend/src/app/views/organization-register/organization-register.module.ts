import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrganizationRegisterComponent } from './organization-register.component';

@NgModule({
  imports: [
    CommonModule
  ],
  declarations: [
    OrganizationRegisterComponent
  ],
  exports: [
    OrganizationRegisterComponent
  ],
})
export class OrganizationRegisterModule {}
