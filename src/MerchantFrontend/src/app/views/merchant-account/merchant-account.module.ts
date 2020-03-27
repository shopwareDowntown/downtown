import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantAccountComponent } from './merchant-account.component';
import { ClrFormsModule, ClrIconModule } from '@clr/angular';
import { ReactiveFormsModule } from '@angular/forms';

@NgModule({
  imports: [
    CommonModule,
    ReactiveFormsModule,
    ClrFormsModule
  ],
  declarations: [
    MerchantAccountComponent
  ],
  exports: [
    MerchantAccountComponent
  ],
})
export class MerchantAccountModule {}
