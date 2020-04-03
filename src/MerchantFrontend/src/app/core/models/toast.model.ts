export interface Toast {
  id?: number;
  title: string;
  message: string;
  type: ToastType;
  timeout?: number;
}

export interface ToastEvent {
  type: ToastEventType,
  value?: any;
}

export enum ToastEventType {
  ADD = 'add',
  CLEAR = 'clear',
  CLEAR_PERMANENT = 'clear_permanent'
}

export enum ToastType {
  SUCCESS = 'success',
  ERROR = 'error',
  INFO = 'info'
}
