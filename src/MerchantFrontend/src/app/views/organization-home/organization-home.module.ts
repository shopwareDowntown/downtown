import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrganizationHomeComponent } from './organization-home.component';
import { SharedModule } from '../../shared/shared.module';
import { ClrIconModule } from '@clr/angular';
import { RouterModule } from '@angular/router';



@NgModule({
  declarations: [OrganizationHomeComponent],
  imports: [
    CommonModule,
    SharedModule,
    ClrIconModule,
    RouterModule
  ]
})
export class OrganizationHomeModule { }
