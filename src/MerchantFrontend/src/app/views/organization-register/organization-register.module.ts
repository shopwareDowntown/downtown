import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrganizationRegisterComponent } from './organization-register.component';
import { ClarityModule } from "@clr/angular";

@NgModule({
  imports: [
    CommonModule,
    ClarityModule
  ],
  declarations: [
    OrganizationRegisterComponent
  ],
  exports: [
    OrganizationRegisterComponent
  ],
})
export class OrganizationRegisterModule {}
