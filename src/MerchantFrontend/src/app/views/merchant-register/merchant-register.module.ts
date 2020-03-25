import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantRegisterComponent } from './merchant-register.component';

@NgModule({
  imports: [
    CommonModule
  ],
  declarations: [
    MerchantRegisterComponent
  ],
  exports: [
    MerchantRegisterComponent
  ],
})
export class MerchantRegisterModule {}
