import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ContainerComponent } from './components/container/container.component';

@NgModule({
  imports: [
    CommonModule
  ],
  declarations: [
    ContainerComponent
  ],
  exports: [
    ContainerComponent
  ],
})
export class SharedModule {}
