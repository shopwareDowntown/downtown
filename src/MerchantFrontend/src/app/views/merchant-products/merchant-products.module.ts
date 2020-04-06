import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MerchantProductsRoutingModule } from './merchant-products-routing.module';
import { MerchantProductsListingComponent } from './merchant-products-listing/merchant-products-listing.component';
import { MerchantProductsDetailComponent } from './merchant-products-detail/merchant-products-detail.component';
import {ClrDatagridModule, ClrFormsModule, ClrIconModule} from '@clr/angular';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { FileUploadModule } from '../../shared/components/file-upload/file-upload.module';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  declarations: [MerchantProductsListingComponent, MerchantProductsDetailComponent],
  imports: [
    CommonModule,
    MerchantProductsRoutingModule,
    FormsModule,
    SharedModule,

    // Clarity
    ClrDatagridModule,
    ClrIconModule,
    ClrFormsModule,
    ReactiveFormsModule,
    FileUploadModule
  ]
})
export class MerchantProductsModule { }
