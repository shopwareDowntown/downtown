import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { ClarityModule, ClrFormsModule } from '@clr/angular';
import { MerchantAccountModule } from '../merchant-account/merchant-account.module';
import { MerchantVouchersComponent } from './merchant-vouchers.component';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
  imports: [
    CommonModule,
    ReactiveFormsModule,
    ClrFormsModule,
    ClarityModule,
    MerchantAccountModule,
    SharedModule
  ],
  declarations: [
    MerchantVouchersComponent
  ],
  exports: [
    MerchantVouchersComponent
  ],
})
export class MerchantVouchersModule {
}
