export interface FileUploadViolation {
  type: 'maxFileSize' | 'accept';
  fileName: string;
  maxFileSize: number;
}
