import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MerchantAccountComponent } from './merchant-account.component';
import { ClrFormsModule, ClrModalModule } from '@clr/angular';
import { ReactiveFormsModule } from '@angular/forms';
import { ChangeMailModalComponent } from './change-mail-modal/change-mail-modal.component';
import { ChangePasswordModalComponent } from './change-password-modal/change-password-modal.component';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
  imports: [
    CommonModule,
    ReactiveFormsModule,
    ClrFormsModule,
    ClrModalModule,
    SharedModule
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
