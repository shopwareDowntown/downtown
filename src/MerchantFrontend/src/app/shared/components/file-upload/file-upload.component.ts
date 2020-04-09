import { Component, ElementRef, EventEmitter, Input, OnInit, Output, ViewChild } from '@angular/core';
import { FileUploadViolation } from './file-upload-violation.model';

@Component({
  selector: 'portal-file-upload',
  templateUrl: './file-upload.component.html',
  styleUrls: ['./file-upload.component.scss']
})
export class FileUploadComponent {

  @Input() label: string;
  @Input() auto = true;
  @Input() maxFileSize: number;
  @Input() accept: string;
  @Input() multiple = false;
  @Input() disabled = false;
  @Input() icon = false;
  @Output() onSelect = new EventEmitter<any[]>();
  @Output() onUpload = new EventEmitter<any[]>();
  @Output() onError = new EventEmitter<FileUploadViolation>();

  @ViewChild('fileInput', { static: true }) fileInput: ElementRef;

  onFileSelect($event): void {
    const selectedFiles = $event.dataTransfer ? $event.dataTransfer.files : $event.target.files;

    const files = [];
    for (let i = 0; i < selectedFiles.length; i++) {
      const file = selectedFiles[i];
      if (this.isValid(file)) {
        files.push(file);
      }
    }

    this.onSelect.emit(files);

    if (files.length > 0 && this.auto) {
      this.upload(files);
    }

    this.fileInput.nativeElement.value = '';
  }

  private upload(files): void {
    this.onUpload.emit(files);
  }

  private isValid(file): boolean {
    if (this.maxFileSize && file.size > this.maxFileSize) {
      this.onError.emit({
        type: 'maxFileSize',
        fileName: file.name,
        maxFileSize: this.maxFileSize
      });
      return false;
    }
    return true;
  }
}
