import {Component, EventEmitter, Input, OnChanges, Output, SimpleChanges} from '@angular/core';

@Component({
  selector: 'portal-single-image-upload-container',
  templateUrl: './single-image-upload-container.component.html',
  styleUrls: ['./single-image-upload-container.component.scss']
})
export class SingleImageUploadContainerComponent implements OnChanges {

  @Input() existingImageUrl: string = null;
  @Input() removable = false;
  @Output() newImageSelected = new EventEmitter<File>();
  @Output() imageRemoved = new EventEmitter<void>();
  selectedImage: File = null;

  constructor() { }

  imageSelected(value: File[]) {
    const image = value[0];
    this.selectedImage = image;
    this.newImageSelected.emit(image);
  }

  ngOnChanges(changes: SimpleChanges): void {
    this.selectedImage = null;
  }
}
