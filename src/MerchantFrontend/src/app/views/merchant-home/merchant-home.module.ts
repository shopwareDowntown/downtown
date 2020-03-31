import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantHomeComponent } from './merchant-home.component';
import { SharedModule } from '../../shared/shared.module';
import { ClrIconModule } from '@clr/angular';
import { RouterModule } from '@angular/router';

@NgModule({
  imports: [
    CommonModule,
    SharedModule,
    ClrIconModule,
    RouterModule
  ],
  declarations: [
    MerchantHomeComponent
  ]
})
export class MerchantHomeModule {}
