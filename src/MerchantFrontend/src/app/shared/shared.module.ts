import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ContainerComponent } from './components/container/container.component';
import { FileUploadModule } from './components/file-upload/file-upload.module';
import { LocaleDatePipe } from './pipes/locale-date.pipe';

@NgModule({
  imports: [
    CommonModule,
    FileUploadModule
  ],
  declarations: [
    ContainerComponent,
    LocaleDatePipe
  ],
  exports: [
    ContainerComponent,
    FileUploadModule,
    LocaleDatePipe
  ]
})
export class SharedModule {}
