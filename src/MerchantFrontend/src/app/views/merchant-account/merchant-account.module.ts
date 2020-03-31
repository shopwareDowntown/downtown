import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantAccountComponent } from './merchant-account.component';
import { ClrFormsModule, ClrModalModule } from '@clr/angular';
import { ReactiveFormsModule } from '@angular/forms';
import { ChangeMailModalComponent } from './change-mail-modal/change-mail-modal.component';
import { ChangePasswordModalComponent } from './change-password-modal/change-password-modal.component';

@NgModule({
  imports: [
    CommonModule,
    ReactiveFormsModule,
    ClrFormsModule,
    ClrModalModule
  ],
  declarations: [
    MerchantAccountComponent,
    ChangeMailModalComponent,
    ChangePasswordModalComponent
  ],
  exports: [
    MerchantAccountComponent
  ],
})
export class MerchantAccountModule {}
