import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SharedModule } from '../../shared/shared.module';
import { ReactiveFormsModule } from '@angular/forms';
import { ClrCheckboxModule, ClrTextareaModule } from '@clr/angular';
import { OrganizationDisclaimerComponent } from './organization-disclaimer.component';




@NgModule({
  declarations: [OrganizationDisclaimerComponent],
  imports: [
    CommonModule,
    SharedModule,
    ReactiveFormsModule,
    ClrCheckboxModule,
    ClrTextareaModule,
  ]
})
export class OrganizationDisclaimerModule { }
