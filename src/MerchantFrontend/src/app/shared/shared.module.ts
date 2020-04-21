import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ContainerComponent } from './components/container/container.component';
import { FileUploadModule } from './components/file-upload/file-upload.module';
import { LocaleDatePipe } from './pipes/locale-date.pipe';
import { TranslateModule } from '@ngx-translate/core';
import { SingleImageUploadContainerComponent } from './components/single-image-upload-container/single-image-upload-container.component';
import { ClrIconModule } from '@clr/angular';

@NgModule({
  imports: [
    CommonModule,
    FileUploadModule,
    TranslateModule,
    ClrIconModule
  ],
  declarations: [
    ContainerComponent,
    LocaleDatePipe,
    SingleImageUploadContainerComponent
  ],
    exports: [
        ContainerComponent,
        FileUploadModule,
        LocaleDatePipe,
        TranslateModule,
        SingleImageUploadContainerComponent
    ]
})
export class SharedModule {}
