import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantRegisterComponent } from './merchant-register.component';
import { ClarityModule } from '@clr/angular';
import { ReactiveFormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
  imports: [
    CommonModule,
    ClarityModule,
    ReactiveFormsModule,
    SharedModule
  ],
  declarations: [
    MerchantRegisterComponent
  ],
  exports: [
    MerchantRegisterComponent
  ]
})
export class MerchantRegisterModule {}
