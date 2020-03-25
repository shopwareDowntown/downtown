import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantDetailsComponent } from './merchant-details.component';

@NgModule({
  imports: [
    CommonModule
  ],
  declarations: [
    MerchantDetailsComponent
  ],
  exports: [
    MerchantDetailsComponent
  ],
})
export class MerchantDetailsModule {}
