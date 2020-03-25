import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrganizationDetailsComponent } from './organization-details.component';

@NgModule({
  imports: [
    CommonModule
  ],
  declarations: [
    OrganizationDetailsComponent
  ],
  exports: [
    OrganizationDetailsComponent
  ],
})
export class OrganizationDetailsModule {}
