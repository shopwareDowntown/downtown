import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ToastComponent } from './toast.component';
import { ClrIconModule } from '@clr/angular';

@NgModule({
  imports: [
    CommonModule,
    ClrIconModule
  ],
  declarations: [
    ToastComponent
  ],
  exports: [
    ToastComponent
  ],
})
export class ToastModule {}
