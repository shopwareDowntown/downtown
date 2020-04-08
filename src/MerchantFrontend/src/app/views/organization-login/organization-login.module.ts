import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrganizationLoginComponent } from './organization-login.component';
import { TranslateModule } from '@ngx-translate/core';
import { SharedModule } from '../../shared/shared.module';
import { ClrIconModule, ClrInputModule } from '@clr/angular';
import { ReactiveFormsModule } from '@angular/forms';

@NgModule({
  imports: [
    CommonModule,
    SharedModule,
    ClrInputModule,
    ClrIconModule,
    ReactiveFormsModule
  ],
  declarations: [
    OrganizationLoginComponent
  ],
  exports: [
    OrganizationLoginComponent
  ],
})
export class OrganizationLoginModule {}
