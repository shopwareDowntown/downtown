import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { OrganizationMerchantListComponent } from './organization-merchant-list.component';
import { SharedModule } from '../../shared/shared.module';
import {ClrDatagridModule, ClrIconModule} from '@clr/angular';



@NgModule({
  declarations: [OrganizationMerchantListComponent],
  imports: [
    CommonModule,
    SharedModule,
    ClrDatagridModule,
    ClrIconModule
  ]
})
export class OrganizationMerchantListModule { }
