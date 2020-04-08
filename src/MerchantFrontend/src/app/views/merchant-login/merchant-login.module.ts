import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantLoginComponent } from './merchant-login.component';
import { ClrFormsModule, ClrModalModule } from '@clr/angular';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ClrFormsModule,
    ClrModalModule,
    SharedModule
  ],
  declarations: [
    MerchantLoginComponent
  ],
  exports: [
    MerchantLoginComponent
  ],
})
export class MerchantLoginModule {}
