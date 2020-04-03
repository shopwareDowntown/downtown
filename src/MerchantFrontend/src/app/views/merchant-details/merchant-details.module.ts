import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantDetailsComponent } from './merchant-details.component';
import { ReactiveFormsModule } from '@angular/forms';
import { ClarityModule, ClrFormsModule } from '@clr/angular';
import { MerchantAccountModule } from '../merchant-account/merchant-account.module';
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
    MerchantDetailsComponent
  ],
  exports: [
    MerchantDetailsComponent
  ],
})
export class MerchantDetailsModule {
}
