import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FileUploadComponent } from './file-upload.component';
import {ClrIconModule} from "@clr/angular";



@NgModule({
  declarations: [FileUploadComponent],
  exports: [
    FileUploadComponent
  ],
    imports: [
        CommonModule,
        ClrIconModule
    ]
})
export class FileUploadModule { }
