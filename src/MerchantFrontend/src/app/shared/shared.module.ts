import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ContainerComponent } from './components/container/container.component';
import { FileUploadModule } from './components/file-upload/file-upload.module';

@NgModule({
  imports: [
    CommonModule,
    FileUploadModule
  ],
  declarations: [
    ContainerComponent
  ],
  exports: [
    ContainerComponent,
    FileUploadModule
  ],
})
export class SharedModule {}
