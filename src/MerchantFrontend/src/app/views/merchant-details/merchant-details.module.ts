import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantDetailsComponent } from './merchant-details.component';
import { ReactiveFormsModule } from '@angular/forms';
import { ClrFormsModule } from '@clr/angular';

@NgModule({
  imports: [
    CommonModule,
    ReactiveFormsModule,
    ClrFormsModule
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
