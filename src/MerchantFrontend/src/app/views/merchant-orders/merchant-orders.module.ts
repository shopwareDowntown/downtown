import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantOrdersListingComponent } from './merchant-orders-listing/merchant-orders-listing.component';
import { MerchantOrdersRoutingModule } from './merchant-orders-routing.module';
import { ClrDatagridModule, ClrIconModule } from '@clr/angular';
import { SharedModule } from '../../shared/shared.module';



@NgModule({
  declarations: [MerchantOrdersListingComponent],
  imports: [
    CommonModule,
    MerchantOrdersRoutingModule,
    ClrDatagridModule,
    ClrIconModule,
    SharedModule
  ]
})
export class MerchantOrdersModule { }
