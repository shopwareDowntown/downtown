import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ToastContainerComponent } from './toast-container.component';
import { ToastModule } from '../toast/toast.module';

@NgModule({
  imports: [
    CommonModule,
    ToastModule
  ],
  declarations: [
    ToastContainerComponent
  ],
  exports: [
    ToastContainerComponent
  ],
})
export class ToastContainerModule {}
